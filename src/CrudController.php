<?php

namespace Emolinablas\LaravelVueCrud;

use App\Http\Controllers\Controller;

use App\Events\GraficasProyeccion;
use App\Events\ProductAdded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class CrudController extends Controller
{
    private static $tabla;
    private static $tablaId;
    private static $titulo;

    private static $colSlug;
    private static $separatorSlug = '-';

    private static $camposShow = [];
    private static $camposEdit = [];

    private static $wheres = [];
    private static $wheresRaw = [];
    private static $lefJoins = [];

    private static $botonesExtra = [];
    private static $subTablas = [];
    private static $orders = [];
    private static $groups = [];

    private static $template = 'layouts/app';

    public function setLeftJoin($tabla, $campo1, $operador,  $campo2) {
        self::$lefJoins[] = ['tabla' => $tabla, 'campo1' => $campo1, 'operador' => $operador, 'campo2' => $campo2 ];
        return $this;
    }

    public function getLeftJoins() {
        return self::$lefJoins;
    }

    public function setSubTabla(array  $subtabla) {
        self::$subTablas[] = $subtabla;
        return $this;
    }

    public function getSubTablas() {
        return self::$subTablas;
    }

    public function setBotonExtra(array $boton) {

        self::$botonesExtra[] = $boton;

        return $this;
    }

    public function getBotonesExtra(){
        return self::$botonesExtra;
    }

    public function setCampo(array $campo)
    {

        if($campo['type'] == 'numeric') {
            if(!isset($campo['decimales'])) {
                $campo['decimales'] = 0;
            }
        }


        if(!isset($campo['as'])) {
            $campo['as'] = $campo['campo'];
        }


        if($campo['show']) {
            self::$camposShow[] = $campo;
        }

        if($campo['edit']) {
            $campo['valor'] = "";

            if($campo['type'] == 'select') {
                $values = DB::table($campo['name-table'])
                    ->select($campo['value-column'].' as value', $campo['text-column'].' as text')
                    ->get()->toArray();

                $campo['values'] = $values;
            }

            self::$camposEdit[] = $campo;
        }

        return $this;
    }

    public function getCamposShow() {
        return self::$camposShow;
    }

    public function getCampoEdit() {
        return self::$camposEdit;
    }

    public function setTitulo($titulo)
    {
        self::$titulo = $titulo;
        return $this;
    }

    public function getTitulo()
    {
        return self::$titulo;
    }

    public function setTabla($tabla)
    {
        self::$tabla = $tabla;
        return $this;
    }

    public function getTabla()
    {
        return self::$tabla;
    }

    public function setTablaId($tablaId) {
        self::$tablaId = $tablaId;
        return $this;
    }

    public function getTablaId()
    {
        return self::$tablaId;
    }

    public function getData() {


    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->has('getSubTabla')) {

            $datosSubTabla = json_decode(request()->input('datosSubTabla'));


            $id = request()->input('id');
            //dd($id);

            $datos = DB::table($datosSubTabla->tabla)->where($datosSubTabla->idtablaprincipal, $id)->get()->toArray();

            return response()->json($datos);

        } else if(request()->has('getDatos')) {


            $columns = json_decode(request()->input('camposShow'));
            $leftJoins = json_decode(request()->input('leftJoins'));

            //dd($columns);

            $length = request()->input('length');
            $column = request()->input('column'); //Index
            $dir = request()->input('dir');
            $searchValue = request()->input('search');

            $query = DB::table(request()->input('tabla'));

            foreach ($columns as $c) {
                $query->addSelect(DB::raw($c->campo . ' as `' . $c->as.'` '));

                if($c->type == 'select') {
                    $query->addSelect(DB::raw($c->{'campo-edit'} . ' as `' . $c->{'campo-edit'}.'` '));
                }
            }

            if(count($leftJoins) > 0 ) {
                foreach ($leftJoins as $l) {
                    $query->leftJoin($l->tabla, $l->campo1, $l->operador, $l->campo2);
                }
            }

            $query->addSelect(request()->input('tabla').'.'.request()->input('tablaid'));


                $query->groupBy(request()->input('tabla').'.'.request()->input('tablaid'));

                $query->orderBy($columns[$column]->campo, $dir);

            if ($searchValue) {
                $query->where(
                    function ($query) use ($searchValue) {
                        $query->where('nombre', 'like', '%' . $searchValue . '%')
                            ->orWhere('stock', 'like', '%' . $searchValue . '%');
                    }
                );
            }

            $projects = $query->paginate($length);

            //dd($projects);

            return response()->json(['data' => $projects, 'draw' => request()->input('draw')]);
        } else {

            //dd($this->getCampoEdit());

            return view('laravel-vue-crud::crud.index')
                ->with('urlEdit', '/'. Route::current()->uri())
                ->with('urlRuta', '/'. Route::current()->uri().'?getDatos=true')
                ->with('tabla', $this->getTabla())
                ->with('tablaid', $this->getTablaId())
                ->with('camposShow', $this->getCamposShow())
                ->with('camposEdit', $this->getCampoEdit())
                ->with('leftJoins', $this->getLeftJoins())
                ->with('subTablas', $this->getSubTablas())
                ->with('botonesExtra', $this->getBotonesExtra())
                ;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd(request()->all());

        $toUpdate   = [];
        $toInsert   = [];
        $campos     = json_decode(request()->input('datos'));

        foreach ($campos as $c) {


            //$toUpdate[$c->campo] = $c->valor;

            if($c->type == 'select'){
                $toUpdate[$c->{'campo-edit'}] = $c->valorid;
            } else {
                $toUpdate[$c->campo] = $c->valor;
            }

            if($c->campo != request()->input('tablaid')) {
                if($c->type == 'select'){
                    $toInsert[$c->{'campo-edit'}] = $c->valorid;
                } else {
                    $toInsert[$c->campo] = $c->valor;
                }
            }
        }

        if(request()->input('id') == 0) {
            $res = DB::table(request()->input('tabla'))
                ->insertGetId($toInsert);
        } else {
            $res = DB::table(request()->input('tabla'))
                ->where(request()->input('tablaid'), request()->input('id'))
                ->update($toUpdate);
        }

        if(request()->input('tabla') === 'producto') {
            event(new ProductAdded());
            event(new GraficasProyeccion());
        }

        if($res) {
            return response()->json(['respuesta' => true, 'mensaje' =>  'Se guardaron los datos con éxito']);
        } else {
            return response()->json(['respuesta' => true, 'mensaje' =>  'Lo sentimos no se pudo guardar, intenta nuevamente.']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $datos      = request()->input('campos');

        $tablaid    = request()->input('tablaid');

        $query = DB::table(request()->input('tabla'))->where($tablaid, $id );

        $campos = json_decode(request()->input('campos'));

        foreach ($campos as $c) {
            $query->addSelect($c->campo);
        }

        $data = $query->first();

        if(request()->input('tabla') === 'producto') {
            event(new ProductAdded());
        }

        return response()->json(['res'=>true, 'datos' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = DB::table(request()->input('tabla'))
            ->where(request()->input('tablaid'), $id)
            ->delete();

        if($res) {
            return response()->json(['respuesta' => true, 'mensaje' =>  'Se eliminó correctamente']);
        } else {
            return response()->json(['respuesta' => true, 'mensaje' =>  'Lo sentimos no se pudo eliminar, intenta nuevamente.']);
        }
    }
}

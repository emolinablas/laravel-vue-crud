<?php

namespace Emolinablas\LaravelVueCrud;

use App\Http\Controllers\Controller;

use App\Events\GraficasProyeccion;
use App\Events\ProductAdded;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class CrudController extends Controller
{
    private static $userId;
    private static $tabla;
    private static $tablaId;
    private static $titulo;

    private static $menu            = [];

    private static $colSlug;
    private static $separatorSlug   = '-';

    private static $camposShow          = [];
    private static $camposTodos         = [];
    private static $camposEdit          = [];
    private static $wheres              = [];
    private static $afterWheres         = [];
    private static $wheresRaw           = [];
    private static $lefJoins            = [];
    private static $botonesExtra        = [];
    private static $botonesEncabezado   = [];
    private static $links               = [];
    private static $subTablas           = [];
    private static $orders              = [];
    private static $groups              = [];

    private static $template        = 'layouts/app';

    private static $buttonNew       = ['component' => ''];

    private static $permissions =
        [
            'create' => true,
            'edit'   => true,
            'delete' => true,
        ];

    public function setUserId($userId){
        self::$userId = $userId;
    }

    public function getUserId(){
        return self::$userId;
    }


    public function setWhere($field1, $operator, $field2)
    {

        $where = ['field1' => $field1, 'operator' => $operator, 'field2' => $field2];

        self::$wheres[] = $where;
        return $this;
    }

    public function getWheres()
    {
        return self::$wheres;
    }

    public function setAfterWhere($field1, $operator, $variable, $tipo)
    {

        $where = ['field1' => $field1, 'operator' => $operator, 'variable' => $variable, 'tipo' => $tipo];

        self::$wheres[] = $where;
        return $this;
    }

    public function getAfterWheres()
    {
        return self::$wheres;
    }

    public function setLink($link)
    {
        self::$links[] = $link;
        return $this;
    }

    public function getLinks()
    {
        return self::$links;
    }

    public function setPermissions($permissions)
    {
        self::$permissions = $permissions;
        return $this;
    }

    public function setSpecificPermission($index, $value)
    {
        self::$permissions[$index] = $value;
    }

    public function getPermissions()
    {
        return self::$permissions;
    }

    public function setButtonNew($buttonNew)
    {
        self::$buttonNew = $buttonNew;
        return $this;
    }

    public function getButtonNew()
    {
        return self::$buttonNew;
    }

    public function setMenu($menu)
    {
        self::$menu = $menu;
        return $this;
    }

    public function getMenu()
    {
        return self::$menu;
    }

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

    public function setBotonEncabezado(array $boton) {

        self::$botonesEncabezado[] = $boton;

        return $this;
    }

    public function getBotonesEncabezado(){
        return self::$botonesEncabezado;
    }

    public function restrictBotonExtra($componente) {

        foreach (self::$botonesExtra as $key => $botonExtra) {
            if($botonExtra['componente'] == $componente) {
                array_splice(self::$botonesExtra,$key,1);
            }
        }

    }

    public function setCampo(array $campo)
    {

        if(!isset($campo['rules'])) {
            $campo['rules'] = '';
        }

        if($campo['type'] == 'numeric') {
            if(!isset($campo['decimales'])) {
                $campo['decimales'] = 0;
            }
        }

        if($campo['type'] == 'image') {
            $campo['src'] = '';
            $campo['imagetype'] = '';
        }

        if(!isset($campo['disabled'])) {
            $campo['disabled'] = false;
        }


        if(!isset($campo['as'])) {
            $campo['as'] = $campo['campo'];
        }


        if($campo['show']) {
            self::$camposShow[] = $campo;
        }

        self::$camposTodos[] = $campo;

        if($campo['edit']) {
            $campo['valor'] = "";
            $campo['valorDefault'] = "";

            if($campo['type'] == 'select') {
                $values = DB::table($campo['name-table']);

                if(isset($campo['where']) && $campo['where'] != '') {
                    $values->whereRaw($campo["where"]);
                }

                    $values = $values->select($campo['value-column'].' as value', $campo['text-column'].' as text')
                    ->get()->toArray();

                $campo['values'] = $values;
            } else if($campo['type'] == 'enum') {
                $values = [];
                foreach ($campo['options'] as $key => $value) {

                    $values[] = ['value' => $value, 'text' => $key];
                }
                $campo['values'] = $values;
            }

            self::$camposEdit[] = $campo;
        }


        return $this;
    }

    public function getCamposShow() {
        return self::$camposShow;
    }

    public function getCamposTodos() {
        return self::$camposTodos;
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


            $columns        = json_decode(request()->input('camposShow'));
            $camposTodos    = json_decode(request()->input('camposTodos'));
            $leftJoins      = json_decode(request()->input('leftJoins'));
            $wheres         = json_decode(request()->input('wheres'));



            //dd($columnsNew);

            $length = request()->input('length');
            $column = request()->input('column'); //Index
            $dir = request()->input('dir');
            $searchValue = request()->input('search');

            $query = DB::table(request()->input('tabla'));

            foreach ($camposTodos as $c) {
                $query->addSelect(DB::raw($c->campo . ' as `' . $c->as.'` '));

                if($c->type == 'select') {
                    $query->addSelect(DB::raw($c->{'campo-edit'} . ' as `' . $c->{'campo-edit'}.'` '));
                }

                $query->addSelect(DB::raw("'".$c->type."' as  `tcc".$c->as.'` '));
            }

            if(count($leftJoins) > 0 ) {
                foreach ($leftJoins as $l) {
                    $query->leftJoin($l->tabla, $l->campo1, $l->operador, $l->campo2);
                }
            }

            $query->addSelect(request()->input('tabla').'.'.request()->input('tablaid'));



            $query->groupBy(request()->input('tabla').'.'.request()->input('tablaid'));

            $query->orderBy($columns[$column]->as, $dir);

            if ($searchValue) {
                $query->where(
                    function ($query) use ($searchValue, $columns) {
                        $query->where(request()->input('tabla').'.'.request()->input('tablaid'), 'like', '%' . $searchValue . '%');

                        foreach($columns as $c) {
                            $query->orWhere($c->as, 'like', '%' . $searchValue . '%');
                        }
                    }
                );
            }

            if(count($wheres) > 0) {
                foreach ($wheres as $w) {
                    $query->where($w->field1, $w->operator, $w->field2);

                }
            }

            $projects = $query->paginate($length);

            //dd($projects);

            return response()->json(['data' => $projects, 'botonesExtra' => $this->getBotonesExtra(), 'draw' => request()->input('draw')]);
        } else {

            //dd($this->getCampoEdit());

            //verificamos si existe la tabla permiso
            try {
                $isPermisos = Schema::hasTable('permiso');
            } catch (\Exception $e) {
                $isPermisos = false;
            }

            //si existe entonces
            if($isPermisos) {
                $permiso = $this->getPermisoByModulo('/'. Route::current()->uri(),auth()->user()->rolid);

                //verificamos si el usuario tiene permisos para este modulo
                if( $permiso != null) {

                    //procedemos a verificar si tiene alguna restriccion
                    try {
                        $isRestricciones = Schema::hasTable('restriccionesusuario');
                    } catch (\Exception $e) {
                        $isRestricciones = false;
                    }

                    //si existe la tabla restricciones seguimos evaluando
                    if ($isRestricciones) {

                        $restricciones = DB::table('restriccionesusuario as r')
                            ->leftJoin('restriccionusuariotipo as rt', 'rt.restriccionusuariotipoid', 'r.restriccionusuariotipoid' )
                            ->where('usersid', auth()->user()->id)
                            ->where('permisoid', $permiso->permisoid)
                            ->get();

                        //si tiene por lo menos alguna restriccion
                        if (count($restricciones) > 0) {


                            foreach ($restricciones as $restriccion) {

                                switch ($restriccion->nombre) {
                                    case 'show':
                                        //si tiene restringido ver la lista entonces abortamos
                                        abort(403, 'Unauthorized action.');
                                        break;

                                    case 'create':
                                        $this->setSpecificPermission('create', false);
                                        break;
                                    case 'edit':
                                        $this->setSpecificPermission('edit', false);
                                        break;

                                    case 'delete':
                                        $this->setSpecificPermission('delete', false);
                                        break;

                                    case 'botonextra':
                                        $this->restrictBotonExtra($restriccion->componente);
                                        break;
                                }
                            }
                        }
                    }
                } else {
                    abort(403, 'Unauthorized action.');
                }
            }



            return view('laravel-vue-crud::crud.index')
                ->with('title', $this->getTitulo())
                ->with('urlEdit', '/'. Route::current()->uri())
                ->with('urlRuta', '/'. Route::current()->uri().'?getDatos=true')
                ->with('tabla', $this->getTabla())
                ->with('tablaid', $this->getTablaId())
                ->with('camposShow', $this->getCamposShow())
                ->with('camposTodos', $this->getCamposTodos())
                ->with('camposEdit', $this->getCampoEdit())
                ->with('leftJoins', $this->getLeftJoins())
                ->with('subTablas', $this->getSubTablas())
                ->with('botonesExtra', $this->getBotonesExtra())
                ->with('botonesEncabezado', $this->getBotonesEncabezado())
                ->with('menu', $this->getMenu())
                ->with('buttonNew', $this->getButtonNew())
                ->with('permissions', $this->getPermissions())
                ->with('links', $this->getLinks())
                ->with('wheres', $this->getWheres())
                ;
        }
    }

    public function getPermisoByModulo($uri, $rolid) {
        $p = DB::table('permiso as p')
            ->select('permisoid')
            ->leftJoin('modulo as m', 'm.moduloid', '=', 'p.moduloid')
            ->where('rolid', $rolid)
            ->where('m.ruta', $uri)->first();

        return $p;

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

        //if()
        //return response()->json(['respuesta' => true, 'mensaje' =>  'Lo sentimos no se pudo guardar, intenta nuevamente.']);

        $toUpdate   = [];
        $toUpdate['updated_at'] = Carbon::now();
        $toInsert   = [];
        $toInsert['created_at'] = Carbon::now();
        $toInsert['updated_at'] = Carbon::now();
        $campos     = json_decode(urldecode(request()->input('datos')));

        //dd($campos);

        foreach ($campos as $c) {


            //$toUpdate[$c->campo] = $c->valor;

            if($c->type == 'select'){
                $toUpdate[$c->{'campo-edit'}] = $c->valorid;
            } elseif($c->type == 'checkbox'){
                $toUpdate[$c->campo] = ($c->valor == '')?0:$c->valor;
            } else {
                $toUpdate[$c->campo] = $c->valor;
            }

            if($c->campo != request()->input('tablaid')) {
                if($c->type == 'select'){
                    $toInsert[$c->{'campo-edit'}] = $c->valorid;
                }elseif($c->type == 'checkbox'){
                    $toInsert[$c->campo] = ($c->valor == '')?0:$c->valor;
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
        if(env('PRODUCT_REPORT', false)) {
            if (request()->input('tabla') === 'producto') {
                event(new ProductAdded());
                event(new GraficasProyeccion());
            }
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

        if(env('PRODUCT_REPORT', false)) {
            if (request()->input('tabla') === 'producto') {
                event(new ProductAdded());
            }
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

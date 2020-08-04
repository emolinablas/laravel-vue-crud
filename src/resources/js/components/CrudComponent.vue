<template>
    <div class="projects">
        <div class="row">
            <div class="form-group col-2">
                <select class="form-control" v-model="tableData.length" @change="getProjects()">
                    <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
                </select>
            </div>
            <div class="form-group col-7">
                <input class="form-control" type="text" v-model="tableData.search" placeholder="Buscar"
                       @input="getProjects()">
            </div>
            <div class="col-sm-3">
                <button type="button" class="btn btn-success btn-block btn-xs" data-toggle="modal" data-target="#staticBackdrop">
                    Nuevo
                </button>
            </div>
        </div>
        <hr>
        <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
            <tbody>
            <tr v-for="(project, index) in projects" :key="project.id">
                <td v-for="campoShow in camposShow">{{project[campoShow.as]}}</td>
                <td>
                    <div class="float-right">

                        <b-button :style="{ marginRight: '5px' }" v-for="botonExtra in botonesExtra" :key="'button-modal-'+botonExtra.componente+index" :variant="botonExtra.variant" v-b-modal="'modal-boton-extra-'+botonExtra.componente+index" >
                            {{ botonExtra.etiqueta }}
                        </b-button>
                        <b-modal :size="botonExtra.size"
                                 v-for="botonExtra in botonesExtra"
                                 :key="'modal-'+botonExtra.componente+index"
                                 :id="'modal-boton-extra-'+botonExtra.componente+index"
                                 :title="botonExtra.etiqueta + ' | ' + project[tabla+'.'+botonExtra.campoNombre]"
                                 ok-only ok-variant="secondary" ok-title="Cancel"
                        >

                            <component @event="actualizarProyectos" :is="botonExtra.componente" :id-row="project[tablaid]"  :data="botonExtra.data" :usersid="usersid">

                            </component>
                        </b-modal>

                        <button v-on:click="setSubTablaActual(subTabla, index)" v-for="subTabla in subTablas" type="button" class="btn btn-outline-primary btn-xs" data-toggle="modal" data-target="#staticBackdropSubTablas">
                            {{ subTabla.etiqueta }}
                        </button>

                        <button v-on:click="getProjectActual(index)" type="button" class="btn btn-outline-primary btn-xs" data-toggle="modal" data-target="#staticBackdrop">
                            Editar
                        </button>

                        <button class="btn btn-outline-danger btn-xs" v-on:click="eliminar(project[tablaid])">
                            Eliminar
                        </button>
                    </div>
                </td>
            </tr>
            </tbody>
        </datatable>
        <pagination :pagination="pagination"
                    @prev="getProjects(pagination.prevPageUrl)"
                    @next="getProjects(pagination.nextPageUrl)">
        </pagination>
        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true" ref="vuemodal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="idActual != 0" class="modal-title" id="staticBackdropLabel">Editar</h5>
                        <h5 v-else class="modal-title" id="staticBackdropLabel">Nuevo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="">
                            <div v-for="(campoEdit, index) in camposEdit" v-bind:class="{'d-none form-group': campoEdit.campo === tablaid,  'form-group': campoEdit.campo !== 0}" >
                                <div class="form-group">
                                    <label>{{ campoEdit.nombre }}</label>
                                    <input v-if="campoEdit.type == 'string'" v-model="camposEditLocal[index].valor"  class="form-control" type="text">
                                    <input v-else-if="campoEdit.type == 'numeric'" v-model="camposEditLocal[index].valor"    class="form-control" type="number">
                                    <input v-else-if="campoEdit.type == 'check'" type="checkbox" v-model="camposEditLocal[index].valor">

                                    <b-form-select v-else-if="campoEdit.type == 'select'" id="select-empleados" v-model="camposEditLocal[index].valorid" :options="camposEditLocal[index].values"></b-form-select>


                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div v-if="mostrarMensaje" class="alert alert-success" role="alert">
                            {{ mensaje }}
                        </div>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button v-if="!enviandoDatos" v-on:click="guardarDatos" type="button" class="btn btn-primary">Guardar Datos</button>
                        <button v-else class="btn btn-secondary" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Enviando pedido...
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div  class="modal fade" id="staticBackdropSubTablas" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabelSubTablas" aria-hidden="true" ref="vuemodalSubTablas">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabelSubTablas">{{ subTablaActualEtiqueta }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row" v-for="(rowSubTabla, index) in subTablaActual.rowsRecibidas" >
                            <div class="col" v-for="(campo, index2) in subTablaActual.campos">
                                <label >{{ campo.nombre }}</label>
                                <input v-model="rowSubTabla[campo.campo]" type="text" class="form-control" placeholder="First name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div v-if="mostrarMensajeSubTablas" class="alert alert-success" role="alert">
                            {{ mensajeSubTablas }}
                        </div>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button v-if="!enviandoDatos" v-on:click="guardarDatosSubTabla" type="button" class="btn btn-primary">Guardar Datos</button>
                        <button v-else class="btn btn-secondary" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Enviando pedido...
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Datatable from './Datatable.vue';
    import Pagination from './Pagination.vue';
    export default {
        props: ['urlRuta',  'urlEdit', 'tabla', 'tablaid', 'camposShow', 'camposEdit', 'leftJoins', 'subTablas', 'botonesExtra', 'usersid'],
        components: { datatable: Datatable, pagination: Pagination},
        mounted: function () {
            $(this.$refs.vuemodal).on("hidden.bs.modal", this.alCerrarElModal);
        },
        created() {
            this.getProjects();
        },
        data() {
            let sortOrders = {};

            // let columns = [
            //     {width: '33%', label: 'Nombre', name: 'nombre' },
            //     {width: '33%', label: 'Stock', name: 'stock'},
            // ];

            let columns = [];

            this.camposShow.forEach((campo) => {
                columns.push({
                    width: '33%',
                    label: campo.nombre,
                    name: campo.campo });
            });

            columns.forEach((column) => {
                sortOrders[column.name] = -1;
            });
            return {
                subTablaActual: {
                    rowsRecibidas: [
                        {
                            nombre: ""
                        }
                    ]
                },
                subTablaActualEtiqueta:"",
                mensaje : "",
                mensajeSubTablas : "",
                mensajeExterno: "",
                mostrarMensajeExterno: false,
                mostrarMensaje: false,
                mostrarMensajeSubTablas: false,
                projectActual: 0,
                idActual: 0,
                camposEditLocal: this.camposEdit,
                enviandoDatos: false,
                projects: [],
                columns: columns,
                sortKey: 'deadline',
                sortOrders: sortOrders,
                perPage: ['20', '30', '40'],
                tableData: {
                    draw: 0,
                    length: 20,
                    search: '',
                    column: 0,
                    dir: 'desc',
                    getDatos: true,
                    camposShow: JSON.stringify(this.camposShow),
                    tabla: this.tabla,
                    tablaid: this.tablaid,
                    leftJoins: JSON.stringify(this.leftJoins)
                },
                pagination: {
                    lastPage: '',
                    currentPage: '',
                    total: '',
                    lastPageUrl: '',
                    nextPageUrl: '',
                    prevPageUrl: '',
                    from: '',
                    to: ''
                },
            }
        },
        computed: {
            /*paramsEdit: function () {

                var paramEdit = {};

                this.camposEditLocal.forEach(function (element2, index2, array) {
                    for (var key in element2) {
                        // skip loop if the property is from prototype
                        if (!element2.hasOwnProperty(key)) continue;

                        var obj = element2[key];

                        if(key == 'campo') {
                            element2[] = actual[obj];
                        }
                    }
                });

                return [];
            }*/
        },
        methods: {
            actualizarProyectos() {
                this.getProjects();
            },
            guardarDatosSubTabla() {

            },
            setSubTablaActual(subTablaViene, index) {

                //console.log(subTablaViene);

                this.subTablaActualEtiqueta = subTablaViene.etiqueta;

                //this.subTablaActual = subTablaViene;

                this.subTablaActual.rowsRecibidas =  [
                    {
                        nombre: ""
                    }
                ];


                var actual = this.projects[index];
                var lineaIdActual = actual[this.tablaid];
                //console.log(actual[this.tablaid]);

                var vm = this;

                axios.get(this.urlEdit + '?getSubTabla=true&datosSubTabla='+JSON.stringify(subTablaViene)+'&id='+lineaIdActual)
                    .then(response => {
                        let data = response.data;
                        this.subTablaActual.rowsRecibidas = data;
                        // this.subTablaActual.campo = subTablaViene.campo;
                        this.subTablaActual.campos = subTablaViene.campos;
                    })
                    .catch(errors => {
                        console.log(errors);
                    });

            },
            alCerrarElModal(){
                this.mostrarMensaje = false;
                this.mensaje = "";
            },
            getProjectActual(index) {
              var actual = this.projects[index];
                //console.log(this.tablaid);
              this.idActual = actual[this.tablaid];


              this.camposEditLocal.forEach(function (element2, index2, array) {
                  for (var key in element2) {
                      // skip loop if the property is from prototype
                      if (!element2.hasOwnProperty(key)) continue;

                      var obj = element2[key];

                      if(key == 'campo') {
                          element2['valor'] = actual[obj];
                      }

                      if(key == 'type') {
                          //console.log(element2[key]);
                          if(element2[key] == 'select') {
                              element2['valorid'] = actual[element2['campo-edit']];

                              console.log(element2);
                          }
                      }
                  }
              });

            },
            eliminar(id) {
                var confirmar = confirm('Está seguro que desea eliminarlo?');

                if(confirmar) {
                    this.enviandoDatos = true;

                    var vm = this;

                    axios.post(this.urlEdit + '/'+id, {
                        _method: 'delete',
                        tabla: this.tabla,
                        tablaid: this.tablaid
                        // _token: csrf
                    })
                        .then(response => {
                            let data = response.data;

                            if(data.respuesta) {
                                vm.mensaje = data.mensaje;
                                vm.mostrarMensaje = true;
                                this.getProjects();
                            }

                            vm.enviandoDatos = false;
                        })
                        .catch(errors => {
                            console.log(errors);
                            vm.enviandoDatos = false;
                        });
                }


            },
            guardarDatos() {

                this.enviandoDatos = true;

                var vm = this;

                axios.post(this.urlEdit + '?tabla='+this.tabla+'&tablaid='+this.tablaid+'&datos='+JSON.stringify(this.camposEditLocal) , {
                        id: this.idActual
                })
                    .then(response => {
                        let data = response.data;

                        if(data.respuesta) {
                            vm.mensaje = data.mensaje;
                            vm.mostrarMensaje = true;
                            this.getProjects();
                        }

                        vm.enviandoDatos = false;
                    })
                    .catch(errors => {
                        console.log(errors);
                        vm.enviandoDatos = false;
                    });
            },
            getProjects(url = this.urlRuta) {
                this.tableData.draw++;
                axios.get(url, {params: this.tableData})
                    .then(response => {
                        let data = response.data;
                        if (this.tableData.draw == data.draw) {
                            this.projects = data.data.data;
                            this.configPagination(data.data);
                        }
                    })
                    .catch(errors => {
                        console.log(errors);
                    });
            },
            configPagination(data) {
                this.pagination.lastPage = data.last_page;
                this.pagination.currentPage = data.current_page;
                this.pagination.total = data.total;
                this.pagination.lastPageUrl = data.last_page_url;
                this.pagination.nextPageUrl = data.next_page_url;
                this.pagination.prevPageUrl = data.prev_page_url;
                this.pagination.from = data.from;
                this.pagination.to = data.to;
            },
            sortBy(key) {
                this.sortKey = key;
                this.sortOrders[key] = this.sortOrders[key] * -1;
                this.tableData.column = this.getIndex(this.columns, 'name', key);
                this.tableData.dir = this.sortOrders[key] === 1 ? 'asc' : 'desc';
                this.getProjects();
            },
            getIndex(array, key, value) {
                return array.findIndex(i => i[key] == value)
            },
        }
    };
</script>
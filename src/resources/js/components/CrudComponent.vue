<style lang="scss">
.input-hide {
    display: none;
}
</style>
<template>
    <div class="projects">
        <div class="row">
            <div class="form-group col-2">
                <select class="form-control" v-model="tableData.length" @change="getProjects()">
                    <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
                </select>
            </div>
            <div class="form-group col-5">
                <input class="form-control" type="text" v-model="tableData.search" placeholder="Buscar"
                       @input="getProjects()">
            </div>
            <div class="col-sm-5">

                <b-row>
                    <b-col>
                        <b-row>
                            <b-col v-for="botonEncabezado in botonesEncabezado" :key="botonEncabezado.url">
                                <a :class="'btn btn-block btn-'+botonEncabezado.variant"  :href="botonEncabezado.url">{{ botonEncabezado.etiqueta }}</a>
                            </b-col>
                        </b-row>
                    </b-col>
                    <b-col>
                        <button v-if="buttonNew.component === '' && permissions.create" v-on:click="crearNuevo()" type="button" class="btn btn-success btn-block btn-xs" data-toggle="modal" data-target="#staticBackdrop">
                            Nuevo
                        </button>

                        <b-button v-if="buttonNew.component !== '' && permissions.create" variant="success" size="md" block v-b-modal="'buttonNew'">
                            Nuevo
                        </b-button>
                        <b-modal v-if="buttonNew.component !== '' && permissions.create" size="lg"

                                 id="buttonNew"
                                 title="Nuevo"
                                 ok-only ok-variant="secondary" ok-title="Cancel"
                                 :no-close-on-backdrop="true"
                        >

                            <component @event="actualizarProyectos" @titulomodal="actualizarTituloModal" :is="buttonNew.component"  :data="buttonNew.data" :usersid="usersid">


                            </component>
                        </b-modal>
                    </b-col>
                </b-row>

            </div>
        </div>
        <hr>
        <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
            <tbody>
            <tr v-for="(project, index) in projects" :key="project.id">
                <td v-for="campoShow in camposShow">
                    <img v-if="project['tcc'+campoShow.as] === 'image'" :src="project[campoShow.as]" alt="" width="50px">
                    <p v-else>
                        {{(project['tcc'+campoShow.as] === 'checkbox')?((project[campoShow.as] == 1)?'SI':'NO'):project[campoShow.as]}}
                    </p>
                </td>
                <td>
                    <div class="float-right">
                        <a v-for="link in links" :target="link.target" :key="'button-link-'+project.id+index" :href="link.ruta+'?id='+project[tablaid]"><b-button :style="{ marginRight: '5px' }"  pill :variant="link.variant" size="sm">{{ link.etiqueta }}</b-button></a>

                        <b-button :style="{ marginRight: '5px' }" v-for="botonExtra in botonesExtraLocal" :key="'button-modal-'+botonExtra.componente+index" :variant="botonExtra.variant" v-b-modal="'modal-boton-extra-'+botonExtra.componente+index" size="sm" >
                            {{ botonExtra.etiqueta }}
                        </b-button>

                        <b-modal :size="botonExtra.size"
                                 v-for="botonExtra in botonesExtraLocal"
                                 :no-close-on-backdrop="true"
                                 :key="'modal-'+botonExtra.componente+index"
                                 :id="'modal-boton-extra-'+botonExtra.componente+index"
                                 :title="botonExtra.etiqueta + ' | ' + project[tabla+'.'+botonExtra.campoNombre]+' | '+tituloModal"
                                 ok-only ok-variant="secondary" ok-title="Cancel"
                        >

                            <component @event="actualizarProyectos" @titulomodal="actualizarTituloModal" :is="botonExtra.componente" :id-row="project[tablaid]"  :data="botonExtra.data" :usersid="usersid">

                            </component>
                        </b-modal>

                        <button v-on:click="setSubTablaActual(subTabla, index)" v-for="subTabla in subTablas" type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#staticBackdropSubTablas">
                            {{ subTabla.etiqueta }}
                        </button>

                        <button v-if="permissions.edit" v-on:click="getProjectActual(index)" type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#staticBackdrop">
                            <b-icon-pencil></b-icon-pencil>
                        </button>

                        <button v-if="permissions.delete" class="btn btn-outline-danger btn-sm" v-on:click="eliminar(project[tablaid])">
                            <b-icon-trash></b-icon-trash>
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
                        <ValidationObserver v-slot="{ invalid }" tag="form">

                            <div v-for="(campoEdit, index) in camposEdit" v-bind:class="{'d-none form-group': campoEdit.campo === tablaid,  'form-group': campoEdit.campo !== 0}" >
                                <div class="form-group">
                                    <ValidationProvider :name="campoEdit.nombre" v-slot="{ errors, valid }" :rules="campoEdit.rules">
                                        <b-form-group
                                                id="fieldset-descripcion"
                                                :label="campoEdit.nombre"
                                                label-for="input-descripcion"
                                                :invalid-feedback="(errors[0] != null)?errors[0]:'*'"
                                                :valid-feedback="'Válido'"
                                                :state="valid"

                                        >
                                        <summernote
                                            v-if="campoEdit.type === 'textarea'"
                                            :disabled="camposEditLocal[index].disabled && idActual > 0"
                                                    name="summernote"
                                                    class="form-control"
                                                    :model="camposEditLocal[index].valor"
                                                    :config="config"
                                                    :valor="camposEditLocal[index].valorDefault"
                                                    v-on:change="value => { camposEditLocal[index].valor = value }"
                                        ></summernote>

                                        <div v-else-if="campoEdit.type === 'image'">
                                                <b-button
                                                    href="#"
                                                    role="button"
                                                    variant="primary"
                                                    @click.prevent="showFileChooser(campoEdit.nombre)"
                                                >
                                                    Seleccionar imagen
                                                </b-button>
                                                <b-button
                                                    href="#"
                                                    role="button"
                                                    variant="success"
                                                    @click.prevent="cropImage(index, campoEdit.nombre, campoEdit['image-options'])"
                                                >
                                                    Crop
                                                </b-button>
                                                <input
                                                    class="input-hide"
                                                    :ref="campoEdit.nombre"
                                                    type="file"
                                                    name="image"
                                                    accept="image/*"
                                                    @change="setImage($event,campoEdit.nombre, index)"
                                                />
                                                <vue-cropper
                                                    class="mt-2"
                                                    :ref="'cropper'+campoEdit.nombre"
                                                    :aspect-ratio="(campoEdit['image-options'].width / campoEdit['image-options'].height) / 1"
                                                    :src="campoEdit.src"
                                                    preview=".preview"
                                                />
                                                <b-badge class="mt-2" variant="success">Foto a guardar</b-badge>
                                                <div class="cropped-image">
                                                    <img width="100%"
                                                        v-if="camposEditLocal[index].valor"
                                                        :src="camposEditLocal[index].valor"
                                                        alt="Cropped Image"
                                                    />
                                                </div>
                                            </div>

                                        <input
                                               v-else-if="campoEdit.type === 'string'"
                                               v-model="camposEditLocal[index].valor"
                                               class="form-control"
                                               :disabled="camposEditLocal[index].disabled && idActual > 0"
                                               type="text"
                                        >
                                        <input v-else-if="campoEdit.type === 'numeric'"
                                               v-model="camposEditLocal[index].valor"
                                               class="form-control"
                                               :disabled="camposEditLocal[index].disabled && idActual > 0"
                                               type="number">
                                        <b-form-datepicker v-else-if="campoEdit.type === 'date'"
                                                           v-model="camposEditLocal[index].valor"
                                                           :disabled="camposEditLocal[index].disabled && idActual > 0"
                                                           locale="es" ></b-form-datepicker>

<!--                                        <b-form-select v-else-if="campoEdit.type == 'select'"-->
<!--                                                       v-model="camposEditLocal[index].valorid"-->
<!--                                                       :options="camposEditLocal[index].values"-->
<!--                                                       :disabled="camposEditLocal[index].disabled && idActual > 0"-->
<!--                                        ></b-form-select>-->

                                            <v-select
                                                v-else-if="campoEdit.type == 'select'"
                                                v-model="camposEditLocal[index].valueSelect"
                                                :options="camposEditLocal[index].values"
                                                :clearable='false'
                                                :disabled="camposEditLocal[index].disabled && idActual > 0"
                                            >

                                            </v-select>

                                        <b-form-select v-else-if="campoEdit.type == 'enum'"
                                                       v-model="camposEditLocal[index].valor"
                                                       :options="camposEditLocal[index].values"
                                                       :disabled="camposEditLocal[index].disabled && idActual > 0"
                                        ></b-form-select>

                                        <b-form-checkbox v-else-if="campoEdit.type = 'checkbox'"
                                                v-model="camposEditLocal[index].valor"
                                                :disabled="camposEditLocal[index].disabled && idActual > 0"
                                                name="checkbox-1"
                                                value="1"
                                                unchecked-value="0"
                                        >

                                        </b-form-checkbox>

                                    </b-form-group>
                                    </ValidationProvider>




                                </div>
                            </div>
                            <div v-if="mostrarMensaje" :class="'alert alert-'+mensajeTipo" role="alert">
                                {{ mensaje }}
                            </div>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button v-if="!enviandoDatos" v-on:click="guardarDatos" type="button" class="btn btn-primary" :disabled="invalid">Guardar Datos</button>
                            <button v-else class="btn btn-secondary" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Enviando pedido...
                            </button>

                        </ValidationObserver>
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
        props: ['urlRuta',  'urlEdit', 'tabla', 'tablaid', 'camposShow', 'camposEdit', 'camposTodos', 'leftJoins', 'subTablas', 'botonesExtra', 'botonesEncabezado', 'usersid',  'buttonNew', 'permissions','links', 'wheres', 'wheresRaw'],
        components: { datatable: Datatable, pagination: Pagination, 'summernote' : require('./Summernote')},
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
                content: null,
                config: {
                    height: 100,
                    toolbar: [
                        // [groupName, [list of button]]
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['gxcode']], // plugin: summernote-ext-codewrapper
                    ],
                },
                tituloModal: "",
                subTablaActual: {
                    rowsRecibidas: [
                        {
                            nombre: ""
                        }
                    ]
                },
                subTablaActualEtiqueta:"",
                mensaje : "",
                mensajeTipo: "success",
                mensajeSubTablas : "",
                mensajeExterno: "",
                mostrarMensajeExterno: false,
                mostrarMensaje: false,
                mostrarMensajeSubTablas: false,
                projectActual: 0,
                idActual: 0,
                camposEditLocal: this.camposEdit,
                botonesExtraLocal: this.botonesExtra,
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
                    camposTodos: JSON.stringify(this.camposTodos),
                    tabla: this.tabla,
                    tablaid: this.tablaid,
                    leftJoins: JSON.stringify(this.leftJoins),
                    wheres: JSON.stringify(this.wheres),
                    wheresRaw: JSON.stringify(this.wheresRaw)
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
            cropImage(index, nombre, imageOptions) {
                console.log('crop');
                // get image data for post processing, e.g. upload or setting image src
                this.camposEditLocal[index].valor = this.$refs['cropper'+nombre][0].getCroppedCanvas(
                    {
                         width: imageOptions.width,
                         height: imageOptions.height,
                         imageSmoothingEnabled: true,
                         imageSmoothingQuality: 'high',
                    }
                ).toDataURL(this.camposEditLocal[index].imagetype.toString(), imageOptions.quality);
            },
            showFileChooser(campo) {
                this.$refs[campo][0].click();
            },
            setImage(e,nombre, index) {
                console.log('set imagen');
                const file = e.target.files[0];
                if (file.type.indexOf('image/') === -1) {
                    alert('Please select an image file');
                    return;
                }
                if (typeof FileReader === 'function') {
                    console.log('si es una imagen');
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        this.imgSrc = event.target.result;
                        // rebuild cropperjs with the updated source
                        this.$refs['cropper'+nombre][0].replace(event.target.result);
                        //console.log(file.type);
                        this.camposEditLocal[index].imagetype = file.type;
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert('Sorry, FileReader API not supported');
                }
            },
            actualizarTituloModal(titulo){
                this.tituloModal = titulo;
            },
            crearNuevo() {
              this.idActual = 0;
              var vm = this;
                this.camposEditLocal.forEach(function (element2, index2, array) {
                    if(vm.idActual === 0) {
                        element2['valor'] = "";
                    }
                });
            },
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
              //console.log(this.projects[index]);
                //console.log(this.tablaid);
              this.idActual = actual[this.tablaid];

                var vm = this;
              this.camposEditLocal.forEach(function (element2, index2, array) {
                  //console.log(this.idActual);
                  for (var key in element2) {
                      // skip loop if the property is from prototype
                      if (!element2.hasOwnProperty(key)) continue;

                      var obj = element2[key];

                      if(key === 'campo') {
                          //console.log(actual[obj]);
                          element2['valor'] = actual[obj];
                          element2['valorDefault'] = actual[obj];
                          //console.log(element2['valor']);
                      }



                      if(key === 'type') {
                          //console.log(element2[key]);
                          if(element2[key] == 'select') {
                              //vm.camposEditLocal[index2].valorid = actual[element2['campo-edit']];
                              element2['valorid'] = actual[element2['campo-edit']];

                              element2['values'].forEach(function (value, index3) {
                                  if(value.value === actual[element2['campo-edit']]) {
                                      element2['valueSelect'] = value;
                                  }
                              });

                              //console.log(element2);
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

                axios.post(this.urlEdit + '?tabla='+this.tabla+'&tablaid='+this.tablaid , {
                    id: this.idActual,
                    usersid: this.usersid,
                    datos: encodeURIComponent(JSON.stringify(this.camposEditLocal)),
                    crud: true
                })
                    .then(response => {
                        let data = response.data;

                        if(data.respuesta) {
                            vm.mensaje = data.mensaje;
                            vm.mensajeTipo = "success";
                            vm.mostrarMensaje = true;
                            this.getProjects();
                        }

                        vm.enviandoDatos = false;
                    })
                    .catch(errors => {
                        vm.mensaje = "Lo sentimos ocurrio un error: "+errors;
                        vm.mensajeTipo = "danger";
                        vm.mostrarMensaje = true;
                        vm.enviandoDatos = false;
                    });
            },
            getProjects(url = this.urlRuta) {
                this.tableData.draw++;
                var vm = this;
                axios.get(url, {
                    params: this.tableData,
                    usersid: this.usersid
                })
                    .then(response => {
                        let data = response.data;
                        if (this.tableData.draw == data.draw) {
                            //console.log(data);
                            this.projects = data.data.data;
                            vm.botonesExtraLocal = data.botonesExtra;
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

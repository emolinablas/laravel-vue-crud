<style lang="scss">
.input-hide {
    display: none;
}
</style>
<template>
    <div class="projects">
        <div class="row">
            <b-col >
                <b-form-group
                    label-for="filter-input"
                    class="mb-0"
                >
                    <b-input-group size="sm">
                        <b-form-input
                            id="filter-input"
                            v-model="tableData.search"
                            @input="doSearch"
                            type="search"
                            placeholder="Buscar"
                        ></b-form-input>

                        <b-input-group-append>
                            <b-button :disabled="!tableData.search" @click="tableData.search = ''">Limpiar</b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
            </b-col>
            <b-col>
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

                        <b-button v-if="buttonNew.component !== '' && permissions.create" variant="success" size="sm" block v-b-modal="'buttonNew'">
                            Nuevo
                        </b-button>
                        <b-modal v-if="buttonNew.component !== '' && permissions.create" size="sm"

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
            </b-col>
        </div>
        <hr>

        <b-container fluid>
            <!-- User Interface controls -->
            <b-row>
                <b-col lg="6" class="my-1">
                    <b-form-group
                        label="Ordenar por"
                        label-for="sort-by-select"
                        label-cols-sm="3"
                        label-align-sm="right"
                        label-size="sm"
                        class="mb-0"
                        v-slot="{ ariaDescribedby }"
                    >
                        <b-input-group size="sm">
                            <b-form-select
                                id="sort-by-select"
                                v-model="sortByTable"
                                :options="sortOptions"
                                :aria-describedby="ariaDescribedby"
                                class="w-75"
                                @change="sortChanged({sortBy : sortByTable})"
                            >
                                <template #first>
                                    <option value="">-- ninguno --</option>
                                </template>
                            </b-form-select>

                            <b-form-select
                                v-model="sortDesc"
                                :disabled="!sortByTable"
                                :aria-describedby="ariaDescribedby"
                                size="sm"
                                class="w-25"
                                @change="sortChanged({sortBy : sortByTable})"
                            >
                                <option :value="false">Asc</option>
                                <option :value="true">Desc</option>
                            </b-form-select>
                        </b-input-group>
                    </b-form-group>
                </b-col>

<!--                <b-col lg="6" class="my-1">
                    <b-form-group
                        label="Ordenamiento inicial"
                        label-for="initial-sort-select"
                        label-cols-sm="3"
                        label-align-sm="right"
                        label-size="sm"
                        class="mb-0"
                    >
                        <b-form-select
                            id="initial-sort-select"
                            v-model="sortDirection"
                            :options="['asc', 'desc', 'last']"
                            size="sm"
                        ></b-form-select>
                    </b-form-group>
                </b-col>-->

<!--                <b-col lg="6" class="my-1">-->
<!--                    <b-form-group
                        v-model="sortDirection"
                        label="Filter On"
                        description="Leave all unchecked to filter on all data"
                        label-cols-sm="3"
                        label-align-sm="right"
                        label-size="sm"
                        class="mb-0"
                        v-slot="{ ariaDescribedby }"
                    >
                        <b-form-checkbox-group
                            v-model="filterOn"
                            :aria-describedby="ariaDescribedby"
                            class="mt-1"
                        >
                            <b-form-checkbox value="name">Name</b-form-checkbox>
                            <b-form-checkbox value="age">Age</b-form-checkbox>
                            <b-form-checkbox value="isActive">Active</b-form-checkbox>
                        </b-form-checkbox-group>
                    </b-form-group>-->
<!--                </b-col>-->

                <b-col sm="5" md="6" class="my-1">
                    <b-form-group
                        label="Por página"
                        label-for="per-page-select"
                        label-cols-sm="6"
                        label-cols-md="4"
                        label-cols-lg="3"
                        label-align-sm="right"
                        label-size="sm"
                        class="mb-0"
                    >
                        <select class="form-control" v-model="tableData.length" @change="getProjects()">
                            <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
                        </select>
                    </b-form-group>
                </b-col>

                <b-col sm="7" md="6" class="my-1">
                    <pagination :pagination="pagination"
                                @prev="getProjects(pagination.prevPageUrl)"
                                @next="getProjects(pagination.nextPageUrl)">
                    </pagination>
                </b-col>
            </b-row>

            <!-- Main table element -->
            <b-table
                striped
                bordered
                :items="projects"
                :fields="columns"
                :current-page="currentPage"
                :per-page="perPageTable"
                :filter="filter"
                :filter-included-fields="filterOn"
                :sort-by.sync="sortByTable"
                :sort-desc.sync="sortDesc"
                :sort-direction="sortDirection"
                no-local-sorting
                :busy.sync="isBusy"
                stacked="md"
                show-empty
                small
                @filtered="onFiltered"
                @sort-changed="sortChanged"

            >

                <template v-for="(c, index2) in columns" #cell()="row">

<!--                    {{JSON.stringify(row.field.key)}}-->
                    <img v-if="row.item['tcc'+row.field.key] === 'image'" :src="row.item[row.field.key]" alt="" width="50px">
                    <p v-else>
                        {{(row.item['tcc'+row.field.key] === 'checkbox')?((row.item[row.field.key] == 1)?'SI':'NO'):row.item[row.field.key]}}
                    </p>
                </template>

<!--                <template #cell(name)="row">
                    {{ row.value.first }}
                </template>-->

                <template #cell(actions)="row">

                    <div class="float-right">
                        <a v-for="link in links" :target="link.target" :key="'button-link-'+row.id+row.index" :href="link.ruta+'?id='+row.item[tablaid]"><b-button :style="{ marginRight: '5px' }"  pill :variant="link.variant" size="sm">{{ link.etiqueta }}</b-button></a>

                        <b-button :style="{ marginRight: '5px' }" v-for="botonExtra in botonesExtraLocal" :key="'button-modal-'+botonExtra.componente+row.index" :variant="botonExtra.variant" v-b-modal="'modal-boton-extra-'+botonExtra.componente+row.index" size="sm" >
                            {{ botonExtra.etiqueta }}
                        </b-button>

                        <b-modal :size="botonExtra.size"
                                 v-for="botonExtra in botonesExtraLocal"
                                 :no-close-on-backdrop="true"
                                 :key="'modal-'+botonExtra.componente+row.index"
                                 :id="'modal-boton-extra-'+botonExtra.componente+row.index"
                                 :title="botonExtra.etiqueta + ' | ' + row.item[tabla+'.'+botonExtra.campoNombre]+' | '+tituloModal"
                                 ok-only ok-variant="secondary" ok-title="Cancel"
                        >

                            <component @event="actualizarProyectos" @titulomodal="actualizarTituloModal" :is="botonExtra.componente" :id-row="row.item[tablaid]"  :data="botonExtra.data" :usersid="usersid">

                            </component>
                        </b-modal>

                        <button v-on:click="setSubTablaActual(subTabla, row.index)" v-for="subTabla in subTablas" type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#staticBackdropSubTablas">
                            {{ subTabla.etiqueta }}
                        </button>

                        <button v-if="permissions.edit" v-on:click="getProjectActual(row.index)" type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#staticBackdrop">
                            <b-icon-pencil></b-icon-pencil>
                        </button>

                        <button v-if="permissions.delete" class="btn btn-outline-danger btn-sm" v-on:click="eliminar(row.item[tablaid])">
                            <b-icon-trash></b-icon-trash>
                        </button>
                    </div>

<!--                    <b-button size="sm" @click="info(row.item, row.index, $event.target)" class="mr-1">
                        Info modal
                    </b-button>
                    <b-button size="sm" @click="row.toggleDetails">
                        {{ row.detailsShowing ? 'Hide' : 'Show' }} Details
                    </b-button>-->
                </template>

<!--                <template #row-details="row">
                    <b-card>
                        <ul>
                            <li v-for="(value, key) in row.item" :key="key">{{ key }}: {{ value }}</li>
                        </ul>
                    </b-card>
                </template>-->
            </b-table>

            <!-- Info modal -->
            <b-modal :id="infoModal.id" :title="infoModal.title" ok-only @hide="resetInfoModal">
                <pre>{{ infoModal.content }}</pre>
            </b-modal>
        </b-container>

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

<!--                                        <b-form-select v-else-if="campoEdit.type == 'select'"
                                                       v-model="camposEditLocal[index].valorid"
                                                       :options="camposEditLocal[index].values"
                                                       :disabled="camposEditLocal[index].disabled && idActual > 0"
                                        ></b-form-select>-->

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
    import Datatableb from './Datatableb.vue';
    import Pagination from './Pagination.vue';

    var delayTimer;

    export default {
        props: ['urlRuta',  'urlEdit', 'tabla', 'tablaid', 'camposShow', 'camposEdit', 'camposTodos', 'leftJoins', 'subTablas', 'botonesExtra', 'botonesEncabezado', 'usersid',  'buttonNew', 'permissions','links', 'wheres', 'wheresRaw'],
        components: { datatableb: Datatableb, pagination: Pagination, 'summernote' : require('./Summernote')},
        watch: {
            // sortByTable(value) {
            //     var ctx = {
            //         sortBy : value
            //     }
            //     this.sortChanged(ctx)
            // },
            // sortDesc() {
            //     var ctx = {
            //         sortBy : this.sortByTable
            //     }
            //     this.sortChanged(ctx)
            // }
        },
        mounted: function () {
            this.totalRows = this.items.length
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
                    sortable: true,
                    label: campo.nombre,
                    key: campo.campo,
                    sortDirection: 'desc'
                });
            });

            columns.push({
                label: 'Acciones',
                key: 'actions'
            });

            columns.forEach((column) => {
                sortOrders[column.key] = -1;
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
                perPageTable: 10,
                tableData: {
                    draw: 0,
                    length: 10,
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

                items: [
                    { isActive: true, age: 40, name: { first: 'Dickerson', last: 'Macdonald' } },
                    { isActive: false, age: 21, name: { first: 'Larsen', last: 'Shaw' } },
                    {
                        isActive: false,
                        age: 9,
                        name: { first: 'Mini', last: 'Navarro' },
                        _rowVariant: 'success'
                    },
                    { isActive: false, age: 89, name: { first: 'Geneva', last: 'Wilson' } },
                    { isActive: true, age: 38, name: { first: 'Jami', last: 'Carney' } },
                    { isActive: false, age: 27, name: { first: 'Essie', last: 'Dunlap' } },
                    { isActive: true, age: 40, name: { first: 'Thor', last: 'Macdonald' } },
                    {
                        isActive: true,
                        age: 87,
                        name: { first: 'Larsen', last: 'Shaw' },
                        _cellVariants: { age: 'danger', isActive: 'warning' }
                    },
                    { isActive: false, age: 26, name: { first: 'Mitzi', last: 'Navarro' } },
                    { isActive: false, age: 22, name: { first: 'Genevieve', last: 'Wilson' } },
                    { isActive: true, age: 38, name: { first: 'John', last: 'Carney' } },
                    { isActive: false, age: 29, name: { first: 'Dick', last: 'Dunlap' } }
                ],
                fields: [
                    { key: 'name', label: 'Person full name', sortable: true, sortDirection: 'desc' },
                    { key: 'age', label: 'Person age', sortable: true, class: 'text-center' },
                    {
                        key: 'isActive',
                        label: 'Is Active',
                        formatter: (value, key, item) => {
                            return value ? 'Yes' : 'No'
                        },
                        sortable: true,
                        sortByFormatted: true,
                        filterByFormatted: true
                    },
                    { key: 'actions', label: 'Actions' }
                ],
                isBusy: false,
                totalRows: 1,
                currentPage: 1,
                perPage: ['10', '20', '30'],
                pageOptions: [5, 10, 15, { value: 100, text: "Mostrar muchos" }],
                sortByTable: '',
                sortDesc: false,
                sortDirection: 'asc',
                filter: null,
                filterOn: [],
                infoModal: {
                    id: 'info-modal',
                    title: '',
                    content: ''
                }
            }
        },
        computed: {
            sortOptions() {
                // Create an options list from our fields
                return this.columns
                    .filter(f => f.sortable)
                    .map(f => {
                        return { text: f.label, value: f.key }
                    })
            },
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
            info(item, index, button) {
                this.infoModal.title = `Row index: ${index}`
                this.infoModal.content = JSON.stringify(item, null, 2)
                this.$root.$emit('bv::show::modal', this.infoModal.id, button)
            },
            resetInfoModal() {
                this.infoModal.title = ''
                this.infoModal.content = ''
            },
            onFiltered(filteredItems) {
                // Trigger pagination to update the number of buttons/pages due to filtering
                this.totalRows = filteredItems.length
                this.currentPage = 1
            },
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
                console.log(id);
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
            doSearch() {
                clearTimeout(delayTimer);
                var vm = this;
                delayTimer = setTimeout(function() {
                    vm.getProjects();
                }, 500); // Will do the ajax stuff after 1000 ms, or 1 s
            },
            getProjects(url = this.urlRuta) {
                this.isBusy = true;
                this.tableData.draw++;
                var vm = this;
                axios.get(url, {
                    params: this.tableData,
                    usersid: this.usersid
                })
                    .then(response => {
                        let data = response.data;
                        this.isBusy = false;
                        if (this.tableData.draw == data.draw) {
                            //console.log(data);
                            this.projects = data.data.data;
                            vm.botonesExtraLocal = data.botonesExtra;
                            this.configPagination(data.data);
                        }
                    })
                    .catch(errors => {
                        console.log(errors);
                        this.isBusy = false;
                    });
            },
            configPagination(data) {
                this.pagination.lastPage = data.last_page;
                //this.pagination.currentPage = data.current_page;
                this.pagination.total = data.total;
                this.pagination.lastPageUrl = data.last_page_url;
                this.pagination.nextPageUrl = data.next_page_url;
                this.pagination.prevPageUrl = data.prev_page_url;
                this.pagination.from = data.from;
                this.pagination.to = data.to;

                this.totalRows = data.total;
                // this.currentPage.data.current_page;
            },
            sortChanged(ctx){
                console.log('cambio el sort');
                this.sortKey = ctx.sortBy;
                this.sortOrders[ctx.sortBy] = this.sortOrders[ctx.sortBy] * -1;
                this.tableData.column = this.getIndex(this.columns, 'key', ctx.sortBy);
                this.tableData.dir = this.sortOrders[ctx.sortBy] === 1 ? 'asc' : 'desc';
                this.getProjects();
            },
            getIndex(array, key, value) {
                return array.findIndex(i => i[key] == value)
            },
        }
    };
</script>

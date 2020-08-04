<template>
    <b-container fluid>
        <!-- User Interface controls -->
        <b-row>
            <b-col sm="6" class="my-1">
                <b-form-group
                        class="mb-0"
                >
                    <b-input-group size="sm">
                        <b-form-input
                                v-model="filter"
                                type="search"
                                id="filterInput"
                                placeholder="Buscar"
                        ></b-form-input>
                        <b-input-group-append>
                            <b-button :disabled="!filter" @click="filter = ''">Limpiar</b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
            </b-col>

            <b-col  sm="4" class="my-1">
                <b-form-group
                        label="Por página"
                        label-cols-sm="6"
                        label-cols-md="4"
                        label-cols-lg="4"
                        label-align-sm="right"
                        label-size="sm"
                        label-for="perPageSelect"
                        class="mb-0"
                >
                    <b-form-select
                            v-model="perPage"
                            id="perPageSelect"
                            size="sm"
                            :options="pageOptions"
                    ></b-form-select>
                </b-form-group>
            </b-col>
        </b-row>

        <!-- Main table element -->
        <b-table
                show-empty
                small
                stacked="md"
                :items="items"
                :fields="fields"
                :current-page="currentPage"
                :per-page="perPage"
                :filter="filter"
                :filterIncludedFields="filterOn"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
                :sort-direction="sortDirection"
                @filtered="onFiltered"
        >

            <template v-slot:cell(actions)="row">

            </template>

        </b-table>
        <b-row>
            <b-col sm="7" md="6" class="my-1">
                <b-pagination
                        v-model="currentPage"
                        :total-rows="totalRows"
                        :per-page="perPage"
                        align="fill"
                        size="sm"
                        class="my-0"
                ></b-pagination>
            </b-col>
        </b-row>

        <!-- Info modal -->
        <b-modal :id="infoModal.id" :title="infoModal.title" ok-only @hide="resetInfoModal">
            <pre>{{ infoModal.content }}</pre>
        </b-modal>
    </b-container>
</template>

<script>
    export default {
        props: ['idRow', 'data'],
        data() {
            return {
            // { 'fecha' : '2019-02-02 20:15:10', 'descripcion' : 'Ingreso de materia prima', 'tipo' : 'Ingreso', 'cantidad' : 2000  },
            // { 'fecha' : '2019-02-01 20:15:10', 'descripcion' : 'Egreso de materia prima', 'tipo' : 'Egreso', 'cantidad' : 200  },
            // { 'fecha' : '2019-02-06 20:15:10', 'descripcion' : 'Ingreso de materia prima', 'tipo' : 'Ingreso', 'cantidad' : 7000  },
                items: [

                ],
                fields: [
                    { key: 'fecha', label: 'Fecha', sortable: false, sortDirection: 'desc' },
                    { key: 'descripcion', label: 'Descripción', sortable: false, class: 'text-center' },
                    { key: 'tipo', label: 'Tipo', sortable: false },
                    { key: 'cantidad', label: 'Cantidad', sortable: false },
                    { key: 'observaciones', label: 'Observaciones', sortable:false  },
                    { key: 'responsable', label:'Responsable', sortable:true},
                    { key: 'actions', label: 'Actions' }
                ],
                totalRows: 1,
                currentPage: 1,
                perPage: 5,
                pageOptions: [5, 10, 15],
                sortBy: '',
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

        },
        mounted() {
            // Set the initial number of items
            this.totalRows = this.items.length;
            this.getData();
        },
        methods: {
            info(item, index, button) {
                this.infoModal.title = `Row index: ${index}`;
                this.infoModal.content = JSON.stringify(item, null, 2);
                this.$root.$emit('bv::show::modal', this.infoModal.id, button);
            },
            resetInfoModal() {
                this.infoModal.title = '';
                this.infoModal.content = '';
            },
            onFiltered(filteredItems) {
                // Trigger pagination to update the number of buttons/pages due to filtering
                this.totalRows = filteredItems.length;
                this.currentPage = 1;
            },
            getData() {
                axios.get('/api/get-kardex/'+this.idRow)
                    .then(response => {
                        let data = response.data;
                        this.items = data;
                    })
                    .catch(errors => {
                        console.log(errors);
                    });
            }
        }
    }
</script>
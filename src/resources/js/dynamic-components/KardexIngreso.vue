<template>
    <div>
        <b-row>
            <b-col>
                <b-form-group
                        id="fieldset-bodegas"
                        label="Seleccione una bodega"
                        label-for="select-bodegas"
                >
                    <b-form-select id="select-bodegas" v-model="selects.selectBodega.selectedBodega" :options="selects.selectBodega.optionsBodega"></b-form-select>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group
                        id="fieldset-tipos"
                        label="Seleccione el tipo"
                        label-for="select-tipos"
                >
                    <b-form-select id="select-tipos" v-model="selects.selectTipo.selectedTipo" :options="selects.selectTipo.optionsTipo"></b-form-select>
                </b-form-group>
            </b-col>
        </b-row>

        <b-row>
            <b-col>
                <b-form-group
                        id="fieldset-empleados"
                        label="Seleccione el responsable"
                        label-for="select-empleados"
                >
                    <b-form-select id="select-empleados" v-model="selects.selectEmpleado.selectedEmpleado" :options="selects.selectEmpleado.optionsEmpleado"></b-form-select>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group
                        id="fieldset-cantidad"
                        description="Ingresa la cantidad de producto."
                        label="Cantidad"
                        label-for="input-cantidad"
                        :invalid-feedback="inputs.inputCantidad.invalidFeedback"
                        :valid-feedback="inputs.inputCantidad.validFeedback"
                        :state="verificarCantidad"
                >
                    <b-form-input type="number" id="input-cantidad" v-model="inputs.inputCantidad.valueCantidad" :state="verificarCantidad" trim></b-form-input>
                </b-form-group>
            </b-col>
        </b-row>



        <b-form-group
                id="fieldset-observaciones"
                description="Ingresa una nota adicional."
                label="Observaciones"
                label-for="input-observaciones"
        >
            <b-form-textarea
                    id="input-observaciones"
                    v-model="inputs.inputObservaciones.valueObservaciones"
                    placeholder="Ingrese una nota adicional (opcional)"
                    rows="3"
                    size="sm"
                    max-rows="6"
            ></b-form-textarea>
        </b-form-group>

        <b-alert v-if="mostrarMensaje" show :variant="varianteMensajeAlerta">{{ mensaje }}</b-alert>

        <div>
            <b-button v-on:click="guardarKardex" :disabled="!verificarCantidad || enviandoDatos" block variant="success">
                <b-spinner v-if="this.enviandoDatos" small type="grow"></b-spinner>
                    {{ textoBoton }}
            </b-button>
        </div>

    </div>
</template>

<script>
    export default {
        props: ['idRow', 'data', 'usersid'],
        data() {
            return {
                mensaje: '',
                mostrarMensaje: false,
                varianteMensajeAlerta: 'success',
                enviandoDatos: false,
                selects: {
                    selectBodega: {
                        selectedBodega: 1,
                        optionsBodega: this.data.bodegas,
                    },
                    selectTipo: {
                        selectedTipo: 1,
                        optionsTipo: this.data.tiposKardex
                    },
                    selectEmpleado: {
                        selectedEmpleado: 1,
                        optionsEmpleado: this.data.empleados
                    }
                },
                inputs: {
                    inputCantidad: {
                        valueCantidad: 0,
                        validFeedback: '',
                        invalidFeedback: 'Debes ingresar un valor',
                    },
                    inputObservaciones: {
                        valueObservaciones: "",
                    }
                }
            }
        },
        mounted() {
            // this.selects.selectBodega.optionsBodega.push({
            //     value: null,
            //     text: 'Seleccione una bodega'
            // })
        },
        methods: {
            guardarKardex() {
                this.enviandoDatos = true;
                axios.post('/api/guardar-kardex',{
                    productoid: this.idRow,
                    bodegaid: this.selects.selectBodega.selectedBodega,
                    kardextipoid: this.selects.selectTipo.selectedTipo,
                    empleadoid: this.selects.selectEmpleado.selectedEmpleado,
                    cantidad: this.inputs.inputCantidad.valueCantidad,
                    observaciones: this.inputs.inputObservaciones.valueObservaciones,
                    usersid: this.usersid,
                })
                    .then(response => {
                        let data = response.data;

                        //console.log(data);

                            this.mensaje = data.mensaje;
                        if(data.respuesta){
                            this.mostrarMensaje = true;
                            this.varianteMensajeAlerta = 'success';
                            this.inputs.inputCantidad.valueCantidad = 0;

                            //this.$parent.$parent.getProjects();
                            //this.$dispatch('actualizarProyectos')
                            this.$emit('event');

                        } else {
                            this.mostrarMensaje = true;
                            this.varianteMensajeAlerta = 'danger';
                        }

                        this.enviandoDatos = false;
                    })
                    .catch(errors => {
                        this.enviandoDatos = false;
                        this.mostrarMensaje = true;
                        this.varianteMensajeAlerta = 'danger';
                        this.mensaje = "Ocurrió un error, ponte en contacto con el desarrollador del sistema";
                        console.log(errors);
                    });
            }
        },
        computed: {
            verificarCantidad(){
                return (this.inputs.inputCantidad.valueCantidad > 0)
            },
            textoBoton() {
                var texto = "Guardar";

                if(this.enviandoDatos) {
                    return 'Enviando Datos';
                } else {
                    return texto;
                }
            }
        }
    }
</script>
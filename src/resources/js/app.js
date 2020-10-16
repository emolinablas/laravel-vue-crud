/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

 window.Vue = require('vue');
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

// Install BootstrapVue
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);



import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

import vSelect from 'vue-select';

Vue.component('v-select', vSelect);

import 'vue-select/dist/vue-select.css';

import { ValidationProvider } from 'vee-validate';
import { ValidationObserver } from 'vee-validate';

import { extend } from 'vee-validate';
import * as rules from 'vee-validate/dist/rules';
import { messages } from 'vee-validate/dist/locale/es.json';

Object.keys(rules).forEach(rule => {
    extend(rule, {
        ...rules[rule], // copies rule configuration
        message: messages[rule] // assign message
    });
});

extend("selectValidation", {
    validate: (value) => {
        return value.value > 0;
    },
    message:
        "Debe seleccionar un valor"
});

// Register it globally
Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);

// import $ from 'jquery';
// import dt from 'datatables.net';
// import buttons from 'datatables.net-buttons';
//
// window.JSZip = require( 'jszip' );
//
// require( 'datatables.net-responsive');
// require( 'datatables.net-buttons/js/buttons.colVis.js' )();
// require( 'datatables.net-buttons/js/buttons.html5.js' )();
// require( 'datatables.net-buttons/js/buttons.flash.js' )();
// require( 'datatables.net-buttons/js/buttons.print.js' )();
//
// var pdfMake = require('pdfmake/build/pdfmake.js');
// var pdfFonts = require('pdfmake/build/vfs_fonts.js');
// pdfMake.vfs = pdfFonts.pdfMake.vfs;




// import Highcharts from 'highcharts';
//
// import HighchartsVue from 'highcharts-vue';
//
// Vue.use(HighchartsVue);
// // Load module after Highcharts is loaded
// require('highcharts/modules/exporting')(Highcharts);
// // Load module after Highcharts is loaded
// require('highcharts/modules/variable-pie')(Highcharts);



/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

 const files = require.context('../../../../../../resources/js/dynamic-components', true, /\.vue$/i)
//console.log(files.keys());
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

 //Vue.component('pagination-component', require('./components/PaginationComponent.vue').default);
 Vue.component('crud-component', require('./components/CrudComponent.vue').default);

// Vue.component('principal-crud-component', require('./components/PrincipalCrudComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */



new Vue({
    el: '#app',
});
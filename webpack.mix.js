const mix = require('laravel-mix');
const path = require('path');
const fs   = require('fs');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// Cuando se compila de forma standalone (desarrollo del paquete), el directorio
// de componentes dinámicos de la app consumidora no existe. Lo creamos como
// stub vacío para que webpack no falle al procesar require.context en app.js.
// Cuando el paquete está instalado en vendor/, ese directorio ya existe con
// los componentes reales de la app consumidora.
const dynamicComponentsDir = path.resolve(__dirname, '../../..', 'resources/js/dynamic-components');
if (!fs.existsSync(dynamicComponentsDir)) {
    fs.mkdirSync(dynamicComponentsDir, { recursive: true });
}

mix.js(['src/resources/js/summernote-bs4.min.js','src/resources/js/app.js'], 'src/public/js/app.js')
    .vue({ version: 2 })
    .sass('src/resources/sass/app.scss', 'src/public/css');

mix.copyDirectory('src/public', '../../../public/vendor/emolinablas/laravel-vue-crud');

const mix = require('laravel-mix');

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

mix.js(['src/resources/js/summernote-bs4.min.js','src/resources/js/app.js'], 'src/public/js/app.js')
    .sass('src/resources/sass/app.scss', 'src/public/css');

mix.copyDirectory('src/public', '../../../public/vendor/emolinablas/laravel-vue-crud');

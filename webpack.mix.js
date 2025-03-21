const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .css('resources/css/app.css', 'public/css')
   .css('resources/css/admin.css', 'public/css')
   .copy('node_modules/admin-lte/dist/css/adminlte.min.css', 'public/css') // AdminLTE CSS
   .copy('node_modules/admin-lte/dist/js/adminlte.min.js', 'public/js') // AdminLTE JS
   .copy('node_modules/admin-lte/plugins', 'public/plugins'); // AdminLTE Plugins (jQuery, Bootstrap va boshqalar)
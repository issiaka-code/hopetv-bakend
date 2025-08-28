const mix = require('laravel-mix');

mix.react('resources/js/index.jsx', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');
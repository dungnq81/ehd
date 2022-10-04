let mix = require('laravel-mix');

const path = require('path');
let directory = path.basename(path.resolve(__dirname));

const dir = 'wp-content/plugins/' + directory;
const assets = dir + '/assets';

mix.disableNotifications()
    .sourceMaps()
    //.copyDirectory(dir + '/resources/img', assets + '/img')

    // .sass(dir + '/resources/sass/editor-style.scss', assets + '/css')
    // .sass(dir + '/resources/sass/admin.scss', assets + '/css')
    .sass(dir + '/resources/sass/ehd.scss', assets + '/css')

    // .js(dir + '/resources/js/admin.js', assets + '/js')
    // .js(dir + '/resources/js/login.js', assets + '/js')
    .js(dir + '/resources/js/ehd.js', assets + '/js');
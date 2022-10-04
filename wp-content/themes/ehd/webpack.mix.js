let mix = require('laravel-mix');
//const purgeCss = require('@fullhuman/postcss-purgecss');

const path = require('path');
let directory = path.basename(path.resolve(__dirname));

const dir = 'wp-content/themes/' + directory;
const assets = dir + '/assets';

mix.disableNotifications()
    .sourceMaps()

    // .copyDirectory(dir + '/resources/img', assets + '/img')
    // .copyDirectory(dir + '/resources/fonts/SVN-Poppins', assets + '/fonts/SVN-Poppins')
    // .copyDirectory(dir + '/resources/fonts/fontawesome/webfonts', assets + '/webfonts')
    // .copyDirectory(dir + '/resources/js/plugins', assets + '/js/plugins')

    // .sass(dir + '/resources/sass/fonts.scss', assets + '/css')

    .sass(dir + '/resources/sass/plugins.scss', assets + '/css')
    .sass(dir + '/resources/sass/layout.scss', assets + '/css')

    .sass(dir + '/resources/sass/app.scss', assets + '/css')
    .sass(dir + '/resources/sass/woocommerce.scss', assets + '/css')
    .sass(dir + '/resources/sass/elementor.scss', assets + '/css')

    // .js(dir + '/resources/js/plugins-dev/draggable.js', assets + '/js/plugins')
    // .js(dir + '/resources/js/plugins-dev/skip-link-focus-fix.js', assets + '/js/plugins')
    // .js(dir + '/resources/js/plugins-dev/flex-gap.js', assets + '/js/plugins')

    .js(dir + '/resources/js/app.js', assets + '/js');
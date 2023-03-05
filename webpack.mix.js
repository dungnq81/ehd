let mix = require('laravel-mix');
const glob = require('glob');

mix
    .webpackConfig({
        stats: {
            children: true,
        }
    })
    .options({
        processCssUrls: false,
        clearConsole: true,
        terser: {
            extractComments: false,
        },
        autoprefixer: {
            remove: false
        }
    });

// Source maps when not in production.
if (!mix.inProduction()) {
    mix
        .sourceMaps()
        .webpackConfig({ devtool: 'source-map' })
}

// Run only for a plugin.
require('./wp-content/plugins/ehd-core/webpack.mix.js');

// Run only for themes.
glob.sync('./wp-content/themes/**/webpack.mix.js').forEach(item => require(item));

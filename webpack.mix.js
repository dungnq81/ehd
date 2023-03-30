const mix = require('laravel-mix');
const { glob, globSync } = require('glob');

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
        .sourceMaps(false, 'source-map');
        //.webpackConfig({devtool: 'source-map'});
}

// Run only for a plugin.
require('./wp-content/plugins/ehd-core/webpack.mix.js');

// Run only for themes.
globSync('./wp-content/themes/**/webpack.mix.js').forEach(file => require(`./${file}`));

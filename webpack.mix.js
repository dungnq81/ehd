let mix = require('laravel-mix');
let glob = require('glob');

mix.sourceMaps()
    .options({
        processCssUrls: false,
        clearConsole: true,
        terser: {
            extractComments: false,
        },
        postCss: [
            require('autoprefixer')({
                // Browserslist’s default browsers (> 0.5%, last 2 versions, Firefox ESR, not dead).
                // browsers: ['defaults'],
                browsers: ['last 3 versions', '> 0.5%', 'iOS >= 9', 'not dead'],
                grid: true
            })
        ]
    });

// Run only for a plugin.
require('./wp-content/plugins/ehd-core/webpack.mix.js');

// Run only for themes.
glob.sync('./wp-content/themes/**/webpack.mix.js').forEach(item => require(item));

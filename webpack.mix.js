let mix = require("laravel-mix");

require("laravel-mix-tailwind");
require("laravel-mix-purgecss");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js("resources/assets/js/app.js", "public/js")
    .postCss("resources/assets/css/app.css", "public/css", [
        require('tailwindcss'),
    ]);

if (mix.inProduction()) {
    mix.version()
        .purgeCss({
            whitelistPatterns: [/language/, /hljs/],
        });
}

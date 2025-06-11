const mix = require('laravel-mix');
const path = require('path');
const dotenv = require('dotenv');

// Load environment variables from the .env file
dotenv.config();

// Webpack Mix configuration
mix.js('resources/js/app.js', 'public/js')
    .vue()  // Enable Vue support
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'),
    ]);

module.exports = {
    module: {
        rules: [
            {
                test: /\.css$/i,
                use: ['style-loader', 'css-loader'],
            },
        ],
    },
};

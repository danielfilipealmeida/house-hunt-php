/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('bulma/css/bulma.css');
require('../css/app.css');
require('leaflet/dist/leaflet.css');

const $ = require('jquery');
require('leaflet');
const maps = require('./maps');



$('document').ready(() => {
    maps.create(L, 'mapid', 51.505, -0.09);
})

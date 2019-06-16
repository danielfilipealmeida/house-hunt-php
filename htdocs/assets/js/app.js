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
const Maps = require('./maps');



$('document').ready(() => {
    /** todo: move this into a system constant */
    const token = 'pk.eyJ1IjoiZGFuaWVsZmlsaXBlYSIsImEiOiJjand5NHczMGwwYTB6M3lwY3lidGp2dDByIn0.8MeQEHRq4QCRhSCxnbOfAw';
    const map = new Maps(L, token);

    map.setId('mapid')
        .setCoordinates(51.505, -0.09)
        .setZoom(13)
        .setMaxZoom(20)
        .create();
});

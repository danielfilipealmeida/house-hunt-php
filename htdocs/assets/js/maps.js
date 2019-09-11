var $ = require('jquery');

/**
 * Embed MapBox maps
 */
class Map {
    /**
     *
     * @param L
     */
    constructor(L, token) {
        this.L = L;
        this.token = token;

        this.zoom = 10;
        this.maxZoom = 20;
        this.latitude = 0;
        this.longitude = 0;
        this.id = 'mapid';

        //this.MAPBOX_BASE_URL = 'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=';
        this.MAPBOX_BASE_URL = '/map/tile/{z}/{x}/{y}';
    }

    /**
     *
     * @param latitude
     * @param longitude
     * @returns {Map}
     */
    setCoordinates(latitude, longitude) {
        this.latitude = latitude;
        this.longitude = longitude;

        return this;
    }

    /**
     *
     * @param id
     * @returns {Map}
     */
    setId(id) {
        this.id = id;

        return this;
    }

    /**
     *
     * @param zoom
     * @returns {Map}
     */
    setZoom(zoom) {
        this.zoom = zoom;

        return this;
    }

    /**
     *
     * @param maxZoom
     * @returns {Map}
     */
    setMaxZoom(maxZoom) {
        this.maxZoom = maxZoom;

        return this;
    }


    /**
     *
     */
    create () {
        this.map = this.L.map(this.id).setView([this.latitude, this.longitude], this.zoom);
        this.L.tileLayer(this.MAPBOX_BASE_URL, {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: this.maxZoom,
            id: 'mapbox.streets'
        }).addTo(this.map);

        this.map.on('click', function(ev) {
            const input = $('#' + this.id).closest('input');
            const latlng = ev.latlng;
            input.val([latlng.lat, latlng.lng].join(','));



            /*
            const centerPosition = ev.target._lastCenter;

            if (typeof centerPosition == null) return;



             */
        }.bind(this));
    }
}

module.exports = Map;
var $ = require('jquery');

/**
 * Embed MapBox maps
 */
class Map {
    /**
     * Map class Constructor
     *
     * url template: 'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=';
     *
     * @param L
     * @param token
     */
    constructor(L, token) {
        this.L = L;
        this.token = token;
        this.zoom = 5
        this.maxZoom = 20;
        this.latitude = 0;
        this.longitude = 0;
        this.id = 'mapid';
        this.MAPBOX_BASE_URL = '/map/tile/{z}/{x}/{y}';
        this.mapOptions = {
            'doubleClickZoom': false,
            'scrollWheelZoom': false,
            'touchZoom': false
        };
    }

    /**
     * Sets up the map using provided information
     */
    setup(id, radiusId) {
        const latitude = $('#' + id).next().val();
        const longitude = $('#' + id).next().next().val();
        const radius = $(radiusId).val();

        this.setId(id)
            .setCoordinates(latitude, longitude, radius)
            .setZoom(13)
            .setMaxZoom(20);

        return this;
    }

    /**
     * @param latitude
     * @param longitude
     * @param radius
     * @returns {Map}
     */
    setCoordinates(latitude, longitude, radius) {
        this.latitude = latitude;
        this.longitude = longitude;
        this.radius = radius;
        return this;
    }

    /**
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
        this.map = this.L.map(this.id, this.mapOptions).setView([this.latitude, this.longitude], this.zoom);
        this.L.tileLayer(this.MAPBOX_BASE_URL, {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: this.maxZoom,
            id: 'mapbox.streets'
        }).addTo(this.map);

        //let marker = L.marker([this.latitude, this.longitude]).addTo(this.map);
        this.circle = L.circle([this.latitude, this.longitude],
            {
                color: 'black',
                radius: this.radius
            }).addTo(this.map);

        /*
        this.map.on('click', function(ev) {
            const input = $('#' + this.id).closest('input');
            const latlng = ev.latlng;
            input.val([latlng.lat, latlng.lng].join(','));
        }.bind(this));
        */

        this.map.on('click', function(event) {
            //let center = event.target.getCenter();
            let center = event.latlng;
            $('#' + this.id).next().val(center.lat);
            $('#' + this.id).next().next().val(center.lng);

            this.circle.setLatLng(center)._update();

        }.bind(this))
    }
}

module.exports = Map;
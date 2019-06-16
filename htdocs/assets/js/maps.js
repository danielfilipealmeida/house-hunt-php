/**
 * Generates a Leaflet.js map
 * @param id
 * @param latitude
 * @param longitude
 */
let create = (L, id, latitude, longitude) => {
    //console.log(L.map);
    let token = 'pk.eyJ1IjoiZGFuaWVsZmlsaXBlYSIsImEiOiJjand5NHczMGwwYTB6M3lwY3lidGp2dDByIn0.8MeQEHRq4QCRhSCxnbOfAw';
    let map = L.map(id).setView([latitude, longitude], 13);
    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=' + token, {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox.streets'
    }).addTo(map);
};

module.exports = {
  create
};

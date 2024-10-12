/*
 *  Document   : readyTimeline.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Timeline page
 */

var ReadyTimeline = function() {

    return {
        init: function() {
            /*
             * With Gmaps.js, Check out examples and documentation at http://hpneo.github.io/gmaps/examples.html
             */

            // Initialize Timeline map
            new GMaps({
                div: '#gmap-timeline',
                lat: 59.32,
                lng: 17.97,
                zoom: 15,
                scrollwheel: false
            }).addMarkers([
                {lat: 59.32, lng: 17.97, title: 'Cafe-Bar', animation: google.maps.Animation.DROP, infoWindow: {content: '<strong>Cafe-Bar</strong>'}}
            ]);
        }
    };
}();
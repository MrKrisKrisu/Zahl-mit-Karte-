<?php
/**
 * @author Kristian Stoeckel https://github.com/MrKrisKrisu
 */
?>
<div id="mapid" style="width: 100%; height: 700px;"></div>

<script>
    var map = L.map('mapid').setView([52.374476, 9.738585], 16);
    var markerCache = [];

    L.tileLayer('https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery by Wikimedia',
        maxZoom: 18,
        id: 'wikimedia'
    }).addTo(map);

    map.on('moveend', function () {
        onMove();
    });

    loadCurrentNodes();
    loadUserLocation();


    function loadUserLocation() {
        if (!navigator.geolocation)
            return;
        navigator.geolocation.getCurrentPosition(function (position) {
            map.panTo(new L.LatLng(position.coords.latitude, position.coords.longitude));
        });
    }

    function onMove() {
        if (map._zoom < 12) {
            console.log("Zoomstufe zu gering. Daten werden nicht geladen.");
            return;
        }
        loadCurrentNodes();
    }

    function loadCurrentNodes() {
        $.ajax({
            url: getOverpassRequestURL(),
            success: function (data) {
                $.each(data.elements, function (elementId, element) {
                    if (!isElementOnMap(element.id))
                        addElementToMap(element);
                });
            },
            error: function () {
                console.log("Ein Fehler beim Laden der Daten von der Overpass API ist aufgetreten.");
            }
        });
    }

    function getOverpassRequestURL() {
        var coords = map.getBounds();
        var lefttop = coords.getNorthWest();
        var rightbottom = coords.getSouthEast();
        var bbox = rightbottom.lat + '%2C' + lefttop.lng + '%2C' + lefttop.lat + '%2C' + rightbottom.lng;
        return "https://overpass-api.de/api/interpreter?data=%5Bout%3Ajson%5D%5Btimeout%3A25%5D%3B%0A%28%0A%20%20node%5B%22payment%3Amastercard%22%3D%22yes%22%5D%28" + bbox + "%29%3B%0A%20%20way%5B%22payment%3Amastercard%22%3D%22yes%22%5D%28" + bbox + "%29%3B%0A%20%20node%5B%22payment%3Avisa%22%3D%22yes%22%5D%28" + bbox + "%29%3B%0A%20%20way%5B%22payment%3Avisa%22%3D%22yes%22%5D%28" + bbox + "%29%3B%0A%20%20node%5B%22payment%3Aamerican_express%22%3D%22yes%22%5D%28" + bbox + "%29%3B%0A%20%20way%5B%22payment%3Aamerican_express%22%3D%22yes%22%5D%28" + bbox + "%29%3B%0A%20%20node%5B%22payment%3Agirocard%22%3D%22yes%22%5D%28" + bbox + "%29%3B%0A%20%20way%5B%22payment%3Agirocard%22%3D%22yes%22%5D%28" + bbox + "%29%3B%0A%29%3B%0Aout%20center%3B";
    }

    function isElementOnMap(osmID) {
        return osmID in markerCache;
    }

    function addElementToMap(element) {
        var lat = element.type == "node" ? element.lat : element.center.lat;
        var lng = element.type == "node" ? element.lon : element.center.lon;
        var marker = L.marker([lat, lng]).bindPopup(getPopupContentForElement(element)).addTo(map);
        markerCache[element.id] = marker;
    }

    function getPopupContentForElement(element) {
        var content = "";
        if (element.tags.name != undefined)
            content += "<b>" + element.tags.name + "</b><br />";
        content += "<i>" + getElementType(element) + "</i><br />";

        content += "<br />";
        content += "<b>Zahlungsmöglichkeiten</b><br />";
        $.each(getPopuplist(), function (name, tag) {
            content += name + ": " + getReadableAcceptanceStatus(element, tag) + "<br />";
        });

        content += "<small>Daten fehlerhaft? <a href='https://www.openstreetmap.org/edit?node=" + element.id + "' target='_blank'>Hier</a> korrigieren. :)</small>"

        return content;
    }

    function getReadableAcceptanceStatus(element, tag) {
        if (element.tags[tag] == undefined)
            return "unbekannt";
        if (element.tags[tag] == "yes")
            return "Ja";
        if (element.tags[tag] == "no")
            return "Nein";
        return "?";
    }

    function getPopuplist() {
        return {
            "Kontaktlos": "payment:contactless",
            "Girokarte": "payment:girocard",
            "MasterCard": "payment:mastercard",
            "VISA": "payment:visa",
            "American Express": "payment:american_express"
        };
    }

    function getElementType(element) {
        if (element.tags.amenity == "vending_machine" && element.tags.vending == "public_transport_tickets")
            return "Fahrkartenautomat";
        if (element.tags.amenity != undefined)
            return element.tags.amenity;
        if (element.tags.shop != undefined)
            return element.tags.shop;
    }
</script>
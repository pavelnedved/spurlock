/**
 * This script handles functions of the "Dashboard" (the initial state of the seach index)
 * It sets up masonry on the tile container and starts loading tiles.
 */

var numTilesToLoad = 12;

var curFactoryState = null;
var numTilesLoaded = 0;

$(document).ready(function () {

    $('#tileCont').masonry({
        itemSelector: '.tile',
        isFitWidth: true
    });

    loadTiles(numTilesToLoad);

});

function loadTiles() {
    loadNextTile();
}

function loadNextTile() {
    $.ajax({
        type: "POST",
        url: "ajaxDashboardTile.php",
        data: { stateJSON: JSON.stringify(curFactoryState) }
    }).done(function (msg) {
            var data = $.parseJSON(msg);
            curFactoryState = data.stateJSON;
            $(data.html).appendTo("#tileCont");
            $("#tileCont").masonry('reload');
            numTilesLoaded++;
            if (numTilesLoaded < numTilesToLoad) {
                loadNextTile();
            }
        });
}
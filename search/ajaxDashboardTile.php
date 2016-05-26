<?php

/**
 *      AJAX Handler for random Dashboard Tiles
 *        Each AJAX call will return a new random tile
 *        Called using a POST request with a specified "state". This state is an array of possible tiles you want chosen from.
 *        The script will return the HTML for a tile and a new "state" array that does not contain the returned tile.
 *        This allows to keep state across AJAX calls so that we don't get duplicate tiles.
 */

require_once("dashboardTileFactory.class.php");

//Grab the "state" from the POST variables
if (isset($_POST['stateJSON'])) {
    $stateJSON = json_decode($_POST['stateJSON'], TRUE);
} else {
    $stateJSON = null;
}

//If no state was specified, assume a blank state
if ($stateJSON === null || count($stateJSON) === 0) {
    $tileFactory = new dashboardTileFactory();
} else {
    $tileFactory = new dashboardTileFactory($stateJSON);
}

$returnJSON = array();

//Grab a random tile from the tile factory
$tile = $tileFactory->getRandomDashboardTile();

//If we got a valid tile, get its HTML, otherwise we return a blank string
if ($tile !== null) {
    $returnJSON['html'] = $tile->display(FALSE);
} else {
    $returnJSON['html'] = '';
}

//Get the state variable from the tile factory
$returnJSON['stateJSON'] = $tileFactory->getState();

//JSON encode the return variables and spit them out in the response
echo json_encode($returnJSON);

?>

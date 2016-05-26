<?php

//DEBUG: SHOW ALL WARNINGS/ERRORS
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

/**
 *        Call this script using AJAX requests.
 *        This script provides an interface for analytics requests. From Javascript,
 *        send an AJAX request to this script specifying the action you want and any
 *        parameters.
 */

require_once("analyticalView.class.php");

date_default_timezone_set("America/Chicago");

$analyticalView = new analyticalView();

switch ($_REQUEST['request']) {
    case "showStats":
        $analyticalView->showStats();
        break;
    case "showAverages":
        $analyticalView->showAverages();
        break;
    case "showAllSearches":
        $analyticalView->showAllSearches();
        break;
    case "showDay":
        $analyticalView->showDay($_REQUEST['year'], $_REQUEST['month'], $_REQUEST['day']);
        break;
    case "getDayData":
        $analyticalView->getDayData($_REQUEST['year'], $_REQUEST['month'], $_REQUEST['day']);
        break;
    case "showMonth":
        $analyticalView->showMonth($_REQUEST['year'], $_REQUEST['month']);
        break;
    case "getMonthData":
        $analyticalView->getMonthData($_REQUEST['year'], $_REQUEST['month']);
        break;
    case "showYear":
        $analyticalView->showYear($_REQUEST['year']);
        break;
    case "getYearData":
        $analyticalView->getYearData($_REQUEST['year']);
        break;
    case "showPastWeek":
        $analyticalView->showPastWeek();
        break;
    case "getPastWeekData":
        $analyticalView->getPastWeekData();
        break;
    case "showBrowsers":
        $analyticalView->showBrowsers();
        break;
    case "showOSs":
        $analyticalView->showOSs();
        break;
    case "showDBsSearched":
        $analyticalView->showDBsSearched();
        break;
    case "showAllYearsByMonth":
        $analyticalView->showAllYearsByMonth();
        break;
    case "getAllYearsByMonthData":
        $analyticalView->getAllYearsByMonthData();
        break;
    case "showAllYearsByYear":
        $analyticalView->showAllYearsByYear();
        break;
    case "getAllYearsByYearData":
        $analyticalView->getAllYearsByYearData();
        break;
    case "showMostCommonSearchQueries":
        $analyticalView->showMostCommonSearchQueries($_REQUEST['numToShow']);
        break;
    case "showRecentSearches":
        $analyticalView->showRecentSearches($_REQUEST['numToShow']);
        break;
    case "showTopSearchers":
        $analyticalView->showTopSearchers($_REQUEST['numToShow']);
        break;
    case "showSearchesByIP":
        $analyticalView->showSearchesByIP($_REQUEST['ip']);
        break;
    case "showSearchesByDateRange":
        $analyticalView->showSearchesByDateRange($_REQUEST['begin'], $_REQUEST['end']);
        break;
    case "showSearchBySid":
        $analyticalView->showSearchBySid($_REQUEST['sid']);
        break;
    case "showTopArtifacts":
        $analyticalView->showTopArtifacts($_REQUEST['numToShow']);
        break;
    default:
        echo "Invalid request";
        break;
}

	
	

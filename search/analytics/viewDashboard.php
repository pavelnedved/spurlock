<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>View Search Tracking - Spurlock Museum</title>
    <link rel="stylesheet" href="../CSS/jquery.jqplot.min.css" type="text/css"/>
    <link rel="stylesheet" href="../CSS/searchStats.css" type="text/css"/>
    <script src="http://spurlock.illinois.edu/support/jquery-1.7.1.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../JS/sorttable.js"></script>
    <script type="text/javascript" src="../JS/jquery.jqplot.min.js"></script>
    <script type="text/javascript" src="../JS/plugins/jqplot.canvasTextRenderer.min.js"></script>
    <script type="text/javascript" src="../JS/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
    <script type="text/javascript" src="../JS/plugins/jqplot.dateAxisRenderer.min.js"></script>
    <script type="text/javascript" src="../JS/analyticsAjax.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            //Update all of the existing data tables/divs
            updateAll();
            //Setup a click handler on the button to toggle real-time updates
            $("#realtime").click(function () {
                toggleRealTimeUpdates();
            });
        });
    </script>
</head>
<body>

<div id="sidebar">
    <div class="sidebar-menu">
        <h2>Navigation</h2>
        <a href="viewDashboard.php">View General Stats</a>
        <a href="viewDate.php">View Stats by Date</a>
        <a href="mostCommonQueries.php">View Most Common Queries</a>
        <a href="viewRecentSearches.php">View Recent Searches</a>
        <a href="viewSearchesByIP.php">View Top Searchers</a>
        <a href="viewTopArtifacts.php">View Top Artifacts</a>
        <a href="updateArtifact.php">Update Artifact(s)</a>
    </div>
</div>

<div id="content">
    <div style="float:right;width:400px;">
        <div id="realtime" style="float:right;padding-bottom:25px;">Real Time Updating: <span
                class="red">Disabled</span></div>
        <div id="day_chart" style="height:400px;width:500px;float:right;"></div>
        <div id="week_chart" style="height:400px;width:500px;float:right;"></div>
    </div>

    <h1>Analytics</h1>

    <h2>Stats</h2>

    <div id="stats">Loading..</div>

    <h2>Averages</h2>

    <div id="avgs">Loading..</div>

    <h2>Browsers</h2>

    <div id="browsers">Loading..</div>

    <h2>Operating Systems</h2>

    <div id="os">Loading..</div>

    <h2>Databases Searched</h2>

    <div id="dbs">Loading..</div>

    <h2>Day Stats</h2>

    <div id="day">Loading..</div>

    <h2>Week Stats</h2>

    <div id="week">Loading..</div>
</div>
</body>
</html>

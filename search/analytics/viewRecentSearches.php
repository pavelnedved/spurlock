<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>View Search Tracking - Spurlock Museum</title>
    <link rel="stylesheet" href="../CSS/jquery.jqplot.min.css" type="text/css"/>
    <link rel="stylesheet" href="../CSS/searchStats.css" type="text/css"/>
    <link rel="stylesheet" href="../CSS/anytime.css" type="text/css"/>
    <script src="http://spurlock.illinois.edu/support/jquery-1.7.1.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../JS/sorttable.js"></script>
    <script type="text/javascript" src="../JS/analyticsAjax.js"></script>
    <script type="text/javascript" src="../JS/anytime.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            //Update the recent searches table
            updateRecentSearches();
            //Setup the date pickers
            AnyTime.picker("beginRange");
            AnyTime.picker("endRange");
        });

        //Update the table based on a date range the user inputted
        function updateBasedOnRange() {
            var begin = $("#beginRange").val();
            var end = $("#endRange").val();
            updateSearchesByDateRange(begin, end);
        }

        //Update the table based on a SID the user inputted
        function updateBasedOnSid() {
            var sid = $("#sid").val();
            updateSearchBySid(sid);
        }

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
    <h2>50 Most Recent Searches</h2>

    <div>
        or search a date range:<br>
        <label for="beginRange">Begin: </label><input type="text" id="beginRange" size="50" value=""/>
        <label for="endRange">End: </label><input type="text" id="endRange" size="50" value=""/>
        <button onclick="updateBasedOnRange();">Search searches</button>
        <br>
        or search a sid:<br>
        <lavel for="sid">SID</lavel>
        <input type="text" id="sid" size="50" value=""/>
        <button onclick="updateBasedOnSid();">Search searches</button>
    </div>
    <div id="recent_searches">Loading..</div>
</div>
</body>
</html>

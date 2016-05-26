<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>View Search Tracking - Spurlock Museum</title>
    <link rel="stylesheet" href="../CSS/jquery.jqplot.min.css" type="text/css"/>
    <link rel="stylesheet" href="../CSS/searchStats.css" type="text/css"/>
    <script src="http://spurlock.illinois.edu/support/jquery-1.7.1.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../JS/sorttable.js"></script>
    <script type="text/javascript" src="../JS/analyticsAjax.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            //Update the most common queries table
            updateMostCommonQueries();
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
    <h2>Top 50 Most Common Search Queries</h2>

    <div id="most_common_queries">Loading..</div>
</div>
</body>
</html>

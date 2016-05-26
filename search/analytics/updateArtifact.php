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
            //Hide the "loading" gif and assign it to appear when the jQuery ajax handlers fire
            $('#loaderImg').hide().ajaxStart(function () {
                $(this).show();
            }).ajaxStop(function () {
                $(this).hide();
            });
            //Set the default message on the status div
            $("#status").html("Input a list of accession numbers delimited by a newline");
        });

        //Take the list of accession numbers from the text area and ask PHP to update those records
        function updateArtifacts() {
            var list = $("#list").val();
            var correctInputRegex = /([0-9]{4}\.[0-9]{2}\.[0-9]{4}[A-z]*\n)*/;
            //Check that the list conforms to the regular expression
            if (correctInputRegex.exec() == null) {
                $("#status").html("Invalid input: Should be a list of accession numbers delimited by a newline");
            } else {
                $("#status").html("Finding records..");
                //Send an AJAX request to the PHP script
                $.ajax({
                    url: "updateArtifacts.php?action=update&list=" + encodeURIComponent(list),
                    cache: false
                }).done(function (data) {
                        //Display any error/success messages from the PHP into the status div
                        $("#status").html(data);
                    });
            }
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
    <h2>Update Artifact(s)</h2>

    <p>Updates the SQL database with the newest information for given records</p>

    <p style="font-size:small;">Note: To update ALL artifacts, use the command line script</p>
    <textarea id="list" rows="10" cols="25"></textarea><br>
    <button onclick="updateArtifacts();">Update</button>
    <image id="loaderImg" src="../elements/ajax-loader.gif"></image>
    <div id="status"></div>
</div>
</body>
</html>

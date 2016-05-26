<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

date_default_timezone_set('America/Chicago');

//Get the current specified date components (if any)
if (isset($_GET['year'])) {
    $year = $_GET['year'];
} else {
    $year = date("Y");
}
if (isset($_GET['month'])) {
    $month = $_GET['month'];
} else {
    $month = date("n");
}
if (isset($_GET['day'])) {
    $day = $_GET['day'];
} else {
    $day = date("j");
}

$monthOfYear = array(
    1 => "January",
    2 => "February",
    3 => "March",
    4 => "April",
    5 => "May",
    6 => "June",
    7 => "July",
    8 => "August",
    9 => "September",
    10 => "October",
    11 => "November",
    12 => "December"
);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>View Search Tracking by Year - Spurlock Museum</title>
    <link rel="stylesheet" href="../CSS/jquery.jqplot.min.css" type="text/css"/>
    <link rel="stylesheet" href="../CSS/searchStats.css" type="text/css"/>
    <script src="http://spurlock.illinois.edu/support/jquery-1.7.1.min.js" type="text/javascript"></script>
    <script src="../JS/sorttable.js" type="text/javascript"></script>
    <script src="../JS/jquery.jqplot.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../JS/plugins/jqplot.canvasTextRenderer.min.js"></script>
    <script type="text/javascript" src="../JS/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
    <script type="text/javascript" src="../JS/plugins/jqplot.dateAxisRenderer.min.js"></script>
    <script src="../JS/analyticsAjax.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            <?php
                //Depending on the date components specified, output the Javscript functions to run
                if($year == "All"){
                    //Display all years accumulated by month
                    //Display all years accumulated by year
                    echo "updateYearsByMonth();";
                    echo "updateYearsByYear();";
                    echo "updateYearsByMonthChart();";
                    echo "updateYearsByYearChart();";
                }else if($month == "All"){
                    //Display stats for $year accumulated by month
                    echo "updateYear($year);";
                    echo "updateYearChart($year);";
                }else if($day == "All"){
                    //Display stats for $year-$month accumulated by day
                    echo "updateMonth($year,$month);";
                    echo "updateMonthChart($year,$month);";
                }else{
                    //Display stats for $year-$month-$day accumulated by hour
                    echo "updateDay($year,$month,$day);";
                    echo "updateDayChart($year,$month,$day);";
                }
            ?>
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
    <h1>Analytics</h1>

    <div id="year_nav">
        <?php
        //Output the navigation (yeah it's ugly)
        if ($year == "All") {
            echo '<span class="cur_year">All</span> | ';
            $dispYear = date("Y");
        } else {
            echo '<span><a href="viewDate.php?year=All&month=' . $month . '">All</a></span> | ';
            $dispYear = $year;
        }
        for ($i = -5; $i < 6; $i++) {
            if (($dispYear + $i) <= date("Y")) {
                if ($i == 0 && $dispYear == $year) {
                    echo '<span class="cur_year">' . ($dispYear + $i) . '</span>';
                    if (($dispYear + $i) != date("Y")) {
                        echo ' | ';
                    }
                } else {
                    echo '<span><a href="viewDate.php?year=' . ($dispYear + $i) . '&month=All">' . ($dispYear + $i) . '</a></span> | ';
                }
            }
        }
        ?>
    </div>
    <div id="month_nav">
        <?php
        if ($month == "All") {
            echo '<span class="cur_month">All</span> | ';
        } else {
            echo '<span><a href="viewDate.php?year=' . $year . '&month=All">All</a></span> | ';
        }
        for ($i = 1; $i < 13; $i++) {
            if ($i == $month) {
                echo '<span class="cur_month">' . $monthOfYear[$i] . '</span>';
            } else if ($year == date("Y") && $i > date("n")) {
                echo '<span>' . $monthOfYear[$i] . '</span>';
            } else {
                echo '<span><a href="viewDate.php?year=' . $year . '&month=' . $i . '&day=All">' . $monthOfYear[$i] . '</a></span>';
            }
            if ($i != 12) {
                echo ' | ';
            }
        }
        ?>
    </div>
    <?php
    if ($month != "All") {
        echo '<div id="day_nav">';
        if ($day == "All") {
            echo '<span class="cur_day">All</span> | ';
        } else {
            echo '<span><a href="viewDate.php?year=' . $year . '&month=' . $month . '&day=All"">All</a></span> | ';
        }
        for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++) {
            if ($i == $day) {
                echo '<span class="cur_day">' . $i . '</span>';
            } else if ($year == date("Y") && $month == date("n") && $i > date("j")) {
                echo '<span>' . $i . '</span>';
            } else {
                echo '<span><a href="viewDate.php?year=' . $year . '&month=' . $month . '&day=' . $i . '">' . $i . '</a></span>';
            }
            if ($i != cal_days_in_month(CAL_GREGORIAN, $month, $year)) {
                echo ' | ';
            }
        }
        echo '</div>';
    }
    ?>
    <!--
        3 Sliders
            << Year >>
            << Month >>
            << Day >>

            Defaults to current day
            Clicking another day shows that day
            Clicking a month shows that month
            Clicking a year shows that year
        -->
    <div id="statContent">
        <?php
        //Display the relevant divs, depending on the specified date components
        if ($year == "All") {
            //Display all years accumulated by month
            //Display all years accumulated by year
            echo '<div id="years_bymonth_chart"></div>';
            echo '<div id="years_byyear_chart"></div>';
            echo '<div id="years_bymonth"></div>';
            echo '<div id="years_byyear"></div>';
        } else if ($month == "All") {
            //Display stats for $year accumulated by month
            echo '<div id="year_chart"></div>';
            echo '<div id="year"></div>';
        } else if ($day == "All") {
            //Display stats for $year-$month accumulated by day
            echo '<div id="month_chart"></div>';
            echo '<div id="month"></div>';
        } else {
            //Display stats for $year-$month-$day accumulated by hour
            echo '<div id="day_chart"></div>';
            echo '<div id="day"></div>';
        }

        ?>
    </div>
</div>

</body>
</html>

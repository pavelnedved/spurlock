/**
 * This script provides the functions necessary to update analytical data and charts via AJAX
 * Unless otherwise specified, each function simply checks if the element it populates exists, if it does
 * then the function executes an AJAX request and populates the element with the results.
 */


function updateStats() {
    if ($("#stats").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showStats",
            cache: false
        }).done(function (html) {
                $("#stats").html(html + '<button type="button" onclick="updateStats()">Update</button>');
            });
    }
}

function updateAvgs() {
    if ($("#avgs").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showAverages",
            cache: false
        }).done(function (html) {
                $("#avgs").html(html + '<button type="button" onclick="updateAvgs()">Update</button>');
            });
    }
}

function updateBrowsers() {
    if ($("#browsers").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showBrowsers",
            cache: false
        }).done(function (html) {
                $("#browsers").html(html + '<button type="button" onclick="updateBrowsers()">Update</button>');
            });
    }
}

function updateOSs() {
    if ($("#os").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showOSs",
            cache: false
        }).done(function (html) {
                $("#os").html(html + '<button type="button" onclick="updateOSs()">Update</button>');
            });
    }
}

function updateDBsSearched() {
    if ($("#dbs").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showDBsSearched",
            cache: false
        }).done(function (html) {
                $("#dbs").html(html + '<button type="button" onclick="updateDBsSearched()">Update</button>');
            });
    }
}

function updateYearsByMonth() {
    if ($("#years_bymonth").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showYearsByMonth",
            cache: false
        }).done(function (html) {
                $("#years_bymonth").html(html + '<button type="button" onclick="updateYearsByMonth()">Update</button>');
            });
    }
}

function updateYear(year) {
    if ($("#year").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showYear&year=" + year,
            cache: false
        }).done(function (html) {
                $("#year").html(html + '<button type="button" onclick="updateYear(' + year + ')">Update</button>');
            });
    }
}

function updateYearChart(year) {
    if ($("#year_chart").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=getYearData&year=" + year,
            cache: false
        }).done(function (html) {
                $.jqplot('year_chart', [eval(html)], {
                    axesDefaults: {
                        labelRenderer: $.jqplot.CanvasAxisLabelRenderer
                    },
                    title: "Year History (Searches vs. Month)",
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.DateAxisRenderer,
                            tickOptions: {formatString: '%b'},
                            label: "Month",
                        },
                        yaxis: {
                            label: "Searches",
                        }
                    }
                });
            });
    }
}

function updateMonth(year, month) {
    if ($("#month").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showMonth&year=" + year + "&month=" + month,
            cache: false
        }).done(function (html) {
                $("#month").html(html + '<button type="button" onclick="updateMonth(' + year + ',' + month + ')">Update</button>');
            });
    }
}

function updateMonthChart(year, month) {
    if ($("#month_chart").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=getMonthData&year=" + year + "&month=" + month,
            cache: false
        }).done(function (html) {
                $.jqplot('month_chart', [eval(html)], {
                    axesDefaults: {
                        labelRenderer: $.jqplot.CanvasAxisLabelRenderer
                    },
                    title: "Month History (Searches vs. Day)",
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.DateAxisRenderer,
                            tickOptions: {formatString: '%#d'},
                            label: "Day",
                        },
                        yaxis: {
                            label: "Searches",
                        }
                    }
                });
            });
    }
}

function updateDay(year, month, day) {
    if ($("#day").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showDay&year=" + year + "&month=" + month + "&day=" + day,
            cache: false
        }).done(function (html) {
                $("#day").html(html + '<button type="button" onclick="updateDay(' + year + ',' + month + ',' + day + ')">Update</button>');
            });
    }
}

function updateDayChart(year, month, day) {
    if ($("#day_chart").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=getDayData&year=" + year + "&month=" + month + "&day=" + day,
            cache: false
        }).done(function (html) {
                $.jqplot('day_chart', [eval(html)], {
                    axesDefaults: {
                        labelRenderer: $.jqplot.CanvasAxisLabelRenderer
                    },
                    title: "Day History (Searches vs. Hour)",
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.DateAxisRenderer,
                            ticks: ['0', '4', '8', '12', '16', '20', '24'],
                            tickOptions: {formatString: '%#I%p'},
                            label: "Hour",
                        },
                        yaxis: {
                            label: "Searches",
                        }
                    }
                });
            });
    }
}

function updateWeek() {
    if ($("#week").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showPastWeek",
            cache: false
        }).done(function (html) {
                $("#week").html(html + '<button type="button" onclick="updateWeek()">Update</button>');
            });
    }
}

function updateWeekChart() {
    if ($("#week_chart").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=getPastWeekData",
            cache: false
        }).done(function (html) {
                $.jqplot('week_chart', [eval(html)], {
                    axesDefaults: {
                        labelRenderer: $.jqplot.CanvasAxisLabelRenderer
                    },
                    title: "Week History (Searches vs. Day)",
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.DateAxisRenderer,
                            tickOptions: {formatString: '%a'},
                            label: "Day",
                        },
                        yaxis: {
                            label: "Searches",
                        }
                    }
                });
            });
    }
}

function updateYearsByMonth() {
    if ($("#years_bymonth").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showAllYearsByMonth",
            cache: false
        }).done(function (html) {
                $("#years_bymonth").html(html + '<button type="button" onclick="updateYearsByMonth()">Update</button>');
            });
    }
}

function updateYearsByMonthChart() {
    if ($("#years_bymonth_chart").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=getAllYearsByMonthData",
            cache: false
        }).done(function (html) {
                $.jqplot('years_bymonth_chart', [eval(html)], {
                    axesDefaults: {
                        labelRenderer: $.jqplot.CanvasAxisLabelRenderer
                    },
                    title: "History (Searches vs. Month)",
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.DateAxisRenderer,
                            tickOptions: {formatString: '%b'},
                            label: "Month",
                        },
                        yaxis: {
                            label: "Searches",
                        }
                    }
                });
            });
    }
}

function updateYearsByYear() {
    if ($("#years_byyear").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showAllYearsByYear",
            cache: false
        }).done(function (html) {
                $("#years_byyear").html(html + '<button type="button" onclick="updateYearsByYear()">Update</button>');
            });
    }
}

function updateYearsByYearChart() {
    if ($("#years_byyear_chart").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=getAllYearsByYearData",
            cache: false
        }).done(function (html) {
                $.jqplot('years_byyear_chart', [eval(html)], {
                    axesDefaults: {
                        labelRenderer: $.jqplot.CanvasAxisLabelRenderer
                    },
                    title: "History (Searches vs. Year)",
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.DateAxisRenderer,
                            tickOptions: {formatString: '%Y'},
                            label: "Year",
                        },
                        yaxis: {
                            label: "Searches",
                        }
                    }
                });
            });
    }
}

function updateMostCommonQueries() {
    if ($("#most_common_queries").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showMostCommonSearchQueries&numToShow=50",
            cache: false
        }).done(function (html) {
                $("#most_common_queries").html(html + '<button type="button" onclick="updateMostCommonQueries()">Update</button>');
            });
    }
}

function updateRecentSearches() {
    if ($("#recent_searches").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showRecentSearches&numToShow=50",
            cache: false
        }).done(function (html) {
                $("#recent_searches").html(html + '<button type="button" onclick="updateRecentSearches()">Update</button>');
            });
    }
}

function updateTopSearchers() {
    if ($("#top_searchers").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showTopSearchers&numToShow=50",
            cache: false
        }).done(function (html) {
                $("#top_searchers").html(html + '<button type="button" onclick="updateTopSearchers()">Update</button>');
            });
    }
}

function updateSearchesByDateRange(begin, end) {
    if ($("#recent_searches").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showSearchesByDateRange&begin=" + encodeURIComponent(begin) + "&end=" + encodeURIComponent(end),
            cache: false
        }).done(function (html) {
                $("#recent_searches").html(html + '<button type="button" onclick="updateSearchesByDateRange(\'' + begin + '\', \'' + end + '\')">Update</button>');
            });
    }
}

function updateSearchBySid(sid) {
    if ($("#recent_searches").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showSearchBySid&sid=" + encodeURIComponent(sid),
            cache: false
        }).done(function (html) {
                $("#recent_searches").html(html + '<button type="button" onclick="updateSearchBySid(\'' + sid + '\')">Update</button>');
            });
    }
}

function updateSearchesByIP(ip) {
    if ($("#searches_ip").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showSearchesByIP&ip=" + encodeURIComponent(ip),
            cache: false
        }).done(function (html) {
                $("#searches_ip").html(html + '<button type="button" onclick="updateSearchesByIP(\'' + ip + '\')">Update</button>');
            });
    }
}

function updateTopArtifacts() {
    if ($("#top_art").length) {
        $.ajax({
            url: "ajaxAnalytics.php?request=showTopArtifacts&numToShow=50",
            cache: false
        }).done(function (html) {
                $("#top_art").html(html + '<button type="button" onclick="updateTopArtifacts()">Update</button>');
            });
    }
}

//Calls each of the standard update functions with the current date
function updateAll() {
    var d = new Date();
    updateStats();
    updateAvgs();
    updateDay(d.getFullYear(), d.getMonth() + 1, d.getDate());
    updateDayChart(d.getFullYear(), d.getMonth() + 1, d.getDate());
    updateWeek()
    updateWeekChart();
    updateBrowsers();
    updateOSs();
    updateDBsSearched();
}

var intervalTimeout;
var intervalCountdown;
var timeLeft = 60;
var realTimeEnabled = false;

/**
 * Handles real time updating. When toggled on, a 60 second countdown is started, and a one second countdown is started
 * that updates the countdown visible to the user. When the 60 second countdown finishes, the stats are all updated.
 * This allows the user to keep the page running in the background and it will stay up to date without refreshing.
 */
function toggleRealTimeUpdates() {
    if (realTimeEnabled) {
        realTimeEnabled = false;
        clearInterval(intervalTimeout);
        clearInterval(intervalCountdown);
        timeLeft = 60;
        $("#realtime").html('Real Time Updating: <span class="red">Disabled</span>');
    } else {
        realTimeEnabled = true;
        intervalTimeout = setInterval(function () {
            updateAll();
            timeLeft = 60;
        }, 60000);
        intervalCountdown = setInterval(function () {
            timeLeft--;
            $("#realtime").html('Real Time Updating: <span class="green">Enabled</span> [' + timeLeft + ']');
        }, 1000)
        $("#realtime").html('Real Time Updating: <span class="green">Enabled</span>');
    }
}

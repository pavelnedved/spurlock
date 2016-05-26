<?php

require_once("analyticalModel.class.php");

/**
 * Class analyticalView
 *
 * Contains functions that interface with the analyticalModel class to provide data in HTML format
 *
 * @package CollectionsSearch
 * @subpackage Analytics
 * @author Michael Robinson
 */
class analyticalView
{

    private $analyticalModel = null;

    public function __construct()
    {
        $this->analyticalModel = new analyticalModel();
    }

    /**
     * Outputs the click-through rate in HTML format
     */
    public function showStats()
    {
        echo "<p>
				Click-Through Rate: " . number_format($this->analyticalModel->getClickThroughRate(), 3) . " [% of searches that resulted in a details view]<br>
		
			</p>";
    }

    /**
     * Outputs the average execution time, average number of results, and average query length in HTML formats
     */
    public function showAverages()
    {
        echo "<p>
				Average execution time: " . $this->analyticalModel->getAvgExecTime() . "s<br>
				Average number of results: " . $this->analyticalModel->getAvgNumResults() . "<br>
				Average query length: " . $this->analyticalModel->getAvgQueryLength() . " characters<br>
			</p>";
    }

    /**
     * Outputs an HTML table displaying the $numToGet most common search queries
     *
     * @param $numToGet The number of queries to display
     */
    public function showMostCommonSearchQueries($numToGet)
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>Term(s)</th>
					<th>Times Searched</th>
					<th>Search</th>
				</tr>';
        $resultSet = $this->analyticalModel->getMostCommonSearchQueries($numToGet);
        foreach ($resultSet as $row) {
            echo '<tr>';
            echo '<td>' . $row['searchq'] . '</td>';
            echo '<td>' . $row['num'] . '</td>';
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setQuery($row['searchq']);
            echo '<td><a href="' . $searchRequest->getURL() . '">View Search</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Outputs an HTML table displaying the $numToGet most recent queries
     *
     * @param $numToGet The number of queries to display
     */
    public function showRecentSearches($numToGet)
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>Time</th>
					<th>IP</th>
					<th>Query</th>
					<th># Results</th>
					<th>Execution Time (s)</th>
					<th>DB Searched</th>
					<th>User Agent</th>
					<th>Search</th>
				</tr>';
        $resultSet = $this->analyticalModel->getRecentSearches($numToGet);
        foreach ($resultSet as $row) {
            echo '<tr>';
            echo '<td>' . $row['timestamp'] . '</td>';
            echo '<td>' . $row['usr_ip'] . '</td>';
            echo '<td>' . $row['searchq'] . '</td>';
            echo '<td>' . $row['num_results'] . '</td>';
            echo '<td>' . $row['exec_time'] . '</td>';
            echo '<td>' . $row['db_searched'] . '</td>';
            echo '<td>' . $row['usr_agent'] . '</td>';
            $searchRequest = unserialize($row['ser_searchq']);
            echo '<td><a href="' . $searchRequest->getURL() . '">View Search</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Outputs an HTML table displaying all searches ever recorded
     */
    public function showAllSearches()
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>Time</th>
					<th>IP</th>
					<th>Query</th>
					<th># Results</th>
					<th>Execution Time (s)</th>
					<th>DB Searched</th>
					<th>User Agent</th>
				</tr>';
        $resultSet = $this->analyticalModel->getAllSearches();
        foreach ($resultSet as $row) {
            echo '<tr>';
            echo '<td>' . $row['timestamp'] . '</td>';
            echo '<td>' . $row['usr_ip'] . '</td>';
            echo '<td>' . $row['searchq'] . '</td>';
            echo '<td>' . $row['num_results'] . '</td>';
            echo '<td>' . $row['exec_time'] . '</td>';
            echo '<td>' . $row['db_searched'] . '</td>';
            echo '<td>' . $row['usr_agent'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Outputs an HTML table displaying the $numToGet top searchers
     *
     * @param $numToGet The number of searchers to display
     */
    public function showTopSearchers($numToGet)
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>IP</th>
					<th>Searches Performed</th>
				</tr>';
        $resultSet = $this->analyticalModel->getTopSearchers($numToGet);
        foreach ($resultSet as $row) {
            echo '<tr>';
            echo '<td><a href="#" onclick="updateSearchesByIP(\'' . $row['usr_ip'] . '\')">' . $row['usr_ip'] . '</a></td>';
            echo '<td>' . $row['num'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Outputs an HTML table displaying the searches performed between $begin and $end
     *
     * @param $begin The beginning of the date range (non-inclusive)
     * @param $end The end of the date range (non-inclusive)
     */
    public function showSearchesByDateRange($begin, $end)
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>Time</th>
					<th>IP</th>
					<th>Query</th>
					<th># Results</th>
					<th>Execution Time (s)</th>
					<th>DB Searched</th>
					<th>User Agent</th>
					<th>Search</th>
				</tr>';
        $resultSet = $this->analyticalModel->getSearchesByDateRange($begin, $end);
        if (count($resultSet) < 1) {
            echo '<tr><td>No searches found</td></tr>';
        }
        foreach ($resultSet as $row) {
            echo '<tr>';
            echo '<td>' . $row['timestamp'] . '</td>';
            echo '<td>' . $row['usr_ip'] . '</td>';
            echo '<td>' . $row['searchq'] . '</td>';
            echo '<td>' . $row['num_results'] . '</td>';
            echo '<td>' . $row['exec_time'] . '</td>';
            echo '<td>' . $row['db_searched'] . '</td>';
            echo '<td style="max-width:200px;font-size:0.6em;">' . $row['usr_agent'] . '</td>';
            $searchRequest = unserialize($row['ser_searchq']);
            echo '<td><a href="' . $searchRequest->getURL() . '">View Search</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Outputs an HTML table displaying a specific search
     *
     * @param $sid The search ID of the search to display
     */
    public function showSearchBySid($sid)
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>Time</th>
					<th>IP</th>
					<th>Query</th>
					<th># Results</th>
					<th>Execution Time (s)</th>
					<th>DB Searched</th>
					<th>User Agent</th>
					<th>Search</th>
				</tr>';
        $resultSet = $this->analyticalModel->getSearchBySid($sid);
        if (count($resultSet) < 1) {
            echo '<tr><td>No searches found</td></tr>';
        }
        foreach ($resultSet as $row) {
            echo '<tr>';
            echo '<td>' . $row['timestamp'] . '</td>';
            echo '<td>' . $row['usr_ip'] . '</td>';
            echo '<td>' . $row['searchq'] . '</td>';
            echo '<td>' . $row['num_results'] . '</td>';
            echo '<td>' . $row['exec_time'] . '</td>';
            echo '<td>' . $row['db_searched'] . '</td>';
            echo '<td style="max-width:200px;font-size:0.6em;">' . $row['usr_agent'] . '</td>';
            $searchRequest = unserialize($row['ser_searchq']);
            echo '<td><a href="' . $searchRequest->getURL() . '">View Search</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Outputs an HTML table displaying all searches performed by $ip
     *
     * @param $ip The IP of the searcher
     */
    public function showSearchesByIP($ip)
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>Time</th>
					<th>IP</th>
					<th>Query</th>
					<th># Results</th>
					<th>Execution Time (s)</th>
					<th>DB Searched</th>
					<th>User Agent</th>
					<th>Search</th>
				</tr>';
        $resultSet = $this->analyticalModel->getSearchesByIP($ip);
        foreach ($resultSet as $row) {
            echo '<tr>';
            echo '<td>' . $row['timestamp'] . '</td>';
            echo '<td>' . $row['usr_ip'] . '</td>';
            echo '<td>' . $row['searchq'] . '</td>';
            echo '<td>' . $row['num_results'] . '</td>';
            echo '<td>' . $row['exec_time'] . '</td>';
            echo '<td>' . $row['db_searched'] . '</td>';
            echo '<td style="max-width:200px;font-size:0.6em;">' . $row['usr_agent'] . '</td>';
            $searchRequest = unserialize($row['ser_searchq']);
            echo '<td><a href="' . $searchRequest->getURL() . '">View Search</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Outputs an HTML table displaying the top $numToGet artifacts (by view count)
     *
     * @param $numToGet The number of artifacts to display
     */
    public function showTopArtifacts($numToGet)
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>Acc. Num</th>
					<th>Name</th>
					<th>Total Views</th>
					<th>Views from Random</th>
					<th>Views from Search Results</th>
					<th>Views from Details</th>
					<th>Direct Link Views</th>
				</tr>';
        $resultSet = $this->analyticalModel->getTopArtifacts($numToGet);
        foreach ($resultSet as $row) {
            echo '<tr>';
            echo '<td><a href="../details.php?a=' . $row['accession number'] . '">' . $row['accession number'] . '</a></td>';
            echo '<td>' . $row['name'] . '</td>';
            echo '<td>' . $row['view_cnt'] . '</td>';
            echo '<td>' . $row['view_rnd_cnt'] . '</td>';
            echo '<td>' . $row['view_results_cnt'] . '</td>';
            echo '<td>' . $row['view_details_cnt'] . '</td>';
            echo '<td>' . ($row['view_cnt'] - $row['view_rnd_cnt'] - $row['view_results_cnt'] - $row['view_details_cnt']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Outputs an HTML table displaying the searches performed per hour in a specified day
     *
     * @param $year The specified year
     * @param $month The specified month
     * @param $day The specified day
     */
    public function showDay($year, $month, $day)
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>Hour</th>
					<th># of searches</th>
				</tr>';
        $resultSet = $this->analyticalModel->getDayGroupedByHour($year, $month, $day);
        if (count($resultSet) < 1) {
            echo '<tr><td colspan="2">No Data</td></tr>';
        }
        $total = 0;
        foreach ($resultSet as $row) {
            $total += $row['cnt'];
            echo '<tr>';
            echo '<td>' . $this->convertTo12HrClockS($row['hour']) . '</td>';
            echo '<td>' . $row['cnt'] . '</td>';
            echo '</tr>';
        }
        echo '<tr><td><i>Total</i></td><td>' . $total . '</td></tr>';
        echo '</table>';
    }

    /**
     * Outputs an JSON encoded array containing the searches performed per hour in a specified day
     *
     * @param $year The specified year
     * @param $month The specified month
     * @param $day The specified day
     */
    public function getDayData($year, $month, $day)
    {
        $resultSet = $this->analyticalModel->getDayGroupedByHour($year, $month, $day);
        $dataSet = array();
        $filled = array(0 => false, 1 => false, 2 => false, 3 => false, 4 => false, 5 => false, 6 => false, 7 => false, 8 => false, 9 => false, 10 => false, 11 => false, 12 => false, 13 => false, 14 => false, 15 => false, 16 => false, 17 => false, 18 => false, 19 => false, 20 => false, 21 => false, 22 => false, 23 => false);
        foreach ($resultSet as $row) {
            $dataPoint = array(0 => $row['hour'], 1 => $row['cnt']);
            $filled[$row['hour']] = true;
            $dataSet[] = $dataPoint;
        }
        $this->fillOutData($dataSet, $filled);
        $temp = array();
        foreach ($dataSet as $point) {
            $temp[] = "['" . $this->convertTo12HrClock($point[0]) . "'," . $point[1] . "]";
        }
        echo '[' . implode(',', $temp) . ']';
    }

    /**
     * Outputs an HTML table displaying the searches performed per day in the past week
     */
    public function showPastWeek()
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>Day</th>
					<th># of searches</th>
				</tr>';
        $resultSet = $this->analyticalModel->getPastWeekGroupedByDay();
        if (count($resultSet) < 1) {
            echo '<tr><td colspan="2">No Data</td></tr>';
        }
        $total = 0;
        foreach ($resultSet as $row) {
            $total += $row['cnt'];
            echo '<tr>';
            echo '<td>' . $row['day'] . '</td>';
            echo '<td>' . $row['cnt'] . '</td>';
            echo '</tr>';
        }
        echo '<tr><td><i>Total</i></td><td>' . $total . '</td></tr>';
        echo '</table>';
    }

    /**
     * Outputs a JSON encoded array containing the searches performed per day in the past week
     */
    public function getPastWeekData()
    {
        $resultSet = $this->analyticalModel->getPastWeekGroupedByDay();
        $dataSet = array();
        $filled = array();
        for ($i = 7; $i >= 0; $i--) {
            $date = date("Y-m-d", strtotime("-$i day"));
            $filled[$date] = false;
        }
        foreach ($resultSet as $row) {
            $dataPoint = array(0 => $row['month'] . "/" . $row['day'] . "/" . $row['year'], 1 => $row['cnt']);
            $dataSet[] = $dataPoint;
            $date = date("Y-m-d", strtotime($row['month'] . "/" . $row['day'] . "/" . $row['year']));
            $filled[$date] = true;
        }
        $this->fillOutData($dataSet, $filled);
        $temp = array();
        foreach ($dataSet as $point) {
            $date = new DateTime($point[0]);
            $temp[] = "['" . $date->format('Y-m-d') . "'," . $point[1] . "]";
        }
        echo '[' . implode(',', $temp) . ']';
    }

    /**
     * Outputs an HTML table displaying the searches performed per day in the specified month
     *
     * @param $year The specified year
     * @param $month The specified month
     */
    public function showMonth($year, $month)
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>Day</th>
					<th># of searches</th>
				</tr>';
        $resultSet = $this->analyticalModel->getMonthGroupedByDay($year, $month);
        $total = 0;
        foreach ($resultSet as $row) {
            $total += $row['cnt'];
            echo '<tr>';
            echo '<td>' . $row['day'] . '</td>';
            echo '<td>' . $row['cnt'] . '</td>';
            echo '</tr>';
        }
        echo '<tr><td><i>Total</i></td><td>' . $total . '</td></tr>';
        echo '</table>';
    }

    /**
     * Outputs a JSON encoded array containing the searches performed per day in the specified month
     *
     * @param $year The specified year
     * @param $month The specified month
     */
    public function getMonthData($year, $month)
    {
        $resultSet = $this->analyticalModel->getMonthGroupedByDay($year, $month);
        $dataSet = array();
        $filled = array();
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for ($i = 1; $i <= $totalDays; $i++) {
            $filled[$i] = false;
        }
        foreach ($resultSet as $row) {
            $dataPoint = array(0 => $row['day'], 1 => $row['cnt']);
            $filled[$row['day']] = true;
            $dataSet[] = $dataPoint;
        }
        $this->fillOutData($dataSet, $filled);
        $temp = array();
        foreach ($dataSet as $point) {
            $date = new DateTime($month . '/' . $point[0] . '/' . $year);
            $temp[] = "['" . $date->format("Y-m-d") . "'," . $point[1] . "]";
        }
        echo '[' . implode(',', $temp) . ']';
    }

    /**
     * Outputs an HTML table displaying the searches performed per month in the specified year
     *
     * @param $year The specified year
     */
    public function showYear($year)
    {
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
        echo '<table class="sortable" border=1>
				<tr>
					<th>Month</th>
					<th># of searches</th>
				</tr>';
        $resultSet = $this->analyticalModel->getYearGroupedByMonth($year);
        $total = 0;
        foreach ($resultSet as $row) {
            $total += $row['cnt'];
            echo '<tr>';
            echo '<td>' . $monthOfYear[$row['month']] . '</td>';
            echo '<td>' . $row['cnt'] . '</td>';
            echo '</tr>';
        }
        echo '<tr><td><i>Total</i></td><td>' . $total . '</td></tr>';
        echo '</table>';
    }

    /**
     * Outputs a JSON encoded array containing the searches performed per month in the specified year
     *
     * @param $year The specified year
     */
    public function getYearData($year)
    {
        $resultSet = $this->analyticalModel->getYearGroupedByMonth($year);
        $dataSet = array();
        $filled = array();
        for ($i = 1; $i <= 12; $i++) {
            $filled[$i] = false;
        }
        foreach ($resultSet as $row) {
            $dataPoint = array(0 => $row['month'], 1 => $row['cnt']);
            $filled[$row['month']] = true;
            $dataSet[] = $dataPoint;
        }
        $this->fillOutData($dataSet, $filled);
        $temp = array();
        foreach ($dataSet as $point) {
            $date = new DateTime($point[0] . '/1/' . $year);
            $temp[] = "['" . $date->format("Y-m-d") . "'," . $point[1] . "]";
        }
        echo '[' . implode(',', $temp) . ']';
    }

    /**
     * Outputs a JSON encoded array containing searches performed per month for all recorded years combined
     */
    public function getAllYearsByMonthData()
    {
        $resultSet = $this->analyticalModel->getAllYearsGroupedByMonth();
        $dataSet = array();
        $filled = array();
        for ($i = 1; $i <= 12; $i++) {
            $filled[$i] = false;
        }
        foreach ($resultSet as $row) {
            $dataPoint = array(0 => $row['month'], 1 => $row['cnt']);
            $filled[$row['month']] = true;
            $dataSet[] = $dataPoint;
        }
        $this->fillOutData($dataSet, $filled);
        $temp = array();
        foreach ($dataSet as $point) {
            $date = new DateTime($point[0] . '/1/1999');
            $temp[] = "['" . $date->format("Y-m-d") . "'," . $point[1] . "]";
        }
        echo '[' . implode(',', $temp) . ']';
    }

    /**
     * Outputs an HTML table displaying searches performed per month for all recorded years combined
     */
    public function showAllYearsByMonth()
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>Month</th>
					<th># of searches</th>
				</tr>';
        $resultSet = $this->analyticalModel->getAllYearsGroupedByMonth();
        $total = 0;
        foreach ($resultSet as $row) {
            $total += $row['cnt'];
            echo '<tr>';
            echo '<td>' . $row['month'] . '</td>';
            echo '<td>' . $row['cnt'] . '</td>';
            echo '</tr>';
        }
        echo '<tr><td><i>Total</i></td><td>' . $total . '</td></tr>';
        echo '</table>';
    }

    /**
     * Outputs a JSON encoded array containing searches performed per year for all recorded years
     */
    public function getAllYearsByYearData()
    {
        $resultSet = $this->analyticalModel->getAllYearsGroupedByYear();
        $dataSet = array();
        $filled = array();
        for ($i = date("Y") - 10; $i <= date("Y"); $i++) {
            $filled[$i] = false;
        }
        foreach ($resultSet as $row) {
            $dataPoint = array(0 => $row['year'], 1 => $row['cnt']);
            $filled[$row['year']] = true;
            $dataSet[] = $dataPoint;
        }
        $this->fillOutData($dataSet, $filled);
        $temp = array();
        foreach ($dataSet as $point) {
            $date = new DateTime('1/1/' . $point[0]);
            $temp[] = "['" . $date->format("Y-m-d") . "'," . $point[1] . "]";
        }
        echo '[' . implode(',', $temp) . ']';
    }

    /**
     * Outputs an HTML table displaying searches performed per year for all recorded years
     */
    public function showAllYearsByYear()
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>Year</th>
					<th># of searches</th>
				</tr>';
        $resultSet = $this->analyticalModel->getAllYearsGroupedByYear();
        $total = 0;
        foreach ($resultSet as $row) {
            $total += $row['cnt'];
            echo '<tr>';
            echo '<td>' . $row['year'] . '</td>';
            echo '<td>' . $row['cnt'] . '</td>';
            echo '</tr>';
        }
        echo '<tr><td><i>Total</i></td><td>' . $total . '</td></tr>';
        echo '</table>';
    }

    /**
     * Outputs an HTML table displaying searches performed per browser
     */
    public function showBrowsers()
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>Browser</th>
					<th># of searches</th>
					<th>%</th>
				</tr>';
        $browsers = $this->analyticalModel->getBrowsers();
        $total = 0;
        foreach ($browsers as $val) {
            $total += $val;
        }
        arsort($browsers);
        foreach ($browsers as $key => $val) {
            $per = $val / $total * 100;
            echo '<tr>';
            echo '<td>' . $key . '</td>';
            echo '<td>' . $val . '</td>';
            echo '<td>' . number_format($per, 1) . '%</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Outputs an HTML table displaying searches performed per operating system
     */
    public function showOSs()
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>OS</th>
					<th># of searches</th>
					<th>%</th>
				</tr>';
        $OSs = $this->analyticalModel->getOSs();
        $total = 0;
        foreach ($OSs as $val) {
            $total += $val;
        }
        arsort($OSs);
        foreach ($OSs as $key => $val) {
            $per = $val / $total * 100;
            echo '<tr>';
            echo '<td>' . $key . '</td>';
            echo '<td>' . $val . '</td>';
            echo '<td>' . number_format($per, 1) . '%</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Outputs an HTML table displaying searches performed per database
     */
    public function showDBsSearched()
    {
        echo '<table class="sortable" border=1>
				<tr>
					<th>Database</th>
					<th># of searches</th>
				</tr>';
        $resultSet = $this->analyticalModel->getDBsSearched();
        foreach ($resultSet as $row) {
            echo '<tr>';
            echo '<td>' . $row['db_searched'] . '</td>';
            echo '<td>' . $row['cnt'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Converts a 24-hour hour into a 12-hour date (13 => YYYY-MM-DD 1:00 PM)
     *
     * @param $hr The 24-hour hour
     * @return string The 12-hour date
     */
    private function convertTo12HrClock($hr)
    {
        $date = date("Y-m-d");
        if ($hr < 12) {
            return $date . " " . $hr . ":00AM";
        } else if ($hr == 12) {
            return $date . " " . $hr . ":00PM";
        } else {
            return $date . " " . ($hr - 12) . ":00PM";
        }
    }

    /**
     * Converts a 24-hour hour into a 12-hour hour (13 => 1:00 PM)
     *
     * @param $hr The 24-hour hour
     * @return string The 12-hour hour
     */
    private function convertTo12HrClockS($hr)
    {
        if ($hr < 12) {
            return $hr . ":00AM";
        } else if ($hr == 12) {
            return $hr . ":00PM";
        } else {
            return ($hr - 12) . ":00PM";
        }
    }

    /**
     * Fills an array with blank data (to avoid empty plot points on a resulting graph)
     *
     * @param $data The data to fill in
     * @param $filled An array, specifying with boolean, which data members to fill in
     */
    private function fillOutData(&$data, $filled)
    {
        foreach ($filled as $key => $val) {
            if (!$val) {
                $data[] = array(0 => $key, 1 => 0);
            }
        }
    }

}

?>
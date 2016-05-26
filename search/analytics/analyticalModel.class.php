<?php

require_once("../collectionsBackend.class.php");

/**
 * Class analyticalModel
 *
 * Contains functions that retrieve and represent analytical data.
 *
 * @package CollectionsSearch
 * @subpackage Analytics
 * @author Michael Robinson
 */
class analyticalModel
{

    const SQL_HOST = collectionsBackend::SQL_HOST;
    const SQL_USER = collectionsBackend::SQL_USER;
    const SQL_PASS = collectionsBackend::SQL_PASS;
    const SQL_DB = "search";

    private $allSearches = null;
    private $DBH = null;

    public function __construct()
    {
        //Create the connection
        $this->DBH = new PDO("mysql:host=" . self::SQL_HOST . ";dbname=" . self::SQL_DB, self::SQL_USER, self::SQL_PASS);
        //Set the handler to always throw exceptions on errors (best practice)
        $this->DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Gets the click-through rate of all recorded searches.
     *
     * @return float The click-through rate
     */
    public function getClickThroughRate()
    {
        $results = array();
        $sqlQuery = "SELECT COUNT(DISTINCT sid) as cnt FROM searchassignments";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[0] = $row;
        }
        $numUniqueClickThroughs = $results[0]['cnt'];
        $sqlQuery = "SELECT COUNT(sid) as cnt FROM searches";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[0] = $row;
        }
        $numUniqueSearches = $results[0]['cnt'];
        return ($numUniqueClickThroughs / $numUniqueSearches * 100);
    }

    /**
     * Gets all recorded searches
     *
     * @return array All searches
     */
    public function getAllSearches()
    {
        $results = array();
        $sqlQuery = "SELECT searchtracking.*, searches.searchq FROM searchtracking
						LEFT JOIN searches ON searchtracking.sid = searches.sid";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        $this->allSearches = $results;
        return $results;
    }

    /**
     * Gets the $numToGet most common search queries.
     *
     * @param $numToGet The number of results to get
     * @return array The $numToGet most common search queries
     */
    public function getMostCommonSearchQueries($numToGet)
    {
        $results = array();
        $sqlQuery = "SELECT searchq, COUNT(1) as num FROM searches
						GROUP BY searchq
						HAVING searchq <> ''
						ORDER BY num DESC
						LIMIT 0," . $numToGet;
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Gets the $numToGet most recent searches.
     *
     * @param $numToGet THe number of results to get
     * @return array The $numToGet most recent search queries
     */
    public function getRecentSearches($numToGet)
    {
        $results = array();
        $sqlQuery = "SELECT searchtracking.*, searches.* FROM searchtracking
						LEFT JOIN searches ON searchtracking.sid = searches.sid
						ORDER BY searchtracking.timestamp DESC
						LIMIT 0," . $numToGet;
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Gets searches that were performed between $begin and $end
     *
     * @param $begin The beginning of the date range (non-inclusive)
     * @param $end The end of the date range (non-inclusive)
     * @return array The searches performed between $begin and $end
     */
    public function getSearchesByDateRange($begin, $end)
    {
        $results = array();
        $sqlQuery = "SELECT searchtracking.*, searches.* FROM searchtracking
						LEFT JOIN searches ON searchtracking.sid = searches.sid
						WHERE searchtracking.timestamp >= '$begin' AND searchtracking.timestamp < '$end'
						ORDER BY searchtracking.timestamp ASC";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Gets a specific search by its $sid (Search ID)
     *
     * @param $sid The search ID
     * @return array The search with ID $sid
     */
    public function getSearchBySid($sid)
    {
        $results = array();
        $sqlQuery = "SELECT searchtracking.*, searches.* FROM searchtracking
						LEFT JOIN searches ON searchtracking.sid = searches.sid
						WHERE searchtracking.id = '$sid'";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Gets the $numToGet top searchers, by number of searches performed
     *
     * @param $numToGet The number of results to get
     * @return array The top $numToGet searches
     */
    public function getTopSearchers($numToGet)
    {
        $results = array();
        $sqlQuery = "SELECT *, COUNT(1) as num FROM searchtracking
						GROUP BY usr_ip
						ORDER BY num DESC
						LIMIT 0," . $numToGet;
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }


    /**
     * Gets all searches performed by $ip
     *
     * @param $ip The IP address of the searcher
     * @return array All searches performed by $ip
     */
    public function getSearchesByIP($ip)
    {
        $results = array();
        $sqlQuery = "SELECT searchtracking.*, searches.* FROM searchtracking
						LEFT JOIN searches ON searchtracking.sid = searches.sid
						WHERE usr_ip = '" . $ip . "'
						ORDER BY searchtracking.timestamp DESC";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Gets the $numToGet top artifacts, by view count
     *
     * @param $numToGet The number of results to get
     * @return array The top $numToGet artifacts
     */
    public function getTopArtifacts($numToGet)
    {
        $results = array();
        $sqlQuery = "SELECT * FROM artifacts
						ORDER BY view_cnt DESC
						LIMIT 0," . $numToGet;
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Gets the number of searches performed per hour in a specified day
     *
     * @param $year The specified year
     * @param $month The specified month
     * @param $day The specified day
     * @return array Searches per hour for each hour in the specified day
     */
    public function getDayGroupedByHour($year, $month, $day)
    {
        //Get day of searches grouped by hour of search (for search/hour)
        $results = array();
        $sqlQuery = "SELECT HOUR(timestamp) as hour, COUNT(id) as cnt FROM searchtracking WHERE timestamp >= STR_TO_DATE('$year-$month-$day-00:00:00','%Y-%c-%e-%T') AND timestamp <= STR_TO_DATE('$year-$month-$day-23:59:59','%Y-%c-%e-%T') GROUP BY HOUR(timestamp) ORDER BY HOUR(timestamp)";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Gets the number of searches performed per day in the past week
     *
     * @return array Searches per day for each day in the past week
     */
    public function getPastWeekGroupedByDay()
    {
        //Get past week of searches grouped by day of search (for search/day)
        $results = array();
        $sqlQuery = "SELECT DAY(timestamp) as day, COUNT(id) as cnt, MONTH(timestamp) as month, YEAR(timestamp) as year FROM searchtracking WHERE timestamp >= DATE_SUB(NOW(),INTERVAL 1 WEEK) GROUP BY DAY(timestamp) ORDER BY DAY(timestamp)";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Gets the number of searches performed per day in a specified month
     *
     * @param $year The specified year
     * @param $month The specified month
     * @return array Searches per day for each day in the specified month
     */
    public function getMonthGroupedByDay($year, $month)
    {
        //Get month of searches grouped by day of search (for search/day)
        $results = array();
        $lastDay = date('t', strtotime($month + '/' + '1/' + $year));
        $sqlQuery = "SELECT DAY(timestamp) as day, COUNT(id) as cnt FROM searchtracking WHERE timestamp >= STR_TO_DATE('$year-$month-1-00:00:00','%Y-%c-%e-%T') AND timestamp <= STR_TO_DATE('$year-$month-$lastDay-23:59:59','%Y-%c-%e-%T') GROUP BY DAY(timestamp) ORDER BY DAY(timestamp)";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Gets the number of searches performed per month in the specified year
     *
     * @param $year The specified year
     * @return array Searches per month for each month in the specified year
     */
    public function getYearGroupedByMonth($year)
    {
        //Get year of searches grouped by month of search (for search/month)
        $results = array();
        $sqlQuery = "SELECT MONTH(timestamp) as month, COUNT(id) as cnt FROM searchtracking WHERE timestamp >= MAKEDATE($year, 1) AND timestamp <= MAKEDATE($year, 365) GROUP BY MONTH(timestamp) ORDER BY MONTH(timestamp)";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Gets the total number of searches performed per month throughout all recorded searches
     *
     * @return array Total Searches per month for each month (Jan-Dec)
     */
    public function getAllYearsGroupedByMonth()
    {
        //Get all time searches grouped by month of search (for search/month)
        $results = array();
        $sqlQuery = "SELECT MONTH(timestamp) as month, COUNT(id) as cnt FROM searchtracking GROUP BY MONTH(timestamp) ORDER BY MONTH(timestamp)";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Gets the total number of searches performed per year throughout all recorded searches
     *
     * @return array Total searches per year for each recorded year
     */
    public function getAllYearsGroupedByYear()
    {
        //Get all time searches grouped by year of search (for search/year)
        $results = array();
        $sqlQuery = "SELECT YEAR(timestamp) as year, COUNT(id) as cnt FROM searchtracking GROUP BY YEAR(timestamp) ORDER BY YEAR(timestamp)";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Gets the average execution time of all recorded searches
     *
     * @return float The average execution time
     */
    public function getAvgExecTime()
    {
        $sqlQuery = "SELECT AVG(exec_time) as avgexec FROM searchtracking WHERE num_results > 0";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $row = $STH->fetch();
        return $row['avgexec'];
    }

    /**
     * Gets the average number of results of all recorded searches
     *
     * @return float The average number of results
     */
    public function getAvgNumResults()
    {
        $sqlQuery = "SELECT AVG(num_results) as avgresults FROM searchtracking";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $row = $STH->fetch();
        return $row['avgresults'];
    }

    /**
     * Gets the average query length (in characters) of all recorded searches
     *
     * @return float The average query length
     */
    public function getAvgQueryLength()
    {
        $sqlQuery = "SELECT AVG(CHAR_LENGTH(searches.searchq)) as avgqlen FROM searchtracking
						LEFT JOIN searches ON searchtracking.sid = searches.sid";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $row = $STH->fetch();
        return $row['avgqlen'];
    }

    /**
     * Gets the number of searches performed per database throughout all recorded searches
     *
     * @return array The number of searches per database
     */
    public function getDBsSearched()
    {
        $results = array();
        $sqlQuery = "SELECT db_searched, COUNT(id) as cnt FROM searchtracking GROUP BY db_searched";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Gets the number of searches performed per browser throughout all recorded searches
     *
     * @return array The number of searches per browser
     */
    public function getBrowsers()
    {
        if ($this->allSearches == null) {
            $this->getAllSearches();
        }
        $browsers = array();
        if ($this->allSearches) {
            //Return an array of browser name -> # of searches performed by it
            foreach ($this->allSearches as $search) {
                $browser = $this->getBrowser($search['usr_agent']);
                if (array_key_exists($browser, $browsers)) {
                    $browsers[$browser]++;
                } else {
                    $browsers[$browser] = 1;
                }
            }
        }
        return $browsers;
    }

    /**
     * Gets the number of searches performed per operating system throughout all recorded searches
     *
     * @return array The number of searches per operating system
     */
    public function getOSs()
    {
        if ($this->allSearches == null) {
            $this->getAllSearches();
        }
        $OSs = array();
        if ($this->allSearches) {
            foreach ($this->allSearches as $search) {
                $os = $this->getOS($search['usr_agent']);
                if (array_key_exists($os, $OSs)) {
                    $OSs[$os]++;
                } else {
                    $OSs[$os] = 1;
                }
            }
        }
        return $OSs;
    }

    /**
     * Gets the operating system name from a user agent string (reported by browsers)
     * Adapted from @link http://www.kingofdevelopers.com/php-classes/extract-os-name.php
     *
     * @param $agentString The user agent string
     * @return string The operating system name, or "Unknown"
     */
    private function getOS($agentString)
    {
        $visitor_user_agent = $agentString;
        //Create list of operating systems with operating system name as array key
        $oses = array(
            'Mac OS X(Apple)' => '(iPhone)|(iPad)|(iPod)|(MAC OS X)|(OS X)',
            'Apple\'s mobile/tablet' => 'iOS',
            'BlackBerry' => 'BlackBerry',
            'Android' => 'Android',
            'Java Mobile Phones (J2ME)' => '(J2ME/MIDP)|(J2ME)',
            'Java Mobile Phones (JME)' => 'JavaME',
            'JavaFX Mobile Phones' => 'JavaFX',
            'Windows Mobile Phones' => '(WinCE)|(Windows CE)',
            'Windows 3.11' => 'Win16',
            'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
            'Windows 98' => '(Windows 98)|(Win98)',
            'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
            'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
            'Windows 2003' => '(Windows NT 5.2)',
            'Windows Vista' => '(Windows NT 6.0)|(Windows Vista)',
            'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
            'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
            'Windows ME' => 'Windows ME',
            'Open BSD' => 'OpenBSD',
            'Sun OS' => 'SunOS',
            'Linux' => '(Linux)|(X11)',
            'Macintosh' => '(Mac_PowerPC)|(Macintosh)',
            'QNX' => 'QNX',
            'BeOS' => 'BeOS',
            'OS/2' => 'OS/2',
            'ROBOT' => '(Spider)|(Bot)|(Ezooms)|(YandexBot)|(AhrefsBot)|(nuhk)|
	                    (Googlebot)|(bingbot)|(Yahoo)|(Lycos)|(Scooter)|
	                    (AltaVista)|(Gigabot)|(Googlebot-Mobile)|(Yammybot)|
	                    (Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)|
	                    (Ask Jeeves/Teoma)|(Java/1.6.0_04)'
        );
        foreach ($oses as $os => $pattern) {
            if (eregi($pattern, $visitor_user_agent)) {
                return $os;
            }
        }
        return 'Unknown';
    }

    /**
     * Gets the browser name from a user agent string (reported by browsers)
     * Adapted from @link http://www.kingofdevelopers.com/php-classes/get-browser-name-version.php
     *
     * @param $agentString The user agent string
     * @return string The browser name
     */
    private function getBrowser($agentString)
    {
        $visitor_user_agent = $agentString;
        $bname = 'Unknown';
        $version = "0.0.0";

        if (eregi('MSIE', $visitor_user_agent) && !eregi('Opera', $visitor_user_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (eregi('Firefox', $visitor_user_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (eregi('Chrome', $visitor_user_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (eregi('Safari', $visitor_user_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (eregi('Opera', $visitor_user_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (eregi('Netscape', $visitor_user_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        } elseif (eregi('Seamonkey', $visitor_user_agent)) {
            $bname = 'Seamonkey';
            $ub = "Seamonkey";
        } elseif (eregi('Konqueror', $visitor_user_agent)) {
            $bname = 'Konqueror';
            $ub = "Konqueror";
        } elseif (eregi('Navigator', $visitor_user_agent)) {
            $bname = 'Navigator';
            $ub = "Navigator";
        } elseif (eregi('Mosaic', $visitor_user_agent)) {
            $bname = 'Mosaic';
            $ub = "Mosaic";
        } elseif (eregi('Lynx', $visitor_user_agent)) {
            $bname = 'Lynx';
            $ub = "Lynx";
        } elseif (eregi('Amaya', $visitor_user_agent)) {
            $bname = 'Amaya';
            $ub = "Amaya";
        } elseif (eregi('Omniweb', $visitor_user_agent)) {
            $bname = 'Omniweb';
            $ub = "Omniweb";
        } elseif (eregi('Avant', $visitor_user_agent)) {
            $bname = 'Avant';
            $ub = "Avant";
        } elseif (eregi('Camino', $visitor_user_agent)) {
            $bname = 'Camino';
            $ub = "Camino";
        } elseif (eregi('Flock', $visitor_user_agent)) {
            $bname = 'Flock';
            $ub = "Flock";
        } elseif (eregi('AOL', $visitor_user_agent)) {
            $bname = 'AOL';
            $ub = "AOL";
        } elseif (eregi('AIR', $visitor_user_agent)) {
            $bname = 'AIR';
            $ub = "AIR";
        } elseif (eregi('Fluid', $visitor_user_agent)) {
            $bname = 'Fluid';
            $ub = "Fluid";
        } else {
            $bname = 'Unknown';
            $ub = "Unknown";
        }
        return $bname;
    }

}

?>
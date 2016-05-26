<?php

require_once("searchRequest.class.php");
require_once("collectionsBackend.class.php");

/**
 * Class searchTracker
 *
 * Implements storing of search request information for tracking and analysis
 *
 * @package CollectionsSearch
 * @subpackage Tracking
 * @author Michael Robinson 
 * @author Yang Lu
 */
class searchTracker
{

    const SQL_HOST = collectionsBackend::SQL_HOST;
    const SQL_USER = collectionsBackend::SQL_USER;
    const SQL_PASS = collectionsBackend::SQL_PASS;
    const SQL_DB = "search";

    /**
     * @var PDO
     */
    private $DBH; //Database handle

    public function __destruct()
    {
        //Destructer for this class
        $this->disconnectSQL();
    }

    /**
     * Saves a given search request to the tracking database
     * @param $searchRequest The search request
     * @param $execTime The execution time of the request
     * @param $numResults The number of results returned by the request
     * @param $dbName The database the request is from
     * @return bool|int False if an error occured, or the ID of the new tracking record
     */
    public function saveRequest($searchRequest, $execTime, $numResults, $dbName)
    {
    		return false; //Comment to turn on search tracking (Disabled 2014-12-08 JST)
        if ($searchRequest instanceof searchRequest) {
            $this->connectSQL();
            $serSearchQ = serialize($searchRequest);

            //Determine if this search request already has a record in the searches table, if it does use its SID, if not, create a new record and get its SID
            $sqlQuery = "SELECT sid FROM searches WHERE ser_searchq = ?";
            $STH = $this->DBH->prepare($sqlQuery);
            $STH->execute(array($serSearchQ));
            $STH->setFetchMode(PDO::FETCH_ASSOC);
            if ($row = $STH->fetch()) {
                //Search has been seen before, get its sid
                $sid = $row['sid'];
            } else {
                //New search never seen before, save it
                $sqlQuery = "INSERT INTO searches (searchq, ser_searchq) VALUES (?,?)";
                $STH = $this->DBH->prepare($sqlQuery);
                $STH->execute(array($searchRequest->getQuery(), $serSearchQ));
                $sid = $this->DBH->lastInsertId();
            }

            $usr_ip = $_SERVER['REMOTE_ADDR'];
            $usr_agent = $_SERVER['HTTP_USER_AGENT'];
            $sqlQuery = "INSERT INTO searchtracking (usr_ip, num_results, exec_time, db_searched, usr_agent, sid) VALUES (?,?,?,?,?,?)";

            $STH = $this->DBH->prepare($sqlQuery);
            $STH->execute(array($usr_ip, $numResults, $execTime, $dbName, $usr_agent, $sid));
            $retVal = $this->DBH->lastInsertId();
            $this->disconnectSQL();
            return $retVal;
        }
        return false;
    }

    /**
     * Updates an artifact's stats in the tracking database
     * @param $sid True if the artifact was viewed from a specific search
     * @param $rel True if the artifact was viewed from a related context (sidebars)
     * @param $rand True if the artifact was viewed from a random context (random artifact)
     * @param $artifact The artifact to update
     */
    public function updateArtifact($sid, $rel, $rand, $artifact)
    {
    		return false; //Comment to turn on search tracking (Disabled 2014-12-08 JST)
        $this->connectSQL();
        $aid = $artifact->getID();
        //Update the main view counter (any kind of view)
        $sqlQuery = "UPDATE artifacts SET view_cnt=view_cnt+1 WHERE aid = ?";
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute(array($aid));
        //Update the 'from search results' counter
        if ($sid) {
            $sqlQuery = "UPDATE artifacts SET view_results_cnt=view_results_cnt+1 WHERE aid = ?";
            $STH = $this->DBH->prepare($sqlQuery);
            $STH->execute(array($aid));
            //In this case, we also want to associate this artifact with the search that provided it
            $sqlQuery = "SELECT count(aid) as cnt FROM searchassignments WHERE aid = ? AND sid = ?";
            $STH = $this->DBH->prepare($sqlQuery);
            $STH->execute(array($aid, $sid));
            $foundCount = $STH->fetch();
            $foundCount = $foundCount[0];
            if ($foundCount < 1) {
                $sqlQuery = "INSERT INTO searchassignments (aid, sid) VALUES (?,?)";
                $STH = $this->DBH->prepare($sqlQuery);
                $STH->execute(array($aid, $sid));
            }
        }
        //Update the 'from random artifacts' counter
        if ($rand) {
            $sqlQuery = "UPDATE artifacts SET view_rnd_cnt=view_rnd_cnt+1 WHERE aid = ?";
            $STH = $this->DBH->prepare($sqlQuery);
            $STH->execute(array($aid));
        }
        //Update the 'from related artifacts' (details.php) counter
        if ($rel) {
            $sqlQuery = "UPDATE artifacts SET view_details_cnt=view_details_cnt+1 WHERE aid = ?";
            $STH = $this->DBH->prepare($sqlQuery);
            $STH->execute(array($aid));
        }
        $this->disconnectSQL();
    }

    /**
     * Creates the connection to the MySQL database
     * @return bool True if connected successfully, False otherwise
     */
    private function connectSQL()
    {
        try {
            //Create the connection
            $this->DBH = new PDO("mysql:host=" . self::SQL_HOST . ";dbname=" . self::SQL_DB, self::SQL_USER, self::SQL_PASS);
            //Set the handler to always throw exceptions on errors (best practice)
            $this->DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            //echo 'Exception: ' . $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * Disconnects from the MySQL database.
     */
    private function disconnectSQL()
    {
        $this->DBH = null;
    }
}

?>

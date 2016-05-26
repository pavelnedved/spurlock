<?php

require_once("searchRequest.class.php");
require_once("artifact.class.php");
require_once("searchAuxiliary.php");
require_once("naturalQuery.class.php");
require_once("searchTracker.class.php");
require_once("facetHandler.class.php");

date_default_timezone_set('America/Chicago');

/** 
 * Class collectionsBackend
 *
 * Our messiah. This class interfaces with almost every other class to provide an abstract interface for searching
 * and displaying search related elements.
 *
 * @package CollectionsSearch
 * @author Yang Lu
 */
class collectionsBackend
{

    // const SQL_HOST = "x";
    // const SQL_PORT = "x";
    /*const SQL_HOST = "localhost";
    const SQL_USER = "root";
    const SQL_PASS = "password";
    const SQL_DB = "spurlock";*/

    // DAVIDTECH INFO
    const SQL_HOST = "davidtech.web.engr.illinois.edu";
    const SQL_USER = "davidtec_test";
    const SQL_PASS = "1991619";
    const SQL_DB = "davidtec_spurlock";


    const RELEVANCE_ACCNUM = 5.0;
    const RELEVANCE_NAME = 1.3;
    const RELEVANCE_PUBLICDESC = 1.2;
    const RELEVANCE_PUBLISHEDDESC = 0.9;
    const RELEVANCE_VISUALDEC = 0.3;
    const RELEVANCE_PERIOD = 0.5;
    const RELEVANCE_MATERIALS = 0.5;
    const RELEVANCE_ARTIST = 0.5;
    const RELEVANCE_CREDIT = 0.5;
    const RELEVANCE_CONTINENT = 0.7;
    const RELEVANCE_COUNTRY = 0.7;
    const RELEVANCE_REGION = 0.6;
    const RELEVANCE_CITY = 0.6;
    const RELEVANCE_LOCALITY = 0.6;
    const RELEVANCE_CULTURE = 0.7;

    /**
     * The minimum percent of parent that a facet must represent to be shown by default
     */
    const FACET_LIMIT = 0.05;

    /**
     * Size of grid image viewport (in px)
     */
    const GRID_IMAGE_SIZE = 135;

    /**
     * Query's with less than this length that return no results will tell the user to search something less common
     */
    const QUERY_LENGTH_TOO_COMMON = 2;

    /**
     * @var int
     */
    private $numRecordsPerPage = 25;

    /**
     * Gallery names corresponding to gallery codes
     * @var array
     */
    private $gallery = array(
        'Africa' => 'AFR',
        'Central Core' => 'CCR',
        'North America' => 'AMN',
        'South America' => 'AMS',
        'Ancient Mediterranean' => 'MED',
        'East Asia' => 'ASE',
        'Egypt' => 'EGY',
        'Europe' => 'EUR',
        'Southeast Asia and Oceania' => 'SEO',
        'Mesopotamia' => 'MSO'
    );

    /**
     * @var searchRequest
     */
    private $currentRequest;
    /**
     * @var PDO
     */
    private $DBH;
    /**
     * @var array
     */
    private $resultSet = array();
    /**
     * @var int
     */
    private $foundSetCount = 0;
    /**
     * @var searchTracker
     */
    private $tracker = null;
    /**
     * @var facetHandler
     */
    private $facetHandler = null;
    /**
     * @var int
     */
    private $savedSearchID = 0;

    public function __construct($numRecordsPerPage = 25)
    {
        //Constructor for this class
        $this->numRecordsPerPage = $numRecordsPerPage;
        $this->tracker = new searchTracker();
        $this->facetHandler = new facetHandler($this);
    }

    public function __destruct()
    {
        //Destructor for this class
        if ($this->isConnectedSQL()) {
            $this->disconnectSQL();
        }
    }

    /**
     * Check if a connection to the SQL server can be made
     * @return bool True if a connection can be made, False otherwise
     */
    public function testConnection()
    {
        if (!$this->isConnectedSQL()) {
            if (!$this->connectSQL()) {
                return false;
            };
        }
        return true;
    }

    /**
     * Escapes any dangerous characters from a string for use in an SQL query
     * @param $str The string to escape
     * @return string The escaped string
     */
    public function escapeString($str)
    {
        if (!$this->isConnectedSQL()) {
            if (!$this->connectSQL()) {
                return "error";
            };
        }
        return substr($this->DBH->quote($str), 1, -1);
    }

    /**
     * Saves the current request into the database
     * @param $execTime The execution time of the request
     */
    public function saveRequest($execTime)
    {
        $this->savedSearchID = $this->tracker->saveRequest($this->currentRequest, $execTime, $this->foundSetCount, "Artifacts");
    }

    /**
     * Get the current request
     * @return searchRequest The request
     */
    public function getCurrentRequest()
    {
        return $this->currentRequest;
    }

    /**
     * Executes a request. A request must be either a searchRequest object or a string. A string request is handled as
     * a SQL query and the results are returned as an array. A searchRequest object is handled as a normal request and
     * the results are saved.
     * @param $request The request to execute, either a searchRequest object or a SQL query string
     * @param bool $getFacets A boolean determining whether facets are retrieved or not
     * @return array|bool Returns false on failure, or an array of results if a string request is given
     */
    public function executeRequest($request, $getFacets = true)
    {
        //Take a request and handle it, returns false on failure
        if (!$this->isConnectedSQL()) {
            if (!$this->connectSQL()) {
                return false;
            };
        }
        if (is_string($request)) {
            return $this->executeCustomSQL($request);
        }
        if (!($request instanceof searchRequest)) {
            return false;
        }
        $this->currentRequest = $request;
        if (!$request->isActive()) {
            return false;
        }
        $this->resultSet = array();
        if (!$request->isAdvanced()) {
            $this->executeSimpleRequest($request, $getFacets);
        } else {
            $this->executeAdvancedRequest($request, $getFacets);
        }
        return true;
    }

    /**
     * Executes a "simple" search request, which is a request that only uses checkboxes and fulltext. Results are
     * stored in the object for later output.
     * @param $request The request to execute
     * @param bool $getFacets A boolean determining whether facets are retrieved or not
     */
    private function executeSimpleRequest($request, $getFacets = true)
    {
        $searchQuery = $request->getQuery();
        $data = preg_split('/\s+/', $searchQuery);
        //sort ($data);
        $my_Country= "";
        $my_Continent= "";
        $my_City= "";
        $my_Region= "";
        $my_Name = "";
        $my_Culture = "";
        $my_accNum = "";
        $my_material = "";
        $my_locality = "";
        $len = sizeof($data);
        for($i = 0; $i < $len; $i++) {
        //foreach ($data as $datavalue) {
            // accession number
            $datavalue = $data[$i];
            $pattern = "/\d{4}\.\d{2}\..*/";                
            if(preg_match($pattern, $datavalue)){
                $my_accNum .= $datavalue;
                $my_accNum .= ", ";
                continue;
            }

            $sqlQuery = "SELECT city
                         FROM geocity 
                         WHERE (city='$datavalue' OR city LIKE '% $datavalue %')";
            $start = $request->getStart();
            $constraints = array();
            $STH = $this->DBH->prepare($sqlQuery);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_ASSOC);
            $citystr = $STH->fetch();
            /*$check = 0;
            echo $data[$i];
            if(($i+1) != $len && $data[$i+1] != "City"){
                $check = 1;
            }*/
            if (!empty($citystr) && $datavalue != "bank"){ //&& $check != 1){
                $my_City .= $datavalue;
                $my_City .= " ";
                //echo "city: $my_City";
                continue;
            }

            $sqlQuery = "SELECT continent
                         FROM geocontinent 
                         WHERE continent = '$datavalue' OR continent LIKE '% $datavalue %'";
            $start = $request->getStart();
            $constraints = array();
            $STH = $this->DBH->prepare($sqlQuery);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_ASSOC);
            $continentstr = $STH->fetch();
            if (!empty($continentstr)){
                $my_Continent .= $datavalue;
                $my_Continent .=", ";
                //echo "continent: $my_Continent";

                continue;
            }

            $sqlQuery = "SELECT country
                         FROM geocountry 
                         WHERE country LIKE '% $datavalue %' OR country = '$datavalue' OR country LIKE '%$datavalue %' OR country LIKE '% $datavalue%'";
            $start = $request->getStart();
            $constraints = array();
            $STH = $this->DBH->prepare($sqlQuery);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_ASSOC);
            $countrystr = $STH->fetch();
            if (!empty($countrystr) && $datavalue != "bank"){
                $my_Country .= $datavalue;
                $my_Country .=" ";
                //echo "country: $my_Country";
                continue;
            }

            $sqlQuery = "SELECT region
                         FROM georegion 
                         WHERE region LIKE '% $datavalue %' OR region = '$datavalue' OR region LIKE '% $datavalue%' OR region LIKE '%$datavalue %'";
            $start = $request->getStart();
            $constraints = array();
            $STH = $this->DBH->prepare($sqlQuery);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_ASSOC);
            $regionstr = $STH->fetch();
            if (!empty($regionstr)){
                $my_Region .= $datavalue;
                $my_Region .=", ";
                //echo "my_Region: $my_Region";

                continue;
            }

            
            $sqlQuery = "SELECT locality
                         FROM geolocality
                         WHERE locality LIKE '%$datavalue%'";
            $start = $request->getStart();
            $constraints = array();
            $STH = $this->DBH->prepare($sqlQuery);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_ASSOC);
            $localitystr = $STH->fetch();
            if (!empty($localitystr)){
                $my_locality .= $datavalue;
                $my_locality .=", ";
                //echo "locality: $my_locality";
                continue;
            }

            $sqlQuery = "SELECT culture.culture
                         FROM culture 
                         WHERE culture.culture LIKE '%$datavalue%'";
            $start = $request->getStart();
            $constraints = array();
            $STH = $this->DBH->prepare($sqlQuery);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_ASSOC);
            $culturestr = $STH->fetch();
            if (!empty($culturestr)){
                $my_Culture .= $datavalue;
                $my_Culture .=", ";
                //echo "my_culture: $my_Culture";
                continue;
            }

            $sqlQuery = "SELECT `name`
                         FROM artifacts 
                         WHERE `name` LIKE '%$datavalue%'";
            $start = $request->getStart();
            $constraints = array();
            $STH = $this->DBH->prepare($sqlQuery);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_ASSOC);
            $namestr = $STH->fetch();
            if (!empty($namestr)){
                $my_Name .= $datavalue;
                $my_Name .=" ";
                continue;
            }

            $sqlQuery = "SELECT `materials 2`
                         FROM artifacts 
                         WHERE `materials 2` LIKE '%$datavalue%'";
            $start = $request->getStart();
            $constraints = array();
            $STH = $this->DBH->prepare($sqlQuery);
            $STH->execute();
            $STH->setFetchMode(PDO::FETCH_ASSOC);
            $materialstr = $STH->fetch();
            if (!empty($materialstr)){
                $my_material .= $datavalue;
                $my_material .= ", ";
                continue;
            }

            
            // country , continent, city, region, 
            
            // haven't recognized this keyword.. just call original simple search
            //echo "calling original simple search";
            $this->executeOldSimpleRequest($request, $getFacets);
            return;
                 
        }
    //$my_Country = substr($my_Country,0,-2);
    //sort( $my_Country);
    $my_Continent = substr($my_Continent,0,-2);
    //$my_City = substr($my_City,0,-2);
    $my_Region = substr($my_Region,0,-2);
    $my_Culture = substr($my_Culture,0,-2);
    $my_accNum = substr($my_accNum, 0, -2);
    $my_material = substr($my_material, 0, -2);

    //$my_Name = substr($my_Name,0,-2);
    if(!empty($my_accNum))
            $request -> setAccessionNumber($my_accNum);
    if(!empty($my_Country))
            $request -> setCountry($my_Country);
    if(!empty($my_City))
            $request -> setCity($my_City);
    if(!empty($my_locality))
            $request -> setLocality($my_locality);
    if(empty($my_City) && empty($my_Country) && !empty($my_Continent))
            $request -> setContinent($my_Continent);
    if(empty($my_City) && empty($my_Country) && !empty($my_Region))
            $request -> setRegion($my_Region);
    if(!empty($my_Culture))
            $request -> setCulture($my_Culture);
    if(!empty($my_material))
            $request -> setMaterial($my_material);
    if(!empty($my_Name))
            $request -> setName($my_Name);
    //    $countrylist =$request-> getCountry();
    //echo "$countrylist";
    $this->executeAdvancedRequest($request,$getFacets);

    }
    
    private function executeOldSimpleRequest($request, $getFacets = true)
    {
        $searchQuery = $request->getQuery();
        $start = $request->getStart();
        $duration = $this->numRecordsPerPage;
        $constraints = array();
        $sqlQuery = "SELECT artifacts.*, geocontinent.continent, geocountry.country, georegion.region, geocity.city, geolocality.locality, culture.culture, nom1_1.category as cat1, nom1_2.classification as class1, nom1_3.subclassification as subclass1, nom2_1.category as cat2, nom2_2.classification as class2, nom2_3.subclassification as subclass2
						FROM artifacts 
						LEFT JOIN geocontinent ON artifacts.geo1 = geocontinent.gid
						LEFT JOIN geocountry ON artifacts.geo2 = geocountry.gid
						LEFT JOIN georegion ON artifacts.geo3 = georegion.gid
						LEFT JOIN geocity ON artifacts.geo4 = geocity.gid
						LEFT JOIN geolocality ON artifacts.geo5 = geolocality.gid
						LEFT JOIN nomcategory AS nom1_1 ON artifacts.nomen1_1 = nom1_1.nid
						LEFT JOIN nomclassification AS nom1_2 ON artifacts.nomen1_2 = nom1_2.nid
						LEFT JOIN nomsubclassification AS nom1_3 ON artifacts.nomen1_3 = nom1_3.nid
						LEFT JOIN nomcategory AS nom2_1 ON artifacts.nomen2_1 = nom2_1.nid
						LEFT JOIN nomclassification AS nom2_2 ON artifacts.nomen2_2 = nom2_2.nid
						LEFT JOIN nomsubclassification AS nom2_3 ON artifacts.nomen2_3 = nom2_3.nid
						LEFT JOIN culture ON artifacts.culture1 = culture.cid WHERE ";
        $sqlCountQuery = "SELECT count(1) FROM artifacts
							LEFT JOIN geocontinent ON artifacts.geo1 = geocontinent.gid
							LEFT JOIN geocountry ON artifacts.geo2 = geocountry.gid
							LEFT JOIN georegion ON artifacts.geo3 = georegion.gid
							LEFT JOIN geocity ON artifacts.geo4 = geocity.gid
							LEFT JOIN geolocality ON artifacts.geo5 = geolocality.gid
							LEFT JOIN culture ON artifacts.culture1 = culture.cid
							WHERE ";
        if ($request->getQuery() != "") {
            if (!$request->isExclusivelyNegated()) {
                $sqlQuery = "SELECT artifacts.*, (((CASE WHEN `Accession Number` LIKE :queryw THEN 1 ELSE 0 END) * " . self::RELEVANCE_ACCNUM . ")
										+ (MATCH(`Name`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_NAME . ")
										+ (MATCH(`public description`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_PUBLICDESC . ")
										+ (MATCH(`Published Description`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_PUBLISHEDDESC . ")
										+ (MATCH(`Visual Description`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_VISUALDEC . ")
										+ (MATCH(`period 1`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_PERIOD . ")
										+ (MATCH(`materials 2`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_MATERIALS . ")
										+ (MATCH(`artist`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_ARTIST . ")
										+ (MATCH(`credit line`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CREDIT . ")
										+ (MATCH(geocontinent.continent) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CONTINENT . ")
										+ (MATCH(geocountry.country) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_COUNTRY . ")
										+ (MATCH(geocity.city) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_REGION . ")
										+ (MATCH(georegion.region) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CITY . ")
										+ (MATCH(geolocality.locality) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_LOCALITY . ")
										+ (MATCH(culture.culture) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CULTURE . ")) AS relevance,
										geocontinent.continent, geocountry.country, georegion.region, geocity.city, geolocality.locality, culture.culture, nom1_1.category as cat1, nom1_2.classification as class1, nom1_3.subclassification as subclass1, nom2_1.category as cat2, nom2_2.classification as class2, nom2_3.subclassification as subclass2
							FROM artifacts 
							LEFT JOIN geocontinent ON artifacts.geo1 = geocontinent.gid
							LEFT JOIN geocountry ON artifacts.geo2 = geocountry.gid
							LEFT JOIN georegion ON artifacts.geo3 = georegion.gid
							LEFT JOIN geocity ON artifacts.geo4 = geocity.gid
							LEFT JOIN geolocality ON artifacts.geo5 = geolocality.gid
							LEFT JOIN nomcategory AS nom1_1 ON artifacts.nomen1_1 = nom1_1.nid
							LEFT JOIN nomclassification AS nom1_2 ON artifacts.nomen1_2 = nom1_2.nid
							LEFT JOIN nomsubclassification AS nom1_3 ON artifacts.nomen1_3 = nom1_3.nid
							LEFT JOIN nomcategory AS nom2_1 ON artifacts.nomen2_1 = nom2_1.nid
							LEFT JOIN nomclassification AS nom2_2 ON artifacts.nomen2_2 = nom2_2.nid
							LEFT JOIN nomsubclassification AS nom2_3 ON artifacts.nomen2_3 = nom2_3.nid
							LEFT JOIN culture ON artifacts.culture1 = culture.cid
							WHERE (MATCH(`accession number`,`name`,`period 1`,`visual description`,`materials 2`,`published description`,`artist`,`credit line`,`public description`) AGAINST (:query IN BOOLEAN MODE) 
							   OR MATCH(geocontinent.continent) AGAINST (:query IN BOOLEAN MODE)
							   OR MATCH(geocountry.country) AGAINST (:query IN BOOLEAN MODE)
							   OR MATCH(georegion.region) AGAINST (:query IN BOOLEAN MODE)
							   OR MATCH(geocity.city) AGAINST (:query IN BOOLEAN MODE)
							   OR MATCH(geolocality.locality) AGAINST (:query IN BOOLEAN MODE)
							   OR MATCH(culture.culture) AGAINST (:query IN BOOLEAN MODE))";
                $sqlCountQuery .= "(MATCH(`accession number`,`name`,`period 1`,`visual description`,`materials 2`,`published description`,`artist`,`credit line`,`public description`) AGAINST (:query IN BOOLEAN MODE)
								OR MATCH(geocontinent.continent) AGAINST (:query IN BOOLEAN MODE)
								OR MATCH(geocountry.country) AGAINST (:query IN BOOLEAN MODE)
								OR MATCH(georegion.region) AGAINST (:query IN BOOLEAN MODE)
								OR MATCH(geocity.city) AGAINST (:query IN BOOLEAN MODE)
								OR MATCH(geolocality.locality) AGAINST (:query IN BOOLEAN MODE)
								OR MATCH(culture.culture) AGAINST (:query IN BOOLEAN MODE))";
            } else {
                $sqlQuery = "SELECT artifacts.*, (((CASE WHEN `Accession Number` LIKE :queryw THEN 1 ELSE 0 END) * " . self::RELEVANCE_ACCNUM . ")
										+ (NOT MATCH(`Name`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_NAME . ")
										+ (NOT MATCH(`public description`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_PUBLICDESC . ")
										+ (NOT MATCH(`Published Description`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_PUBLISHEDDESC . ")
										+ (NOT MATCH(`Visual Description`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_VISUALDEC . ")
										+ (NOT MATCH(`period 1`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_PERIOD . ")
										+ (NOT MATCH(`materials 2`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_MATERIALS . ")
										+ (NOT MATCH(`artist`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_ARTIST . ")
										+ (NOT MATCH(`credit line`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CREDIT . ")
										+ (NOT MATCH(geocontinent.continent) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CONTINENT . ")
										+ (NOT MATCH(geocountry.country) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_COUNTRY . ")
										+ (NOT MATCH(geocity.city) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_REGION . ")
										+ (NOT MATCH(georegion.region) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CITY . ")
										+ (NOT MATCH(geolocality.locality) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_LOCALITY . ")
										+ (NOT MATCH(culture.culture) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CULTURE . ")) AS relevance,
										geocontinent.continent, geocountry.country, georegion.region, geocity.city, geolocality.locality, culture.culture, nom1_1.category as cat1, nom1_2.classification as class1, nom1_3.subclassification as subclass1, nom2_1.category as cat2, nom2_2.classification as class2, nom2_3.subclassification as subclass2
							FROM artifacts 
							LEFT JOIN geocontinent ON artifacts.geo1 = geocontinent.gid
							LEFT JOIN geocountry ON artifacts.geo2 = geocountry.gid
							LEFT JOIN georegion ON artifacts.geo3 = georegion.gid
							LEFT JOIN geocity ON artifacts.geo4 = geocity.gid
							LEFT JOIN geolocality ON artifacts.geo5 = geolocality.gid
							LEFT JOIN nomcategory AS nom1_1 ON artifacts.nomen1_1 = nom1_1.nid
							LEFT JOIN nomclassification AS nom1_2 ON artifacts.nomen1_2 = nom1_2.nid
							LEFT JOIN nomsubclassification AS nom1_3 ON artifacts.nomen1_3 = nom1_3.nid
							LEFT JOIN nomcategory AS nom2_1 ON artifacts.nomen2_1 = nom2_1.nid
							LEFT JOIN nomclassification AS nom2_2 ON artifacts.nomen2_2 = nom2_2.nid
							LEFT JOIN nomsubclassification AS nom2_3 ON artifacts.nomen2_3 = nom2_3.nid
							LEFT JOIN culture ON artifacts.culture1 = culture.cid
							WHERE (NOT MATCH(`accession number`,`name`,`period 1`,`visual description`,`materials 2`,`published description`,`artist`,`credit line`,`public description`) AGAINST (:query IN BOOLEAN MODE) 
							  AND NOT MATCH(geocontinent.continent) AGAINST (:query IN BOOLEAN MODE)
							  AND NOT MATCH(geocountry.country) AGAINST (:query IN BOOLEAN MODE)
							  AND NOT MATCH(georegion.region) AGAINST (:query IN BOOLEAN MODE)
							  AND NOT MATCH(geocity.city) AGAINST (:query IN BOOLEAN MODE)
							  AND NOT MATCH(geolocality.locality) AGAINST (:query IN BOOLEAN MODE)
							  AND NOT MATCH(culture.culture) AGAINST (:query IN BOOLEAN MODE))";
                $sqlCountQuery .= "(NOT MATCH(`accession number`,`name`,`period 1`,`visual description`,`materials 2`,`published description`,`artist`,`credit line`,`public description`) AGAINST (:query IN BOOLEAN MODE)
							   AND NOT MATCH(geocontinent.continent) AGAINST (:query IN BOOLEAN MODE)
							   AND NOT MATCH(geocountry.country) AGAINST (:query IN BOOLEAN MODE)
							   AND NOT MATCH(georegion.region) AGAINST (:query IN BOOLEAN MODE)
							   AND NOT MATCH(geocity.city) AGAINST (:query IN BOOLEAN MODE)
							   AND NOT MATCH(geolocality.locality) AGAINST (:query IN BOOLEAN MODE)
							   AND NOT MATCH(culture.culture) AGAINST (:query IN BOOLEAN MODE))";
            }
        }
        if ($request->getWithImages()) {
            $constraints[] = "`Image Source` LIKE 'Images%'";
        }
        if ($request->getOnDisplay()) {
            $constraints[] = "`on_display` = 1";
            if ($request->getGallery() != "All") {
                $constraints[] = "`Spurlock Loc 2` = :gallery";
            }
        }
        if ($request->getWithHiResImages()) {
            $constraints[] = "`hiresimagecheck` = 'Yes'";
        }
        if ($request->getQuery() != "" && count($constraints) > 0) {
            $sqlQuery .= " AND ";
            $sqlCountQuery .= " AND ";
        } else if ($request->getQuery() == "" && count($constraints) < 1) {
            $sqlQuery .= ' 1 = 1 ';
            $sqlCountQuery .= ' 1 = 1 ';
        }
        $sqlQuery .= implode(" AND ", $constraints);
        $sqlCountQuery .= implode(" AND ", $constraints);
        if ($request->getQuery() != "") {
            switch ($request->getSortParameter()) {
                case "relevance":
                    $sqlQuery .= " ORDER BY relevance DESC";
                    break;
                case "popularity":
                    $sqlQuery .= " ORDER BY `view_cnt` DESC, relevance DESC";
                    break;
                case "name":
                    $sqlQuery .= " ORDER BY `name` ASC";
                    break;
                case "rand":
                    $sqlQuery .= " ORDER BY RAND()";
                    break;
                default:
                    $sqlQuery .= " ORDER BY relevance DESC";
                    break;
            }
        } else {
            switch ($request->getSortParameter()) {
                case "popularity":
                    $sqlQuery .= " ORDER BY `view_cnt` DESC";
                    break;
                case "name":
                    $sqlQuery .= " ORDER BY `name` ASC";
                    break;
                case "rand":
                    $sqlQuery .= " ORDER BY RAND()";
                    break;
                default:
                    break;
            }
        }
        $sqlQuery .= " LIMIT :start, :dur";
        $STH = $this->DBH->prepare($sqlQuery);
        if ($request->getQuery() != "") {
            if ($request->isExclusivelyNegated()) {
                $STH->bindValue(':queryw', $request->getReversedQuery() . '%', PDO::PARAM_STR);
                $STH->bindValue(':query', $request->getReversedQuery(), PDO::PARAM_STR);
            } else {
                $STH->bindValue(':queryw', $searchQuery . '%', PDO::PARAM_STR);
                $STH->bindValue(':query', $searchQuery, PDO::PARAM_STR);
            }
        }
        if ($request->getOnDisplay() && $request->getGallery() != "All") {
            $STH->bindValue(':gallery', $this->gallery[$request->getGallery()], PDO::PARAM_STR);
        }
        $STH->bindValue(':start', $start, PDO::PARAM_INT);
        $STH->bindValue(':dur', $duration, PDO::PARAM_INT);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $this->resultSet[] = new artifact($row);
        }
        $STH = $this->DBH->prepare($sqlCountQuery);
        if ($request->getQuery() != "") {
            if ($request->isExclusivelyNegated()) {
                $STH->bindValue(':query', $request->getReversedQuery(), PDO::PARAM_STR);
                echo $request->getReversedQuery();
            } else {
                $STH->bindValue(':query', $searchQuery, PDO::PARAM_STR);
            }
        }
        if ($request->getOnDisplay() && $request->getGallery() != "All") {
            $STH->bindValue(':gallery', $this->gallery[$request->getGallery()], PDO::PARAM_STR);
        }
        $STH->execute();
        $this->foundSetCount = $STH->fetch();
        $this->foundSetCount = $this->foundSetCount[0];
        if ($getFacets) {
            $this->getFacets($sqlCountQuery);
        }
    }
    
    /**
     * Executes an "advanced" search request, which is one that is more than just checkboxes and fulltext. Results are
     * stored for later output.
     * @param $request The request to execute
     * @param bool $getFacets A boolean determining whether facets are retrieved or not
     */
    private function executeAdvancedRequest($request, $getFacets = TRUE)
    {
        //Csonstruct the query (AND)
        $searchQuery = $request->getQuery();
        $start = $request->getStart();
        $duration = $this->numRecordsPerPage;
        $params = array();
        $sqlQuery = "SELECT artifacts.*, geocontinent.continent, geocountry.country, georegion.region, geocity.city, geolocality.locality, culture.culture, nom1_1.category as cat1, nom1_2.classification as class1, nom1_3.subclassification as subclass1, nom2_1.category as cat2, nom2_2.classification as class2, nom2_3.subclassification as subclass2
						FROM artifacts 
						LEFT JOIN geocontinent ON artifacts.geo1 = geocontinent.gid
						LEFT JOIN geocountry ON artifacts.geo2 = geocountry.gid
						LEFT JOIN georegion ON artifacts.geo3 = georegion.gid
						LEFT JOIN geocity ON artifacts.geo4 = geocity.gid
						LEFT JOIN geolocality ON artifacts.geo5 = geolocality.gid
						LEFT JOIN nomcategory AS nom1_1 ON artifacts.nomen1_1 = nom1_1.nid
						LEFT JOIN nomclassification AS nom1_2 ON artifacts.nomen1_2 = nom1_2.nid
						LEFT JOIN nomsubclassification AS nom1_3 ON artifacts.nomen1_3 = nom1_3.nid
						LEFT JOIN nomcategory AS nom2_1 ON artifacts.nomen2_1 = nom2_1.nid
						LEFT JOIN nomclassification AS nom2_2 ON artifacts.nomen2_2 = nom2_2.nid
						LEFT JOIN nomsubclassification AS nom2_3 ON artifacts.nomen2_3 = nom2_3.nid
						LEFT JOIN culture ON artifacts.culture1 = culture.cid WHERE ";
        $sqlCountQuery = "SELECT count(1) FROM artifacts
							LEFT JOIN geocontinent ON artifacts.geo1 = geocontinent.gid
							LEFT JOIN geocountry ON artifacts.geo2 = geocountry.gid
							LEFT JOIN georegion ON artifacts.geo3 = georegion.gid
							LEFT JOIN geocity ON artifacts.geo4 = geocity.gid
							LEFT JOIN geolocality ON artifacts.geo5 = geolocality.gid
							LEFT JOIN nomcategory AS nom1_1 ON artifacts.nomen1_1 = nom1_1.nid
							LEFT JOIN nomclassification AS nom1_2 ON artifacts.nomen1_2 = nom1_2.nid
							LEFT JOIN nomsubclassification AS nom1_3 ON artifacts.nomen1_3 = nom1_3.nid
							LEFT JOIN nomcategory AS nom2_1 ON artifacts.nomen2_1 = nom2_1.nid
							LEFT JOIN nomclassification AS nom2_2 ON artifacts.nomen2_2 = nom2_2.nid
							LEFT JOIN nomsubclassification AS nom2_3 ON artifacts.nomen2_3 = nom2_3.nid
							LEFT JOIN culture ON artifacts.culture1 = culture.cid
							WHERE ";
        $constraints = array();
        if ($request->getQuery() != "") {
            if (!$request->isExclusivelyNegated()) {
                $sqlQuery = "SELECT artifacts.*, (((CASE WHEN `Accession Number` LIKE :queryw THEN 1 ELSE 0 END) * " . self::RELEVANCE_ACCNUM . ")
										+ (MATCH(`Name`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_NAME . ")
										+ (MATCH(`public description`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_PUBLICDESC . ")
										+ (MATCH(`Published Description`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_PUBLISHEDDESC . ")
										+ (MATCH(`Visual Description`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_VISUALDEC . ")
										+ (MATCH(`period 1`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_PERIOD . ")
										+ (MATCH(`materials 2`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_MATERIALS . ")
										+ (MATCH(`artist`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_ARTIST . ")
										+ (MATCH(`credit line`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CREDIT . ")
										+ (MATCH(geocontinent.continent) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CONTINENT . ")
										+ (MATCH(geocountry.country) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_COUNTRY . ")
										+ (MATCH(geocity.city) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_REGION . ")
										+ (MATCH(georegion.region) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CITY . ")
										+ (MATCH(geolocality.locality) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_LOCALITY . ")
										+ (MATCH(culture.culture) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CULTURE . ")) AS relevance,
										geocontinent.continent, geocountry.country, georegion.region, geocity.city, geolocality.locality, culture.culture, nom1_1.category as cat1, nom1_2.classification as class1, nom1_3.subclassification as subclass1, nom2_1.category as cat2, nom2_2.classification as class2, nom2_3.subclassification as subclass2
							FROM artifacts 
							LEFT JOIN geocontinent ON artifacts.geo1 = geocontinent.gid
							LEFT JOIN geocountry ON artifacts.geo2 = geocountry.gid
							LEFT JOIN georegion ON artifacts.geo3 = georegion.gid
							LEFT JOIN geocity ON artifacts.geo4 = geocity.gid
							LEFT JOIN geolocality ON artifacts.geo5 = geolocality.gid
							LEFT JOIN nomcategory AS nom1_1 ON artifacts.nomen1_1 = nom1_1.nid
							LEFT JOIN nomclassification AS nom1_2 ON artifacts.nomen1_2 = nom1_2.nid
							LEFT JOIN nomsubclassification AS nom1_3 ON artifacts.nomen1_3 = nom1_3.nid
							LEFT JOIN nomcategory AS nom2_1 ON artifacts.nomen2_1 = nom2_1.nid
							LEFT JOIN nomclassification AS nom2_2 ON artifacts.nomen2_2 = nom2_2.nid
							LEFT JOIN nomsubclassification AS nom2_3 ON artifacts.nomen2_3 = nom2_3.nid
							LEFT JOIN culture ON artifacts.culture1 = culture.cid
							WHERE (MATCH(`accession number`,`name`,`period 1`,`visual description`,`materials 2`,`published description`,`artist`,`credit line`,`public description`) AGAINST (:query IN BOOLEAN MODE) 
							   OR MATCH(geocontinent.continent) AGAINST (:query IN BOOLEAN MODE)
							   OR MATCH(geocountry.country) AGAINST (:query IN BOOLEAN MODE)
							   OR MATCH(georegion.region) AGAINST (:query IN BOOLEAN MODE)
							   OR MATCH(geocity.city) AGAINST (:query IN BOOLEAN MODE)
							   OR MATCH(geolocality.locality) AGAINST (:query IN BOOLEAN MODE)
							   OR MATCH(culture.culture) AGAINST (:query IN BOOLEAN MODE))";
                $sqlCountQuery .= "(MATCH(`accession number`,`name`,`period 1`,`visual description`,`materials 2`,`published description`,`artist`,`credit line`,`public description`) AGAINST (:query IN BOOLEAN MODE)
								OR MATCH(geocontinent.continent) AGAINST (:query IN BOOLEAN MODE)
								OR MATCH(geocountry.country) AGAINST (:query IN BOOLEAN MODE)
								OR MATCH(georegion.region) AGAINST (:query IN BOOLEAN MODE)
								OR MATCH(geocity.city) AGAINST (:query IN BOOLEAN MODE)
								OR MATCH(geolocality.locality) AGAINST (:query IN BOOLEAN MODE)
								OR MATCH(culture.culture) AGAINST (:query IN BOOLEAN MODE))";
            } else {
                $sqlQuery = "SELECT artifacts.*, (((CASE WHEN `Accession Number` LIKE :queryw THEN 1 ELSE 0 END) * " . self::RELEVANCE_ACCNUM . ")
										+ (NOT MATCH(`Name`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_NAME . ")
										+ (NOT MATCH(`public description`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_PUBLICDESC . ")
										+ (NOT MATCH(`Published Description`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_PUBLISHEDDESC . ")
										+ (NOT MATCH(`Visual Description`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_VISUALDEC . ")
										+ (NOT MATCH(`period 1`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_PERIOD . ")
										+ (NOT MATCH(`materials 2`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_MATERIALS . ")
										+ (NOT MATCH(`artist`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_ARTIST . ")
										+ (NOT MATCH(`credit line`) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CREDIT . ")
										+ (NOT MATCH(geocontinent.continent) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CONTINENT . ")
										+ (NOT MATCH(geocountry.country) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_COUNTRY . ")
										+ (NOT MATCH(geocity.city) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_REGION . ")
										+ (NOT MATCH(georegion.region) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CITY . ")
										+ (NOT MATCH(geolocality.locality) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_LOCALITY . ")
										+ (NOT MATCH(culture.culture) AGAINST (:query IN BOOLEAN MODE) * " . self::RELEVANCE_CULTURE . ")) AS relevance,
										geocontinent.continent, geocountry.country, georegion.region, geocity.city, geolocality.locality, culture.culture, nom1_1.category as cat1, nom1_2.classification as class1, nom1_3.subclassification as subclass1, nom2_1.category as cat2, nom2_2.classification as class2, nom2_3.subclassification as subclass2
							FROM artifacts 
							LEFT JOIN geocontinent ON artifacts.geo1 = geocontinent.gid
							LEFT JOIN geocountry ON artifacts.geo2 = geocountry.gid
							LEFT JOIN georegion ON artifacts.geo3 = georegion.gid
							LEFT JOIN geocity ON artifacts.geo4 = geocity.gid
							LEFT JOIN geolocality ON artifacts.geo5 = geolocality.gid
							LEFT JOIN nomcategory AS nom1_1 ON artifacts.nomen1_1 = nom1_1.nid
							LEFT JOIN nomclassification AS nom1_2 ON artifacts.nomen1_2 = nom1_2.nid
							LEFT JOIN nomsubclassification AS nom1_3 ON artifacts.nomen1_3 = nom1_3.nid
							LEFT JOIN nomcategory AS nom2_1 ON artifacts.nomen2_1 = nom2_1.nid
							LEFT JOIN nomclassification AS nom2_2 ON artifacts.nomen2_2 = nom2_2.nid
							LEFT JOIN nomsubclassification AS nom2_3 ON artifacts.nomen2_3 = nom2_3.nid
							LEFT JOIN culture ON artifacts.culture1 = culture.cid
							WHERE (NOT MATCH(`accession number`,`name`,`period 1`,`visual description`,`materials 2`,`published description`,`artist`,`credit line`,`public description`) AGAINST (:query IN BOOLEAN MODE) 
							  AND NOT MATCH(geocontinent.continent) AGAINST (:query IN BOOLEAN MODE)
							  AND NOT MATCH(geocountry.country) AGAINST (:query IN BOOLEAN MODE)
							  AND NOT MATCH(georegion.region) AGAINST (:query IN BOOLEAN MODE)
							  AND NOT MATCH(geocity.city) AGAINST (:query IN BOOLEAN MODE)
							  AND NOT MATCH(geolocality.locality) AGAINST (:query IN BOOLEAN MODE)
							  AND NOT MATCH(culture.culture) AGAINST (:query IN BOOLEAN MODE))";
                $sqlCountQuery .= "NOT (MATCH(`accession number`,`name`,`period 1`,`visual description`,`materials 2`,`published description`,`artist`,`credit line`,`public description`) AGAINST (:query IN BOOLEAN MODE)
							   AND NOT MATCH(geocontinent.continent) AGAINST (:query IN BOOLEAN MODE)
							   AND NOT MATCH(geocountry.country) AGAINST (:query IN BOOLEAN MODE)
							   AND NOT MATCH(georegion.region) AGAINST (:query IN BOOLEAN MODE)
							   AND NOT MATCH(geocity.city) AGAINST (:query IN BOOLEAN MODE)
							   AND NOT MATCH(geolocality.locality) AGAINST (:query IN BOOLEAN MODE)
							   AND NOT MATCH(culture.culture) AGAINST (:query IN BOOLEAN MODE))";
            }
        }
        if ($request->getWithImages()) {
            $constraints[] = "`Image Source` LIKE 'Images%'";
        }
        if ($request->getOnDisplay()) {
            $constraints[] = "`on_display` = 1";
            if ($request->getGallery() != "All") {
                $constraints[] = "`Spurlock Loc 2` = :gallery";
                $params[":gallery"] = $this->gallery[$request->getGallery()];
            }
        }
        if ($request->getWithHiResImages()) {
            $constraints[] = "`hiresimagecheck` = 'Yes'";
        }
        if ($request->getName() != "") {
            $constraints[] = "`Name` LIKE :name";
            $params[":name"] = '%' . $request->getName() . '%';
        }
        if ($request->getAccessionNumber() != "") {
            if ($request->isAccNumExact()) {
                $constraints[] = "`Accession Number` = :accnum";
                $params[":accnum"] = $request->getAccessionNumber();
            } else {
                $constraints[] = "`Accession Number` LIKE :accnum";
                $params[":accnum"] = '%' . $request->getAccessionNumber() . '%';
            }
        }
        if ($request->getAID() != null) {
            $constraints[] = "`aid` = :aid";
            $params[":aid"] = $request->getAID();
        }
        if ($request->getDate() != "") {
            $constraints[] = "`period 3 date` = :date";
            $params[":date"] = $request->getDate();
        }
        if ($request->getContinent() != "All" && $request->getContinent() != "") {
            $constraints[] = "`continent` LIKE :continent";
            $params[":continent"] = $request->getContinent();
        }
        if ($request->getCountry() != "") {
            $constraints[] = "`country` LIKE :country";
            $params[":country"] = '%' . $request->getCountry() . '%';
        }
        if ($request->getRegion() != "") {
            $constraints[] = "`region` LIKE :region";
            $params[":region"] = '%' . $request->getRegion() . '%';
        }
        if ($request->getCity() != "") {
            $constraints[] = "`city` LIKE :city";
            $params[":city"] = '%' . $request->getCity() . '%';
        }
        if ($request->getLocality() != "") {
            $constraints[] = "`locality` LIKE :locality";
            $params[":locality"] = '%' . $request->getLocality() . '%';
        }
        if ($request->getCulture() != "") {
            $constraints[] = "`culture` LIKE :culture";
            $params[":culture"] = '%' . $request->getCulture() . '%';
        }
        if ($request->getMaterial() != "") {
            $constraints[] = "`materials 2` LIKE :material";
            $params[":material"] = '%' . $request->getMaterial() . '%';
        }
        if ($request->getManufacturingProcess() != "") {
            $constraints[] = "`manufacturing processes 2` LIKE :process";
            $params[":process"] = '%' . $request->getManufacturingProcess() . '%';
        }
        if ($request->getWorkingSet() != "") {
            $constraints[] = "`working set 123` LIKE :workingset";
            $params[":workingset"] = '%' . $request->getWorkingSet() . '%';
        }
        if ($request->getCreditLine() != "") {
            $constraints[] = "`Credit Line` LIKE :credit";
            $params[":credit"] = '%' . $request->getCreditLine() . '%';
        }
        if ($request->getCategory() != "All" && $request->getCategory() != "") {
            $constraints[] = "(nom1_1.category = :category1 OR nom2_1.category = :category2)";
            $params[":category1"] = $request->getCategory();
            $params[":category2"] = $request->getCategory();
        }
        if ($request->getClassification() != "All" && $request->getClassification() != "") {
            $constraints[] = "(nom1_2.classification = :class1 OR nom2_2.classification = :class2)";
            $params[":class1"] = $request->getClassification();
            $params[":class2"] = $request->getClassification();
        }
        if ($request->getSubClassification() != "All" && $request->getSubClassification() != "") {
            $constraints[] = "(nom1_3.subclassification = :subclass1 OR nom2_3.subclassification = :subclass2)";
            $params[":subclass1"] = $request->getSubClassification();
            $params[":subclass2"] = $request->getSubClassification();
        }
        if ($request->getQuery() != "" && count($constraints) > 0) {
            $sqlQuery .= " AND ";
            $sqlCountQuery .= " AND ";
        }

        $sqlQuery .= implode(" AND ", $constraints);
        $sqlCountQuery .= implode(" AND ", $constraints);
        if ($request->getQuery() != "") {
            switch ($request->getSortParameter()) {
                case "relevance":
                    $sqlQuery .= " ORDER BY relevance DESC";
                    break;
                case "popularity":
                    $sqlQuery .= " ORDER BY `view_cnt` DESC";
                    break;
                case "name":
                    $sqlQuery .= " ORDER BY `name` ASC";
                    break;
                case "rand":
                    $sqlQuery .= " ORDER BY RAND()";
                    break;
                default:
                    $sqlQuery .= " ORDER BY relevance DESC";
                    break;
            }
        } else {
            switch ($request->getSortParameter()) {
                case "popularity":
                    $sqlQuery .= " ORDER BY `view_cnt` DESC";
                    break;
                case "name":
                    $sqlQuery .= " ORDER BY `name` ASC";
                    break;
                case "rand":
                    $sqlQuery .= " ORDER BY RAND()";
                    break;
                default:
                    break;
            }
        }
        $sqlQuery .= " LIMIT :start, :dur";
        $STH = $this->DBH->prepare($sqlQuery);
        if ($request->getQuery() != "") {
            if ($request->isExclusivelyNegated()) {
                $STH->bindValue(':queryw', $request->getReversedQuery() . '%', PDO::PARAM_STR);
                $STH->bindValue(':query', $request->getReversedQuery(), PDO::PARAM_STR);
            } else {
                $STH->bindValue(':queryw', $searchQuery . '%', PDO::PARAM_STR);
                $STH->bindValue(':query', $searchQuery, PDO::PARAM_STR);
            }
        }
        foreach ($params as $param => $value) {
            $STH->bindValue($param, $value, PDO::PARAM_STR);
        }
        $STH->bindValue(':start', $start, PDO::PARAM_INT);
        $STH->bindValue(':dur', $duration, PDO::PARAM_INT);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $STH->fetch()) {
            $this->resultSet[] = new artifact($row);
        }
        $STH = $this->DBH->prepare($sqlCountQuery);
        if ($request->getQuery() != "") {
            if ($request->isExclusivelyNegated()) {
                $STH->bindValue(':query', $request->getReversedQuery(), PDO::PARAM_STR);
            } else {
                $STH->bindValue(':query', $searchQuery, PDO::PARAM_STR);
            }
        }
        foreach ($params as $param => $value) {
            $STH->bindValue($param, $value, PDO::PARAM_STR);
        }
        $STH->execute();
        $this->foundSetCount = $STH->fetch();
        $this->foundSetCount = $this->foundSetCount[0];
        if ($getFacets) {
            $this->getFacets($sqlCountQuery, $params);
        }
        if ($this->foundSetCount <= 5){
            $this->executeOldSimpleRequest($request,$getFacets);
        }
    }

    /**
     * Executes any given SQL query, returns the results in an array
     * @param $sqlQuery A SQL query to execute
     * @return array The query results
     */
    private function executeCustomSQL($sqlQuery)
    {
        $STH = $this->DBH->prepare($sqlQuery);
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $results = array();
        while ($row = $STH->fetch()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Get the current result set
     * @return array The results
     */
    public function getResults()
    {
        return $this->resultSet;
    }

    /**
     * Get the number of the first record of the current request
     * @return int The number
     */
    public function getResultStart()
    {
        return $this->currentRequest->getStart() + 1;
    }

    /**
     * Get the starting record of the page $pageOffset pages away from the current page
     * @param $pageOffset The number of pages (- or +) away from the current page to look
     * @return int The starting record
     */
    public function getPageStartFromCurrent($pageOffset)
    {
        if ($pageOffset < 0) {
            return max($this->currentRequest->getStart() + ($pageOffset * $this->numRecordsPerPage), 0);
        } else {
            return min($this->currentRequest->getStart() + ($pageOffset * $this->numRecordsPerPage), $this->foundSetCount - ($this->foundSetCount % $this->numRecordsPerPage));
        }
    }

    /**
     * Get the starting record of the page $pageOffset pages away from the last page
     * @param $pageOffset The number of pages away from the last page to look (negative offset only)
     * @return int The starting record
     */
    public function getPageStartFromEnd($pageOffset)
    {
        if ($pageOffset < 0) {
            return max($this->foundSetCount - ($this->foundSetCount % $this->numRecordsPerPage) + ($pageOffset * $this->numRecordsPerPage), 0);
        }
    }

    /**
     * Get the starting record of the page $pageOffset pages away from the first page
     * @param $pageOffset The number of pages away from the first page to look (positive offset only)
     * @return int The starting record
     */
    public function getPageStartFromBeginning($pageOffset)
    {
        if ($pageOffset >= 0) {
            return min($pageOffset * $this->numRecordsPerPage, $this->foundSetCount - ($this->foundSetCount % $this->numRecordsPerPage));
        }
    }

    /**
     * Get the total number of pages
     * @return int The number of pages
     */
    public function getNumberOfPages()
    {
        return ceil($this->foundSetCount / $this->numRecordsPerPage);
    }

    /**
     * Get the current page number
     * @return int The page number
     */
    public function getCurrentPage()
    {
        return ($this->currentRequest->getStart() / $this->numRecordsPerPage) + 1;
    }

    /**
     * Get the last record number on the current page
     * @return int The last record
     */
    public function getResultEnd()
    {
        return $this->currentRequest->getStart() + $this->numRecordsPerPage;
    }

    /**
     * Get the number of records in the found set
     * @return int The count
     */
    public function getFoundSetCount()
    {
        return $this->foundSetCount;
    }

    /**
     * Check whether the current query is too short
     * @return bool True if the query is too short, False otherwise
     */
    public function currentQueryIsShort()
    {
        if ($this->currentRequest->getQuery() == "") {
            return false;
        }
        return (strlen($this->currentRequest->getQuery()) < collectionsBackend::QUERY_LENGTH_TOO_COMMON);
    }

    /**
     * Outputs the HTML to display the list of results
     */
    public function outputResultsListView()
    {
        $i = 1;
        echo '<div id="listCont"><div class="ajaxLoad">';
        foreach ($this->resultSet as $artifact) {
            if ($artifact instanceof artifact) {
                if ($this->savedSearchID > 0) {
                    $searchIDstr = "&sid=" . $this->savedSearchID;
                } else {
                    $searchIDstr = "";
                }
                //echo '<div class="clearfloat"></div>';
                echo '<div class="records"><div class="fltrt">';
                echo '<a href="details.php?a=' . $artifact->getAccessionNumber() . $searchIDstr . '">';
                List($width, $height) = getimagesize($artifact->getThumbImage(artifact::SEARCH_CONTEXT));
                if ($width == $height) {
                    echo '<img src="' . $artifact->getThumbImage(artifact::SEARCH_CONTEXT) . '" style="border:1px solid black;" alt="' . $artifact->getAccessionNumber() . '" width="125"></a>';
                } else if ($width > $height) {
                    echo '<img src="' . $artifact->getThumbImage(artifact::SEARCH_CONTEXT) . '" style="border:1px solid black;" alt="' . $artifact->getAccessionNumber() . '" width="125"></a>';
                } else { //$width < $height
                    echo '<img src="' . $artifact->getThumbImage(artifact::SEARCH_CONTEXT) . '" style="border:1px solid black;" alt="' . $artifact->getAccessionNumber() . '" height="125"></a>';
                }
                echo '</div><div class="artifactinfo"><div class="artifactbadges">';
                if (count($artifact->getHiResImages()) > 0) {
                    echo '<br><span class="noborder"><a href="details.php?a=' . $artifact->getAccessionNumber() . $searchIDstr . '"><img src="elements/hires.gif"" height="20" width="75" alt="High Resolution Image Available"></a></span>';
                }
                if ($artifact->isOnDisplay()) {
                    echo '<br><span class="noborder"><img src="elements/ondisplay.jpg" height="20" width="75"></span>';
                }
                if ($artifact->isVirtualTour()) {
                    echo '<br><span class="noborder"><a href="redir.php?imgacc=' . $artifact->getAccessionNumber() . '"><img src="elements/v' . $artifact->getLocation2() . '.jpg" height="20" width="75" alt="Virtual Tour"></a></span>';
                }
                echo '</div>';
                //echo '</div><div class="artifactinfo">';
                echo '<a href="details.php?a=' . $artifact->getAccessionNumber() . $searchIDstr . '">' . $artifact->getName() . '</a>';
                echo '<span style="font-size:.9em;"> (' . $artifact->getAccessionNumber() . ')</span><br>';
                echo '<div style="margin-top:.2em; font-size:.8em;">' . $artifact->getMeasurements() . '</div>';
                echo '<div style="margin-top:.4em;">';
                echo 'Location: ' . $artifact->getGeography() . '<br>';
                echo 'Period: ' . $artifact->getPeriodText() . '<br>';
                echo 'Classification: ' . $artifact->getClassificationText() . '<br>';
                if ($artifact->hasSecondaryNomenclature()) {
                    echo 'Secondary Classification: ' . $artifact->getSecondaryClassificationText() . '<br>';
                }
                if ($artifact->getPublicDescription() != "N/A") {
                    echo 'Description: ' . descTruncate($artifact->getPublicDescription(), 100) . '<br>';
                }
                //Relevance and View Count hidden at launch, 2012-05-09.
				//echo '<span class="bold">Relevance: ' . round($artifact->getRelevance(), 3) * 100 . '%</span><br>';
                //echo '<span class="bold">View Count (details): ' . $artifact->getViewCount() . '</span><br>';
                //echo '<span class="bold">Num: ' . ($this->currentRequest->getStart() + $i) . '</span>';
                $i++;
                echo '</div></div></div>';
            }
        }
        echo '</div></div>';
        echo '<div style="clear:both;"></div>';
    }

    /**
     * Outputs the HTML to display the grid of results
     */
    public function outputResultsGridView()
    {
        echo '<div id="gridCont"><div class="ajaxLoad">';
        foreach ($this->resultSet as $artifact) {
            if ($artifact instanceof artifact) {
                if ($this->savedSearchID > 0) {
                    $searchIDstr = "&sid=" . $this->savedSearchID;
                } else {
                    $searchIDstr = "";
                }
                echo '<div class="artWrapper"><div class="artCont">';
                echo '<a href="details.php?a=' . $artifact->getAccessionNumber() . $searchIDstr . '">';
                List($width, $height) = getimagesize($artifact->getThumbImage(artifact::SEARCH_CONTEXT));
                if ($width == $height) {
                    echo '<img class="artImage" src="' . $artifact->getThumbImage(artifact::SEARCH_CONTEXT) . '" alt="' . $artifact->getAccessionNumber() . '" width="' . self::GRID_IMAGE_SIZE . '"></a>';
                } else if ($width > $height) {
                    $newWidth = self::GRID_IMAGE_SIZE / $height * $width;
                    echo '<img class="artImage" src="' . $artifact->getThumbImage(artifact::SEARCH_CONTEXT) . '" alt="' . $artifact->getAccessionNumber() . '" height="' . self::GRID_IMAGE_SIZE . '" style="margin-left: -' . ($newWidth - self::GRID_IMAGE_SIZE) / 2 . 'px"></a>';
                } else { //$width < $height
                    echo '<img class="artImage" src="' . $artifact->getThumbImage(artifact::SEARCH_CONTEXT) . '" alt="' . $artifact->getAccessionNumber() . '" width="' . self::GRID_IMAGE_SIZE . '"></a>';
                }
                echo '<div class="artTitle hidden">' . $artifact->getName() . '</div>';
                echo '<div class="artInfo hidden">';
                echo $artifact->getAccessionNumber() . '<br>';
                echo '<div style="margin-top:.2em;">' . $artifact->getMeasurements() . '</div>';
                echo '<div style="margin-top:.4em;">';
                echo 'Origin: ' . $artifact->getGeography() . '<br>';
                echo 'Period: ' . $artifact->getPeriodText() . '<br>';
                echo 'Classification: ' . $artifact->getClassificationText() . '<br>';
                if ($artifact->hasSecondaryNomenclature()) {
                    echo 'Secondary Classification: ' . $artifact->getSecondaryClassificationText() . '<br>';
                }
                if ($artifact->getPublicDescription() != "N/A") {
                    echo 'Description: ' . descTruncate($artifact->getPublicDescription(), 100) . '<br>';
                }
                echo '</div></div></div></div>';
            }
        }
        echo '</div></div>';
        echo '<div style="clear:both;"></div>';
    }

    /**
     * Output the HTML to display the sort buttons/links
     */
    public function displaySort()
    {
        $sortParam = $this->currentRequest->getSortParameter();
        echo '<div id="sortSelect"><span>Sort by:</span><ul class="button-group radius">';
        if ($sortParam == "relevance") {
            echo '<li><a href="#" class="small button disabled"><i class="icon-trophy"></i> Relevance</a></li>';
        } else {
            echo '<li><a href="' . changeGetVarInURL($_SERVER['REQUEST_URI'], 'sort', 'relevance') . '" class="small button"><i class="icon-trophy"></i> Relevance</a></li>';
        }
        if ($sortParam == "popularity") {
            echo '<li><a href="#" class="small button disabled"><i class="icon-star"></i> Popularity</a></li>';
        } else {
            echo '<li><a href="' . changeGetVarInURL($_SERVER['REQUEST_URI'], 'sort', 'popularity') . '" class="small button"><i class="icon-star"></i> Popularity</a></li>';
        }
        if ($sortParam == "name") {
            echo '<li><a href="#" class="small button disabled"><i class="icon-font icon-name-A"></i><i class="icon-bold icon-name-B"></i> Name</a></li>';
        } else {
            echo '<li><a href="' . changeGetVarInURL($_SERVER['REQUEST_URI'], 'sort', 'name') . '" class="small button"><i class="icon-font icon-name-A"></i><i class="icon-bold icon-name-B"></i> Name</a></li>';
        }
        echo '  </ul></div>';
    }

    /**
     * Output the HTML to display the pagination
     * @param $requestURI The URL of the search page, or blank for default value
     */
    public function displayPagination($requestURI)
    {
        if ($requestURI == '') {
            $requestURI = $_SERVER['REQUEST_URI'];
        }
        $curPage = $this->getCurrentPage();
        $numPages = $this->getNumberOfPages();
        echo '<div id="pageSelect"><a href="' . setStartInURL($requestURI, $this->getPageStartFromCurrent(-1)) . '">[Previous]</a> ';
        $pages = array();
        for ($i = 0; $i < $numPages; $i++) {
            if ($i < 5 || ($i < ($curPage + 2) && $i > ($curPage - 4) || $i > ($numPages - 6))) {
                $pages[$i] = true;
            }
        }
        $prevPage = 0;
        foreach ($pages as $pageNum => $show) {
            if ($prevPage < ($pageNum - 1)) {
                echo '...';
            }
            echo '<a ';
            if (($pageNum + 1) == $curPage) {
                echo 'class="bold" ';
            }
            echo 'href="' . setStartInURL($requestURI, $this->getPageStartFromBeginning($pageNum)) . '">' . ($pageNum + 1) . '</a> ';
            $prevPage = $pageNum;
        }
        echo '<a href="' . setStartInURL($requestURI, $this->getPageStartFromCurrent(1)) . '">[Next]</a></div>';
    }

    /**
     * Connect to the SQL database
     * @return bool True if successfully connected, False otherwise
     */
    private function connectSQL()
    {
        try {
            //Create the connection
            $this->DBH = new PDO("mysql:host=" . self::SQL_HOST . ";dbname=" . self::SQL_DB, self::SQL_USER, self::SQL_PASS);
            //Set the handler to always throw exceptions on errors (best practice)
            $this->DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Exception: ' . $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * Disconnect from the MySQL database.
     */
    private function disconnectSQL()
    {
        $this->DBH = null;
    }

    /**
     * Check if the connection to the SQL server is active
     * @return bool True if the connection is active, False otherwise
     */
    private function isConnectedSQL()
    {
        if ($this->DBH) {
            return true;
        }
        return false;
    }

    /**
     * Display the natural language version of the query
     */
    public function displayNaturalEnglishQuery()
    {
        $natQ = new naturalQuery($this->currentRequest);
        echo $natQ;
    }

    /**
     * Gets the facets and stores them for later output
     * @param $query The SQL query to create the temporary table representing the same results as the search query
     * @param null $params The parameters to bind to the SQL string
     */
    private function getFacets($query, $params = null)
    {
        //Create a temporary SQL table with the same records as the result set, use this table to find facets
        $newQuery = preg_replace('/SELECT count\(1\)/', 'SELECT `geo1`, `geo2`, `geo3`, `geo4`, `geo5`, `nomen1_1`, `nomen1_2`, `nomen1_3`, `nomen2_1`, `nomen2_2`, `nomen2_3` ', $query);
        $newQuery = 'CREATE TEMPORARY TABLE IF NOT EXISTS temp_artifacts (' . $newQuery . ')';
        $STH = $this->DBH->prepare($newQuery);
        if ($this->currentRequest->getQuery() != "") {
            if ($this->currentRequest->isExclusivelyNegated()) {
                $STH->bindValue(':query', $this->currentRequest->getReversedQuery(), PDO::PARAM_STR);
            } else {
                $STH->bindValue(':query', $this->currentRequest->getQuery(), PDO::PARAM_STR);
            }
        }
        if ($params) {
            foreach ($params as $param => $value) {
                $STH->bindValue($param, $value, PDO::PARAM_STR);
            }
        } else {
            if ($this->currentRequest->getOnDisplay() && $this->currentRequest->getGallery() != "All") {
                $STH->bindValue(':gallery', $this->gallery[$this->currentRequest->getGallery()], PDO::PARAM_STR);
            }
        }
        $STH->execute();
        $this->facetHandler->parseFacets();
    }

    /**
     * Outputs the facets for HTML display
     */
    public function displayFacets()
    {
        $this->facetHandler->displayFacets();
    }

}

?>

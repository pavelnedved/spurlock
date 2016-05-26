<?php

/**
 * Class searchRequest
 *
 * Holds data about a search request. Used to store and pass requests between classes.
 *
 * @package CollectionsSearch
 * @author Michael Robinson
 */
class searchRequest
{

    /** 
     * @var string
     */
    private $query = ""; 
    /**
     * @var bool
     */
    private $withImages = false; 
    /**
     * @var bool
     */
    private $withHiResImages = false; 
    /**
     * @var bool
     */
    private $onDisplay = false; 
    /**
     * @var string
     */
    private $gallery = ""; 
    /**
     * @var bool
     */
    private $isAdvanced = false; 
    /**
     * @var string
     */
    private $name = ""; 
    /**
     * @var string
     */
    private $accessionNumber = ""; 
    /**
     * @var string
     */
    private $continent = ""; 
    /**
     * @var string
     */
    private $country = ""; 
    /**
     * @var string
     */
    private $region = ""; 
    /**
     * @var string
     */
    private $city = ""; 
    /**
     * @var string
     */
    private $locality = ""; 
    /**
     * @var string
     */
    private $culture = ""; 
    /**
     * @var string
     */
    private $creditLine = ""; 
    /**
     * @var string
     */
    private $category = ""; 
    /**
     * @var string
     */
    private $classification = ""; 
    /**
     * @var string
     */
    private $subclassification = ""; 
    /**
     * @var string
     */
    private $material = ""; 
    /**
     * @var string
     */
    private $manufacturingProcess = ""; 
    /**
     * @var string
     */
    private $workingSet = ""; 
    /**
     * @var string
     */
    private $_date = ""; 
    /**
     * @var int
     */
    private $aid = null; 

    /**
     * @var string
     */
    private $facet_continent = ""; 
    /**
     * @var string
     */
    private $facet_country = ""; 
    /**
     * @var string
     */
    private $facet_region = ""; 
    /**
     * @var string
     */
    private $facet_city = ""; 
    /**
     * @var string
     */
    private $facet_locality = ""; 

    /**
     * @var bool
     */
    private $isActive = false;
    /**
     * @var bool
     */
    private $accnumExact = false;

    /**
     * @var int
     */
    private $startingRecord = 0; 
    /**
     * @var string
     */
    private $sortParam = "relevance";


    public function __construct($url = "")
    {
        //default constructor
        if ($url == "") {
            $this->constructFromGET();
        } else {
            $this->constructFromURL($url);
        }
    }

    /**
     * Construct the searchRequest from the GET parameters
     */
    private function constructFromGET()
    {
        if (isset($_GET['q'])) {
            $this->setQuery($_GET['q']);
        }
        if (isset($_GET['m'])) {
            $this->setWithImages($_GET['m'] == 1);
        }
        if (isset($_GET['d'])) {
            $this->setOnDisplay($_GET['d'] == 1);
        }
        if (isset($_GET['h'])) {
            $this->setWithHiResImages($_GET['h'] == 1);
        }
        if (isset($_GET['g'])) {
            $this->setGallery($_GET['g']);
        }
        if (isset($_GET['n'])) {
            $this->setName($_GET['n']);
        }
        if (isset($_GET['a'])) {
            $this->setAccessionNumber($_GET['a']);
        }
        if (isset($_GET['date'])) {
            $this->setDate($_GET['date']);
        }
        if (isset($_GET['c1'])) {
            $this->setCategory($_GET['c1']);
        }
        if (isset($_GET['c2'])) {
            $this->setClassification($_GET['c2']);
        }
        if (isset($_GET['c3'])) {
            $this->setSubClassification($_GET['c3']);
        }
        if (isset($_GET['g1'])) {
            $this->setContinent($_GET['g1']);
        }
        if (isset($_GET['g2'])) {
            $this->setCountry($_GET['g2']);
        }
        if (isset($_GET['g3'])) {
            $this->setRegion($_GET['g3']);
        }
        if (isset($_GET['g4'])) {
            $this->setCity($_GET['g4']);
        }
        if (isset($_GET['g5'])) {
            $this->setLocality($_GET['g5']);
        }
        if (isset($_GET['cl'])) {
            $this->setCulture($_GET['cl']);
        }
        if (isset($_GET['cr'])) {
            $this->setCreditLine($_GET['cr']);
        }
        if (isset($_GET['mat'])) {
            $this->setMaterial($_GET['mat']);
        }
        if (isset($_GET['man'])) {
            $this->setManufacturingProcess($_GET['man']);
        }
        if (isset($_GET['ws'])) {
            $this->setWorkingSet($_GET['ws']);
        }
        if (isset($_GET['start']) && is_numeric($_GET['start'])) {
            $this->startingRecord = (int)$_GET['start'];
        }
        if (isset($_GET['sort'])) {
            $this->setSortParameter($_GET['sort']);
        }

        if (isset($_GET['fc1'])) {
            $this->setFacetContinent($_GET['fc1']);
        }
        if (isset($_GET['fc2'])) {
            $this->setFacetCountry($_GET['fc2']);
        }
        if (isset($_GET['fc3'])) {
            $this->setFacetRegion($_GET['fc3']);
        }
        if (isset($_GET['fc4'])) {
            $this->setFacetCity($_GET['fc4']);
        }
        if (isset($_GET['fc5'])) {
            $this->setFacetLocality($_GET['fc5']);
        }
    }

    /**
     * Construct the searchRequest from a URL
     * @param $url The URL
     */
    private function constructFromURL($url)
    {
        //construct from referrer URL
        $queryVars = array();
        $parts = explode('?', $url);
        if (count($parts) < 2) {
            return $this->constructFromGET();
        }
        $queryString = $parts[1];
        $vars = explode('&', urldecode($queryString));
        if (count($vars) < 1) {
            return $this->constructFromGET();
        }
        foreach ($vars as $var) {
            $p = explode('=', $var);
            if (count($p) == 2) {
                $queryVars[$p[0]] = $p[1];
            }
        }
        $this->constructFromArray($queryVars);
        $this->isActive = true;
    }

    /**
     * Construct the searchRequest from an array
     * @param $arr The array
     */
    private function constructFromArray($arr)
    {
        if (array_key_exists('q', $arr)) {
            $this->setQuery($arr['q']);
        }
        if (array_key_exists('m', $arr)) {
            $this->setWithImages($arr['m'] == 1);
        }
        if (array_key_exists('d', $arr)) {
            $this->setOnDisplay($arr['d'] == 1);
        }
        if (array_key_exists('h', $arr)) {
            $this->setWithHiResImages($arr['h'] == 1);
        }
        if (array_key_exists('g', $arr)) {
            $this->setGallery($arr['g']);
        }
        if (array_key_exists('n', $arr)) {
            $this->setName($arr['n']);
        }
        if (array_key_exists('a', $arr)) {
            $this->setAccessionNumber($arr['a']);
        }
        if (array_key_exists('date', $arr)) {
            $this->setDate($arr['date']);
        }
        if (array_key_exists('c1', $arr)) {
            $this->setCategory($arr['c1']);
        }
        if (array_key_exists('c2', $arr)) {
            $this->setClassification($arr['c2']);
        }
        if (array_key_exists('c3', $arr)) {
            $this->setSubClassification($arr['c3']);
        }
        if (array_key_exists('g1', $arr)) {
            $this->setContinent($arr['g1']);
        }
        if (array_key_exists('g2', $arr)) {
            $this->setCountry($arr['g2']);
        }
        if (array_key_exists('g3', $arr)) {
            $this->setRegion($arr['g3']);
        }
        if (array_key_exists('g4', $arr)) {
            $this->setCity($arr['g4']);
        }
        if (array_key_exists('g5', $arr)) {
            $this->setLocality($arr['g5']);
        }
        if (array_key_exists('cl', $arr)) {
            $this->setCulture($arr['cl']);
        }
        if (array_key_exists('cr', $arr)) {
            $this->setCreditLine($arr['cr']);
        }
        if (array_key_exists('mat', $arr)) {
            $this->setMaterial($arr['mat']);
        }
        if (array_key_exists('man', $arr)) {
            $this->setManufacturingProcess($arr['man']);
        }
        if (array_key_exists('ws', $arr)) {
            $this->setWorkingSet($arr['ws']);
        }
        if (array_key_exists('start', $arr) && is_numeric($arr['start'])) {
            $this->startingRecord = (int)$arr['start'];
        }
        if (array_key_exists('sort', $arr)) {
            $this->setSortParameter($arr['sort']);
        }

        if (array_key_exists('fc1', $arr)) {
            $this->setFacetContinent($arr['fc1']);
        }
        if (array_key_exists('fc2', $arr)) {
            $this->setFacetCountry($arr['fc2']);
        }
        if (array_key_exists('fc3', $arr)) {
            $this->setFacetRegion($arr['fc3']);
        }
        if (array_key_exists('fc4', $arr)) {
            $this->setFacetCity($arr['fc4']);
        }
        if (array_key_exists('fc5', $arr)) {
            $this->setFacetLocality($arr['fc5']);
        }
    }

    /**
     * Returns the URL that will execute this search request
     * @return string The URL
     */
    public function getURL()
    {
        $URLVars = array();
        $URLVars[] = 'q=' . urlencode(htmlspecialchars_decode($this->getQuery()));
        $URLVars[] = 'm=' . ($this->getWithImages() ? 1 : 0);
        $URLVars[] = 'd=' . ($this->getOnDisplay() ? 1 : 0);
        $URLVars[] = 'h=' . ($this->getWithHiResImages() ? 1 : 0);
        $URLVars[] = 'g=' . urlencode(htmlspecialchars_decode($this->getGallery()));
        $URLVars[] = 'n=' . urlencode(htmlspecialchars_decode($this->getName()));
        $URLVars[] = 'a=' . urlencode(htmlspecialchars_decode($this->getAccessionNumber()));
        $URLVars[] = 'date=' . urlencode(htmlspecialchars_decode($this->getDate()));
        $URLVars[] = 'c1=' . urlencode(htmlspecialchars_decode($this->getCategory()));
        $URLVars[] = 'c2=' . urlencode(htmlspecialchars_decode($this->getClassification()));
        $URLVars[] = 'c3=' . urlencode(htmlspecialchars_decode($this->getSubClassification()));
        $URLVars[] = 'g1=' . urlencode(htmlspecialchars_decode($this->getContinent()));
        $URLVars[] = 'g2=' . urlencode(htmlspecialchars_decode($this->getCountry()));
        $URLVars[] = 'g3=' . urlencode(htmlspecialchars_decode($this->getRegion()));
        $URLVars[] = 'g4=' . urlencode(htmlspecialchars_decode($this->getCity()));
        $URLVars[] = 'g5=' . urlencode(htmlspecialchars_decode($this->getLocality()));
        $URLVars[] = 'cl=' . urlencode(htmlspecialchars_decode($this->getCulture()));
        $URLVars[] = 'cr=' . urlencode(htmlspecialchars_decode($this->getCreditLine()));
        $URLVars[] = 'mat=' . urlencode(htmlspecialchars_decode($this->getMaterial()));
        $URLVars[] = 'man=' . urlencode(htmlspecialchars_decode($this->getManufacturingProcess()));
        $URLVars[] = 'ws=' . urlencode(htmlspecialchars_decode($this->getWorkingSet()));
        $URLVars[] = 'start=' . $this->startingRecord;
        $URLVars[] = 'sort=' . urlencode(htmlspecialchars_decode($this->getSortParameter()));
        $URLVars[] = 'fc1=' . urlencode(htmlspecialchars_decode($this->getFacetContinent()));
        $URLVars[] = 'fc2=' . urlencode(htmlspecialchars_decode($this->getFacetCountry()));
        $URLVars[] = 'fc3=' . urlencode(htmlspecialchars_decode($this->getFacetRegion()));
        $URLVars[] = 'fc4=' . urlencode(htmlspecialchars_decode($this->getFacetCity()));
        $URLVars[] = 'fc5=' . urlencode(htmlspecialchars_decode($this->getFacetLocality()));
        return "/".basename(__DIR__)."/index.php?" . implode('&', $URLVars);
    }

    /**
     * Check if this search request is active
     * @return bool True if the search request is active, False otherwise
     */
    public function isActive()
    {
        /*if(trim($this->query) == "" && !$this->isAdvanced() && !$this->withImages && !$this->onDisplay && !$this->withHiResImages){
            return false;
        }*/
        if ($this->isActive) {
            return true;
        }
        return false;
    }

    /**
     * Check if the search request query is exclusively negated (ie. it is made up of only exclusion terms)
     * @return bool True if the query is exclusively negated, False otherwise
     */
    public function isExclusivelyNegated()
    {
        if (preg_match('/^([\-](([^\s\-]*)|(["][^"\-]*["]))[\s]*)+$/', $this->getQuery()) == 1) {
            return true;
        }
        return false;
    }

    /**
     * Check if the search request is liberal (ie. it is a browse request)
     * @return bool True if the query is liberal, False otherwise
     */
    public function isLiberal()
    {
        if ($this->query == "" &&
            $this->withImages == false &&
            $this->withHiResImages == false &&
            $this->onDisplay == false &&
            ($this->gallery == "" || $this->gallery == "All") &&
            $this->name == "" &&
            $this->accessionNumber == "" &&
            ($this->continent == "" || $this->continent == "All") &&
            $this->country == "" &&
            $this->region == "" &&
            $this->city == "" &&
            $this->locality == "" &&
            $this->culture == "" &&
            $this->creditLine == "" &&
            ($this->category == "" || $this->category == "All") &&
            ($this->classification == "" || $this->classification == "All") &&
            ($this->subclassification == "" || $this->subclassification == "All") &&
            $this->material == "" &&
            $this->manufacturingProcess == "" &&
            $this->workingSet == "" &&
            $this->_date == "" &&
            $this->aid == null
        ) {
            return true;
        }
        return false;
    }

    /**
     * Set the search request to be exact instead of wildcard
     */
    public function setSearchExactAccession()
    {
        $this->accnumExact = true;
    }

    /**
     * Set the query
     * @param $query The query
     */
    public function setQuery($query)
    {
        $query = preg_replace("/[,]/", " ", $query);
        $this->query = htmlspecialchars(trim($query), ENT_NOQUOTES);
        $this->isActive = true;
    }

    /**
     * Set the with images flag
     * @param $bool The flag
     */
    public function setWithImages($bool)
    {
        $this->withImages = $bool;
        $this->isActive = true;
    }

    /**
     * Set the with Hi-Res images flag
     * @param $bool The flag
     */
    public function setWithHiResImages($bool)
    {
        $this->withHiResImages = $bool;
        $this->isActive = true;
    }

    /**
     * Set the on display flag
     * @param $bool The flag
     */
    public function setOnDisplay($bool)
    {
        $this->onDisplay = $bool;
        $this->isActive = true;
    }

    /**
     * Set the gallery
     * @param $gallery The gallery
     * @param bool $markAsActive
     */
    public function setGallery($gallery, $markAsActive = true)
    {
        $this->gallery = htmlspecialchars(trim($gallery));
        if ($markAsActive) {
            $this->isActive = true;
        }
    }

    /**
     * Set the artifact ID
     * @param $aid The ID
     */
    public function setAID($aid)
    {
        if (htmlspecialchars(trim($aid)) != "") {
            $this->isAdvanced = true;
            $this->aid = htmlspecialchars(trim($aid));
        }
        $this->isActive = true;
    }

    /**
     * Set the name
     * @param $name The name
     */
    public function setName($name)
    {
        if (htmlspecialchars(trim($name)) != "") {
            $this->isAdvanced = true;
            $this->name = htmlspecialchars(trim($name));
        }
        $this->isActive = true;
    }

    /**
     * Set the accession number
     * @param $accnum The accession number
     */
    public function setAccessionNumber($accnum)
    {
        if (htmlspecialchars(trim($accnum)) != "") {
            $this->isAdvanced = true;
            $this->accessionNumber = htmlspecialchars(trim($accnum));
        }
        $this->isActive = true;
    }

    /**
     * Set the working set
     * @param $ws The working set
     */
    public function setWorkingSet($ws)
    {
        if (htmlspecialchars(trim($ws)) != "") {
            $this->isAdvanced = true;
            $this->workingSet = htmlspecialchars(trim($ws));
        }
        $this->isActive = true;
    }

    /**
     * Set the dat
     * @param $date The date
     */
    public function setDate($date)
    {
        if (htmlspecialchars(trim($date)) != "") {
            $this->isAdvanced = true;
            $this->_date = htmlspecialchars(trim($date));
        }
        $this->isActive = true;
    }

    /**
     * Set the continent
     * @param $continent The continent
     * @param bool $trim Should the continent be trimmed?
     */
    public function setContinent($continent, $trim = true)
    {
        if ($trim) {
            $continent = htmlspecialchars(trim($continent));
        }
        if ($continent != "" && $continent != "All") {
            $this->isAdvanced = true;
        }
        $this->continent = $continent;
        $this->isActive = true;
    }

    /**
     * Set the country
     * @param $country The country
     * @param bool $trim Should the country be trimmed?
     */
    public function setCountry($country, $trim = true)
    {
        if ($trim) {
            $country = htmlspecialchars(trim($country));
        }
        if ($country != "") {
            $this->isAdvanced = true;
            $this->country = $country;
        }
        $this->isActive = true;
    }

    /**
     * Set the region
     * @param $region The region
     * @param bool $trim Should the region be trimmed?
     */
    public function setRegion($region, $trim = true)
    {
        if ($trim) {
            $region = htmlspecialchars(trim($region));
        }
        if ($region != "") {
            $this->isAdvanced = true;
            $this->region = $region;
        }
        $this->isActive = true;
    }

    /**
     * Set the city
     * @param $city The city
     * @param bool $trim Should the city be trimmed?
     */
    public function setCity($city, $trim = true)
    {
        if ($trim) {
            $city = htmlspecialchars(trim($city));
        }
        if ($city != "") {
            $this->isAdvanced = true;
            $this->city = $city;
        }
        $this->isActive = true;
    }

    /**
     * Set the locality
     * @param $locality The locality
     * @param bool $trim Should the locality be trimmed?
     */
    public function setLocality($locality, $trim = true)
    {
        if ($trim) {
            $locality = htmlspecialchars(trim($locality));
        }
        if ($locality != "") {
            $this->isAdvanced = true;
            $this->locality = $locality;
        }
        $this->isActive = true;
    }

    /**
     * Set the facet selected continent
     * @param $continent The continent
     * @param bool $trim Should it be trimmed?
     */
    public function setFacetContinent($continent, $trim = true)
    {
        if ($trim) {
            $continent = htmlspecialchars(trim($continent));
        }
        if ($continent != "" && $continent != "All") {
            $this->isAdvanced = true;
        }
        $this->facet_continent = $continent;
        $this->isActive = true;
    }

    /**
     * Set the facet selected country
     * @param $country The country
     * @param bool $trim Should it be trimmed?
     */
    public function setFacetCountry($country, $trim = true)
    {
        if ($trim) {
            $country = htmlspecialchars(trim($country));
        }
        if ($country != "") {
            $this->isAdvanced = true;
            $this->facet_country = $country;
        }
        $this->isActive = true;
    }

    /**
     * Set the facet selected region
     * @param $region The region
     * @param bool $trim Should it be trimmed?
     */
    public function setFacetRegion($region, $trim = true)
    {
        if ($trim) {
            $region = htmlspecialchars(trim($region));
        }
        if ($region != "") {
            $this->isAdvanced = true;
            $this->facet_region = $region;
        }
        $this->isActive = true;
    }

    /**
     * Set the facet selected city
     * @param $city The city
     * @param bool $trim Should it be trimmed?
     */
    public function setFacetCity($city, $trim = true)
    {
        if ($trim) {
            $city = htmlspecialchars(trim($city));
        }
        if ($city != "") {
            $this->isAdvanced = true;
            $this->facet_city = $city;
        }
        $this->isActive = true;
    }

    /**
     * Set the facet selected locality
     * @param $locality The locality
     * @param bool $trim Should it be trimmed?
     */
    public function setFacetLocality($locality, $trim = true)
    {
        if ($trim) {
            $locality = htmlspecialchars(trim($locality));
        }
        if ($locality != "") {
            $this->isAdvanced = true;
            $this->facet_locality = $locality;
        }
        $this->isActive = true;
    }

    /**
     * Set the culture
     * @param $culture The culture
     * @param bool $trim Should it be trimmed?
     */
    public function setCulture($culture, $trim = true)
    {
        if ($trim) {
            $culture = htmlspecialchars(trim($culture));
        }
        if ($culture != "") {
            $this->isAdvanced = true;
            $this->culture = $culture;
        }
        $this->isActive = true;
    }

    /**
     * Set the credit line/dedication
     * @param $creditline The credit line/dedication
     * @param bool $trim Should it be trimmed?
     */
    public function setCreditLine($creditline, $trim = true)
    {
        if ($trim) {
            $creditline = htmlspecialchars(trim($creditline));
        }
        if ($creditline != "") {
            $this->isAdvanced = true;
            $this->creditLine = $creditline;
        }
        $this->isActive = true;
    }

    /**
     * Set the category
     * @param $cat The category
     * @param bool $trim Should it be trimmed?
     */
    public function setCategory($cat, $trim = true)
    {
        if ($trim) {
            $cat = htmlspecialchars(trim($cat));
        }
        if ($cat != "" && $cat != "All") {
            $this->isAdvanced = true;
        }
        $this->category = $cat;
        $this->isActive = true;
    }

    /**
     * Set the classification
     * @param $class The classification
     * @param bool $trim Should it be trimmed?
     */
    public function setClassification($class, $trim = true)
    {
        if ($trim) {
            $class = htmlspecialchars(trim($class));
        }
        if ($class != "" && $class != "All") {
            $this->isAdvanced = true;
        }
        $this->classification = $class;
        $this->isActive = true;
    }

    /**
     * Set the sub-classification
     * @param $subclass The sub-classification
     * @param bool $trim Should it be trimmed?
     */
    public function setSubClassification($subclass, $trim = true)
    {
        if ($trim) {
            $subclass = htmlspecialchars(trim($subclass));
        }
        if ($subclass != "" && $subclass != "All") {
            $this->isAdvanced = true;
        }
        $this->subclassification = $subclass;
        $this->isActive = true;
    }

    /**
     * Set the material
     * @param $mat The material
     * @param bool $trim Should it be trimmed?
     */
    public function setMaterial($mat, $trim = true)
    {
        if ($trim) {
            $mat = htmlspecialchars(trim($mat));
        }
        if ($mat != "") {
            $this->isAdvanced = true;
        }
        $this->material = $mat;
        $this->isActive = true;
    }

    /**
     * Set the manufacturing process
     * @param $man The manufacturing process
     * @param bool $trim Should it be trimmed?
     */
    public function setManufacturingProcess($man, $trim = true)
    {
        if ($trim) {
            $man = htmlspecialchars(trim($man));
        }
        if ($man != "") {
            $this->isAdvanced = true;
        }
        $this->manufacturingProcess = $man;
        $this->isActive = true;
    }

    /**
     * Set the sort parameter (like 'relevance', 'name', 'popularity'..)
     * @param $sParam The parameter
     */
    public function setSortParameter($sParam)
    {
        if (htmlspecialchars(trim($sParam)) != "") {
            $this->sortParam = htmlspecialchars(trim($sParam));
        }
        $this->isActive = true;
    }

    /**
     * Check if the request is advanced
     * @return bool
     */
    public function isAdvanced()
    {
        return $this->isAdvanced;
    }

    /**
     * Check if the request is set for exact accession number search
     * @return bool
     */
    public function isAccNumExact()
    {
        return $this->accnumExact;
    }

    /**
     * Get the query
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get the with images flag
     * @return bool
     */
    public function getWithImages()
    {
        return $this->withImages;
    }

    /**
     * Get the on display flag
     * @return bool
     */
    public function getOnDisplay()
    {
        return $this->onDisplay;
    }

    /**
     * Get the gallery
     * @return string
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Get the name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the artifact ID
     * @return int
     */
    public function getAID()
    {
        return $this->aid;
    }

    /**
     * Get the accession number
     * @return string
     */
    public function getAccessionNumber()
    {
        return $this->accessionNumber;
    }

    /**
     * Get the working set
     * @return string
     */
    public function getWorkingSet()
    {
        return $this->workingSet;
    }

    /**
     * Get the date
     * @return string
     */
    public function getDate()
    {
        return $this->_date;
    }

    /**
     * Get the continent
     * @return string
     */
    public function getContinent()
    {
        return $this->continent;
    }

    /**
     * Get the country
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Get the region
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Get the city
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Get the locality
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Get the selected facet continent
     * @return string
     */
    public function getFacetContinent()
    {
        return $this->facet_continent;
    }

    /**
     * Get the selected facet country
     * @return string
     */
    public function getFacetCountry()
    {
        return $this->facet_country;
    }

    /**
     * Get the selected facet region
     * @return string
     */
    public function getFacetRegion()
    {
        return $this->facet_region;
    }

    /**
     * Get the selected facet city
     * @return string
     */
    public function getFacetCity()
    {
        return $this->facet_city;
    }

    /**
     * Get the selected facet locality
     * @return string
     */
    public function getFacetLocality()
    {
        return $this->facet_locality;
    }

    /**
     * Get the culture
     * @return string
     */
    public function getCulture()
    {
        return $this->culture;
    }

    /**
     * Get the credit line
     * @return string
     */
    public function getCreditLine()
    {
        return $this->creditLine;
    }

    /**
     * Get the category
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Get the classification
     * @return string
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * Get the sub-classification
     * @return string
     */
    public function getSubClassification()
    {
        return $this->subclassification;
    }

    /**
     * Get the material
     * @return string
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * Get the manufacturing process
     * @return string
     */
    public function getManufacturingProcess()
    {
        return $this->manufacturingProcess;
    }

    /**
     * Get the with Hi-res images flag
     * @return bool
     */
    public function getWithHiResImages()
    {
        return $this->withHiResImages;
    }

    /**
     * Get the starting record number
     * @return int
     */
    public function getStart()
    {
        return $this->startingRecord;
    }

    /**
     * Get the sorting parameter
     * @return string
     */
    public function getSortParameter()
    {
        return $this->sortParam;
    }

    /**
     * Get the reversed query (excludes turned into includes)
     * @return string
     */
    public function getReversedQuery()
    {
        return preg_replace('/[-]([^\s\-]+)|(["][^"\-]*["])/', '$1', $this->getQuery());
    }

    /**
     * Clears the search request to a blank search (browse)
     */
    public function clear()
    {
        $this->query = "";
        $this->withImages = false;
        $this->withHiResImages = false;
        $this->onDisplay = false;
        $this->gallery = "";
        $this->isAdvanced = false;
        $this->name = "";
        $this->accessionNumber = "";
        $this->continent = "";
        $this->country = "";
        $this->region = "";
        $this->city = "";
        $this->locality = "";
        $this->culture = "";
        $this->creditLine = "";
        $this->category = "";
        $this->classification = "";
        $this->subclassification = "";
        $this->material = "";
        $this->manufacturingProcess = "";
        $this->workingSet = "";
        $this->sortParam = "relevance";
        $this->startingRecord = 0;
        $this->facet_continent = "";
        $this->facet_country = "";
        $this->facet_region = "";
        $this->facet_city = "";
        $this->facet_locality = "";
    }
}

?>

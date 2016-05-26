<?php

require_once("searchRequest.class.php");

/**
 * Class artifact
 *
 * Holds data belonging to a single artifact.
 *
 * @package CollectionsSearch
 * @author Michael Robinson
 */
class artifact
{
    /**
     * Specified unit of dimension measurement
     */
    const DIMENSION_UNIT = " cm";
    /**
     * Specified unit of weight measurement
     */
    const WEIGHT_UNIT = " g";


    const SEARCH_CONTEXT = 0;
    const SIDEBAR_CONTEXT = 1;
    const TILE_CONTEXT = 2;

    /**
     * @var int
     */
    private $ID = 0;
    /**
     * @var string
     */
    private $accessionNumber = "N/A";
    /**
     * @var string
     */
    private $name = "N/A";
    /**
     * @var string
     */
    private $continent = "N/A";
    /**
     * @var string
     */
    private $country = "N/A";
    /**
     * @var string
     */
    private $region = "N/A";
    /**
     * @var string
     */
    private $city = "N/A";
    /**
     * @var string
     */
    private $locality = "N/A";
    /**
     * @var string
     */
    private $nomenCategory = "N/A";
    /**
     * @var string
     */
    private $nomenClassification = "N/A";
    /**
     * @var string
     */
    private $nomenSubClassification = "N/A";
    /**
     * @var string
     */
    private $sec_nomenCategory = "N/A";
    /**
     * @var string
     */
    private $sec_nomenClassification = "N/A";
    /**
     * @var string
     */
    private $sec_nomenSubClassification = "N/A";
    /**
     * @var string
     */
    private $period = "N/A";
    /**
     * @var string
     */
    private $periodDate = "N/A";
    /**
     * @var string
     */
    private $visualDescription = "N/A";
    /**
     * @var string
     */
    private $religion = "N/A";
    /**
     * @var string
     */
    private $culture = "N/A";
    /**
     * @var float
     */
    private $dimension1 = 0.0;
    /**
     * @var float
     */
    private $dimension2 = 0.0;
    /**
     * @var float
     */
    private $dimension3 = 0.0;
    /**
     * @var string
     */
    private $dimensionType1 = "N/A";
    /**
     * @var string
     */
    private $dimensionType2 = "N/A";
    /**
     * @var string
     */
    private $dimensionType3 = "N/A";
    /**
     * @var string
     */
    private $materials = "N/A";
    /**
     * @var string
     */
    private $manufacturingProcess = "N/A";
    /**
     * @var string
     */
    private $measuringRemarks = "N/A";
    /**
     * @var int
     */
    private $weight = 0;
    /**
     * @var string
     */
    private $munsellColorInfo = "N/A";
    /**
     * @var string
     */
    private $reproduction = "N/A";
    /**
     * @var string
     */
    private $reproductionNotes = "N/A";
    /**
     * @var string
     */
    private $publishedDescription = "N/A";
    /**
     * @var string
     */
    private $scholarlyNotes = "N/A";
    /**
     * @var string
     */
    private $bibliography = "N/A";
    /**
     * @var string
     */
    private $comparanda = "N/A";
    /**
     * @var string
     */
    private $exhibitLabel = "N/A";
    /**
     * @var string
     */
    private $artist = "N/A";
    /**
     * @var string
     */
    private $location = "N/A";
    /**
     * @var string
     */
    private $location2 = "N/A";
    /**
     * @var bool
     */
    private $onDisplay = false;
    /**
     * @var string
     */
    private $archaeologicalData = "N/A";
    /**
     * @var string
     */
    private $creditLine = "N/A";
    /**
     * @var string
     */
    private $provenance = "N/A";
    /**
     * @var string
     */
    private $museumDedication = "N/A";
    /**
     * @var string
     */
    private $publicDescription = "N/A";
    /**
     * @var string
     */
    private $workingSet5 = "";
    /**
     * @var string
     */
    private $imageSource = "";
    /**
     * @var string
     */
    private $CMMecMA = "";
    /**
     * @var string
     */
    private $webPrivate = "";
    /**
     * @var bool
     */
    private $hasSecondaryNomen = false;
    /**
     * @var int
     */
    private $viewCount = 0;

    /**
     * @var array
     */
    private $hiresImages = array();
    /**
     * @var string
     */
    private $thumbImage = "";

    /**
     * @var int
     */
    private $relevance = 0;

    public function __construct($row)
    {
        //Construct this artifact from an SQL row
        $this->ID = $row['aid'];
        if (trim($row['accession number']) != "") {
            $this->accessionNumber = trim($row['accession number']);
        }
        if (trim($row['name']) != "") {
            $this->name = trim($row['name']);
        }
        if (trim($row['continent']) != "") {
            $this->continent = trim($row['continent']);
        }
        if (trim($row['country']) != "") {
            $this->country = trim($row['country']);
        }
        if (trim($row['region']) != "") {
            $this->region = trim($row['region']);
        }
        if (trim($row['city']) != "") {
            $this->city = trim($row['city']);
        }
        if (trim($row['locality']) != "") {
            $this->locality = trim($row['locality']);
        }
        if (trim($row['cat1']) != "") {
            $this->nomenCategory = trim($row['cat1']);
        }
        if (trim($row['class1']) != "") {
            $this->nomenClassification = trim($row['class1']);
        }
        if (trim($row['subclass1']) != "") {
            $this->nomenSubClassification = trim($row['subclass1']);
        }
        if (trim($row['nomen2_1']) > 0) {
            $this->hasSecondaryNomen = true;
        }
        if (trim($row['cat2']) != "") {
            $this->sec_nomenCategory = trim($row['cat2']);
        }
        if (trim($row['class2']) != "") {
            $this->sec_nomenClassification = trim($row['class2']);
        }
        if (trim($row['subclass2']) != "") {
            $this->sec_nomenSubClassification = trim($row['subclass2']);
        }
        if (trim($row['period 1']) != "") {
            $this->period = trim($row['period 1']);
        }
        if (trim($row['period 3 date']) != "") {
            $this->periodDate = trim($row['period 3 date']);
        }
        if (trim($row['visual description']) != "") {
            $this->visualDescription = trim($row['visual description']);
        }
        if (trim($row['religion 1']) != "") {
            $this->religion = trim($row['religion 1']);
        }
        if (trim($row['culture']) != "") {
            $this->culture = trim($row['culture']);
        }
        $this->dimension1 = $row['dimen 1 number'];
        $this->dimension2 = $row['dimen 2 number'];
        $this->dimension3 = $row['dimen 3 number'];
        if (trim($row['dimen 1 type']) != "") {
            $this->dimensionType1 = trim($row['dimen 1 type']);
        }
        if (trim($row['dimen 2 type']) != "") {
            $this->dimensionType2 = trim($row['dimen 2 type']);
        }
        if (trim($row['dimen 3 type']) != "") {
            $this->dimensionType3 = trim($row['dimen 3 type']);
        }
        if (trim($row['materials 2']) != "") {
            $this->materials = trim($row['materials 2']);
        }
        if (trim($row['manufacturing processes 2']) != "") {
            $this->manufacturingProcess = trim($row['manufacturing processes 2']);
        }
        if (trim($row['measuring remarks']) != "") {
            $this->measuringRemarks = trim($row['measuring remarks']);
        }
        $this->weight = $row['weight'];
        if (trim($row['munsell color information']) != "") {
            $this->munsellColorInfo = trim($row['munsell color information']);
        }
        if (trim($row['reproduction']) != "") {
            $this->reproduction = trim($row['reproduction']);
        }
        if (trim($row['reproduction notes']) != "") {
            $this->reproductionNotes = trim($row['reproduction notes']);
        }
        if (trim($row['published description']) != "") {
            $this->publishedDescription = trim($row['published description']);
        }
        if (trim($row['scholarly notes']) != "") {
            $this->scholarlyNotes = trim($row['scholarly notes']);
        }
        if (trim($row['bibliography']) != "") {
            $this->bibliography = nl2br(trim($row['bibliography']));
        }
        if (trim($row['comparanda']) != "") {
            $this->comparanda = nl2br(trim($row['comparanda']));
        }
        if (trim($row['exhibit label']) != "") {
            $this->exhibitLabel = trim($row['exhibit label']);
        }
        if (trim($row['artist']) != "") {
            $this->artist = trim($row['artist']);
        }
        if (trim($row['spurlock loc 2']) != "") {
            $this->location2 = trim($row['spurlock loc 2']);
        }
        if (trim($row['spurlock loc 3']) != "") {
            $this->location = trim($row['spurlock loc 3']);
        }
        if (trim($row['archaeological data']) != "") {
            $this->archaeologicalData = trim($row['archaeological data']);
        }
        if (trim($row['credit line']) != "") {
            $this->creditLine = trim($row['credit line']);
        }
        if (trim($row['provenance']) != "") {
            $this->provenance = trim($row['provenance']);
        }
        if (trim($row['museum dedication']) != "") {
            $this->museumDedication = trim($row['museum dedication']);
        }
        if (trim($row['on_display']) != "") {
            $this->onDisplay = (boolean)trim($row['on_display']);
        }
        if (trim($row['public description']) != "") {
            $this->publicDescription = trim($row['public description']);
        }
        if (trim($row['working set 5 wb']) != "") {
            $this->workingSet5 = trim($row['working set 5 wb']);
        }
        if (trim($row['image source']) != "") {
            $this->imageSource = trim($row['image source']);
        }
        if (trim($row['cm mec ma']) != "") {
            $this->CMMecMA = trim($row['cm mec ma']);
        }
        if (trim($row['webprivate']) != "") {
            $this->webPrivate = trim($row['webprivate']);
        }
        $this->viewCount = $row['view_cnt'];

        //If high res images are not suppressed, then find them
        if (strpos($this->webPrivate, "Hi-Res") === FALSE) {
            $this->hiresImages = $this->findODPs();
        }

        $this->thumbImage = $this->findThumb();

        if (array_key_exists('relevance', $row)) {
            $this->relevance = $row['relevance'];
        }
    }

    /**
     * Get this artifact's relevance
     * @return int The artifact's relevance
     */
    public function getRelevance()
    {
        return $this->relevance;
    }

    /**
     * Get this artifact's ID
     * @return int The artifact's ID
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * Get this artifact's view count
     * @return int The artifact's view count
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * Check if this artifact is on display
     * @return bool True if the artifact is on display, False otherwise
     */
    public function isOnDisplay()
    {
        return $this->onDisplay;
    }

    /**
     * Check if this artifact is in the virtual tour
     * @return bool True if the artifact is in the virtual tour, False otherwise
     */
    public function isVirtualTour()
    {
        $loc2 = $this->location2;
        if ($loc2 === "AMN" || $loc2 === "AMS" || $loc2 === "MED") {
            return true;
        }
        return false;
    }

    /**
     * Get this artifact's accession number
     * @return string The artifact's accession number
     */
    public function getAccessionNumber()
    {
        return $this->accessionNumber;
    }

    /**
     * Get this artifact's name
     * @return string The artifact's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get this artifact's geography information as a semi-colon delimited string
     * @return string The artifact's geography information
     */
    public function getGeography()
    {
        $geo = array();
        $geo[] = $this->continent;
        $geo[] = $this->country;
        $geo[] = $this->region;
        $geo[] = $this->city;
        $geo[] = $this->locality;
        $result = $geo[0];
        foreach (array_slice($geo, 1) as $geoitem) {
            $geoitem = trim($geoitem);
            if ($geoitem != "N/A") {
                $result .= '; ' . $geoitem;
            }
        }
        return $result;
    }

    /**
     * Get this artifact's geography information as an HTML anchor
     * @return string The artifact's geography information
     */
    public function getGeographyLinked()
    {
        $geo = array();
        $searchRequest = new searchRequest();
        $searchRequest->clear();
        $searchRequest->setContinent($this->continent, FALSE);
        $geo[] = '<a href="' . $searchRequest->getURL() . '" title="Other objects from ' . $this->continent . '">' . $this->continent . '</a>';
        $searchRequest->clear();
        $searchRequest->setCountry($this->country, FALSE);
        $geo[] = '<a href="' . $searchRequest->getURL() . '" title="Other objects from ' . $this->country . '">' . $this->country . '</a>';
        $searchRequest->clear();
        $searchRequest->setRegion($this->region, FALSE);
        $geo[] = '<a href="' . $searchRequest->getURL() . '" title="Other objects from ' . $this->region . '">' . $this->region . '</a>';
        $searchRequest->clear();
        $searchRequest->setCity($this->city, FALSE);
        $geo[] = '<a href="' . $searchRequest->getURL() . '" title="Other objects from ' . $this->city . '">' . $this->city . '</a>';
        $searchRequest->clear();
        $searchRequest->setLocality($this->locality, FALSE);
        $geo[] = '<a href="' . $searchRequest->getURL() . '" title="Other objects from ' . $this->locality . '">' . $this->locality . '</a>';
        $result = $geo[0];
        foreach (array_slice($geo, 1) as $geoitem) {
            $geoitem = trim($geoitem);
            if (stripos($geoitem, '>N/A<') === FALSE) {
                $result .= '; ' . $geoitem;
            }
        }
        return $result;
    }

    /**
     * Get this artifact's continent
     * @return string The artifact's continent
     */
    public function getContinent()
    {
        return $this->continent;
    }

    /**
     * Get this artifact's country
     * @return string The artifact's country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Get this artifact's region
     * @return string The artifact's region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Get this artifact's city
     * @return string The artifact's city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Get this artifact's locality
     * @return string The artifact's locality
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Get this artifact's nomenclature category
     * @return string The artifact's category
     */
    public function getNomenclatureCategory()
    {
        return $this->nomenCategory;
    }

    /**
     * Get this artifact's nomenclature classification
     * @return string The artifact's classification
     */
    public function getNomenclatureClassification()
    {
        return $this->nomenClassification;
    }

    /**
     * Get this artifact's nomenclature sub-classification
     * @return string The artifact's sub-classification
     */
    public function getNomenclatureSubClassification()
    {
        return $this->nomenSubClassification;
    }

    /**
     * Get this artifact's secondary nomenclature category
     * @return string The artifact's secondary category
     */
    public function getSecondaryNomenclatureCategory()
    {
        return $this->sec_nomenCategory;
    }

    /**
     * Get this artifact's secondary nomenclature classification
     * @return string The artifact's secondary classification
     */
    public function getSecondaryNomenclatureClassification()
    {
        return $this->sec_nomenClassification;
    }

    /**
     * Get this artifact's secondary nomenclature sub-classification
     * @return string The artifact's secondary sub-classification
     */
    public function getSecondaryNomenclatureSubClassification()
    {
        return $this->sec_nomenSubClassification;
    }

    /**
     * Check if this artifact has a secondary nomenclature
     * @return bool True if the artifact has a secondary nomenclature, False otherwise
     */
    public function hasSecondaryNomenclature()
    {
        return $this->hasSecondaryNomen;
    }

    /**
     * Get this artifact's nomenclature as a string, or if $linked is True then as a HTML anchor
     * @param bool $linked If set to true, then a HTML anchor is returned instead of a basic string
     * @return string The artifact's nomenclature
     */
    public function getClassificationText($linked = false)
    {
        if ($linked) {
            return $this->getClassificationTextLinked();
        }
        $result = "";
        if ($this->nomenCategory != "N/A") {
            $result .= $this->nomenCategory;
        }
        if ($this->nomenClassification != "N/A") {
            $result .= ' > ' . $this->nomenClassification;
        }
        if ($this->nomenSubClassification != "N/A") {
            $result .= ' > ' . $this->nomenSubClassification;
        }
        if ($result == "") {
            return "N/A";
        }
        return $result;
    }

    /**
     * Get this artifact's nomenclature as a HTML anchor
     * @return string The artifact's nomenclature
     */
    private function getClassificationTextLinked()
    {
        $result = "";
        if ($this->nomenCategory != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setCategory($this->nomenCategory, FALSE);
            $result .= '<a href="' . $searchRequest->getURL() . '" title="Other ' . constructNomenclatureTitle($this->nomenCategory) . '">' . $this->nomenCategory . '</a>';
        }
        if ($this->nomenClassification != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setClassification($this->nomenClassification, FALSE);
            $result .= ' > <a href="' . $searchRequest->getURL() . '" title="Other ' . constructNomenclatureTitle($this->nomenClassification) . '">' . $this->nomenClassification . '</a>';
        }
        if ($this->nomenSubClassification != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setSubClassification($this->nomenSubClassification, FALSE);
            $result .= ' > <a href="' . $searchRequest->getURL() . '" title="Other ' . constructNomenclatureTitle($this->nomenSubClassification) . '">' . $this->nomenSubClassification . '</a>';
        }
        if ($result == "") {
            return "N/A";
        }
        return $result;
    }

    /**
     * Get this artifact's secondary nomenclature as a string, or if $linked is True then as a HTML anchor
     * @param bool $linked If set to true, then a HTML anchor is returned instead of a basic string
     * @return string The artifact's secondary nomenclature
     */
    public function getSecondaryClassificationText($linked = false)
    {
        if ($linked) {
            return $this->getSecondaryClassificationTextLinked();
        }
        $result = "";
        if ($this->sec_nomenCategory != "N/A") {
            $result .= $this->sec_nomenCategory;
        }
        if ($this->sec_nomenClassification != "N/A") {
            $result .= ' > ' . $this->sec_nomenClassification;
        }
        if ($this->sec_nomenSubClassification != "N/A") {
            $result .= ' > ' . $this->sec_nomenSubClassification;
        }
        if ($result == "") {
            return "N/A";
        }
        return $result;
    }

    /**
     * Get this artifact's secondary nomenclature as a HTML anchor
     * @return string The artifact's secondary nomenclature
     */
    private function getSecondaryClassificationTextLinked()
    {
        $result = "";
        if ($this->sec_nomenCategory != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setCategory($this->sec_nomenCategory, FALSE);
            $result .= '<a href="' . $searchRequest->getURL() . '" title="Other ' . constructNomenclatureTitle($this->sec_nomenCategory) . '">' . $this->sec_nomenCategory . '</a>';
        }
        if ($this->sec_nomenClassification != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setClassification($this->sec_nomenClassification, FALSE);
            $result .= ' > <a href="' . $searchRequest->getURL() . '" title="Other ' . constructNomenclatureTitle($this->sec_nomenClassification) . '">' . $this->sec_nomenClassification . '</a>';
        }
        if ($this->sec_nomenSubClassification != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setSubClassification($this->sec_nomenSubClassification, FALSE);
            $result .= ' > <a href="' . $searchRequest->getURL() . '" title="Other ' . constructNomenclatureTitle($this->sec_nomenSubClassification) . '">' . $this->sec_nomenSubClassification . '</a>';
        }
        if ($result == "") {
            return "N/A";
        }
        return $result;
    }

    /**
     * Get this artifact's period
     * @return string The artifact's period
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Get this artifact's period and date as a semi-colon delimited string
     * @return string The artifact's period and date
     */
    public function getPeriodText()
    {
        if ($this->period == "N/A" && $this->periodDate != "N/A") {
            return $this->periodDate;
        }
        if ($this->period != "N/A" && $this->periodDate == "N/A") {
            return $this->period;
        }
        if ($this->period != "N/A" && $this->periodDate != "N/A") {
            return $this->period . '; ' . $this->periodDate;
        }
        return "N/A";
    }

    /**
     * Get this artifact's date
     * @return string The artifact's date
     */
    public function getPeriodDate()
    {
        return $this->peroidDate;
    }

    /**
     * Get this artifact's visual description
     * @return string The artifact's visual description
     */
    public function getVisualDescription()
    {
        return $this->visualDescription;
    }

    /**
     * Get this artifact's religious designation
     * @return string The artifact's religious designation
     */
    public function getReligion()
    {
        return $this->religion;
    }

    /**
     * Get this artifact's culture as a string, or if $linked is True, as a HTML anchor
     * @param bool $linked If set to True, then a HTML anchor is returned instead of a basic string
     * @return string The artifact's culture
     */
    public function getCulture($linked = false)
    {
        if ($linked) {
            return $this->getCultureLinked();
        }
        return $this->culture;
    }

    /**
     * Get this artifact's culture as a HTML anchor
     * @return string The artifact's culture
     */
    private function getCultureLinked()
    {
        $searchRequest = new searchRequest();
        $searchRequest->clear();
        $searchRequest->setCulture($this->culture, FALSE);
        if ($this->culture !== 'N/A') {
            return '<a href="' . $searchRequest->getURL() . '" title="Other objects with culture: ' . $this->culture . '">' . $this->culture . '</a>';
        }
        return 'N/A';
    }

    /**
     * Get this artifact's first dimension
     * @return string The artifact's first dimension
     */
    public function getDimension1()
    {
        return $this->dimension1 . self::DIMENSION_UNIT;
    }

    /**
     * Get this artifact's second dimension
     * @return string The artifact's second dimension
     */
    public function getDimension2()
    {
        return $this->dimension2 . self::DIMENSION_UNIT;
    }

    /**
     * Get this artifact's third dimension
     * @return string The artifact's third dimension
     */
    public function getDimension3()
    {
        return $this->dimension3 . self::DIMENSION_UNIT;
    }

    /**
     * Get this artifact's first dimension type
     * @return string The artifact's first dimension type
     */
    public function getDimensionType1()
    {
        return $this->dimensionType1;
    }

    /**
     * Get this artifact's second dimension type
     * @return string The artifact's second dimension type
     */
    public function getDimensionType2()
    {
        return $this->dimensionType2;
    }

    /**
     * Get this artifact's third dimension type
     * @return string The artifact's third dimension type
     */
    public function getDimensionType3()
    {
        return $this->dimensionType3;
    }

    /**
     * Get this artifact's dimensions as a combined string
     * @return string The artifact's dimensions
     */
    public function getDimensions()
    {
        return round($this->dimension1, 1) . "x" . round($this->dimension2, 1) . "x" . round($this->dimension3, 1) . ' ' . self::DIMENSION_UNIT;
    }

    /**
     * Get this artifact's dimensions and weight as a combined string
     * @return string The artifact's measurements and weight
     */
    public function getMeasurements()
    {
        return $this->getDimensions() . ' (' . round($this->weight) . ' g)';
    }

    /**
     * Get this artifact's materials as a string, or if $linked is True, as a HTML anchor
     * @param bool $linked If set to True, then a HTML anchor is returned instead of a basic string
     * @return string The artifact's materials
     */
    public function getMaterials($linked = false)
    {
        if ($linked) {
            return $this->getMaterialsLinked();
        }
        return $this->materials;
    }

    /**
     * Get this artifact's materials as a HTML anchor
     * @return string The artifact's materials
     */
    private function getMaterialsLinked()
    {
        //Parse into individual materials and link each one
        $materials = explode(', ', $this->materials);
        $linkedMaterials = array();
        $searchRequest = new searchRequest();
        foreach ($materials as $material) {
            $searchRequest->clear();
            $searchRequest->setMaterial($material, FALSE);
            $linkedMaterials[] = '<a href="' . $searchRequest->getURL() . '" title="Other objects with material type: ' . $material . '">' . $material . '</a>';
        }
        return implode(' | ', $linkedMaterials);
    }

    /**
     * Get this artifact's manufacturing processes as a string, or if $linked is True, as a HTML anchor
     * @param bool $linked If set to True, then a HTML anchor is returned instead of a basic string
     * @return string The artifact's manufacturing processes
     */
    public function getManufacturingProcess($linked = false)
    {
        if ($linked) {
            return $this->getManufacturingProcessLinked();
        }
        return $this->manufacturingProcess;
    }

    /**
     * Get this artifact's manufacturing processes as a HTML anchor
     * @return string The artifact's manufacturing processes
     */
    private function getManufacturingProcessLinked()
    {
        //Parse into individual processes and link each one
        $processes = explode(', ', $this->manufacturingProcess);
        $linkedProcesses = array();
        $searchRequest = new searchRequest();
        foreach ($processes as $process) {
            $searchRequest->clear();
            $searchRequest->setManufacturingProcess($process, FALSE);
            $linkedProcesses[] = '<a href="' . $searchRequest->getURL() . '" title="Other objects with manufacturing process: ' . $process . '">' . $process . '</a>';
        }
        return implode(' | ', $linkedProcesses);
    }

    /**
     * Get this artifact's measuring remarks
     * @return string The artifact's measuring remarks
     */
    public function getMeasuringRemarks()
    {
        return $this->measuringRemarks;
    }

    /**
     * Get this artifact's weight
     * @return string The artifact's weight
     */
    public function getWeight()
    {
        return $this->weight . ' g';
    }

    /**
     * Get this artifact's Munsell color information
     * @return string The artifact's Munsell color information
     */
    public function getMunsellColorInformation()
    {
        return $this->munsellColorInfo;
    }

    /**
     * Get this artifact's reproduction flag (Yes/No)
     * @return string The artifact's reproduction flag
     */
    public function getReproduction()
    {
        return $this->reproduction;
    }

    /**
     * Get this artifact's reproduction notes
     * @return string The artifact's reproduction notes
     */
    public function getReproductionNotes()
    {
        return $this->reproductionNotes;
    }

    /**
     * Get this artifact's published description
     * @return string The artifact's published description
     */
    public function getPublishedDescription()
    {
        return $this->publishedDescription;
    }

    /**
     * Get this artifact's scholarly notes
     * @return string The artifact's scholarly notes
     */
    public function getScholarlyNotes()
    {
        return $this->scholarlyNotes;
    }

    /**
     * Get this artifact's bibliography
     * @return string The artifact's bibliography
     */
    public function getBibliography()
    {
        return $this->bibliography;
    }

    /**
     * Get this artifact's comparanda
     * @return string The artifact's comparanda
     */
    public function getComparanda()
    {
        return $this->comparanda;
    }

    /**
     * Get this artifact's exhibit label
     * @return string The artifact's exhibit label
     */
    public function getExhibitLabel()
    {
        return $this->exhibitLabel;
    }

    /**
     * Get this artifact's artist
     * @return string The artifact's artist
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * Get this artifact's location
     * @return string The artifact's location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Get this artifact's secondary location
     * @return string The artifact's secondary location
     */
    public function getLocation2()
    {
        return $this->location2;
    }

    /**
     * Get this artifact's archaeological data
     * @return string The artifact's archaeological data
     */
    public function getArchaeologicalData()
    {
        return $this->archaeologicalData;
    }

    /**
     * Get this artifact's credit/dedication
     * @return string The artifact's credit/dedication
     */
    public function getCreditLine()
    {
        return $this->creditLine;
    }

    /**
     * Get this artifact's provenance
     * @return string The artifact's provenance
     */
    public function getProvenance()
    {
        return $this->provenance;
    }

    /**
     * Get this artifact's museum dedication
     * @return string The artifact's museum dedication
     */
    public function getMuseumDedication()
    {
        return $this->museumDedication;
    }

    /**
     * Get this artifact's credit/dedication as a string, or if $linked is True, as a HTML anchor
     * @param bool $linked If set to True, then a HTML anchor is returned instead of a basic string
     * @return string The artifact's credit/dedication
     */
    public function getCreditDedication($linked = false)
    {
        if ($linked) {
            return $this->getCreditDedicationLinked();
        }
        $parts = array();
        if ($this->creditLine != "N/A") {
            $parts[] = $this->creditLine;
        }
        if ($this->museumDedication != "N/A") {
            $parts[] = $this->museumDedication;
        }
        if (count($parts) > 0) {
            return implode(", ", $parts);
        }
        return "N/A";
    }

    /**
     * Get this artifact's credit/dedication as a HTML anchor
     * @return string The artifact's credit/dedication
     */
    private function getCreditDedicationLinked()
    {
        $parts = array();
        if ($this->creditLine != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setCreditLine($this->creditLine, FALSE);
            $parts[] = '<a href="' . $searchRequest->getURL() . '" title="Other ' . constructCreditTitle($this->creditLine) . '">' . $this->creditLine . '</a>';
        }
        if ($this->museumDedication != "N/A") {
            $parts[] = $this->museumDedication;
        }
        if (count($parts) > 0) {
            return implode(", ", $parts);
        }
        return "N/A";
    }

    /**
     * Get this artifact's spurlock status
     * @return string The artifact's spurlock status
     */
    public function getSpurlockStatus()
    {
        return $this->spurlockStatus;
    }

    /**
     * Get this artifact's public description
     * @return string The artifact's public description
     */
    public function getPublicDescription()
    {
        return nl2br($this->publicDescription);
    }

    /**
     * Get this artifact's Hi-Res images
     * @return array The Hi-Res images (links)
     */
    public function getHiResImages()
    {
        return $this->hiresImages;
    }

    /**
     * Get this artifact's thumbnail image
     * @param $context The context you are calling from, provides suppression awareness
     * @return string The link to the artifact's thumbnail image
     */
    public function getThumbImage($context)
    {
        switch ($context) {
            case self::SEARCH_CONTEXT:
                if (strpos($this->webPrivate, 'ThumbSrch') !== FALSE) {
                    return "elements/dina.jpg";
                }
                break;
            case self::SIDEBAR_CONTEXT:
                if (strpos($this->webPrivate, 'ThumbFeat') !== FALSE) {
                    return "elements/dina.jpg";
                }
                break;
            case self::TILE_CONTEXT:
                if (strpos($this->webPrivate, 'ThumbFeat') !== FALSE) {
                    return "elements/dina.jpg";
                }
                break;
            default:
                return $this->thumbImage;
                break;
        }
        return $this->thumbImage;
    }

    /**
     * Get this artifact's thumbnail image link/path
     * @return string The thumbnail image link/path
     */
    private function findThumb()
    {
        $CMMECMA = str_replace(" ", "-", $this->CMMecMA);

        if ($this->imageSource === 'No Image') {
        		return "elements/dina.jpg";}
		else if ($this->imageSource === 'ImagesMNH') {
            if (!file_exists($_SERVER{'DOCUMENT_ROOT'} . "/DBimages/recognition/$this->imageSource/$CMMECMA.jpg")) {
                //if(!$this->fileExists("http://www.spurlock.illinois.edu/DBimages/recognition/$this->imageSource/$CMMECMA.jpg")){
                return "elements/notfound.gif";
            } else {
                return "http://www.spurlock.illinois.edu/DBimages/recognition/$this->imageSource/$CMMECMA.jpg";
            }
        } else {
            if (!file_exists($_SERVER{'DOCUMENT_ROOT'} . "/DBimages/recognition/$this->imageSource/$this->accessionNumber.jpg")) {
                //if(!$this->fileExists("http://www.spurlock.illinois.edu/DBimages/recognition/$this->imageSource/$this->accessionNumber.jpg")){
                return "elements/notfound.gif";
            } else {
                return "http://www.spurlock.illinois.edu/DBimages/recognition/$this->imageSource/$this->accessionNumber.jpg";
            }
        }
    }

    /**
     * Get this artifact's Hi-Res images (ODPs)
     * @return array Array of paths/links to images
     */
    private function findODPs()
    {
        $artYear = substr($this->accessionNumber, 0, 4);
        $artLot = substr($this->accessionNumber, 5, 2);
        $digitalimgs = array();

        if (file_exists($_SERVER{'DOCUMENT_ROOT'} . "/DBimages/orig-digi/$artYear/$artYear.$artLot/")) {
            if (strpos($this->webPrivate, "Hi-Res") === FALSE) {
                $index = 0;
                $dirName = "../DBimages/orig-digi/$artYear/$artYear.$artLot/";
                if ($dir = opendir($dirName)) {
                    while ($filename = readdir($dir)) {
                        if (strpos($filename, $this->accessionNumber) > -1) {
                            $digitalimgs[$index] = "$dirName$filename";
                            $index++;
                        }
                    }
                    closedir($dir);
                }
                if (sizeof($digitalimgs) == 0) {
                    $thousandsDigit = (int)substr($this->accessionNumber, 8, 1);
                    $lot = (int)substr($this->accessionNumber, 8, 4);
                    $lowerRange = $thousandsDigit * 1000;
                    if (($lowerRange != 0) && ($lot % $lowerRange) == 0) {
                        $lowerRange = $lowerRange - 1000;
                    }
                    $lowerRange = $lowerRange + 1;
                    $upperRange = $lowerRange + 999;
                    $dirName = "../DBimages/orig-digi/$artYear/$artYear.$artLot/$lowerRange-$upperRange";
                    if ($dir = opendir($dirName)) {
                        while ($filename = readdir($dir)) {
                            if (strpos($filename, $this->accessionNumber) > -1) {
                                $digitalimgs[$index] = "$dirName/$filename";
                                $index++;
                            }
                        }
                        closedir($dir);
                    }
                }
            }
        }
        return $digitalimgs;
    }
}


?>

<?php

require_once("collectionsBackend.class.php");
require_once("detailsSidebar.class.php");

/**
 * Class randomSidebarFactory
 *
 * Factory class that allows the client to retrieve a random sidebar.
 * Add possible tiles by adding a new entry to the possibilities array and a new private
 * function to the class.
 *
 * @package CollectionsSearch
 * @author Michael Robinson 
 */
class randomSidebarFactory
{

    /**
     * @var array
     */
    private $possibilities = array();
    /**
     * @var detailsSidebar
     */
    private $sidebar = null;
    /**
     * @var artifact
     */
    private $curRecord = null;
    /**
     * @var collectionsBackend
     */
    private $collectionsBackend = null;

    /**
     * The number of artifacts in each sidebar
     */
    const NUM_ARTIFACTS_PER_SIDEBAR = 3;

    /**
     * Should sidebar contents be randomized?
     * @var boolean
     */
    const RANDOMIZE_SIDEBAR_CONTENTS = true;


    public function __construct()
    {
        $this->possibilities['sameBaseAccNum'] = true;

        $this->possibilities['sameCategory'] = true;
        $this->possibilities['sameClassification'] = true;
        $this->possibilities['sameSubClassification'] = true;

        $this->possibilities['sameContinent'] = true;
        $this->possibilities['sameCountry'] = true;
        $this->possibilities['sameRegion'] = true;
        $this->possibilities['sameCity'] = true;
        $this->possibilities['sameLocality'] = true;

        $this->possibilities['sameCulture'] = true;
        $this->possibilities['sameCreditLine'] = true;

        $this->collectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_SIDEBAR);
    }

    /**
     * Get a random sidebar
     * @param $currentRecord The current record, given so that the artifact the user is viewing on details page doesn't also show up in the sidebar
     * @return null|detailsSidebar A sidebar or null if there are none left to get
     */
    public function getRandomSidebar($currentRecord)
    {
        $this->curRecord = $currentRecord;
        $key = array_rand($this->possibilities, 1);
        while (!$this->$key()) {
            unset($this->possibilities[$key]);
            if (count($this->possibilities) < 1) {
                return null;
            }
            $key = array_rand($this->possibilities, 1);
        }
        unset($this->possibilities[$key]);
        return $this->sidebar;
    }

    /**
     * Sidebar to display artifacts with the same accession year
     * @return bool
     */
    private function sameBaseAccNum()
    {
        $searchRequest = new searchRequest();
        $searchRequest->clear();
        $year = substr($this->curRecord->getAccessionNumber(), 0, 4);
        $searchRequest->setAccessionNumber($year);
        if (self::RANDOMIZE_SIDEBAR_CONTENTS) {
            $searchRequest->setSortParameter("rand");
        }
        $this->collectionsBackend->executeRequest($searchRequest, FALSE);
        if ($this->collectionsBackend->getFoundSetCount() > 1) {
            $searchRequest->setSortParameter("relevance");
            $this->sidebar = new detailsSidebar($this->collectionsBackend->getResults(), $this->curRecord, "Other objects acquired in " . $year, $searchRequest->getURL());
            return true;
        }
        return false;
    }

    /**
     * Sidebar to display artifacts with the same category
     * @return bool
     */
    private function sameCategory()
    {
        if ($this->curRecord->getNomenclatureCategory() != "" && $this->curRecord->getNomenclatureCategory() != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setCategory($this->curRecord->getNomenclatureCategory());
            if (self::RANDOMIZE_SIDEBAR_CONTENTS) {
                $searchRequest->setSortParameter("rand");
            }
            $this->collectionsBackend->executeRequest($searchRequest, FALSE);
            if ($this->collectionsBackend->getFoundSetCount() > 1) {
                $searchRequest->setSortParameter("relevance");
                $this->sidebar = new detailsSidebar($this->collectionsBackend->getResults(), $this->curRecord, "Other " . constructNomenclatureTitle($this->curRecord->getNomenclatureCategory()), $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Sidebar to display artifacts with the same classification
     * @return bool
     */
    private function sameClassification()
    {
        if ($this->curRecord->getNomenclatureClassification() != "" && $this->curRecord->getNomenclatureClassification() != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setClassification($this->curRecord->getNomenclatureClassification());
            if (self::RANDOMIZE_SIDEBAR_CONTENTS) {
                $searchRequest->setSortParameter("rand");
            }
            $this->collectionsBackend->executeRequest($searchRequest, FALSE);
            if ($this->collectionsBackend->getFoundSetCount() > 1) {
                $searchRequest->setSortParameter("relevance");
                $this->sidebar = new detailsSidebar($this->collectionsBackend->getResults(), $this->curRecord, "Other " . constructNomenclatureTitle($this->curRecord->getNomenclatureClassification()), $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Sidebar to display artifacts with the same sub-classification
     * @return bool
     */
    private function sameSubClassification()
    {
        if ($this->curRecord->getNomenclatureSubClassification() != "" && $this->curRecord->getNomenclatureSubClassification() != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setSubClassification($this->curRecord->getNomenclatureSubClassification());
            if (self::RANDOMIZE_SIDEBAR_CONTENTS) {
                $searchRequest->setSortParameter("rand");
            }
            $this->collectionsBackend->executeRequest($searchRequest, FALSE);
            if ($this->collectionsBackend->getFoundSetCount() > 1) {
                $searchRequest->setSortParameter("relevance");
                $this->sidebar = new detailsSidebar($this->collectionsBackend->getResults(), $this->curRecord, "Other  " . constructNomenclatureTitle($this->curRecord->getNomenclatureSubClassification()), $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Sidebar to display artifacts with the same continent
     * @return bool
     */
    private function sameContinent()
    {
        if ($this->curRecord->getContinent() != "" && $this->curRecord->getContinent() != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setContinent($this->curRecord->getContinent());
            if (self::RANDOMIZE_SIDEBAR_CONTENTS) {
                $searchRequest->setSortParameter("rand");
            }
            $this->collectionsBackend->executeRequest($searchRequest, FALSE);
            if ($this->collectionsBackend->getFoundSetCount() > 1) {
                $searchRequest->setSortParameter("relevance");
                $this->sidebar = new detailsSidebar($this->collectionsBackend->getResults(), $this->curRecord, "Other objects from " . $this->curRecord->getContinent(), $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Sidebar to display artifacts with the same country
     * @return bool
     */
    private function sameCountry()
    {
        if ($this->curRecord->getCountry() != "" && $this->curRecord->getCountry() != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setCountry($this->curRecord->getCountry());
            if (self::RANDOMIZE_SIDEBAR_CONTENTS) {
                $searchRequest->setSortParameter("rand");
            }
            $this->collectionsBackend->executeRequest($searchRequest, FALSE);
            if ($this->collectionsBackend->getFoundSetCount() > 1) {
                $searchRequest->setSortParameter("relevance");
                $this->sidebar = new detailsSidebar($this->collectionsBackend->getResults(), $this->curRecord, "Other objects from " . $this->curRecord->getCountry(), $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Sidebar to display artifacts with the same region
     * @return bool
     */
    private function sameRegion()
    {
        if ($this->curRecord->getRegion() != "" && $this->curRecord->getRegion() != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setRegion($this->curRecord->getRegion());
            if (self::RANDOMIZE_SIDEBAR_CONTENTS) {
                $searchRequest->setSortParameter("rand");
            }
            $this->collectionsBackend->executeRequest($searchRequest, FALSE);
            if ($this->collectionsBackend->getFoundSetCount() > 1) {
                $searchRequest->setSortParameter("relevance");
                $this->sidebar = new detailsSidebar($this->collectionsBackend->getResults(), $this->curRecord, "Other objects from " . $this->curRecord->getRegion(), $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Sidebar to display artifacts with the same city
     * @return bool
     */
    private function sameCity()
    {
        if ($this->curRecord->getCity() != "" && $this->curRecord->getCity() != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setCity($this->curRecord->getCity());
            if (self::RANDOMIZE_SIDEBAR_CONTENTS) {
                $searchRequest->setSortParameter("rand");
            }
            $this->collectionsBackend->executeRequest($searchRequest, FALSE);
            if ($this->collectionsBackend->getFoundSetCount() > 1) {
                $searchRequest->setSortParameter("relevance");
                $this->sidebar = new detailsSidebar($this->collectionsBackend->getResults(), $this->curRecord, "Other objects from " . $this->curRecord->getCity(), $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Sidebar to display artifacts with the same locality
     * @return bool
     */
    private function sameLocality()
    {
        if ($this->curRecord->getLocality() != "" && $this->curRecord->getLocality() != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setLocality($this->curRecord->getLocality());
            if (self::RANDOMIZE_SIDEBAR_CONTENTS) {
                $searchRequest->setSortParameter("rand");
            }
            $this->collectionsBackend->executeRequest($searchRequest, FALSE);
            if ($this->collectionsBackend->getFoundSetCount() > 1) {
                $searchRequest->setSortParameter("relevance");
                $this->sidebar = new detailsSidebar($this->collectionsBackend->getResults(), $this->curRecord, "Other objects from " . $this->curRecord->getLocality(), $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Sidebar to display artifacts with the same culture
     * @return bool
     */
    private function sameCulture()
    {
        if ($this->curRecord->getCulture() != "" && $this->curRecord->getCulture() != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setCulture($this->curRecord->getCulture());
            if (self::RANDOMIZE_SIDEBAR_CONTENTS) {
                $searchRequest->setSortParameter("rand");
            }
            $this->collectionsBackend->executeRequest($searchRequest, FALSE);
            if ($this->collectionsBackend->getFoundSetCount() > 1) {
                $searchRequest->setSortParameter("relevance");
                $this->sidebar = new detailsSidebar($this->collectionsBackend->getResults(), $this->curRecord, "Other objects with culture: " . $this->curRecord->getCulture(), $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Sidebar to display artifacts with the same credit line/dedication
     * @return bool
     */
    private function sameCreditLine()
    {
        if ($this->curRecord->getCreditLine() != "" && $this->curRecord->getCreditLine() != "N/A") {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setCreditLine($this->curRecord->getCreditLine());
            if (self::RANDOMIZE_SIDEBAR_CONTENTS) {
                $searchRequest->setSortParameter("rand");
            }
            $this->collectionsBackend->executeRequest($searchRequest, FALSE);
            if ($this->collectionsBackend->getFoundSetCount() > 1) {
                $searchRequest->setSortParameter("relevance");
                $this->sidebar = new detailsSidebar($this->collectionsBackend->getResults(), $this->curRecord, "Other " . constructCreditTitle($this->curRecord->getCreditLine()), $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

}

?>
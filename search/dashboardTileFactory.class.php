<?php

require_once("collectionsBackend.class.php");
require_once("dashboardTile.class.php");
require_once("customDashboardTile.class.php");


/**
 * Class dashboardTileFactory
 *
 * Factory class that allows the client to retrieve a random or specific dashboard tile.
 * Add possible tiles by adding a new entry to the allowedField array and a new private
 * function to the class.
 *
 * @package CollectionsSearch
 * @author Michael Robinson
 */
class dashboardTileFactory
{
    /**
     * @var array
     */
    private $allowedFields = null;
    /**
     * @var array
     */
    private $possibilities = array();
    /**
     * @var dashboardTile
     */
    private $tile = null;
    /**
     * @var collectionsBackend
     */
    private $collectionsBackend = null;
    /**
     * The number of artifacts per tile
     */
    const NUM_ARTIFACTS_PER_TILE = 2;

    //Pass in allowed fields to keep state across calls (AJAX)
    public function __construct($allowedFields = null)
    {
        if ($allowedFields !== null) {
            $this->allowedFields = $allowedFields;
        } else {
            //Populate $allowedFields with the names of the fields (columns) we are interested in and their probabilities
            $this->allowedFields = array();
            //Probabilities are relative, for example, if fieldA has probability 1 and fieldB has 2, then fieldB is twice as likely to appear as fieldA
            $this->allowedFields['sameAccYear'] = 1;
            $this->allowedFields['sameDate'] = 1;
			$this->allowedFields['currentyear2'] = 8; //Custom-made tile

            $this->allowedFields['sameCategory'] = 1;
            $this->allowedFields['sameClassification'] = 1;
            $this->allowedFields['sameSubClassification'] = 1;

            $this->allowedFields['sameContinent'] = 1;
            $this->allowedFields['sameCountry'] = 1;
            $this->allowedFields['sameRegion'] = 1;
            $this->allowedFields['sameCity'] = 1;
            $this->allowedFields['sameLocality'] = 1;

            $this->allowedFields['sameCulture'] = 1;
            $this->allowedFields['sameCreditLine'] = 1;
            $this->allowedFields['sameMaterials'] = 1;
			
            $this->allowedFields['mostPopular'] = 1;
            $this->allowedFields['randomArtifact'] = 1;
            $this->allowedFields['randomArtifact1'] = 1;
            $this->allowedFields['randomArtifact2'] = 1;
            $this->allowedFields['randomArtifact3'] = 1;
            $this->allowedFields['randomArtifact4'] = 1;
            $this->allowedFields['randomArtifact5'] = 1;

            //This is an example custom content tile, with 10 times the probability of others to appear, see the corresponding function featureFreund()
            $this->allowedFields['featureFreund'] = 10;
			$this->allowedFields['featureCollectionHighlight'] = 10;
        }
        //Generate the array with probabilities
        foreach ($this->allowedFields as $field => $probability) {
            for ($i = 0; $i < $probability; $i++) {
                $this->possibilities[] = $field;
            }
        }
        $this->collectionsBackend = new collectionsBackend(100);
    }

    /**
     * Returns the current state of the allowed fields array. The array can be passed into a new dashboardTileFactory
     * to restore it to the same state. Useful to avoid duplicate tiles across AJAX calls.
     * @return array The current state of the allowed fields array
     */
    public function getState()
    {
        return $this->allowedFields;
    }

    /**
     * Get a specific tile
     * @param $tileName The name of the tile to get
     * @return dashboardTile The tile
     */
    public function getSpecificDashboardTile($tileName)
    {
        $this->$tileName();
        return $this->tile;
    }

    /**
     * Get a random tile
     * @return bool|dashboardTile The tile, or False if no more tiles are left
     */
    public function getRandomDashboardTile()
    {
        if (count($this->possibilities) > 0) {
            $key = array_rand($this->possibilities, 1);
            $funct = $this->possibilities[$key];
            $result = $this->$funct();
            while (!$result) {
                $this->removeOption($this->possibilities[$key]);
                if (count($this->possibilities) < 1) {
                    return false;
                }
                $key = array_rand($this->possibilities, 1);
                $funct = $this->possibilities[$key];
                $result = $this->$funct();
            }
            $this->removeOption($this->possibilities[$key]);
            return $this->tile;
        }
        return false;
    }

    /**
     * Removes all occurences of the $option from the possibilities
     * @param $option The option to remove
     */
    private function removeOption($option)
    {
        $this->possibilities = array_filter($this->possibilities, function ($val) use ($option) {
            if ($val === $option) {
                return false;
            } else {
                return true;
            }
        });
        unset($this->allowedFields[$option]);
    }

    /**
     * Tile to display artifacts with the same accession year
     * @return bool
     */
    private function sameAccYear()
    {
        $myCollectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_TILE);
        //Try a max of 10 times to find a year with artifacts
        for ($i = 0; $i < 10; $i++) {
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $year = rand(1900, date("Y"));
            $searchRequest->setAccessionNumber($year . '.');
            $searchRequest->setWithImages(TRUE);
            $searchRequest->setSortParameter("rand");
            $myCollectionsBackend->executeRequest($searchRequest, FALSE);
            if ($myCollectionsBackend->getFoundSetCount() > 1) {
                $searchRequest->setWithImages(FALSE);
                $searchRequest->setSortParameter("relevance");
                $this->tile = new dashboardTile($myCollectionsBackend->getResults(), "Objects acquired in " . $year, 'yearTile', $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Tile to display artifacts with the same date
     * @return bool
     */
    private function sameDate()
    {
        //Get the dates grouped up and sorted by popularity
        $sqlQuery = "SELECT `period 3 date` as date, COUNT(1) as num
						FROM artifacts
						GROUP BY date
						HAVING num > 10
						ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($sqlQuery);
        if (count($results) > 1) {
            //Now pick a random date from the list
            $key = array_rand($results, 1);
            $randDate = $results[$key]['date'];
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setDate($randDate);
            $searchRequest->setWithImages(TRUE);
            $searchRequest->setSortParameter("rand");
            //echo "<p>Searching for artifacts with date: $randDate</p>";
            $myCollectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_TILE);
            $myCollectionsBackend->executeRequest($searchRequest, FALSE);
            if ($myCollectionsBackend->getFoundSetCount() > 0) {
                $searchRequest->setWithImages(FALSE);
                $searchRequest->setSortParameter("relevance");
                $this->tile = new dashboardTile($myCollectionsBackend->getResults(), "Objects from " . $randDate, 'dateTile', $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Tile to display artifacts with the same nomenclature category
     * @return bool
     */
    private function sameCategory()
    {
        //Get the categories grouped up and sorted by popularity
        $sqlQuery = "SELECT nom1_1.category as cat, COUNT(1) as num
						FROM artifacts
						LEFT JOIN nomcategory AS nom1_1 ON artifacts.nomen1_1 = nom1_1.nid
						WHERE nom1_1.suppressed = 0 
						GROUP BY cat
						HAVING num > 10
						ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($sqlQuery);
        if (count($results) > 1) {
            //Now pick a random category from the list
            $key = array_rand($results, 1);
            $randCat = $results[$key]['cat'];
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setCategory($randCat, FALSE);
            $searchRequest->setWithImages(TRUE);
            $searchRequest->setSortParameter("rand");
            //echo "<p>Searching for artifacts with category: $randCat</p>";
            $myCollectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_TILE);
            $myCollectionsBackend->executeRequest($searchRequest, FALSE);
            if ($myCollectionsBackend->getFoundSetCount() > 0) {
                $searchRequest->setWithImages(FALSE);
                $searchRequest->setSortParameter("relevance");
                $this->tile = new dashboardTile($myCollectionsBackend->getResults(), constructNomenclatureTitle($randCat), 'catTile', $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Tile to display artifacts with the same nomenclature classification
     * @return bool
     */
    private function sameClassification()
    {
        //Get the classifications grouped up and sorted by popularity
        $sqlQuery = "SELECT nom1_2.classification as class, COUNT(1) as num
						FROM artifacts 
						LEFT JOIN nomclassification AS nom1_2 ON artifacts.nomen1_2 = nom1_2.nid
						WHERE nom1_2.suppressed = 0
						GROUP BY class
						HAVING num > 10
						ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($sqlQuery);
        if (count($results) > 1) {
            //Now pick a random classification from the list
            $key = array_rand($results, 1);
            $randClass = $results[$key]['class'];
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setClassification($randClass, FALSE);
            $searchRequest->setWithImages(TRUE);
            $searchRequest->setSortParameter("rand");
            //echo "<p>Searching for artifacts with classification: $randClass</p>";
            $myCollectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_TILE);
            $myCollectionsBackend->executeRequest($searchRequest, FALSE);
            if ($myCollectionsBackend->getFoundSetCount() > 0) {
                $searchRequest->setWithImages(FALSE);
                $searchRequest->setSortParameter("relevance");
                $this->tile = new dashboardTile($myCollectionsBackend->getResults(), constructNomenclatureTitle($randClass), 'classTile', $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Tile to display artifacts with the same nomenclature sub-classification
     * @return bool
     */
    private function sameSubClassification()
    {
        //Get the sub-classifications grouped up and sorted by popularity
        $sqlQuery = "SELECT nom1_3.subclassification as subclass, COUNT(1) as num
						FROM artifacts 
						LEFT JOIN nomsubclassification AS nom1_3 ON artifacts.nomen1_3 = nom1_3.nid
						WHERE nom1_3.suppressed = 0 
						GROUP BY subclass
						HAVING num > 10
						ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($sqlQuery);
        if (count($results) > 1) {
            //Now pick a random sub-classification from the list
            $key = array_rand($results, 1);
            $randSubClass = $results[$key]['subclass'];
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setSubClassification($randSubClass, FALSE);
            $searchRequest->setWithImages(TRUE);
            $searchRequest->setSortParameter("rand");
            //echo "<p>Searching for artifacts with sub-classification: $randSubClass</p>";
            $myCollectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_TILE);
            $myCollectionsBackend->executeRequest($searchRequest, FALSE);
            if ($myCollectionsBackend->getFoundSetCount() > 0) {
                $searchRequest->setWithImages(FALSE);
                $searchRequest->setSortParameter("relevance");
                $this->tile = new dashboardTile($myCollectionsBackend->getResults(), constructNomenclatureTitle($randSubClass), 'subClassTile', $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Tile to display artifacts with the same continent
     * @return bool
     */
    private function sameContinent()
    {
        //Get the continents grouped up and sorted by popularity
        $sqlQuery = "SELECT geo.continent as continent, COUNT(1) as num
						FROM artifacts 
						LEFT JOIN geocontinent AS geo ON artifacts.geo1 = geo.gid
						WHERE geo.suppressed = 0 
						GROUP BY continent
						HAVING num > 10
						ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($sqlQuery);
        if (count($results) > 1) {
            //Now pick a random continent from the list
            $key = array_rand($results, 1);
            $randContinent = $results[$key]['continent'];
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setContinent($randContinent, FALSE);
            $searchRequest->setWithImages(TRUE);
            $searchRequest->setSortParameter("rand");
            //echo "<p>Searching for artifacts with continent: $randContinent</p>";
            $myCollectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_TILE);
            $myCollectionsBackend->executeRequest($searchRequest, FALSE);
            if ($myCollectionsBackend->getFoundSetCount() > 0) {
                $searchRequest->setWithImages(FALSE);
                $searchRequest->setSortParameter("relevance");
                $this->tile = new dashboardTile($myCollectionsBackend->getResults(), 'Objects from ' . $randContinent, 'continentTile', $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Tile to display artifacts with the same country year
     * @return bool
     */
    private function sameCountry()
    {
        //Get the countries grouped up and sorted by popularity
        $sqlQuery = "SELECT geo.country as country, COUNT(1) as num
						FROM artifacts 
						LEFT JOIN geocountry AS geo ON artifacts.geo2 = geo.gid
						WHERE geo.suppressed = 0 
						GROUP BY country
						HAVING num > 10
						ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($sqlQuery);
        if (count($results) > 1) {
            //Now pick a random country from the list
            $key = array_rand($results, 1);
            $randCountry = $results[$key]['country'];
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setCountry($randCountry, FALSE);
            $searchRequest->setWithImages(TRUE);
            $searchRequest->setSortParameter("rand");
            //echo "<p>Searching for artifacts with country: $randCountry</p>";
            $myCollectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_TILE);
            $myCollectionsBackend->executeRequest($searchRequest, FALSE);
            if ($myCollectionsBackend->getFoundSetCount() > 0) {
                $searchRequest->setWithImages(FALSE);
                $searchRequest->setSortParameter("relevance");
                $this->tile = new dashboardTile($myCollectionsBackend->getResults(), 'Objects from ' . $randCountry, 'countryTile', $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Tile to display artifacts with the same region
     * @return bool
     */
    private function sameRegion()
    {
        //Get the regions grouped up and sorted by popularity
        $sqlQuery = "SELECT geo.region as region, COUNT(1) as num
						FROM artifacts 
						LEFT JOIN georegion AS geo ON artifacts.geo3 = geo.gid
						WHERE geo.suppressed = 0 
						GROUP BY region
						HAVING num > 10
						ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($sqlQuery);
        if (count($results) > 1) {
            //Now pick a random region from the list
            $key = array_rand($results, 1);
            $randRegion = $results[$key]['region'];
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setRegion($randRegion, FALSE);
            $searchRequest->setWithImages(TRUE);
            $searchRequest->setSortParameter("rand");
            //echo "<p>Searching for artifacts with region: $randRegion</p>";
            $myCollectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_TILE);
            $myCollectionsBackend->executeRequest($searchRequest, FALSE);
            if ($myCollectionsBackend->getFoundSetCount() > 0) {
                $searchRequest->setWithImages(FALSE);
                $searchRequest->setSortParameter("relevance");
                $this->tile = new dashboardTile($myCollectionsBackend->getResults(), 'Objects from ' . $randRegion, 'regionTile', $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Tile to display artifacts with the same city
     * @return bool
     */
    private function sameCity()
    {
        //Get the cities grouped up and sorted by popularity
        $sqlQuery = "SELECT geo.city as city, COUNT(1) as num
						FROM artifacts 
						LEFT JOIN geocity AS geo ON artifacts.geo4 = geo.gid
						WHERE geo.suppressed = 0
						GROUP BY city
						HAVING num > 10
						ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($sqlQuery);
        if (count($results) > 1) {
            //Now pick a random city from the list
            $key = array_rand($results, 1);
            $randCity = $results[$key]['city'];
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setCity($randCity, FALSE);
            $searchRequest->setWithImages(TRUE);
            $searchRequest->setSortParameter("rand");
            //echo "<p>Searching for artifacts with city: $randCity</p>";
            $myCollectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_TILE);
            $myCollectionsBackend->executeRequest($searchRequest, FALSE);
            if ($myCollectionsBackend->getFoundSetCount() > 0) {
                $searchRequest->setWithImages(FALSE);
                $searchRequest->setSortParameter("relevance");
                $this->tile = new dashboardTile($myCollectionsBackend->getResults(), 'Objects from ' . $randCity, 'cityTile', $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Tile to display artifacts with the same locality
     * @return bool
     */
    private function sameLocality()
    {
        //Get the localities grouped up and sorted by popularity
        $sqlQuery = "SELECT geo.locality as locality, COUNT(1) as num
						FROM artifacts 
						LEFT JOIN geolocality AS geo ON artifacts.geo5 = geo.gid
						WHERE geo.suppressed = 0 
						GROUP BY locality
						HAVING num > 10
						ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($sqlQuery);
        if (count($results) > 1) {
            //Now pick a random locality from the list
            $key = array_rand($results, 1);
            $randLocality = $results[$key]['locality'];
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setLocality($randLocality, FALSE);
            $searchRequest->setWithImages(TRUE);
            $searchRequest->setSortParameter("rand");
            //echo "<p>Searching for artifacts with locality: $randLocality</p>";
            $myCollectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_TILE);
            $myCollectionsBackend->executeRequest($searchRequest, FALSE);
            if ($myCollectionsBackend->getFoundSetCount() > 0) {
                $searchRequest->setWithImages(FALSE);
                $searchRequest->setSortParameter("relevance");
                $this->tile = new dashboardTile($myCollectionsBackend->getResults(), 'Objects from ' . $randLocality, 'localityTile', $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Tile to display artifacts with the same culture
     * @return bool
     */
    private function sameCulture()
    {
        //Get the cultures grouped up and sorted by popularity
        $sqlQuery = "SELECT cul.culture as culture, COUNT(1) as num
						FROM artifacts 
						LEFT JOIN culture as cul ON artifacts.culture1 = cul.cid
						WHERE cul.suppressed = 0 
						GROUP BY culture
						HAVING num > 10
						ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($sqlQuery);
        if (count($results) > 1) {
            //Now pick a random culture from the list
            $key = array_rand($results, 1);
            $randCulture = $results[$key]['culture'];
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setCulture($randCulture, FALSE);
            $searchRequest->setWithImages(TRUE);
            $searchRequest->setSortParameter("rand");
            //echo "<p>Searching for artifacts with culture: $randCulture</p>";
            $myCollectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_TILE);
            $myCollectionsBackend->executeRequest($searchRequest, FALSE);
            if ($myCollectionsBackend->getFoundSetCount() > 0) {
                $searchRequest->setWithImages(FALSE);
                $searchRequest->setSortParameter("relevance");
                $this->tile = new dashboardTile($myCollectionsBackend->getResults(), 'Objects with culture: ' . $randCulture, 'cultureTile', $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Tile to display artifacts with the same credit line
     * @return bool
     */
    private function sameCreditLine()
    {
        //Get the credit lines grouped up and sorted by popularity
        $sqlQuery = "SELECT `credit line` as credit, COUNT(1) as num
						FROM artifacts 
						GROUP BY credit
						HAVING credit IS NOT NULL AND num > 10 AND credit <> '' AND CHAR_LENGTH(credit) < 50
						ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($sqlQuery);
        if (count($results) > 1) {
            //Now pick a random credit line from the list
            $key = array_rand($results, 1);
            $randCredit = $results[$key]['credit'];
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setCreditLine($randCredit, FALSE);
            $searchRequest->setWithImages(TRUE);
            $searchRequest->setSortParameter("rand");
            //echo "<p>Searching for artifacts with credit line: $randCredit</p>";
            $myCollectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_TILE);
            $myCollectionsBackend->executeRequest($searchRequest, FALSE);
            if ($myCollectionsBackend->getFoundSetCount() > 0) {
                $searchRequest->setWithImages(FALSE);
                $searchRequest->setSortParameter("relevance");
                $this->tile = new dashboardTile($myCollectionsBackend->getResults(), constructCreditTitle($randCredit), 'creditLineTile', $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Tile to display artifacts with the same materials
     * @return bool
     */
    private function sameMaterials()
    {
        //Get the materials grouped up and sorted by popularity
        $sqlQuery = "SELECT `materials 2` as material, COUNT(1) as num
						FROM artifacts 
						GROUP BY material
						HAVING material IS NOT NULL AND num > 10 AND material <> '' AND CHAR_LENGTH(material) < 50
						ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($sqlQuery);
        if (count($results) > 1) {
            //Now pick a random material from the list
            $key = array_rand($results, 1);
            $randMat = $results[$key]['material'];
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setMaterial($randMat, FALSE);
            $searchRequest->setWithImages(TRUE);
            $searchRequest->setSortParameter("rand");
            //echo "<p>Searching for artifacts with material: $randMat</p>";
            $myCollectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_TILE);
            $myCollectionsBackend->executeRequest($searchRequest, FALSE);
            if ($myCollectionsBackend->getFoundSetCount() > 0) {
                $searchRequest->setWithImages(FALSE);
                $searchRequest->setSortParameter("relevance");
                $this->tile = new dashboardTile($myCollectionsBackend->getResults(), 'Objects with material: ' . $randMat, 'materialTile', $searchRequest->getURL());
                return true;
            }
        }
        return false;
    }

    /**
     * Tile to display the most popular artifacts
     * @return bool
     */
    private function mostPopular()
    {
        //Get the top 30 most popular artifacts (with images)
        $myCollectionsBackend = new collectionsBackend(25);
        $searchRequest = new searchRequest();
        $searchRequest->clear();
        $searchRequest->setWithImages(TRUE);
        $searchRequest->setSortParameter('popularity');
        //echo "<p>Searching for popular artifacts</p>";
        $myCollectionsBackend->executeRequest($searchRequest, FALSE);
        if ($myCollectionsBackend->getFoundSetCount() > 0) {
            $searchRequest->setWithImages(FALSE);
            $this->tile = new dashboardTile(array_slice($myCollectionsBackend->getResults(), 0, self::NUM_ARTIFACTS_PER_TILE), 'Popular Objects', 'popularityTile', $searchRequest->getURL(), FALSE);
            return true;
        }
        return false;
    }

    //Allow for more random artifacts per page
    private function randomArtifact1()
    {
        return $this->randomArtifact();
    }

    private function randomArtifact2()
    {
        return $this->randomArtifact();
    }

    private function randomArtifact3()
    {
        return $this->randomArtifact();
    }

    private function randomArtifact4()
    {
        return $this->randomArtifact();
    }

    private function randomArtifact5()
    {
        return $this->randomArtifact();
    }

    /**
     * Tile to display a random artifact
     * @return bool
     */
    private function randomArtifact()
    {
        //Find out how many artifacts there are
        $sqlQuery = "SELECT MAX(aid) as max FROM artifacts";
        $results = $this->collectionsBackend->executeRequest($sqlQuery);
        if (count($results) < 1) {
            return false;
        }
        $maxAID = $results[0]['max'];
        $escapeCount = 0;
        while (true) {
            $escapeCount++;
            $randAID = rand(0, $maxAID);
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $searchRequest->setAID($randAID);
            $this->collectionsBackend->executeRequest($searchRequest, FALSE);
            $results = $this->collectionsBackend->getResults();
            $artifact = $results[0];
            $img = $artifact->getThumbImage(artifact::TILE_CONTEXT);
            if (strpos($img, 'dina') !== FALSE) {
                continue;
            }
            if (strpos($img, 'notfound') !== FALSE) {
                continue;
            }
            if ($escapeCount > 20) {
                return false;
            }
            break;
        }
        List($width, $height) = getimagesize($img);
        $ratio = $height / $width;
        //Display different tile size/shape depending on image aspect ratio?
        if ($ratio > 0.9 && $ratio < 1.25) {
            $this->tile = new customDashboardTile(function ($echo) use ($artifact, $width, $height, $img) {
                $output = "";
                $output .= '<div class="tile randomTile">';
                $output .= '	<a href="details.php?a=' . $artifact->getAccessionNumber() . '&rand=1">';
                $output .= '		<div class="tileTitle"><span>' . $artifact->getName() . '</span></div>';
                $output .= '		<div class="tileItem lastTileItem">';
                $output .= '			<div class="tileImgCont">';
                $newWidth = 305 / $height * $width;
                $output .= '			<img src="' . $img . '" alt="' . $artifact->getAccessionNumber() . '" height="334"';
                if ($newWidth > 305) {
                    $output .= ' style="margin-left: -' . ($newWidth - 305) / 2 . 'px;">';
                } else {
                    $output .= '>';
                }
                $output .= '			</div>';
                $output .= '		</div>';
                $output .= '	</a>';
                $output .= '</div>';
                if ($echo) {
                    echo $output;
                }
                return $output;
            });
        } else if ($ratio > 1.25) {
            $this->tile = new customDashboardTile(function ($echo) use ($artifact, $width, $height, $img) {
                $output = "";
                $output .= '<div class="tile randomTile tall">';
                $output .= '	<a href="details.php?a=' . $artifact->getAccessionNumber() . '&rand=1">';
                $output .= '		<div class="tileTitle"><span>' . $artifact->getName() . '</span></div>';
                $output .= '		<div class="tileItem lastTileItem">';
                $output .= '			<div class="tileImgCont">';
                $newHeight = 150 / $width * $height;
                $newWidth = 334 / $height * $width;
                if ($newHeight < 334) {
                    $output .= '			<img src="' . $img . '" alt="' . $artifact->getAccessionNumber() . '" height="334"';
                    if ($newWidth > 150) {
                        $output .= ' style="margin-left: -' . ($newWidth - 150) / 2 . 'px">';
                    } else {
                        $output .= '>';
                    }
                } else {
                    $output .= '			<img src="' . $img . '" alt="' . $artifact->getAccessionNumber() . '" width="150">';
                }
                $output .= '			</div>';
                $output .= '		</div>';
                $output .= '	</a>';
                $output .= '</div>';
                if ($echo) {
                    echo $output;
                }
                return $output;
            });
        } else { //$ratio < 0.9
            $this->tile = new customDashboardTile(function ($echo) use ($artifact, $width, $height, $img) {
                $output = "";
                $output .= '<div class="tile randomTile wide">';
                $output .= '	<a href="details.php?a=' . $artifact->getAccessionNumber() . '&rand=1">';
                $output .= '		<div class="tileTitle"><span>' . $artifact->getName() . '</span></div>';
                $output .= '		<div class="tileItem lastTileItem">';
                $output .= '			<div class="tileImgCont">';
                $newWidth = 150 / $height * $width;
                $newHeight = 305 / $width * $height;
                if ($newWidth < 305) {
                    $output .= '			<img src="' . $img . '" alt="' . $artifact->getAccessionNumber() . '" width="305"';
                    if ($newHeight > 150) {
                        $output .= ' style="margin-top: -' . ($newHeight - 150) / 2 . 'px">';
                    } else {
                        $output .= '>';
                    }
                } else {
                    $output .= '			<img src="' . $img . '" alt="' . $artifact->getAccessionNumber() . '" height="150"';
                    if ($newWidth > 305) {
                        $output .= ' style="margin-left: -' . ($newWidth - 305) / 2 . 'px">';
                    } else {
                        $output .= '>';
                    }
                }
                $output .= '			</div>';
                $output .= '		</div>';
                $output .= '	</a>';
                $output .= '</div>';
                if ($echo) {
                    echo $output;
                }
                return $output;
            });
        }
        return true;
    }

    private function currentyear2()
    {
        $myCollectionsBackend = new collectionsBackend(self::NUM_ARTIFACTS_PER_TILE);
        //Try a max of 10 times to find a year with artifacts
            $searchRequest = new searchRequest();
            $searchRequest->clear();
            $CurrentYear = date("Y");
            $searchRequest->setAccessionNumber($CurrentYear . '.');
            $searchRequest->setWithImages(TRUE);
            $searchRequest->setSortParameter("name");
            $myCollectionsBackend->executeRequest($searchRequest, FALSE);
        if ($myCollectionsBackend->getFoundSetCount() > 1) {
                $searchRequest->setWithImages(FALSE);
                $searchRequest->setSortParameter("name");
                $this->tile = new dashboardTile($myCollectionsBackend->getResults(), "Objects Acquired in the Current Year" . $year, 'CurrentYearTile2', $searchRequest->getURL());
                return true;
            }
        
        return false;
    }
    /**
     * An Example custom content function. You can do whatever you want here,
     * just create a customDashboardTile object and give it your custom
     * anonymous function to display the content.
     * @return bool
     */
    private function featureFreund()
    {
        //Create a search request that would find objects with images that have a credit line of freund
        $searchRequest = new searchRequest();
        $searchRequest->clear();
        $searchRequest->setWithImages(TRUE);
        $searchRequest->setCreditLine('Freund');
        //Create a new custom tile that displays some custom image and links to the search request, the custom anonymous function must return true if it successfully displays content.
        $this->tile = new customDashboardTile(function ($echo) use ($searchRequest) {
            $output = "";
            $output .= '<div class="tile freundTile">';
            $output .= '	<a href="' . $searchRequest->getURL() . '">';
            $output .= '	<div class="tileTitle"><span>Objects from our Freund Collection</span></div>';
            $output .= '		<div class="tileItem lastTileItem">';
            $output .= '			<img src="elements/someFancyFreundImage.jpg" style="border: 1px solid black;" alt="Fancy Freund Image" height="150" width="307">';
            $output .= '		</div>';
            $output .= '	</a>';
            $output .= '</div>';
            if ($echo) {
                echo $output;
            }
            return $output;
        });
        return true;
    }


	private function featureCollectionHighlight()
    {
        //Create a search request that would find objects with images that have a credit line of freund
        $searchRequest = new searchRequest();
        $searchRequest->clear();
        $searchRequest->setWithImages(TRUE);
		global $randnumber;
		$randnumber=mt_rand(1,13);  //Choose random number from 1-13....Switches set tile fields appropriately based on the number picked.
		switch ($randnumber)
		{
		case 1:
		$featCline = "Kieffer-Lopez Collection";
		break;
		case 2:
		$featCline = "Horowitz Collection";
		break;
		case 3:
		$featCline = "Edgar J. Banks Collection";
		break;
		case 4:
		$featCline = "Charles Bur Harper";
		break;
		case 5:
		$featCline = "Chiurazzi & De Angelis";
		break;
		case 6:
		$featCline = "Reginald and Gladys Laubin Collection";
		break;
		case 7:
		$featCline = "Norman E. and Dorothea S. Whitten";
		break;
		case 8:
		$featCline = "The Seymour and Muriel Yale Collection of Coins of the Ottoman Empire and Other Middle East States";
		break;
		case 9:
		$featCline = "Gift of Drs. Albert V. and Marguerite Carozzi";
		break;
		case 10:
		$featCline = "Richard and Barbara Faletti Family Collection";
		break;
		case 11:
		$featCline = "Transfer from Department of Anthropology, UIUC";
		$material11 = "Textile--Bark";
		$culture11 = "Tukuna";
		$searchRequest->setCulture($culture11);
		break;
		case 12:
		$featCline = "Museum of Natural History, UIUC";
		$culture12 = "Arctic: Inuit, Native American";
		$searchRequest->setCulture($culture12);
		break;
		case 13:
		$featCline = "Gift of Harlan J. and Pamela Berk";
		$BerkSubClass = "Religious Objects";
		$searchRequest->setSubClassification($BerkSubClass, FALSE);
		break;
		}
    $searchRequest->setCreditLine($featCline);    	
        //Create a new custom tile that displays some custom image and links to the search request, the custom anonymous function must return true if it successfully displays content.
        $this->tile = new customDashboardTile(function ($echo) use ($searchRequest) {
			global $randnumber;
			switch ($randnumber)
		{
		case 1:
		$featTitle = "Objects from the Kieffer-Lopez Collection";
		$featImgSrc = "elements/SomeFancyKiefferLopezImage.jpg";
		$featImgAlt = "The Kieffer-Lopez Collection Image";
		break;
		case 2:
		$featTitle = "Objects from the Horowitz Collection";
		$featImgSrc = "elements/SomeFancyHorowitzImage.jpg";
		$featImgAlt = "The Kieffer-Lopez Collection Image";
		break;
		case 3:
		$featTitle = "Objects from our Edgar J. Banks Collection";
		$featImgSrc = "elements/SomeFancyBanksImage.jpg";
		$featImgAlt = "Fancy Edgar J. Banks Collection Image";
		break;
		case 4:
		$featTitle = "The Charles Bur Harper Collection";
		$featImgSrc = "elements/SomeFancyHarperImage.jpg";
		$featImgAlt = "Fancy Charles Bur Harper Image";
		break;
		case 5:
		$featTitle = "Objects from Chiurazzi & De Angelis";
		$featImgSrc = "elements/SomeFancyChiurazzi&DeAngelisImage.jpg";
		$featImgAlt = "Fancy Chiurazzi & De Angelis Image";
		break;
		case 6:
		$featTitle = "Objects from our Reginald and Gladys Laubin Collection";
		$featImgSrc = "elements/someFancyLaubinImage.jpg";
		$featImgAlt = "Fancy Reginald and Gladys Laubin Collection Image";
		break;
		case 7:
		$featTitle = "Objects from our Whitten Collection";
		$featImgSrc = "elements/someFancyWhittenImage.jpg";
		$featImgAlt = "Fancy Whitten Collection Image";
		break;
		case 8:
		$featTitle = "The Seymour and Muriel Yale Collection of Coins";
		$featImgSrc = "elements/someFancyYaleImage.jpg";
		$featImgAlt = "Fancy Seymour and Muriel Yale Collection of Coins Image";
		break;
		case 9:
		$featTitle = "Objects from Albert V. and Marguerite Carozzi";
		$featImgSrc = "elements/someFancyCarozziImage.jpg";
		$featImgAlt = "Drs. Albert V. and Marguerite Carozzi Collection Image";
		break;
		case 10:
		$featTitle = "Objects from The Faletti Collection";
		$featImgSrc = "elements/someFancyFalettiImage.jpg";
		$featImgAlt = "Faletti Collection Image";
		break;
		case 11:
		$featTitle = "The Bolian Tukuna Bark Cloth Collection";
		$featImgSrc = "elements/someFancyBarkImage.jpg";
		$featImgAlt = "Bolian Tukuna Bark Cloth Collection Image";
		break;
		case 12:
		$featTitle = "The Crocker Land Expedition";
		$featImgSrc = "elements/someFancyCrocker3Image.jpg";
		$featImgAlt = "The Crocker Land Expedition Collection from the Arctic";
		break;
		case 13:
		$featTitle = "Berk \"Stargazer\" Collection";
		$featImgSrc = "elements/someFancyBerkImage.jpg";
		$featImgAlt = "The Berk \"Stargazer\" Collection of Neolithic and Bronze Age Figurines";
		break;
					}
            $output = "";
            $output .= '<div class="tile freundTile">';
            $output .= '	<a href="' . $searchRequest->getURL() . '">';
            $output .= '	<div class="tileTitle"><span>' . $featTitle . '</span></div>';
            $output .= '		<div class="tileItem lastTileItem">';
            $output .= '			<img src="'. $featImgSrc .'" style="border: 1px solid black;" alt="'. $featImgAlt .'" height="150" width="307">';
            $output .= '		</div>';
            $output .= '	</a>';
            $output .= '</div>';
            if ($echo) {
                echo $output;
            }
            return $output;
        });
        return true;
    }

	}  
?>
<?php

require_once("collectionsBackend.class.php");
require_once("searchRequest.class.php");

/**
 * Class facetHandler
 *
 * Handles collection and display of facets for a given search
 *
 * @package CollectionsSearch
 * @author Michael Robinson
 */
class facetHandler
{

    /**
     * @var collectionsBackend
     */
    private $collectionsBackend = null;

    /**
     * @var array
     */
    private $nomenFacets = array();
    /**
     * @var array
     */
    private $geoFacets = array();

    /**
     * The facet showing threshold, inherited from collectionsBackend
     */
    const FACET_LIMIT = collectionsBackend::FACET_LIMIT;

    public function __construct($collectionsBackend)
    {
        $this->collectionsBackend = $collectionsBackend;
    }

    /**
     * Look at the search results and create a list of facets
     */
    public function parseFacets()
    {
        $this->parseNomenclature();
        $this->parseGeography();
    }

    /**
     * Display the collected list of facets
     */
    public function displayFacets()
    {
        $this->displayNomenclature();
        $this->displayGeography();
    }

    /**
     * Display the nomenclature facet set
     */
    private function displayNomenclature()
    {
        List($curLevel, $curParent) = $this->getCurrentNomenclatureFacetLevel();
        if (count($this->nomenFacets) < 1 && $curParent == '') {
            return;
        }
        $totalNomenclature = 0;
        foreach ($this->nomenFacets as $facetAttrs) {
            $totalNomenclature += $facetAttrs[0];
        }
        echo '<div id="nomenFacet">';
        echo '<ul class="facetList">';
        echo '<h4>Classification</h4>';
        $hasMore = false;
        //Display the current parent facets
        $parents = $this->getCurrentNomenclatureParentTree();
        foreach ($parents as $parent) {
            echo '<li><span class="curFacet">' . $parent[0] . '</span> <a class="retractTerm" href="' . $parent[1] . '">[x]</a><ul>';
        }
        foreach ($this->nomenFacets as $facetValue => $facetAttrs) {
            List($facetCount, $facetURL) = $facetAttrs;
            $anchor = '<a href="' . $facetURL . '">' . $facetValue . '</a> <span class="facetNumber">[' . $facetCount . ']</span>';
            if ($facetCount < $totalNomenclature * self::FACET_LIMIT) {
                if (!$hasMore) {
                    echo '<li><a class="more">[More]</a></li>';
                    $hasMore = true;
                }
                echo '<li class="hiddenFacet">' . $anchor . '</li>';
            } else {
                echo '<li>' . $anchor . '</li>';
            }
        }
        if ($curParent != '') {
            echo '</ul>';
        }
        foreach ($parents as $parent) {
            echo '</li></ul>';
        }
        echo '</ul>';
        echo '</div>';
    }

    /**
     * Display the geography/origin facet set
     */
    private function displayGeography()
    {
        List($curLevel, $curParent) = $this->getCurrentGeographyFacetLevel();
        if (count($this->geoFacets) < 1 && $curParent == '') {
            return;
        }
        $totalGeography = 0;
        foreach ($this->geoFacets as $facetAttrs) {
            $totalGeography += $facetAttrs[0];
        }
        echo '<div id="originFacet">';
        echo '<ul class="facetList">';
        echo '<h4>Origin</h4>';
        $hasMore = false;
        //Display the current parent facet
        $parents = $this->getCurrentGeographyParentTree();
        foreach ($parents as $parent) {
            echo '<li><span class="curFacet">' . $parent[0] . '</span> <a class="retractTerm" href="' . $parent[1] . '">[x]</a><ul>';
        }
        foreach ($this->geoFacets as $facetValue => $facetAttrs) {
            List($facetCount, $facetURL) = $facetAttrs;
            $anchor = '<a href="' . $facetURL . '">' . $facetValue . '</a> <span class="facetNumber">[' . $facetCount . ']</span>';
            if ($facetCount < $totalGeography * self::FACET_LIMIT) {
                if (!$hasMore) {
                    echo '<li><a class="more">[More]</a></li>';
                    $hasMore = true;
                }
                echo '<li class="hiddenFacet">' . $anchor . '</li>';
            } else {
                echo '<li>' . $anchor . '</li>';
            }
        }
        if ($curParent != '') {
            echo '</ul>';
        }
        foreach ($parents as $parent) {
            echo '</li></ul>';
        }
        echo '</ul>';
        echo '</div>';
    }

    /**
     * Find all nomenclature facets under the current facet parent
     */
    private function parseNomenclature()
    {
        //Get the nomenclature facets under the current level
        List($curLevel, $curParent) = $this->getCurrentNomenclatureFacetLevel();
        //echo "<p>curLevel: $curLevel | curParent: $curParent</p>";
        switch ($curLevel) {
            case 0:
                //Get all categories
                $this->nomenFacets = $this->getFacets_Category();
                break;
            case 1:
                //Get all classifications with current parent
                $this->nomenFacets = $this->getFacets_Classification($curParent);
                break;
            case 2:
                //Get all sub classifications with current parent
                $this->nomenFacets = $this->getFacets_SubClassification($curParent);
                break;
            default:
                break;
        }
    }

    /**
     * Find all geography/origin facets under the current facet parent
     */
    private function parseGeography()
    {
        //Get the geography facets under the current level
        List($curLevel, $curParent) = $this->getCurrentGeographyFacetLevel();
        switch ($curLevel) {
            case 0:
                //Get all continents
                $this->geoFacets = $this->getFacets_Continent();
                break;
            case 1:
                //Get all countries with current parent
                $this->geoFacets = $this->getFacets_Country($curParent);
                break;
            case 2:
                //Get all regions with current parent
                $this->geoFacets = $this->getFacets_Region($curParent);
                break;
            case 3:
                //Get all cities with current parent
                $this->geoFacets = $this->getFacets_City($curParent);
                break;
            case 4:
                //Get all localities with current parent
                $this->geoFacets = $this->getFacets_Locality($curParent);
                break;
            default:
                break;
        }
    }

    /**
     * Get all of the categories represented by the search result set
     * @return array The categories
     */
    private function getFacets_Category()
    {
        $facets = array();
        //Get the categories
        $query = "SELECT COUNT(1) AS num, nomcategory.category FROM temp_artifacts
					LEFT JOIN nomcategory ON (temp_artifacts.nomen1_1 = nomcategory.nid OR temp_artifacts.nomen2_1 = nomcategory.nid)
					WHERE suppressed = 0
					GROUP BY nomcategory.category
					ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($query);
        foreach ($results as $result) {
            //Skip blank categories
            if ($result['category'] == '') {
                continue;
            }
            $facets[$result['category']] = array($result['num'], changeGetVarInURL($_SERVER['REQUEST_URI'], 'c1', $result['category']));
        }
        return $facets;
    }

    /**
     * Get all of the classifications represented by the search result set with the category $parent
     * @param $parent The parent category
     * @return array The classifications
     */
    private function getFacets_Classification($parent)
    {
        $facets = array();
        //Get the classifications with parent $parent
        $parent = $this->collectionsBackend->escapeString($parent);
        $query = "SELECT nid FROM nomcategory WHERE category = '$parent'";
        $results = $this->collectionsBackend->executeRequest($query);
        if (count($results) < 1) {
            return $facets;
        }
        $pid = $results[0]['nid'];
        $query = "SELECT COUNT(1) AS num, nomclassification.classification FROM temp_artifacts
					LEFT JOIN nomclassification ON (temp_artifacts.nomen1_2 = nomclassification.nid OR temp_artifacts.nomen2_2 = nomclassification.nid)
					WHERE pid = $pid AND suppressed = 0
					GROUP BY nomclassification.classification
					ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($query);
        foreach ($results as $result) {
            //Skip blank classifications
            if ($result['classification'] == '') {
                continue;
            }
            $facets[$result['classification']] = array($result['num'], changeGetVarInURL($_SERVER['REQUEST_URI'], 'c2', $result['classification']));
        }
        return $facets;
    }

    /**
     * Get all of the sub-classifications represented by the search result set with the classification $parent
     * @param $parent The parent classification
     * @return array The sub-classifications
     */
    private function getFacets_SubClassification($parent)
    {
        $facets = array();
        //Get the sub classifications with parent $parent
        $parent = $this->collectionsBackend->escapeString($parent);
        $query = "SELECT nid FROM nomclassification WHERE classification = '$parent'";
        $results = $this->collectionsBackend->executeRequest($query);
        if (count($results) < 1) {
            return $facets;
        }
        $pid = $results[0]['nid'];
        $query = "SELECT COUNT(1) AS num, nomsubclassification.subclassification FROM temp_artifacts
					LEFT JOIN nomsubclassification ON (temp_artifacts.nomen1_3 = nomsubclassification.nid OR temp_artifacts.nomen2_3 = nomsubclassification.nid)
					WHERE pid = $pid AND suppressed = 0
					GROUP BY nomsubclassification.subclassification
					ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($query);
        foreach ($results as $result) {
            //Skip blank sub classifications
            if ($result['subclassification'] == '') {
                continue;
            }
            $facets[$result['subclassification']] = array($result['num'], changeGetVarInURL($_SERVER['REQUEST_URI'], 'c3', $result['subclassification']));
        }
        return $facets;
    }

    /**
     * Get all of the continents represented by the search result set
     * @return array The continents
     */
    public function getFacets_Continent()
    {
        $facets = array();
        //Get the continents
        $query = "SELECT gid, COUNT(1) AS num, geocontinent.continent FROM temp_artifacts
					LEFT JOIN geocontinent ON temp_artifacts.geo1 = geocontinent.gid
					WHERE suppressed = 0 
					GROUP BY geocontinent.gid 
					ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($query);
        foreach ($results as $result) {
            //Skip blank continents
            if ($result['continent'] == '') {
                continue;
            }
            $facets[$result['continent']] = array($result['num'], changeGetVarsInURL($_SERVER['REQUEST_URI'], array('g1' => $result['continent'], 'fc1' => $result['continent'])));
        }
        return $facets;
    }

    /**
     * Get all of the countries represented by the search result set with the continent $parent
     * @param $parent The parent continent
     * @return array The countries
     */
    public function getFacets_Country($parent)
    {
        $facets = array();
        //Get the countries with parent $parent
        $parent = $this->collectionsBackend->escapeString($parent);
        $query = "SELECT gid FROM geocontinent WHERE continent = '$parent'";
        $results = $this->collectionsBackend->executeRequest($query);
        if (count($results) < 1) {
            return $facets;
        }
        $pid = $results[0]['gid'];
        $query = "SELECT COUNT(1) AS num, geocountry.country FROM temp_artifacts
					LEFT JOIN geocountry ON temp_artifacts.geo2 = geocountry.gid
					WHERE pid = $pid AND suppressed = 0
					GROUP BY geocountry.gid
					ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($query);
        foreach ($results as $result) {
            //Skip blank countries
            if ($result['country'] == '') {
                continue;
            }
            $facets[$result['country']] = array($result['num'], changeGetVarsInURL($_SERVER['REQUEST_URI'], array('g2' => $result['country'], 'fc2' => $result['country'])));
        }
        return $facets;
    }

    /**
     * Get all of the regions represented by the search result set with the country $parent
     * @param $parent The parent country
     * @return array The regions
     */
    public function getFacets_Region($parent)
    {
        $facets = array();
        //Get the regions with parent $parent
        $parent = $this->collectionsBackend->escapeString($parent);
        $query = "SELECT gid FROM geocountry WHERE country = '$parent'";
        $results = $this->collectionsBackend->executeRequest($query);
        if (count($results) < 1) {
            return $facets;
        }
        $pid = $results[0]['gid'];
        $query = "SELECT COUNT(1) AS num, georegion.region FROM temp_artifacts
					LEFT JOIN georegion ON temp_artifacts.geo3 = georegion.gid
					WHERE pid = $pid AND suppressed = 0
					GROUP BY georegion.gid
					ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($query);
        foreach ($results as $result) {
            //Skip blank regions
            if ($result['region'] == '') {
                continue;
            }
            $facets[$result['region']] = array($result['num'], changeGetVarsInURL($_SERVER['REQUEST_URI'], array('g3' => $result['region'], 'fc3' => $result['region'])));
        }
        return $facets;
    }

    /**
     * Get all of the cities represented by the search result set with the region $parent
     * @param $parent The parent region
     * @return array The cities
     */
    public function getFacets_City($parent)
    {
        $facets = array();
        //Get the cities with parent $parent
        $parent = $this->collectionsBackend->escapeString($parent);
        $query = "SELECT gid FROM georegion WHERE region = '$parent'";
        $results = $this->collectionsBackend->executeRequest($query);
        if (count($results) < 1) {
            return $facets;
        }
        $pid = $results[0]['gid'];
        $query = "SELECT COUNT(1) AS num, geocity.city FROM temp_artifacts
					LEFT JOIN geocity ON temp_artifacts.geo4 = geocity.gid
					WHERE pid = $pid AND suppressed = 0
					GROUP BY geocity.gid
					ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($query);
        foreach ($results as $result) {
            //Skip blank cities
            if ($result['city'] == '') {
                continue;
            }
            $facets[$result['city']] = array($result['num'], changeGetVarsInURL($_SERVER['REQUEST_URI'], array('g4' => $result['city'], 'fc4' => $result['city'])));
        }
        return $facets;
    }

    /**
     * Get all of the localities represented by the search result set with the city $parent
     * @param $parent The parent city
     * @return array The localities
     */
    public function getFacets_Locality($parent)
    {
        $facets = array();
        //Get the localities with parent $parent
        $parent = $this->collectionsBackend->escapeString($parent);
        $query = "SELECT gid FROM geocity WHERE city = '$parent'";
        $results = $this->collectionsBackend->executeRequest($query);
        if (count($results) < 1) {
            return $facets;
        }
        $pid = $results[0]['gid'];
        $query = "SELECT COUNT(1) AS num, geolocality.locality FROM temp_artifacts
					LEFT JOIN geolocality ON temp_artifacts.geo5 = geolocality.gid
					WHERE pid = $pid
					GROUP BY geolocality.gid
					ORDER BY num DESC";
        $results = $this->collectionsBackend->executeRequest($query);
        foreach ($results as $result) {
            //Skip blank localities
            if ($result['locality'] == '') {
                continue;
            }
            $facets[$result['locality']] = array($result['num'], changeGetVarsInURL($_SERVER['REQUEST_URI'], array('g5' => $result['locality'], 'fc5' => $result['locality'])));
        }
        return $facets;
    }

    /**
     * Get the current nomenclature facet level, i.e. how zoomed in the facets are.
     * @return array An array containing the facet level and the value
     */
    private function getCurrentNomenclatureFacetLevel()
    {
        $searchRequest = $this->collectionsBackend->getCurrentRequest();
        if ($searchRequest->getSubClassification() != 'All' && $searchRequest->getSubClassification() != '') {
            return array(3, $searchRequest->getSubClassification());
        }
        if ($searchRequest->getClassification() != 'All' && $searchRequest->getClassification() != '') {
            return array(2, $searchRequest->getClassification());
        }
        if ($searchRequest->getCategory() != 'All' && $searchRequest->getCategory() != '') {
            return array(1, $searchRequest->getCategory());
        }
        return array(0, '');
    }

    /**
     * Get the current geography/origin facet level, i.e. how zoomed in the facets are.
     * @return array An array containing the facet level and the value
     */
    private function getCurrentGeographyFacetLevel()
    {
        $searchRequest = $this->collectionsBackend->getCurrentRequest();
        if ($searchRequest->getFacetLocality() != '') {
            return array(5, $searchRequest->getFacetLocality());
        }
        if ($searchRequest->getFacetCity() != '') {
            return array(4, $searchRequest->getFacetCity());
        }
        if ($searchRequest->getFacetRegion() != '') {
            return array(3, $searchRequest->getFacetRegion());
        }
        if ($searchRequest->getFacetCountry() != '') {
            return array(2, $searchRequest->getFacetCountry());
        }
        if ($searchRequest->getFacetContinent() != 'All' && $searchRequest->getFacetContinent() != '') {
            return array(1, $searchRequest->getFacetContinent());
        }
        return array(0, '');
    }

    /**
     * Get the parent tree of the current nomenclature facet level
     * @return array An array containing each step of the tree to the current level, each step contains a value and a URL to remove it
     */
    private function getCurrentNomenclatureParentTree()
    {
        $searchRequest = $this->collectionsBackend->getCurrentRequest();
        $parents = array();
        if ($searchRequest->getCategory() != 'All' && $searchRequest->getCategory() != '') {
            $parents[] = array($searchRequest->getCategory(), changeGetVarInURL($_SERVER['REQUEST_URI'], 'c1', 'All'));
        }
        if ($searchRequest->getClassification() != 'All' && $searchRequest->getClassification() != '') {
            $parents[] = array($searchRequest->getClassification(), changeGetVarInURL($_SERVER['REQUEST_URI'], 'c2', 'All'));
        }
        if ($searchRequest->getSubClassification() != 'All' && $searchRequest->getSubClassification() != '') {
            $parents[] = array($searchRequest->getSubClassification(), changeGetVarInURL($_SERVER['REQUEST_URI'], 'c3', 'All'));
        }
        return $parents;
    }

    /**
     * Get the parent tree of the current geography/origin facet level
     * @return array An array containing each step of the tree to the current level, each step contains a value and a URL to remove it
     */
    private function getCurrentGeographyParentTree()
    {
        $searchRequest = $this->collectionsBackend->getCurrentRequest();
        $parents = array();
        if ($searchRequest->getFacetContinent() != 'All' && $searchRequest->getFacetContinent() != '') {
            $parents[] = array($searchRequest->getFacetContinent(), changeGetVarsInURL($_SERVER['REQUEST_URI'], array('g1' => '', 'fc1' => '')));
        }
        if ($searchRequest->getFacetCountry() != '') {
            $parents[] = array($searchRequest->getFacetCountry(), changeGetVarsInURL($_SERVER['REQUEST_URI'], array('g2' => '', 'fc2' => '')));
        }
        if ($searchRequest->getFacetRegion() != '') {
            $parents[] = array($searchRequest->getFacetRegion(), changeGetVarsInURL($_SERVER['REQUEST_URI'], array('g3' => '', 'fc3' => '')));
        }
        if ($searchRequest->getFacetCity() != '') {
            $parents[] = array($searchRequest->getFacetCity(), changeGetVarsInURL($_SERVER['REQUEST_URI'], array('g4' => '', 'fc4' => '')));
        }
        if ($searchRequest->getFacetLocality() != '') {
            $parents[] = array($searchRequest->getFacetLocality(), changeGetVarsInURL($_SERVER['REQUEST_URI'], array('g5' => '', 'fc5' => '')));
        }
        return $parents;
    }
}


?>

<?php

require_once("searchRequest.class.php");

/**
 * Class naturalQuery
 *
 * Converts a searchRequest into an English readable string
 *
 * @package CollectionsSearch
 * @author Michael Robinson
 */
class naturalQuery
{

    /**
     * @var string
     */
    private $naturalString = "";

    /**
     * Overridden toString() function for ease of use
     * @return string The natural query string
     */
    public function __toString()
    {
        return $this->naturalString;
    }

    public function __construct($searchRequest)
    {
        //TODO: This function *really* should be broken up
        $natQ = array();
        $query = html_entity_decode($searchRequest->getQuery());
        $orTerms = array();
        $andTerms = array();
        $notTerms = array();

        //HANDLE QUOTED TERMS
        $quotedTerms = array();
        $quotedNotTerms = array();
        $quotedAndTerms = array();
        preg_match_all('/[\+\-]?"([^"]*)"/', $query, $quotedTerms);
        $queryWithoutQuotedTerms = preg_replace('/[\+\-]?"([^"]*)"/', "", $query);

        //HANDLE NON QUOTED TERMS
        $terms = explode(" ", $queryWithoutQuotedTerms);
        if (count($terms) > 0) {
            foreach ($terms as $term) {
                //if it begins with '+' it is AND, if '-' then it is NOT
                if ($term != "") {
                    if (strStartsWith($term, "+")) {
                        //AND term
                        $andTerms[] = '<span class="italic">' . preg_replace("/[+]/", "", $term) . '</span>';
                    } else if (strStartsWith($term, "-")) {
                        //NOT term
                        $notTerms[] = '<span class="italic">' . preg_replace("/[-]/", "", $term) . '</span>';
                    } else {
                        //OR term
                        $orTerms [] = '<span class="italic">' . $term . '</span>';
                    }
                }
            }
        }

        //Output the included terms
        if (count($andTerms) > 0 || count($orTerms) > 0) {
            $strAr = array();
            $str = 'containing the terms: ';
            if (count($andTerms) > 0) {
                $strAr[] = implode(" [and] ", $andTerms);
            }
            if (count($orTerms) > 0) {
                $strAr[] = implode(" [or] ", $orTerms);
            }
            $natQ[] = $str . implode(" [or] ", $strAr);
        }

        //Output the excluded terms
        if (count($notTerms) > 0) {
            $str = 'without the terms: ';
            $str .= implode(" [and] ", $notTerms);
            $natQ[] = $str;
        }

        if (count($quotedTerms[0]) > 0) {
            foreach ($quotedTerms[0] as $key => $term) {
                if (strStartsWith($term, "-")) {
                    $quotedNotTerms[] = preg_replace("/[-]/", "", $term);
                    unset($quotedTerms[0][$key]);
                }
                if (strStartsWith($term, "+")) {
                    $quotedAndTerms[] = preg_replace("/[+]/", "", $term);
                    unset($quotedTerms[0][$key]);
                }
            }
            $strAr = array();
            if (count($quotedAndTerms) > 0) {
                $strAr[] = implode(" [and] ", $quotedAndTerms);
            }
            if (count($quotedTerms[0]) > 0) {
                $strAr[] = implode(" [or] ", $quotedTerms[0]);
            }
            //Output the included phrases
            if (count($quotedTerms[0]) > 0 || count($quotedAndTerms) > 0) {
                $str = 'containing the phrases: ';
                $str .= implode(" [or] ", $strAr);
                $natQ[] = $str;
            }
            //Output the excluded phrases
            if (count($quotedNotTerms) > 0) {
                $str = 'without the phrases: ';
                $str .= implode(" [and] ", $quotedNotTerms);
                $natQ[] = $str;
            }
        }

        //Output the on display message
        if ($searchRequest->getOnDisplay()) {
            $retractedQueryURL = changeGetVarInURL($_SERVER['REQUEST_URI'], 'd', '0');
            $retractedQueryURL = changeGetVarInURL($retractedQueryURL, 'g', '');
            $gal = $searchRequest->getGallery();
            $str = 'on display <a class="retractTerm" href="' . $retractedQueryURL . '">[x]</a>';
            if ($gal != "" && $gal != "All") {
                $str .= ' in the <span class="italic">"' . $gal . '"</span> gallery';
            }
            $natQ[] = $str;
        }

        //Output the with images message
        if ($searchRequest->getWithImages()) {
            $retractedQueryURL = changeGetVarInURL($_SERVER['REQUEST_URI'], 'm', '0');
            $natQ[] = 'with images available <a class="retractTerm" href="' . $retractedQueryURL . '">[x]</a>';
        }

        //Output the with hi-res images message
        if ($searchRequest->getWithHiResImages()) {
            $retractedQueryURL = changeGetVarInURL($_SERVER['REQUEST_URI'], 'h', '0');
            $natQ[] = 'with high resolution images available <a class="retractTerm" href="' . $retractedQueryURL . '">[x]</a>';
        }

        if ($searchRequest->isAdvanced()) {
            //Output the specified name
            if ($searchRequest->getName() != "") {
                $retractedQueryURL = changeGetVarInURL($_SERVER['REQUEST_URI'], 'n', '');
                $natQ[] = 'named <span class="italic">' . $searchRequest->getName() . '</span> <a class="retractTerm" href="' . $retractedQueryURL . '">[x]</a>';
            }
            //Output the specified accession number
            if ($searchRequest->getAccessionNumber() != "") {
                $retractedQueryURL = changeGetVarInURL($_SERVER['REQUEST_URI'], 'a', '');
                $natQ[] = 'with accession number <span class="italic">' . $searchRequest->getAccessionNumber() . '</span> <a class="retractTerm" href="' . $retractedQueryURL . '">[x]</a>';
            }
            //Output the specified date
            if ($searchRequest->getDate() != "") {
                $retractedQueryURL = changeGetVarInURL($_SERVER['REQUEST_URI'], 'date', '');
                $natQ[] = 'from: <span class="italic">' . $searchRequest->getDate() . '</span> <a class="retractTerm" href="' . $retractedQueryURL . '">[x]</a>';
            }
            //Output the specified geography/origin
            if (($searchRequest->getContinent() != "" && $searchRequest->getContinent() != "All")
                || $searchRequest->getCountry() != "" || $searchRequest->getRegion() != ""
                || $searchRequest->getCity() != "" || $searchRequest->getLocality() != ""
            ) {
                $str = "from ";
                $geo = array();
                if ($searchRequest->getContinent() != "" && $searchRequest->getContinent() != "All") {
                    $geo[] = '<span class="italic">' . $searchRequest->getContinent() . '</span> <a class="retractTerm" href="' . changeGetVarInURL($_SERVER['REQUEST_URI'], 'g1', '') . '">[x]</a>';
                }
                if ($searchRequest->getCountry() != "") {
                    $geo[] = '<span class="italic">' . $searchRequest->getCountry() . '</span> <a class="retractTerm" href="' . changeGetVarInURL($_SERVER['REQUEST_URI'], 'g2', '') . '">[x]</a>';
                }
                if ($searchRequest->getRegion() != "") {
                    $geo[] = '<span class="italic">' . $searchRequest->getRegion() . '</span> <a class="retractTerm" href="' . changeGetVarInURL($_SERVER['REQUEST_URI'], 'g3', '') . '">[x]</a>';
                }
                if ($searchRequest->getCity() != "") {
                    $geo[] = '<span class="italic">' . $searchRequest->getCity() . '</span> <a class="retractTerm" href="' . changeGetVarInURL($_SERVER['REQUEST_URI'], 'g4', '') . '">[x]</a>';
                }
                if ($searchRequest->getLocality() != "") {
                    $geo[] = '<span class="italic">' . $searchRequest->getLocality() . '</span> <a class="retractTerm" href="' . changeGetVarInURL($_SERVER['REQUEST_URI'], 'g5', '') . '">[x]</a>';
                }
                $natQ[] = $str . implode("; ", $geo);
            }
            //Output the specified culture
            if ($searchRequest->getCulture() != "") {
                $retractedQueryURL = changeGetVarInURL($_SERVER['REQUEST_URI'], 'cl', '');
                $natQ[] = 'with culture: <span class="italic">' . $searchRequest->getCulture() . '</span> <a class="retractTerm" href="' . $retractedQueryURL . '">[x]</a>';
            }
            //Output the specified credit line
            if ($searchRequest->getCreditLine() != "") {
                $retractedQueryURL = changeGetVarInURL($_SERVER['REQUEST_URI'], 'cr', '');
                $natQ[] = 'with credit line: <span class="italic">' . $searchRequest->getCreditLine() . '</span> <a class="retractTerm" href="' . $retractedQueryURL . '">[x]</a>';
            }
            //Output the specified nomenclature
            if (($searchRequest->getCategory() != "" && $searchRequest->getCategory() != "All") ||
                ($searchRequest->getClassification() != "" && $searchRequest->getClassification() != "All") ||
                ($searchRequest->getSubClassification() != "" && $searchRequest->getSubClassification() != "All")
            ) {
                $classification = array();
                if ($searchRequest->getCategory() != "" && $searchRequest->getCategory() != "All") {
                    $classification[] = $searchRequest->getCategory() . ' <a class="retractTerm" href="' . changeGetVarInURL($_SERVER['REQUEST_URI'], 'c1', 'All') . '">[x]</a>';
                }
                if ($searchRequest->getClassification() != "" && $searchRequest->getClassification() != "All") {
                    $classification[] = $searchRequest->getClassification() . ' <a class="retractTerm" href="' . changeGetVarInURL($_SERVER['REQUEST_URI'], 'c2', 'All') . '">[x]</a>';
                }
                if ($searchRequest->getSubClassification() != "" && $searchRequest->getSubClassification() != "All") {
                    $classification[] = $searchRequest->getSubClassification() . ' <a class="retractTerm" href="' . changeGetVarInURL($_SERVER['REQUEST_URI'], 'c3', 'All') . '">[x]</a>';
                }
                $natQ[] = 'classified as <span class="italic">' . implode(" : ", $classification) . '</span>';
            }
            //Output the specified material
            if ($searchRequest->getMaterial() != "") {
                $retractedQueryURL = changeGetVarInURL($_SERVER['REQUEST_URI'], 'mat', '');
                $natQ[] = 'with material type: <span class="italic">' . $searchRequest->getMaterial() . '</span> <a class="retractTerm" href="' . $retractedQueryURL . '">[x]</a>';
            }
            //Output the specified manufacturing process
            if ($searchRequest->getManufacturingProcess() != "") {
                $retractedQueryURL = changeGetVarInURL($_SERVER['REQUEST_URI'], 'man', '');
                $natQ[] = 'with manufacturing process: <span class="italic">' . $searchRequest->getManufacturingProcess() . '</span> <a class="retractTerm" href="' . $retractedQueryURL . '">[x]</a>';
            }
            //Output the specified working set
            if ($searchRequest->getWorkingSet() != "") {
                $retractedQueryURL = changeGetVarInURL($_SERVER['REQUEST_URI'], 'ws', '');
                $natQ[] = 'with working set: <span class="italic">' . $searchRequest->getWorkingSet() . '</span> <a class="retractTerm" href="' . $retractedQueryURL . '">[x]</a>';
            }
        }

        $this->naturalString = implode("<br>", $natQ);
    }
}

?>
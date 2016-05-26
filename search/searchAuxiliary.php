<?php

require_once("nomenDefinition.php");

/**
 * This script holds basic helper functions that are used all over the search
 */

$Categories = array();
$Classifications = array();
$SubClassifications = array();

/**
 * Get the proper title for a given nomenclature name
 * @param $nomen The nomenclature name
 * @return string The proper title to use
 */
function constructNomenclatureTitle($nomen)
{
    if (stripos($nomen, 'T&amp;E') === FALSE) {
        return $nomen;
    }
    return $nomen . ' objects';
}

/**
 * Get the proper title for a given credit line
 * @param $credit The credit line
 * @return string The proper title to use
 */
function constructCreditTitle($credit)
{
    //Array of regex to string prepend key pairs
    $CreditRegex = array(
        '/(^from the collection of.*)|(^courtesy of.*)|(^on loan from.*)|(^purchased with funds from.*)/i' => 'Objects ',
        '/(.*collection.*)|(^estate of.*)/i' => 'Objects from the ',
        '/(^transfer from.*)|(^purchase.*)/i' => 'Objects obtained via ',
        '/^in memory of.*/i' => 'Objects received ',
        '/(^bequest of.*)|(anonymous gift)|(^gift of.*)|(^donation of.*)/i' => 'Objects received as a '
    );
    //Check for a match in each regex in $CreditRegex
    foreach ($CreditRegex as $regex => $prepend) {
        if (preg_match($regex, $credit)) {
            return $prepend . $credit;
        }
    }
    return "Objects received from " . $credit;
}

/**
 * Check if a string starts with a character
 * @param $string The string to check
 * @param $firstChar The character to look for
 * @return bool True if $string starts with $firstChar, False otherwise
 */
function strStartsWith($string, $firstChar)
{
    return strpos($string, $firstChar) === 0;
}

/**
 * Get the URL with the 'start' parameter changed to $newStart
 * @param $url The URL to change
 * @param $newStart The new value for 'start'
 * @return string The URL with 'start' changed
 */
function setStartInURL($url, $newStart)
{
    if (stripos($url, "&start=") !== FALSE) {
        $pattern = "/(&start=)\d*/";
        $replacement = '&start=' . $newStart;
        return preg_replace($pattern, $replacement, $url);
    }
    return $url . '&start=' . $newStart;
}

/**
 * Get the URL with the query parameter changed to $newQuery
 * @param $url The URl to change
 * @param $newQuery The new query value
 * @return string The URL with the query changed
 */
function changeQueryInURL($url, $newQuery)
{
    if (stripos($url, "?q=") !== FALSE) {
        $pattern = "/(\?q=).*?[&]/";
        $replacement = '?q=' . $newQuery . '&';
        return preg_replace($pattern, $replacement, $url);
    }
    return $url . '?q=' . $newQuery;
}

/**
 * Get the URL with the specified parameter $varToChange changed to $newValue
 * @param $url The URL to change
 * @param $varToChange The parameter to change
 * @param $newValue The new value
 * @return string The URL with $varToChange set to $newValue
 */
function changeGetVarInURL($url, $varToChange, $newValue)
{
    $newValue = urlencode(trim(htmlspecialchars_decode($newValue)));
    $newValue = str_replace('&', '%26', $newValue);
    if (stripos($url, $varToChange . '=') !== FALSE) {
        $pattern = "/($varToChange=)[^&]*/";
        $replacement = "$varToChange=$newValue";
        return preg_replace($pattern, $replacement, $url);
    }
    return $url . '&' . $varToChange . '=' . $newValue;
}

/**
 * Get the URL with the specified parameters changed to the specified values.
 * @param $url The URL to change
 * @param $values An array of key => value pairs, where the key is the parameter to change, and the value is what to set it to
 * @return string The URL with the specified parameters changed to the specified values
 */
function changeGetVarsInURL($url, $values)
{
    foreach ($values as $key => $val) {
        $url = changeGetVarInURL($url, $key, $val);
    }
    return $url;
}

/**
 * Truncates a string to the end of the first sentence after the break character after limit length
 * @param $string The string to truncate
 * @param $limit The maximum length of the string
 * @param string $break The character to break after
 * @param string $pad A string to append to the end of the truncated string
 * @return string The truncated string
 */
function descTruncate($string, $limit, $break = ".", $pad = "...")
{
    // return with no change if string is shorter than $limit
    if (strlen($string) <= $limit) return $string;
    // is $break present between $limit and the end of the string?
    if (false !== ($breakpoint = strpos($string, $break, $limit))) {
        if ($breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $pad;
        }
    }
    return $string;
}

/**
 * Outputs the HTML elements necessary to create a drop-down with the appropriate values
 * @param $field The field the drop-down is related to
 * @param $fieldname The name of the newly created field
 * @param $valueList Unused
 */
function populateDropDown($field, $fieldname, $valueList)
{
    global $nomen_categories;
    global $nomen_classifications;
    global $nomen_subclassifications;
    global $Categories;
    global $Classifications;
    global $SubClassifications;
    $selectedValue = html_entity_decode($field);
    $vlist = "";
    //categories
    if ($fieldname == "c1") {
        foreach ($nomen_categories as $key => $catName) {
            //if($key == 0){
            if ($catName == $selectedValue) {
                $vlist .= "\t<option selected=\"selected\">" . htmlentities($catName) . "</option>\n";
            } else {
                $vlist .= "\t<option>" . htmlentities($catName) . "</option>\n";
            }
        }
        $Categories = $nomen_categories;
        //classifications
    } else if ($fieldname == "c2") {
        $classifications = array();
        foreach ($nomen_classifications as $key => $value) {
            foreach ($value as $class) {
                if ($class != "All") {
                    array_push($classifications, $class);
                }
            }
        }
        //remove duplicates
        $classifications = array_unique($classifications, SORT_STRING);
        //sort the array A->Z
        //asort($classifications);
        array_unshift($classifications, "All");
        $Classifications = $classifications;
        foreach ($classifications as $key => $value) {
            //if($key == 0){
            if ($value == $selectedValue) {
                $vlist .= "\t<option selected=\"selected\">" . htmlentities($value) . "</option>\n";
            } else {
                $vlist .= "\t<option>" . htmlentities($value) . "</option>\n";
            }
        }
        //subclassifications
    } else if ($fieldname == "c3") {
        $subclassifications = array();
        foreach ($nomen_subclassifications as $key => $value) {
            foreach ($value as $subclass) {
                if ($subclass != "All") {
                    array_push($subclassifications, $subclass);
                }
            }
        }
        //remove duplicates
        $subclassifications = array_unique($subclassifications, SORT_STRING);
        array_unshift($subclassifications, "All");
        $SubClassifications = $subclassifications;
        foreach ($subclassifications as $key => $value) {
            //if($key == 0){
            if ($value == $selectedValue) {
                $vlist .= "\t<option selected=\"selected\">" . htmlentities($value) . "</option>\n";
            } else {
                $vlist .= "\t<option>" . htmlentities($value) . "</option>\n";
            }
        }
    }
    echo "<select id=\"" . htmlentities($fieldname) . "\" name=\"" . htmlentities($fieldname) . "\"";
    if ($fieldname == "c1") {
        echo " onChange=\"updateClass(this.options.selectedIndex)\"";
    }
    if ($fieldname == "c2") {
        echo " onChange=\"updateSubClass(this.options.selectedIndex)\"";
    }
    echo ">\n";
    if (empty($field)) {
        $field = "All";
    }
    echo $vlist;
    echo "</select>\n";
}

/**
 * Outputs the Javascript to handle changing the nomenclature drop-downs
 * @param $searchRequest The current search request
 */
function createDropDownJS($searchRequest)
{
    global $Categories;
    global $Classifications;
    global $SubClassifications;
    global $nomen_categories;
    global $nomen_classifications;
    global $nomen_subclassifications;
    global $nomen_categories_subclasses;

    $c1 = html_entity_decode($searchRequest->getCategory());
    $c2 = html_entity_decode($searchRequest->getClassification());
    $c3 = html_entity_decode($searchRequest->getSubClassification());

    $numSubclass = count($nomen_subclassifications);
    $numClass = count($nomen_classifications);
    echo "<script type=\"text/javascript\">
			var classes=new Object();
			var cat=new Object();
			var catclasses=new Object();";
    echo "\n\n";
    $i = 0;
    foreach ($Categories as $key => $value) {
        $j = 0;
        echo "cat[\"$value\"]=new Object()\n";
        foreach ($nomen_classifications[$value] as $class) {
            echo "cat[\"$value\"][$j]=new Option(\"$class\",\"$class\")\n";
            $j++;
        }
        $i++;
    }
    $i = 0;
    foreach ($nomen_categories_subclasses as $key => $value) {
        $j = 0;
        echo "catclasses[\"$key\"]=new Object()\n";
        foreach ($value as $subclass) {
            echo "catclasses[\"$key\"][$j]=new Option(\"$subclass\",\"$subclass\")\n";
            $j++;
        }
    }
    $i = 0;
    foreach ($Classifications as $key => $value) {
        $j = 0;
        echo "classes[\"$value\"]=new Object()\n";
        foreach ($nomen_subclassifications[$value] as $subclass) {
            echo "classes[\"$value\"][$j]=new Option(\"$subclass\",\"$subclass\")\n";
            $j++;
        }
        $i++;
    }

    echo '
 			function assLength(assArr){
				element_count = 0;
				for(var e in assArr){
    				if(assArr.hasOwnProperty(e)){
        				element_count++;
					}
				}
				return element_count
			}
			function updateClass(c){
				//temp=document.search.c2
				//temp2=document.search.c1
				temp = document.getElementById("c2")
				temp2 = document.getElementById("c1")

				temp.options.length=0
				c=temp2.options[c].text
					for(i=0;i<assLength(cat[c]);i++){
						temp.options[i]=new Option(cat[c][i].text, cat[c][i].value)
					}
				
				temp.options[0].selected=true;
				updateSubClass(0)
			}
			function updateSubClass(c){
				//temp=document.search.c3
				//temp2=document.search.c2
				//temp3=document.search.c1
				temp = document.getElementById("c3")
				temp2 = document.getElementById("c2")
				temp3 = document.getElementById("c1")

				temp.options.length=0
				c=temp2.options[c].text
				if(c == "All"){
					cate=temp3.options[temp3.selectedIndex].text
					for(i=0;i<assLength(catclasses[cate]);i++){
						temp.options[i]=new Option(catclasses[cate][i].text, catclasses[cate][i].value)	
					}
				}else{
					for(i=0;i<assLength(classes[c]);i++){
						temp.options[i]=new Option(classes[c][i].text, classes[c][i].value)	
					}
				}
				temp.options[0].selected=true;
			}
 			function go(){
 				location=temp.options[temp.selectedIndex].value
 			}
			function selectOptionByValue(selObj, val){
        		var A=selObj.options, L=A.length;
        		while(L){
            		if(A[--L].value==val){
                		selObj.selectedIndex=L;
                		L=0;
            		}
        		}
   			}';
    if (!empty($c1)) {
        //echo "selectOptionByValue(document.search.c1, \"$c1\")\n";
        echo "updateClass(document.search.c1.selectedIndex)\n";
    }
    if (!empty($c2)) {
        //echo "selectOptionByValue(document.search.c2, \"$c2\")\n";
        echo "updateSubClass(document.search.c2.selectedIndex)\n";
    }
    if (!empty($c3)) {
        //echo "selectOptionByValue(document.search.c3, \"$c3\")\n";
    }
    echo '</script>';
}

/**
 * Gets possible spelling/query corrections from Google
 * @param $query The bad query
 * @return array An array of possible corrections
 */
function getPossibleSpellingCorrections($query)
{
    if ($query != "") {
        $lang = 'en';
        $url = 'http://suggestqueries.google.com/complete/search?output=firefox&client=firefox&hl=' . $lang . '&q=' . urlencode($query);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.0; rv:2.0.1) Gecko/20100101 Firefox/4.0.1");
        $data = curl_exec($ch);
        curl_close($ch);

        $suggestions = json_decode($data, true);

        if ($suggestions) {
            return $suggestions[1];
        }
    }
    return array();
}

/**
 * Check an array of suggestions to see if any of them return results
 * @param $suggestions The list of suggestions, returned by getPossibleSpellingCorrections
 * @param $searchRequest The current search request
 * @return array An array of suggestions with the number of results found for each one
 */
function checkSuggestionsForResults($suggestions, $searchRequest)
{
    $newRequest = $searchRequest;
    $backend = new collectionsBackend();
    $suggestionResults = array();
    foreach ($suggestions as $suggestion) {
        $newRequest->setQuery($suggestion);
        if ($backend->executeRequest($searchRequest)) {
            if ($backend->getFoundSetCount() > 0) {
                $suggestionResults[$suggestion] = $backend->getFoundSetCount();
            }
        }
    }
    return $suggestionResults;
}

/**
 * Displays the error page
 */
function showError()
{
    $db_name = 'collections';
    include('../db_error.php');
    echo '</div><br class="clearfloat"><div id="footer"><div class="fltrt"><p><a href="http://www.spurlock.illinois.edu/info/people.html">Contact Us</a></p></div><div><a href="http://www.spurlock.illinois.edu/index.html">Spurlock Museum</a> | 600 S. Gregory St. | Urbana, Illinois 61801 | (217) 333-2360<br>&copy; <script type="text/javascript">document.write("2001-"+(new Date).getFullYear());</script> University of Illinois Board of Trustees | <a href="http://illinois.edu">University of Illinois at Urbana-Champaign</a></div></div></div></body></html>';
}

/**
 * Takes a noun and returns its plural version
 * @param $word The word to pluralize
 * @return bool|string False if there was a problem, or the pluralized string
 * @author Akelos Media, S.L.
 * @license GNU Lesser General Public License
 * @link http://www.akelos.com/
 */
function pluralize($word)
{
    $plural = array(
        '/(quiz)$/i' => '1zes',
        '/^(ox)$/i' => '1en',
        '/([m|l])ouse$/i' => '1ice',
        '/(matr|vert|ind)ix|ex$/i' => '1ices',
        '/(x|ch|ss|sh)$/i' => '1es',
        '/([^aeiouy]|qu)ies$/i' => '1y',
        '/([^aeiouy]|qu)y$/i' => '1ies',
        '/(hive)$/i' => '1s',
        '/(?:([^f])fe|([lr])f)$/i' => '12ves',
        '/sis$/i' => 'ses',
        '/([ti])um$/i' => '1a',
        '/(buffal|tomat)o$/i' => '1oes',
        '/(bu)s$/i' => '1ses',
        '/(alias|status)/i' => '1es',
        '/(octop|vir)us$/i' => '1i',
        '/(ax|test)is$/i' => '1es',
        '/s$/i' => 's',
        '/$/' => 's');

    $uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep');

    $irregular = array(
        'person' => 'people',
        'man' => 'men',
        'child' => 'children',
        'sex' => 'sexes',
        'move' => 'moves');

    $lowercased_word = strtolower($word);

    foreach ($uncountable as $_uncountable) {
        if (substr($lowercased_word, (-1 * strlen($_uncountable))) == $_uncountable) {
            return $word;
        }
    }

    foreach ($irregular as $_plural => $_singular) {
        if (preg_match('/(' . $_plural . ')$/i', $word, $arr)) {
            return preg_replace('/(' . $_plural . ')$/i', substr($arr[0], 0, 1) . substr($_singular, 1), $word);
        }
    }

    foreach ($plural as $rule => $replacement) {
        if (preg_match($rule, $word)) {
            return preg_replace($rule, $replacement, $word);
        }
    }
    return false;
}

?>

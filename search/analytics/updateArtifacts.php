<?php

//Remove the script timeout and increase the memory limit to 4gb
ini_set('memory_limit', '4096M');
set_time_limit(0);

require_once('../artifactRetriever.class.php');
require_once('../collectionsBackend.class.php');

//Get the requested action
$action = $_REQUEST['action'];

if ($action == "update") {
    //Get the list of accession numbers to update from the request
    $accNumsList = $_REQUEST['list'];
    //Split that list into individual numbers
    $accNums = explode("\n", $accNumsList);
    //Create an artifact retriever
    $artifactRetriever = new artifactRetriever('CGIdetails', false);
    //Tell the artifact retriever to get the records for this list of accession numbers
    $records = $artifactRetriever->retrieveArtifactSet($accNums);

    //Attempt to connect to the MySQL database (inherit connection parameters from collectionsBackend)
    $host = collectionsBackend::SQL_HOST;
    $dbname = collectionsBackend::SQL_DB;
    $user = collectionsBackend::SQL_USER;
    $pass = collectionsBackend::SQL_PASS;

    $link = mysql_connect($host, $user, $pass);
    if (!$link) {
        echo "MySQL Error: Unable to connect: " . mysql_error() . "<br>";
        die();
    } else {
        echo "Connected to MySQL server..<br>";
    }
    $result = mysql_select_db($dbname);
    if (!$result) {
        echo "MySQL Error: Unable to select database: " . mysql_error() . "<br>";
        ob_flush;
        flush();
        die();
    }

    if (count($records) < 1) {
        echo "No records to copy<br>";
    }

    $i = 1;
    foreach ($records as $record) {
        //Cycle through each record
        echo "Copying record #{$i}...";
        //Grab fields
        $accnum = mysql_real_escape_string($record->getField('Accession Number'));
        $name = mysql_real_escape_string($record->getField('Name'));
        $geo1continent = mysql_real_escape_string($record->getField('Geo 1 Continent'));
        $geo2country = mysql_real_escape_string($record->getField('Geo 2 Country'));
        $geo3region = mysql_real_escape_string($record->getField('Geo 3 Region'));
        $geo4city = mysql_real_escape_string($record->getField('Geo 4 City'));
        $geo5locality = mysql_real_escape_string($record->getField('Geo 5 Locality'));
        $nomen1 = mysql_real_escape_string($record->getField('Nomenclature 1'));
        $nomen2 = mysql_real_escape_string($record->getField('Nomenclature 2'));
        $nomen3 = mysql_real_escape_string($record->getField('Nomenclature 3'));
        $period1 = mysql_real_escape_string($record->getField('Period 1'));
        $period3date = mysql_real_escape_string($record->getField('Period 3 Date'));
        $visualdescription = mysql_real_escape_string($record->getField('Visual Description'));
        $religion1 = mysql_real_escape_string($record->getField('Religion 1'));
        $culture1 = mysql_real_escape_string($record->getField('Culture 1'));
        $dimen1num = mysql_real_escape_string($record->getField('Dimen 1 Number')); //in centimeters
        $dimen2num = mysql_real_escape_string($record->getField('Dimen 2 Number')); //in centimeters
        $dimen3num = mysql_real_escape_string($record->getField('Dimen 3 Number')); //in centimeters
        $dimen1type = mysql_real_escape_string($record->getField('Dimen 1 Type'));
        $dimen2type = mysql_real_escape_string($record->getField('Dimen 2 Type'));
        $dimen3type = mysql_real_escape_string($record->getField('Dimen 3 Type'));
        $materials2 = mysql_real_escape_string($record->getField('Materials 2'));
        $manufacturingprocesses2 = mysql_real_escape_string($record->getField('Manufacturing Processes 2'));
        $weight = mysql_real_escape_string($record->getField('Weight')); //in grams
        $measuringremarks = mysql_real_escape_string($record->getField('Measuring Remarks'));
        $munsellcolorinfo = mysql_real_escape_string($record->getField('Munsell Color Information'));
        $reproduction = mysql_real_escape_string($record->getField('Reproduction'));
        $reproductionnote = mysql_real_escape_string($record->getField('Reproduction Note'));
        $publisheddescription = mysql_real_escape_string($record->getField('Published Description'));
        $scholarlynotes = mysql_real_escape_string($record->getField('Scholarly Notes'));
        $bibliography = mysql_real_escape_string($record->getField('Bibliography'));
        $comparanda = mysql_real_escape_string($record->getField('Comparanda'));
        $exhibitlabel = mysql_real_escape_string($record->getField('Exhibit Label'));
        $artist = mysql_real_escape_string($record->getField('Artist'));
        $spurlockloc2 = mysql_real_escape_string($record->getField('Spurlock Loc 2'));
        $spurlockloc3 = mysql_real_escape_string($record->getField('Spurlock Loc 3'));
        $archaeologicaldata = mysql_real_escape_string($record->getField('Archaeological Data'));
        $creditline = mysql_real_escape_string($record->getField('Credit Line'));
        $provenance = mysql_real_escape_string($record->getField('Provenance'));
        $museumdedication = mysql_real_escape_string($record->getField('Museum Dedication'));
        $spurlockstatus = mysql_real_escape_string($record->getField('Spurlock Status'));
        $publicdescription = mysql_real_escape_string($record->getField('Public Description'));

        $workingset1 = mysql_real_escape_string($record->getField('Working Set 1 GP'));
        $workingset2 = mysql_real_escape_string($record->getField('Working Set 2 GP'));
        $workingset3 = mysql_real_escape_string($record->getField('Working Set 3 SER'));
        $workingsetCombined = $workingset1 . ' ' . $workingset2 . ' ' . $workingset3;
        $workingset5 = mysql_real_escape_string($record->getField('Working Set 5 WB'));
        $imgsrc = mysql_real_escape_string($record->getField('Image Source'));
        $CMMECMA = mysql_real_escape_string($record->getField('CM Mec MA'));
        $webprivate = mysql_real_escape_string($record->getField('webprivate'));
        $hiresimagecheck = mysql_real_escape_string($record->getField('hiresimagecheck'));

        //Skip blank records
        if ($accnum === "") {
            echo "blank record..skipping..<br>";
            $numRecordsSkipped++;
            continue;
        }

        //Skip suppressed records
        if (strpos($webprivate, "Record") !== FALSE) {
            echo "suppressed..skipping..[$accnum]<br>";
            $numRecordsSkipped++;
            continue;
        }

        //Check if the continent exists
        $query = "SELECT gid FROM geocontinent WHERE continent='$geo1continent'";
        $result = mysql_query($query);
        if ($row = mysql_fetch_assoc($result)) {
            //It exists, just grab the ID
            $geo1ID = $row['gid'];
        } else {
            //It doesn't exist, insert it and then grab the ID
            if (shouldBeSuppressed($geo1continent)) {
                $suppressed = 1;
            } else {
                $suppressed = 0;
            }
            $query = "INSERT INTO geocontinent (`continent`,`suppressed`)
						VALUES ('$geo1continent','$suppressed')";
            $result = mysql_query($query);
            $query = "SELECT gid FROM geocontinent WHERE continent='$geo1continent'";
            $result = mysql_query($query);
            $row = mysql_fetch_assoc($result);
            $geo1ID = $row['gid'];
        }


        //Check if the country exists (with same parent)
        $query = "SELECT gid FROM geocountry WHERE country='$geo2country' AND pid='$geo1ID'";
        $result = mysql_query($query);
        if ($row = mysql_fetch_assoc($result)) {
            //It exists, just grab the ID
            $geo2ID = $row['gid'];
        } else {
            //It doesn't exist, insert it and then grab the ID
            if (shouldBeSuppressed($geo2country)) {
                $suppressed = 1;
            } else {
                $suppressed = 0;
            }
            $query = "INSERT INTO geocountry (`country`,`pid`,`suppressed`)
						VALUES ('$geo2country','$geo1ID','$suppressed')";
            $result = mysql_query($query);
            $query = "SELECT gid FROM geocountry WHERE country='$geo2country' AND pid='$geo1ID'";
            $result = mysql_query($query);
            $row = mysql_fetch_assoc($result);
            $geo2ID = $row['gid'];
        }

        //Check if the region exists (with same parent)
        $query = "SELECT gid FROM georegion WHERE region='$geo3region' AND pid='$geo2ID'";
        $result = mysql_query($query);
        if ($row = mysql_fetch_assoc($result)) {
            //It exists, just grab the ID
            $geo3ID = $row['gid'];
        } else {
            //It doesn't exist, insert it and then grab the ID
            if (shouldBeSuppressed($geo3region)) {
                $suppressed = 1;
            } else {
                $suppressed = 0;
            }
            $query = "INSERT INTO georegion (`region`,`pid`,`suppressed`)
						VALUES ('$geo3region','$geo2ID','$suppressed')";
            $result = mysql_query($query);
            $query = "SELECT gid FROM georegion WHERE region='$geo3region' AND pid='$geo2ID'";
            $result = mysql_query($query);
            $row = mysql_fetch_assoc($result);
            $geo3ID = $row['gid'];
        }

        //Check if the city exists (with same parent)
        $query = "SELECT gid FROM geocity WHERE city='$geo4city' AND pid='$geo3ID'";
        $result = mysql_query($query);
        if ($row = mysql_fetch_assoc($result)) {
            //It exists, just grab the ID
            $geo4ID = $row['gid'];
        } else {
            //It doesn't exist, insert it and then grab the ID
            if (shouldBeSuppressed($geo4city)) {
                $suppressed = 1;
            } else {
                $suppressed = 0;
            }
            $query = "INSERT INTO geocity (`city`,`pid`,`suppressed`)
						VALUES ('$geo4city','$geo3ID','$suppressed')";
            $result = mysql_query($query);
            $query = "SELECT gid FROM geocity WHERE city='$geo4city' AND pid='$geo3ID'";
            $result = mysql_query($query);
            $row = mysql_fetch_assoc($result);
            $geo4ID = $row['gid'];
        }

        //Check if the locality exists (with same parent)
        $query = "SELECT gid FROM geolocality WHERE locality='$geo5locality' AND pid='$geo4ID'";
        $result = mysql_query($query);
        if ($row = mysql_fetch_assoc($result)) {
            //It exists, just grab the ID
            $geo5ID = $row['gid'];
        } else {
            //It doesn't exist, insert it and then grab the ID
            if (shouldBeSuppressed($geo5locality)) {
                $suppressed = 1;
            } else {
                $suppressed = 0;
            }
            $query = "INSERT INTO geolocality (`locality`,`pid`,`suppressed`)
						VALUES ('$geo5locality','$geo4ID','$suppressed')";
            $result = mysql_query($query);
            $query = "SELECT gid FROM geolocality WHERE locality='$geo5locality' AND pid='$geo4ID'";
            $result = mysql_query($query);
            $row = mysql_fetch_assoc($result);
            $geo5ID = $row['gid'];
        }

        //Check if the culture exists
        $query = "SELECT cid FROM culture WHERE culture='$culture1'";
        $result = mysql_query($query);
        if ($row = mysql_fetch_assoc($result)) {
            //It exists, just grab the ID
            $cultureID = $row['cid'];
        } else {
            //It doesn't exist, insert it and then grab the ID
            if (shouldBeSuppressed($culture1)) {
                $suppressed = 1;
            } else {
                $suppressed = 0;
            }
            $query = "INSERT INTO culture (`culture`,`suppressed`)
						VALUES ('$culture1','$suppressed')";
            $result = mysql_query($query);
            $query = "SELECT cid FROM culture WHERE culture='$culture1'";
            $result = mysql_query($query);
            $row = mysql_fetch_assoc($result);
            $cultureID = $row['cid'];
        }

        $nomen2_1ID = 0;
        $nomen2_2ID = 0;
        $nomen2_3ID = 0;
        //Check if the nomenclature has distinct trees
        //NOTE: Only going to consider 2 trees here, it is bad style but we don't want the database to be too complicated
        if (strpos($nomen1, ",") !== FALSE) {
            //Split the nomenclature into its distinct trees
            $categories = explode(", ", $nomen1);
            $classifications = explode(", ", $nomen2);
            $subclassifications = explode(", ", $nomen3);
            $nomen2_1 = '';
            $nomen2_2 = '';
            $nomen2_3 = '';
            if (count($categories) > 1) {
                $nomen2_1 = $categories[1];
            }
            if (count($classifications) > 1) {
                $nomen2_2 = $classifications[1];
            }
            if (count($subclassifications) > 1) {
                $nomen2_3 = $subclassifications[1];
            }
            $nomens = addNomenclature($nomen2_1, $nomen2_2, $nomen2_3);
            $nomen2_1ID = $nomens[0];
            $nomen2_2ID = $nomens[1];
            $nomen2_3ID = $nomens[2];
            $nomen1 = $categories[0];
            $nomen2 = $classifications[0];
            $nomen3 = $subclassifications[0];
        }

        $nomens = addNomenclature($nomen1, $nomen2, $nomen3);
        $nomen1_1ID = $nomens[0];
        $nomen1_2ID = $nomens[1];
        $nomen1_3ID = $nomens[2];

        if (isOnDisplay($spurlockloc2)) {
            $onDisplay = 1;
        } else {
            $onDisplay = 0;
        }

        //Check if artifact record already exists
        $query = "SELECT aid FROM artifacts WHERE `accession number`='$accnum'";
        $result = mysql_query($query);
        if ($row = mysql_fetch_assoc($result)) {
            //It exists. just update it
            $query = "UPDATE artifacts
						SET `name` = '$name',
						`geo1` = '$geo1ID',
					    `geo2` = '$geo2ID',
					    `geo3` = '$geo3ID',
					    `geo4` = '$geo4ID',
					    `geo5` = '$geo5ID',
					    `nomen1_1` = '$nomen1_1ID', 
					    `nomen1_2` = '$nomen1_2ID', 
					    `nomen1_3` = '$nomen1_3ID',
					    `nomen2_1` = '$nomen2_1ID', 
					    `nomen2_2` = '$nomen2_2ID', 
					    `nomen2_3` = '$nomen2_3ID', 
					    `period 1` = '$period1', 
					    `period 3 date` = '$period3date',
					    `visual description` = '$visualdescription',
					    `religion 1` = '$religion1',
					    `culture1` = '$cultureID', 
					    `dimen 1 number` = '$dimen1num', 
					    `dimen 2 number` = '$dimen2num', 
					    `dimen 3 number` = '$dimen3num', 
					    `dimen 1 type` = '$dimen1type', 
					    `dimen 2 type` = '$dimen2type', 
					    `dimen 3 type` = '$dimen3type',
					    `materials 2` = '$materials2',
					    `manufacturing processes 2` = '$manufacturingprocesses2',
					    `weight` = '$weight',
					    `measuring remarks` = '$measuringremarks', 
					    `munsell color information` = '$munsellcolorinfo',
					    `reproduction` = '$reproduction', 
					    `reproduction notes` = '$reproductionnote',
					    `published description` = '$publisheddescription', 
					    `scholarly notes` = '$scholarlynotes', 
					    `bibliography` = '$bibliography',
					    `comparanda` = '$comparanda',
					    `exhibit label` = '$exhibitlabel',
					    `artist` = '$artist',
					    `spurlock loc 2` = '$spurlockloc2',
					    `spurlock loc 3` = '$spurlockloc3', 
					    `archaeological data` = '$archaeologicaldata', 
					    `credit line` = '$creditline',
					    `provenance` = '$provenance', 
					    `museum dedication` = '$museumdedication', 
					    `on_display` = '$onDisplay', 
					    `public description` = '$publicdescription', 
					    `working set 5 wb` = '$workingset5', 
					    `working set 123` = '$workingsetCombined',
					    `image source` = '$imgsrc',
					    `cm mec ma` = '$CMMECMA', 
					    `webprivate` = '$webprivate',
					    `hiresimagecheck` = '$hiresimagecheck'
					    WHERE `accession number` = '$accnum'";
            $result = mysql_query($query);
        } else {
            //It doesn't exist, insert it
            $query = "INSERT INTO artifacts
					    (`accession number`, 
					     `name`, 
					     `geo1`, 
					     `geo2`, 
					     `geo3`, 
					     `geo4`, 
					     `geo5`, 
					     `nomen1_1`, 
					     `nomen1_2`, 
					     `nomen1_3`, 
					     `nomen2_1`, 
					     `nomen2_2`, 
					     `nomen2_3`, 
					     `period 1`, 
					     `period 3 date`, 
					     `visual description`, 
					     `religion 1`, 
					     `culture1`, 
					     `dimen 1 number`, 
					     `dimen 2 number`, 
					     `dimen 3 number`, 
					     `dimen 1 type`, 
					     `dimen 2 type`, 
					     `dimen 3 type`, 
					     `materials 2`, 
					     `manufacturing processes 2`, 
					     `weight`, 
					     `measuring remarks`, 
					     `munsell color information`, 
					     `reproduction`, 
					     `reproduction notes`, 
					     `published description`, 
					     `scholarly notes`, 
					     `bibliography`, 
					     `comparanda`, 
					     `exhibit label`, 
					     `artist`,
					     `spurlock loc 2`, 
					     `spurlock loc 3`, 
					     `archaeological data`, 
					     `credit line`, 
					     `provenance`, 
					     `museum dedication`, 
					     `on_display`, 
					     `public description`, 
					     `working set 5 wb`, 
					     `working set 123`,
					     `image source`, 
					     `cm mec ma`, 
					     `webprivate`,
					     `hiresimagecheck`) 
				 VALUES ('$accnum', 
					     '$name', 
					     '$geo1ID', 
					     '$geo2ID', 
					     '$geo3ID', 
					     '$geo4ID', 
					     '$geo5ID', 
					     '$nomen1_1ID', 
					     '$nomen1_2ID', 
					     '$nomen1_3ID', 
					     '$nomen2_1ID', 
					     '$nomen2_2ID', 
					     '$nomen2_3ID',
					     '$period1', 
					     '$period3date', 
					     '$visualdescription', 
					     '$religion1', 
					     '$cultureID', 
					     '$dimen1num', 
					     '$dimen2num', 
					     '$dimen3num', 
					     '$dimen1type', 
					     '$dimen2type', 
					     '$dimen3type', 
					     '$materials2', 
					     '$manufacturingprocesses2', 
					     '$weight', 
					     '$measuringremarks', 
					     '$munsellcolorinfo', 
					     '$reproduction', 
					     '$reproductionnote', 
					     '$publisheddescription', 
					     '$scholarlynotes', 
					     '$bibliography', 
					     '$comparanda', 
					     '$exhibitlabel', 
					     '$artist', 
					     '$spurlockloc2',
					     '$spurlockloc3', 
					     '$archaeologicaldata', 
					     '$creditline', 
					     '$provenance', 
					     '$museumdedication', 
					     '$onDisplay', 
					     '$publicdescription', 
					     '$workingset5', 
					     '$workingsetCombined',
					     '$imgsrc', 
					     '$CMMECMA', 
					     '$webprivate',
					     '$hiresimagecheck')";
            $result = mysql_query($query);
        }

        echo "  done [$accnum]<br>";
        $i++;
    }
}

/**
 * Checks if a facet (string) should be suppressed. Checks for appropriate length and against a "bad" pattern.
 *
 * @param $facet The facet to check (string)
 * @return bool True if the facet should be suppressed, False otherwise
 */
function shouldBeSuppressed($facet)
{
    if (strlen($facet) > MAX_FACET_LENGTH || strlen($facet) < 1) {
        return true;
    }
    if (preg_match(BAD_FACET_REGEX, $facet)) {
        return true;
    }
    return false;
}

/**
 * Checks if a location attribute corresponds to the artifact being on display.
 *
 * @param $loc2 The location attribute of the artifact
 * @return bool True if the attribute corresponds to an 'On Display' status, False otherwise
 */
function isOnDisplay($loc2)
{
    $gallery_array = array('AFR', 'CCR', 'AMN', 'AMS', 'MED', 'ASE', 'EGY', 'EUR', 'SEO', 'MSO');
    if (in_array($loc2, $gallery_array)) {
        return true;
    }
    return false;
}

/**
 * Inserts a nomenclature tree into the database
 *
 * @param string $nom1 The base nomenclature
 * @param string $nom2 The secondary nomenclature
 * @param string $nom3 The tertiary nomenclature
 * @return array An array containing the IDs of the newly inserted nomenclature values
 */
function addNomenclature($nom1 = '', $nom2 = '', $nom3 = '')
{

    if ($nom1 == '') {
        return array(0, 0, 0);
    }
    //Check if category exists
    $query = "SELECT nid FROM nomcategory WHERE category='$nom1'";
    $result = mysql_query($query);
    if ($row = mysql_fetch_assoc($result)) {
        //It exists, just grab the ID
        $nomen1ID = $row['nid'];
    } else {
        if (shouldBeSuppressed($nom1)) {
            $suppressed = 1;
        } else {
            $suppressed = 0;
        }
        //It doesn't exist, insert it and then grab the ID
        $query = "INSERT INTO nomcategory (`category`,`suppressed`)
					VALUES ('$nom1','$suppressed')";
        $result = mysql_query($query);
        $query = "SELECT nid FROM nomcategory WHERE category='$nom1'";
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $nomen1ID = $row['nid'];
    }

    if ($nom2 == '') {
        return array($nomen1ID, 0, 0);
    }
    //Check if classification exists
    $query = "SELECT nid FROM nomclassification WHERE classification='$nom2'";
    $result = mysql_query($query);
    if ($row = mysql_fetch_assoc($result)) {
        //It exists, just grab the ID
        $nomen2ID = $row['nid'];
    } else {
        if (shouldBeSuppressed($nom2)) {
            $suppressed = 1;
        } else {
            $suppressed = 0;
        }
        //It doesn't exist, insert it and then grab the ID
        $query = "INSERT INTO nomclassification (`classification`,`pid`,`suppressed`)
					VALUES ('$nom2','$nomen1ID','$suppressed')";
        $result = mysql_query($query);
        $query = "SELECT nid FROM nomclassification WHERE classification='$nom2'";
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $nomen2ID = $row['nid'];
    }

    if ($nom3 == '') {
        return array($nomen1ID, $nomen2ID, 0);
    }
    //Check if sub-classification exists
    $query = "SELECT nid FROM nomsubclassification WHERE subclassification='$nom3'";
    $result = mysql_query($query);
    if ($row = mysql_fetch_assoc($result)) {
        //It exists, just grab the ID
        $nomen3ID = $row['nid'];
    } else {
        if (shouldBeSuppressed($nom3)) {
            $suppressed = 1;
        } else {
            $suppressed = 0;
        }
        //It doesn't exist, insert it and then grab the ID
        $query = "INSERT INTO nomsubclassification (`subclassification`,`pid`,`suppressed`)
					VALUES ('$nom3','$nomen2ID','$suppressed')";
        $result = mysql_query($query);
        $query = "SELECT nid FROM nomsubclassification WHERE subclassification='$nom3'";
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $nomen3ID = $row['nid'];
    }
    return array($nomen1ID, $nomen2ID, $nomen3ID);
}

?>

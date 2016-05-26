<?php

//Remove script timeout and increase memory limit to 4gb
ini_set('memory_limit', '4096M');
set_time_limit(0);

//Display all errors
//error_reporting(E_ALL ^ E_NOTICE);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

require_once('FileMaker.php');

/**
 * Class artifactRetriever
 *
 *      Because this script loads all artifacts into a single array, this
 *    script can eat a large chunk of memory. You can see that I raised
 *    the 'memory_limit' derivative for this script to 4GB. Which is
 *    plenty. When first creating this script, we found that the database
 *    to web connection dies when you try to grab all of the artifacts
 *    at one time. To get around this, I came up with a system of doing
 *    roughly twenty-five smaller queries and condensing all of the results
 *    into one array outside of the database to avoid any down time. You can
 *    see the array 'folders' that holds the various names of all of the
 *    possible 'image source' folders that the artifacts in the database
 *    use. I loop through this array searching only for artifacts that say
 *    their 'image source' is that folder, and then pause for a few seconds
 *    between each query so that the database does not become overloaded.
 *    After I have all of the artifacts into one array, I serialize it and
 *    write it to file, forming a sort of cache file. If you when rerun the
 *    script while this cache file is still present, it will read that file
 *    instead of going all the way back to the database.
 *
 *        To use this class, simple instantiate it and call retrieveArtifacts()
 *        This will return an array of FileMaker PHP API record objects that you
 *        can then access individually. The constructor takes in one argument,
 *        the name of the layout to use
 *
 *        Make sure that the 'artifacts' cache file has permissions.
 *        Basically the script needs write permissions on/in the folder.
 *
 *    You may notice some weird flush and ob_flush stuff all over the script;
 *    this is to force the output to appear in the browser before the script
 *    is done executing, otherwise nothing would be output until the very end.
 *
 * @package CollectionsSearch
 * @subpackage Import
 * @author Michael Robinson
 * @author Yang Lu
 */
class artifactRetriever
{

    const DATABASE_SERVER_IP = "x";
    // const DATABASE_SERVER_IP = "x";

    /**
     * @var FileMaker
     */
    public $fm;
    /**
     * @var array
     */
    public $records = array();
    /**
     * @var string
     */
    public $layout;
    /**
     * @var bool
     */
    private $useCache = true;

    function __construct($layoutToUse, $useCache = true)
    {
        $this->fm = new FileMaker('Artifacts_web', artifactRetriever::DATABASE_SERVER_IP, NULL, NULL); //create connection to database with guest login
        if (FileMaker::isError($this->fm)) {
            echo "Error: " . $this->fm->getMessage() . "\n";
            exit;
        }
        $this->layout = $layoutToUse;
        $this->useCache = $useCache;
    }

    /**
     * Retrieve a specific set of artifacts
     *
     * @param $accNums An array of accession numbers to find
     * @return array Resulting artifacts, as Filemaker objects
     */
    public function retrieveArtifactSet($accNums)
    {
        //doesn't use cache
        $results = array();
        foreach ($accNums as $accnum) {
            if (strlen($accnum) < 2) {
                continue;
            }
            $find = $this->fm->newFindCommand($this->layout); //use a simple layout that only has the fields we need
            //check for errors
            if (FileMaker::isError($find)) {
                echo "Error: " . $find->getMessage() . "<br>";
                continue;
            }
            $find->addFindCriterion('Accession Number', $accnum);
            //check for errors
            if (FileMaker::isError($find)) {
                echo "Error: " . $find->getMessage() . "<br>";
                continue;
            }
            $result = $find->execute();
            //throw an error if there was a problem
            if (FileMaker::isError($result)) {
                echo "Error: " . $result->getMessage() . "<br>";
                continue;
            }
            //retrieve those record objects into an array
            foreach ($result->getRecords() as $record) {
                $results[] = $record;
            }
        }
        return $results;
    }

    /**
     * Retrieve all artifacts
     * @return array Resulting artifacts, as Filemaker objects
     */
    public function retrieveArtifacts()
    {echo "Hopefully creating the folder array variable?";
	$CGIlayout = $this->fm->getLayout('CGIdetails');
	if (FileMaker::isError($ImgSourceAssoc)) {
                echo "Error: " . $ImgSourceAssoc->getMessage() . "<br>";
                continue;
            }
	$ImgSourceAssoc = $CGIlayout->getValueListTwoFields('PHP: Values in Image Source');
		if (FileMaker::isError($ImgSourceAssoc)) {
                echo "Error: " . $ImgSourceAssoc->getMessage() . "<br>";
                continue;
            }
    $this->folders = array_values($ImgSourceAssoc);  //Re-indexes Array by Numeric "Keys"
	echo "Finished creating folder array variable";

        ob_implicit_flush();
        if (ob_get_level() == 0) {
            ob_start();
        }
        echo str_pad('Loading... ', 4096) . "\n";

        if (file_exists('artifacts') && $this->useCache) {
            //If the cache file exists, load from it
            $ser = file_get_contents('artifacts');
            $this->records = unserialize($ser);
            echo "Loading artifacts from local cached file. Delete 'artifacts' to force recaching.\n";
            ob_flush();
            flush();
        } else { //Not cached, do queries to collect all of the artifacts
            //First check permissions on cache file
            $fh = fopen('artifacts', 'w') or die("Failed to open 'artifacts' (cache file), check permissions.\n");
            fclose($fh);
            for ($i = 0; $i < count($this->folders); $i++) {
                echo "Creating query #{$i}..\r\n";
                //Create a new query
                flush();
                $find = $this->fm->newFindCommand($this->layout); //use a simple layout that only has the fields we need

                //Check for errors
                if (FileMaker::isError($find)) {
                    echo "Error: " . $find->getMessage() . "\n";
                    exit;
                }

                echo "Looking for artifacts with images in folder '{$this->folders[$i]}'..\n";
                ob_flush();
                flush();

                //Set up the find to only pick artifacts that have a certain image source
                $find->addFindCriterion('Image Source', $this->folders[$i]);

                //Check for errors
                if (FileMaker::isError($find)) {
                    echo "Error: " . $find->getMessage() . "\n";
                    exit;
                }

                echo "Executing query..\n";
                ob_flush();
                flush();

                //Execute the query
                $result = $find->execute();

                //Throw an error if there was a problem
                if (FileMaker::isError($result)) {
                    echo "Error: " . $result->getMessage() . "\n";
                    exit;
                }

                //Find the number of results from this query
                $num_results[$i] = $result->getFoundSetCount();

                echo "Found {$num_results[$i]} artifacts..\n";
                ob_flush();
                flush();
                //Retrieve those record objects into an array
                $my_records = $result->getRecords();

                echo "Adding these results to our total inventory..\n";
                ob_flush();
                flush();
                //Merge the records arrays
                $this->mergeToGlobal($my_records);

                echo "Waiting a few seconds so we don't kill the server..\n";
                ob_flush();
                flush();
                for ($j = 3; $j >= 0; $j--) {
                    echo $j;
                    ob_flush();
                    flush();
                    sleep(1);
                    echo ".";
                    flush();
                    ob_flush();
                    sleep(1);
                    echo ".";
                    flush();
                    ob_flush();
                    sleep(1);
                    echo ".";
                    ob_flush();
                    flush();
                    sleep(1);
                    echo ".";
                    ob_flush();
                    flush();
                }
                $this_count = count($this->records);
                echo "\nDEBUG: total inventory contains {$this_count} items.\n";
                $kb = memory_get_usage() / 1024;
                $mb = $kb / 1024;
                $mb = number_format($mb, 2, '.', '');
                echo "DEBUG: currently using {$mb}MB of memory.\n";
                ob_flush();
                flush();
            }

            $num_results = count($this->records);
            echo "Found {$num_results} total records.\n";
            ob_flush();
            flush();

            //Write array to file (cache)
            $this->writeArrayToFile($this->records, 'artifacts');
        }
        return $this->records;
    }


    /**
     * Appends each element of $array into the records array
     * @param $array The array to merge into the records array
     */
    private function mergeToGlobal(&$array)
    {
        foreach ($array as $new_record) {
            array_push($this->records, $new_record);
        }
        unset($array);
    }

    /**
     * Serializes the records array and writes it to a file in the current directory (essentially caching it)
     * @param $array The array to write to file
     * @param $filename The filename to write the file as
     */
    private function writeArrayToFile(&$array, $filename)
    {
        $ser = serialize($array);
        $fh = fopen($filename, 'w') or die("Failed to open '{$filename}', check permissions.\n");
        fwrite($fh, $ser);
        fclose($fh);
    }
}

?>

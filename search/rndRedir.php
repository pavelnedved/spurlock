<?php

/*
	Random Artifacts Redirection Script
		Date created: Unknown
		Modifications:
			[4/26/11] MTR - Cleaned up the code to be more concise, added better error handling in the event of database downtime.
*/ 

$rnd = "random{$_GET['rnd']}";
include_once("../support/php/FX/FX.php");
include_once("../support/php/FX/server_data.php");
include_once("../support/php/include.php");
$DatasetQuery = new FX($serverIP, $webCompanionPort, $dataSourceType);
$DatasetQuery->SetDBData("Artifacts_web", "CGI", 1);
$DatasetQuery->AddDBParam("Working Set 5 WB", $rnd, "eq");
$results = $DatasetQuery->FMFind();

//Check that there was no error
if (!isError($results)) {
    foreach ($results['data'] as $art) {
        $imgacc = $art['Accession Number'][0];
    }
    if ($results['foundCount'] > 1) {
        $q = substr($imgacc, 0, 12);
        $link = "http://www.spurlock.illinois.edu/search/index.php?q={$q}";
    } else {
        $link = "http://www.spurlock.illinois.edu/search/details.php?a={$imgacc}";
    }
} else {
//there was an error, most likely database is down, so just send them to an arbitrary details page and let it handle the database issue
    $link = "http://www.spurlock.illinois.edu/search/details.php?a=1971.15.3389";
    //echo $results['errorCode'];
}
?>
<script language="javascript" type="text/javascript">
    <!--
    window.location = "<?php echo $link; ?>";
    //-->
</script>
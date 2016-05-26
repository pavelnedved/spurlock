<?php

/**
 * Needed to redirect to virtual tour
 */

include_once("../support/php/FX/FX.php");
include_once("../support/php/FX/server_data.php");
$imgacc = $_GET['imgacc'];
//echo $imgacc;
$vTourQuery = new FX($serverIP, $webCompanionPort, $dataSourceType);
$vTourQuery->SetDBData("Virtual Tour_web", "Layout #1", 1);
$vTourQuery->AddDBParam("accession number", $imgacc, "cn");
$vTourInfo = $vTourQuery->FMFind();
//echo $vTourInfo['errorCode']; 
$vtLink = "/vtour/";
//print_r ($vTourInfo['data']);
if ($vTourInfo['foundCount'] > 0) {
    foreach ($vTourInfo['data'] as $data) {
        $location = $data['Spurlock Loc 2'][0];
        $title = $data['Keyword'][0];
    }
    //possible sections - this will need to be updated as the vtour expands
    include_once("../vtour/vtour_php/static/phpjs/gallery.php");
    if (definePath($location))
        $section = definePath($location);
    else {
        echo "Item not found in Virtal Tour";
        echo "</br>";
        echo "section= " . $section;
        echo "</br>";
        echo "title= " . $title;
        echo "</br>";
        ?>
        <a href="javascript:history.go(-1)" title="Previous Page"
           onMouseOver="window.status='Previous Page'; return true"
           onMouseOut="window.status=''; return true">Go Back</a>
        <?php exit();
    }

} else { //not in vtour (probably in an incomplete section)
    echo "Item not found in Virtal Tour";  ?>
    <a href="javascript:history.go(-1)" title="Previous Page"
       onMouseOver="window.status='Previous Page'; return true"
       onMouseOut="window.status=''; return true">Go Back</a>
    <?php exit();
}
$vtLink = $vtLink . $section . "/" . $title;
?>

<?php
/*
<a href="<?php echo $vtLink; ?>"> Click Here</a>

<script language="javascript" type="text/javascript">
<!--
window.location = "<?php echo $vtLink; ?>"
//-->
</script>*/

//elaine doesn't want to use javascript! harumph...

header("Location: $vtLink");

echo "<a href='$vtLink; ?>Click Here</a>";

?>

<?php

//DEBUG: SHOW ALL WARNINGS/ERRORS
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

require_once("searchAuxiliary.php");
require_once("collectionsBackend.class.php");
require_once("detailsSidebar.class.php");
require_once("randomSidebarFactory.class.php");
require_once("searchTracker.class.php");

$prevSearchRequest = null;

//If the referer is set, try to make a search request out of it, it might be that the user came from a results page
if (isset($_SERVER["HTTP_REFERER"])) {
    $referer = $_SERVER["HTTP_REFERER"];
    $prevSearchRequest = new searchRequest($referer);
    $searchRequest = new searchRequest();
} else {
    $searchRequest = new searchRequest();
}

//Tell the search request to search exactly the accession number
$searchRequest->setSearchExactAccession();

if (isset($_GET['a'])) {
    $a = $_GET['a'];
}
if (isset($_GET['sid'])) {
    $sid = $_GET['sid'];
} else {
    $sid = 0;
}
if (isset($_GET['rand'])) {
    $rand = $_GET['rand'];
} else {
    $rand = 0;
}
if (isset($_GET['rel'])) {
    $rel = $_GET['rel'];
} else {
    $rel = 0;
}

$collectionsBackend = new collectionsBackend();
$randomSidebarFactory = new randomSidebarFactory();
$searchTracker = new searchTracker();
$resultSet = array();
$artifact = null;

//Execute the search request to find the single artifact
if ($collectionsBackend->executeRequest($searchRequest, FALSE)) {
    if ($collectionsBackend->getFoundSetCount() != 1) {
        //If the accession number given results in more than one artifact
        header('Location: index.php?q=' . $a . '&g=All&Search=Search');
        exit;
    }
    $resultSet = $collectionsBackend->getResults();
    $artifact = $resultSet[0];
} else {
    //If the search fails, redirect to the main search page
    header('Location: index.php');
    exit;
}

//If there was a previous search request, show that sidebar
if ($prevSearchRequest !== null) {
    $prevCollectionsBackend = new collectionsBackend(randomSidebarFactory::NUM_ARTIFACTS_PER_SIDEBAR + 1);
    $prevCollectionsBackend->executeRequest($prevSearchRequest, FALSE);
    $prevResultSet = $prevCollectionsBackend->getResults();
}

//Update the artifact's stats in the tracking database
$searchTracker->updateArtifact($sid, $rel, $rand, $artifact);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en"><!-- InstanceBegin template="/Templates/SM-Main.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- InstanceBeginEditable name="doctitle" -->
<title>Collections, Spurlock Museum, University of Illinois at Urbana-Champaign</title>
<!-- InstanceEndEditable -->
<link href="http://www.spurlock.illinois.edu/support/CSS/twoColElsRtHdr.css" rel="stylesheet" type="text/css">
<link href="http://www.spurlock.illinois.edu/support/CSS/Design.css" rel="stylesheet" type="text/css" media="screen">
<link href="http://www.spurlock.illinois.edu/support/CSS/Print.css" rel="stylesheet" type="text/css" media="print">
<link href="http://www.spurlock.illinois.edu/support/CSS/IE.css" rel="stylesheet" type="text/css" media="screen">
<link rel="icon" href="http://www.spurlock.illinois.edu/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="http://www.spurlock.illinois.edu/favicon.ico" type="image/x-icon">
<!-- SmartMenus 6 config and script core files -->
<link href="http://www.spurlock.illinois.edu/support/CSS/SMenuCORE.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="http://www.spurlock.illinois.edu/support/c_config.js"></script>
<script type="text/javascript" src="http://www.spurlock.illinois.edu/support/c_smartmenus.js"></script>
<script type="text/javascript" src="http://www.spurlock.illinois.edu/support/jquery.js"></script>
<script type="text/javascript" src="http://www.spurlock.illinois.edu/support/headRotating.js"></script>
<script type="text/javascript" src="http://www.spurlock.illinois.edu/search/JS/PhpFormMailing.js"></script>
<!--[if IE]>
<style type="text/css"> 
/* place css fixes for all versions of IE in this conditional comment */
.twoColElsRtHdr #sidebar1 { padding-top: 30px; }
.twoColElsRtHdr #mainContent { zoom: 1; padding-top: 15px; }
/* the above proprietary zoom property gives IE the hasLayout it needs to avoid several bugs */
</style>
<![endif]-->
<!-- the below script allows for PNG transparency in PC IE, prior to IE7 -->
<!--[if lt IE 7]>
<script defer type="text/javascript" src="http://www.spurlock.illinois.edu/support/pngfix.js"></script>
<![endif]-->
<script type="text/JavaScript">
	$('html').addClass('randArtRotating');
	$(document).ready(function() {
		$('html').removeClass('randArtRotating');
		$("#SMrotating").html('');
		$("#SMrotating").rotatingElement({
			'enableRotation' : true,
			'imageFolder' : '/elements/random/',
			'refreshInterval' : 15000,
			'animationLength' : 2000,
			'preload' : true,
			'fadeInAnimation' : {opacity: 1.0},
			'fadeOutAnimation' : {opacity: 0.0}
		});
	});
</script>
<script>
//Did have phpmailer js here..now in separate file  -Remove this comment
</script>
<!--[if lte IE 7]>
<style type="text/css">
	.twoColElsRtHdr #container #SMheaderbase #SMheadertitle{
    	padding-left: 10px !important;
     }
</style>
<![endif]-->
<!--[if lte IE 8]>
<script type="text/JavaScript">
	$(document).ready(function() {
		checkSize();
		$(window).resize(function() {
			checkSize();
		});
	});
	function checkSize(){
		width = $(window).width();
		if(width < 970){
			$("#SMrotating").addClass("disable_rotate");
		}else{
			$("#SMrotating").removeClass("disable_rotate");	
		}
	}
</script>
<![endif]-->
<!-- InstanceBeginEditable name="localstyles" -->
<link href="CSS/details.css" rel="stylesheet" type="text/css">
<link href="CSS/foundationPBBFG.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--


 -->
</style>
<!-- InstanceEndEditable --><!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
<!-- Do not edit this parameter directly.   -->
<!-- InstanceParam name="containerstyle" type="text" value="background-color:#FFFFFF" --><!-- InstanceParam name="collectionsCurrent" type="text" value="" --><!-- InstanceParam name="artmonCurrent" type="text" value="" --><!-- InstanceParam name="newacqCurrent" type="text" value="" --><!-- InstanceParam name="collhighCurrent" type="text" value="" --><!-- InstanceParam name="searchCurrent" type="text" value="" --><!-- InstanceParam name="exhibitsCurrent" type="text" value="" --><!-- InstanceParam name="exhibinfoCurrent" type="text" value="" --><!-- InstanceParam name="featurehighCurrent" type="text" value="" --><!-- InstanceParam name="vtourCurrent" type="text" value="" --><!-- InstanceParam name="prevexhibitsCurrent" type="text" value="" --><!-- InstanceParam name="educationCurrent" type="text" value="" --><!-- InstanceParam name="calenderCurrent" type="text" value="" --><!-- InstanceParam name="inhouseCurrent" type="text" value="" --><!-- InstanceParam name="outreachCurrent" type="text" value="" --><!-- InstanceParam name="reservingCurrent" type="text" value="" --><!-- InstanceParam name="informationCurrent" type="text" value="" --><!-- InstanceParam name="newsCurrent" type="text" value="" --><!-- InstanceParam name="explorationsCurrent" type="text" value="" --><!-- InstanceParam name="policiesCurrent" type="text" value="" --><!-- InstanceParam name="historyCurrent" type="text" value="" --><!-- InstanceParam name="employCurrent" type="text" value="" --><!-- InstanceParam name="onresWhyCurrent" type="text" value="" --><!-- InstanceParam name="onresPngCurrent" type="text" value="" --><!-- InstanceParam name="onresCurrent" type="text" value="" --><!-- InstanceParam name="TPCurrent" type="text" value="NOLINK" --><!-- InstanceParam name="exhistoryCurrent" type="text" value="" --><!-- InstanceParam name="exmovingCurrent" type="text" value="" --><!-- InstanceParam name="randArt_EnableRotation" type="text" value="true" --><!-- InstanceParam name="randArt_ImageFolder" type="text" value="/elements/random/" -->
</head>

<body class="twoColElsRtHdr">

<div id="container" style="background-color:#FFFFFF">
  <div id="SMheaderbase">
  	<div id="SMheaderimark">
    	<a href="http://illinois.edu"><img src="http://www.spurlock.illinois.edu/support/elements/header/images/imark.gif" alt="I mark logo" width="38" height="50" class="noborder" ></a>
   	</div>
    <div id="SMrotating">
    	<a href="http://spurlock.illinois.edu/search/details.php?a=1900.18.0001"><img src="http://spurlock.illinois.edu/elements/random/1900.18.0001.jpg" alt="random artifact" width="320" height="109"></a>
    </div>
  	<div id="SMheadertitle">
  		<a href="http://spurlock.illinois.edu/"><img src="http://www.spurlock.illinois.edu/support/elements/header/images/smtitle.png" alt="The William R. and Clarice V. Spurlock Museum at University of Illinois at Urbana-Champaign" width="441" height="74"></a>
    </div>
  </div>

<div id="header">
    
    <div id="navigation" class="clearFix">
      <div class="hide"><a href="#PostNav">Skip to Content</a></div>
      <h2 class="hide">Navigation</h2>
      <ul id="Menu1" class="MM">
        <li><a href="http://www.spurlock.illinois.edu/index.html">HOME</a></li>
      <li><a class="" href="http://www.spurlock.illinois.edu/info/index.html">INFORMATION</a>
        <ul>
            <li><a href="http://www.spurlock.illinois.edu/info/index.html">General Information</a></li>
            <li><a href="http://www.spurlock.illinois.edu/info/people.html">People</a></li>
            <li><a class="" href="http://www.spurlock.illinois.edu/info/employment.html">Employment &amp; Volunteer Opportunities</a></li>
            <li><a href="http://www.spurlock.illinois.edu/info/facilities.html">Facilities</a></li>
            <li><a href="http://www.spurlock.illinois.edu/info/giving.html">Giving</a></li>
          </ul>
        </li>
      <li><a class="" href="http://www.spurlock.illinois.edu/news/index.html">NEWS</a>
        <ul>
            <li><a href="http://www.spurlock.illinois.edu/news/index.html">Current Story</a></li>
            <li><a href="http://www.spurlock.illinois.edu/news/archive.html">News Archive</a></li>
            <li><a href="http://www.spurlock.illinois.edu/news/publications.html">Publications</a></li>
          </ul>
        </li>
      <li><a class="" href="http://www.spurlock.illinois.edu/explorations/index.html">EXPLORATIONS</a>
      <ul>
            <li><a class="" href="http://www.spurlock.illinois.edu/explorations/online/index.html">Online Resources</a>
              <ul>
                <li><a href="http://www.spurlock.illinois.edu/explorations/online/MandarinSquares/index.html">Chinese Mandarin Squares</a></li>
                <li><a href="http://www.spurlock.illinois.edu/explorations/online/mummification/index.html">Egyptian Mummification</a></li>
                <li><a href="http://www.spurlock.illinois.edu/explorations/online/bali/index.html">Journeying Through Balinese Lives</a></li>
                  <li><a href="http://www.spurlock.illinois.edu/explorations/online/senufomusic/index.html">Musical Expressions of the Senufo-Tagba</a></li>
                  <li><a href="http://www.spurlock.illinois.edu/explorations/online/senufo/index.html">Senufo Tagba of West Africa</a></li>
                  <li><a class="" href="http://www.spurlock.illinois.edu/explorations/online/papua/index.html">The Transforming Arts of Papua New Guinea</a></li>
                <li><a class="" href="http://www.spurlock.illinois.edu/explorations/online/whyknot/index.html">Why Knot?</a></li>
              </ul>
            </li>
            <li><a class="" href="http://www.spurlock.illinois.edu/explorations/history/index.html">Spurlock History</a>
                <ul>
                	<li><a class="" href="http://www.spurlock.illinois.edu/explorations/history/history/index.html">History of the Museum</a></li>
                	<li><a class="" href="http://www.spurlock.illinois.edu/explorations/history/moving/index.html">Moving the Museum</a></li>
                </ul>  
            </li>
            <li><a href="http://www.spurlock.illinois.edu/explorations/research/index.html">Research</a></li>
          </ul>
        </li>
    <li><a class="" href="http://www.spurlock.illinois.edu/collections/index.html">COLLECTIONS</a>
      <ul>
            <li><a class="" href="http://www.spurlock.illinois.edu/collections/artifact/index.html">Featured Artifact</a></li>
            <li><a class="" href="http://www.spurlock.illinois.edu/collections/new/index.html">New Acquisitions</a></li>
            <li><a class="" href="http://www.spurlock.illinois.edu/collections/browse/index.html">Collection Highlights</a></li>
            <li><a class="" href="http://www.spurlock.illinois.edu/search/index.php">Search Collections</a></li>
          </ul>
        </li>
    <li><a class="" href="http://www.spurlock.illinois.edu/exhibits/index.html">EXHIBITS</a>
        <ul>
            <li><a class="" href="http://www.spurlock.illinois.edu/exhibits/index.html">General Exhibits Information</a></li>
            <li><a class="" href="http://www.spurlock.illinois.edu/exhibits/highlights/index.html">Feature Gallery Highlights      </a></li>
            <li><a class="" href="http://www.spurlock.illinois.edu/exhibits/vtour/index.html">Virtual Tour of the Feature Galleries</a></li>
            <li><a class="" href="http://www.spurlock.illinois.edu/exhibits/previous.html">Previous Exhibits</a></li>
          </ul>
        </li>
      <li><a class="" href="http://www.spurlock.illinois.edu/education/index.php">PROGRAMS &amp; EVENTS </a>
      <ul>
            <li><a class="" href="http://www.spurlock.illinois.edu/education/calendar/index.php">Calendar of Events</a></li>
            <li><a href="#" class="NOLINK">Tours and Programs Information
                </a>
              <ul>
                <li><a class="" href="http://www.spurlock.illinois.edu/education/in-house/index.html">In-house Tours &amp; Programs</a></li>
                  <li><a class="" href="http://www.spurlock.illinois.edu/education/outreach/index.html">Outreach Programs</a></li>
                  <li><a class="" href="http://www.spurlock.illinois.edu/education/reserving/index.html">Reserving Programs &amp; Tours</a></li>
              </ul>
            </li>
            <li><a href="http://www.spurlock.illinois.edu/education/res_center/index.html">Educational Resource Center</a></li>
              <li><a href="http://www.spurlock.illinois.edu/education/facilities_rental.html">Facilities Rental</a></li>
              <li><a href="http://www.spurlock.illinois.edu/education/inviteus.html">Invite Us to Your Next Meeting</a></li>
          </ul>
        </li>
        <li><a class="" href="http://www.spurlock.illinois.edu/policy/index.html">POLICIES</a></li>
      </ul>
        
    </div>

<!-- Please leave at least one new line or white space symbol after the closing </ul>
     tag of the root ul element of the menu tree. This will allow the browsers to init
     the menu tree as soon as it is loaded and not wait for the page load event. -->

   
  <!-- end #header --></div>

  
  
  <a name="PostNav"></a>
  
  
  <!-- InstanceBeginEditable name="sidebar" -->

	<!-- Sidebar (Optional DIV) START [Delete entire DIV if not needed]-->
  <div id="rightSidebar">

    <!--- (RIGHT) SIDEBAR OBJECTS DISPLAYED HERE -->
    <?php

    $totalSidebars = 0;

    //Show the previous results if we found earlier that the user came from a search results page
    if ($prevSearchRequest !== null) {
        $otherResults = new detailsSidebar($prevResultSet, $artifact, "Other objects from your search", $prevSearchRequest->getURL());
        if ($otherResults->display()) {
            $totalSidebars++;
        }
    }

    //Create some more sidebars for a total of 3
    while ($totalSidebars < 2) {
        $newSidebar = $randomSidebarFactory->getRandomSidebar($artifact);
        if ($newSidebar == null) {
            break;
        }
        $newSidebar->display();
        $totalSidebars++;
    }

    ?>

</div>
	<!-- Sidebar (Optional DIV) END [Delete entire DIV if not needed]-->

  <!-- InstanceEndEditable -->

  
  
    <!-- InstanceBeginEditable name="MainContent" -->
	<div id="crumbs">
 	 	<span><a href="http://www.spurlock.illinois.edu/index.html">Home</a></span>&nbsp;&gt;

       		<!-- Page Crumb Trail START -->
<h1><a href="../collections/index.html">Collections</a></h1>&nbsp;&gt;
	 	 	<h2><a href="index.php">Search Collections</a></h2>&nbsp;&gt;
      		<h3>Artifact Record Details</h3>
			<!-- Page Crumb Trail END -->
    </div>


    <div id="mainContent">

	<!-- Actual Content START -->

	<div class="fauxpageheader">Artifact Record Details</div>

    <div class="imageBorder" style="text-align:center">
        <img src="<?php echo $artifact->getThumbImage(artifact::SEARCH_CONTEXT); ?>">
    </div>
    
    <?php

    //Get the Hi-Res images and display the link to them
    $digitalImages = $artifact->getHiResImages();
    if (!empty($digitalImages)) {
        echo '<br><div style="text-align:center"><img src="elements/hires_b.gif" height="20" width="150" alt="High Resolution Images"><br>';
        $highrescount = count($digitalImages);
        for ($i = 0; $i < $highrescount; $i++) {
            $loc = $digitalImages[$i];
            $c = $i + 1;
            echo '<a href="' . $loc . '">' . $c . '</a> ';
        }
        echo "</div>";
    }

    ?>

    <p style="font-size:.6em; line-height:1.2em; text-align:center">
        Copyright of the Spurlock Museum. Not-for-profit use allowed for personal, educational, and/or research purposes
        only, not for publication.<br>
        To request permission for publication or other use, please contact the <a href="mailto:jenwhite@illinois.edu">Spurlock
            Museum Registrar</a>.
    </p>

    <table title="This table shows artifact data from the database"
           summary="This table outlines information from our databases regarding the given artifact. The information is divided into four sections: Basic Information, Physical Analysis, Research Remarks, and Artifact History.">
        <tr>
            <th id="basic" colspan="2"><h4>Basic Information</h4></th>
            <th>
        </tr>
        <tr>
            <td headers="basic" class="leftcol">Artifact Identification</td>
            <td class="rightcol">
                <?php echo $artifact->getName(); ?> (<?php echo $artifact->getAccessionNumber(); ?>)
            </td>
        </tr>

        <tr>
            <td headers="basic" class="leftcol">Classification</td>
            <td class="rightcol">
                <?php echo $artifact->getClassificationText(TRUE); ?>
            </td>
        </tr>
        <?php
        if ($artifact->hasSecondaryNomenclature()) {
            echo "<tr>
                			<td headers=\"basic\" class=\"leftcol\">Secondary Classification</td>
                			<td class=\"rightcol\"> 
								" . $artifact->getSecondaryClassificationText(TRUE) . "
							</td>
              			</tr>";
        }
        ?>
        <tr>
            <td headers="basic" class="leftcol">Artist/Maker</td>
            <td class="rightcol">
                <?php echo $artifact->getArtist(); ?>
            </td>
        </tr>
        <tr>
            <td headers="basic" class="leftcol">Geographic Location</td>
            <td class="rightcol">
                <?php echo $artifact->getGeographyLinked(); ?>
            </td>
        </tr>
        <tr>
            <td headers="basic" class="leftcol">Period/Date</td>
            <td class="rightcol">
                <?php echo $artifact->getPeriodText(); ?>
            </td>
        </tr>
        <tr>
            <td headers="basic" class="leftcol">Culture</td>
            <td class="rightcol">
                <?php echo $artifact->getCulture(TRUE); ?>
            </td>
        </tr>
        <tr>
            <th id="physical" colspan="2"><h4>Physical Analysis</h4></th>
        </tr>
        <tr>
            <td headers="physical" class="leftcol">Dimension 1 (<?php echo $artifact->getDimensionType1(); ?>)</td>
            <td class="rightcol">
                <?php echo $artifact->getDimension1(); ?>
            </td>
        </tr>
        <tr>
            <td headers="physical" class="leftcol">Dimension 2 (<?php echo $artifact->getDimensionType2(); ?>)</td>
            <td class="rightcol">
                <?php echo $artifact->getDimension2(); ?>
            </td>
        </tr>
        <tr>
            <td headers="physical" class="leftcol">Dimension 3 (<?php echo $artifact->getDimensionType3(); ?>)</td>
            <td class="rightcol">
                <?php echo $artifact->getDimension3(); ?>
            </td>
        </tr>
        <tr>
            <td headers="physical" class="leftcol">Weight</td>
            <td class="rightcol">
                <?php echo $artifact->getWeight(); ?>
            </td>
        </tr>
        <tr>
            <td headers="physical" class="leftcol">Measuring Remarks</td>
            <td class="rightcol">
                <?php echo $artifact->getMeasuringRemarks(); ?>
            </td>
        </tr>
        <tr>
            <td headers="physical" class="leftcol">Materials</td>
            <td class="rightcol">
                <?php echo $artifact->getMaterials(TRUE); ?>
            </td>
        </tr>
        <tr>
            <td headers="physical" class="leftcol">Manufacturing Processes</td>
            <td class="rightcol">
                <?php echo $artifact->getManufacturingProcess(TRUE); ?>
            </td>
        </tr>
        <tr>
            <td headers="physical" class="leftcol">Munsell Color Information</td>
            <td class="rightcol">
                <?php echo $artifact->getMunsellColorInformation(); ?>
            </td>
        </tr>
        <tr>
            <th id="research" colspan="2">
                <h4>Research Remarks</h4>
            </th>
        </tr>
        <tr>
            <td headers="research" class="leftcol">Published Description</td>
            <td class="rightcol">
                <?php echo $artifact->getPublishedDescription(); ?>
            </td>
        </tr>
        <tr>
            <td headers="research" class="leftcol">Description</td>
            <td class="rightcol">
                <?php echo $artifact->getPublicDescription(); ?>
            </td>
        </tr>
        <tr>
            <td headers="research" class="leftcol">Comparanda</td>
            <td class="rightcol">
                <?php echo $artifact->getComparanda(); ?>
            </td>
        </tr>
        <tr>
            <td headers="research" class="leftcol">Bibliography</td>
            <td class="rightcol">
                <?php echo $artifact->getBibliography(); ?>
            </td>
        </tr>
        <tr>
            <th id="history" colspan="2"><h4>Artifact History</h4></th>
        </tr>
        <tr>
            <td headers="history" class="leftcol">Archaeological Data</td>
            <td class="rightcol">
                <?php echo $artifact->getArchaeologicalData(); ?>
            </td>
        </tr>
        <tr>
            <td headers="history" class="leftcol">Credit Line/Dedication</td>
            <td class="rightcol">
                <?php echo $artifact->getCreditDedication(TRUE); ?>
            </td>
        </tr>
        <tr>
            <td headers="history" class="leftcol">Reproduction</td>
            <td class="rightcol">
                <?php echo $artifact->getReproduction(); ?>
            </td>
        </tr>
        <tr>
            <td headers="history" class="leftcol">Reproduction Information</td>
            <td class="rightcol">
                <?php echo $artifact->getReproductionNotes(); ?>
            </td>
        </tr>
        </table>
        <div id="samewidthastable" style="width:78%;margin-left:5px;">
        <hr style="height:4px;background-color: rgba(102, 102, 102, 1);">
        <div id="ShareInfo" colspan="2"><h4>Share What You Know!</h4></div>        
        <div id="ShareInfo2" colspan="2">The Spurlock Museum actively seeks opportunities to improve what we know and record about our collections.  If you have knowledge about this object, please get in touch with our Registration staff by using the form below.</div>
        <div class="row">
        <div class="radius large-9 small-8 small-centered columns">
        <!--<div class="panel">-->
        <form id="ShareInfoForm" name="ShareInfoForm" action="PHPMailer/send_form_email.php" method="POST">
        	<fieldset id="ShareFieldset" style="background-color: rgba(243, 240, 216, 0.4);">
    <!--<legend>Share Your Knowledge</legend>-->
   		<div class="row">	
    		<div class="large-6 columns">
    			<label for="ShareInfofirst_name">First Name</label>
             	<input type="text" name="ShareInfofirst_name" id="ShareInfofirst_name" placeholder="your first name"> 
             	<label for="ShareInfolast_name">Last Name</label>    
        		<input type="text" name="ShareInfolast_name" id="ShareInfolast_name" placeholder="your last name">
        	</div>
        	<div class="large-6 columns">
        		<label for="ShareInfoemail">E-mail Address</label>     
        		<input type="text" name="ShareInfoemail" id="ShareInfoemail" placeholder="your e-mail">  
        		<label for="ShareInfoartifact">Artifact Identification</label>    
        		<input disabled type="text" name="ShareInfoartifact_fake" readonly="readonly" size="50" style="" value="<?php echo $artifact->getName(); ?> (<?php echo $artifact->getAccessionNumber(); ?>)">
        		<input type="text" name="ShareInfoartifact" id="ShareInfoartifact" readonly="readonly" size="50" style="display:none;" value="<?php echo $artifact->getName(); ?> (<?php echo $artifact->getAccessionNumber(); ?>)">
       		</div>	
       	</div>
       	<div class="row">
       		<div class="large-12 centered columns">
       			<label for="ShareInfocomments">Comments</label>
            	<textarea id="ShareInfocomments" class="textarea" name="ShareInfocomments" rows="5" cols="50" style="resize:vertical;"></textarea>
			</div>
		</div>
        <input class="button tiny secondary" id="submit" type="Submit" value="Submit">
		<input class="button tiny secondary" id="reset" type="reset" value="Reset">
		<div id="validation" aria-live="polite" style="display:inline-block;font-size: 0.875em;font-weight: 500;">&nbsp;&nbsp;&nbsp;All fields are required.</div>
		<label for="AmIHuman" style="display:none;">Leave this field blank to prove that you are not a robot.</label>
		<input id="AmIHuman" name="AmIHuman" type="text" style="display:none;">
		<label for="AmIHuman2" style="display:none;">Do not alter the time in this field</label>
		<input type="AmIHuman2" name="formtime" style="display:none;" value="<?php echo time(); ?>" />
        </fieldset>         
		</form>
		<div class="panel secondary" id="msgsubmitted" style="display:none;">	
    <div id="sendingmessageajaxloader" style="display:none;">
    	<img alt="" src="elements/ajax-loader-horizontal-green.gif">
    </div>
    <div id="thanks">Your message is sending...</div>
    </div>	
    <!--</div>-->
    </div>
    </div>
    </div> <!-- #samewidthastable -->
	<!-- Actual Content END -->
    
  <!-- end #mainContent -->
  </div>
  <!-- InstanceEndEditable -->
  
  
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat">

	<br class="clearfloat">

  <div id="footer">

    <div class="fltrt">


<!-- AddThis Follow BEGIN -->
<div class="addthis_toolbox addthis_32x32_style addthis_default_style">
<a class="addthis_button_facebook_follow" addthis:userid="SpurlockMuseum"></a>
<a class="addthis_button_twitter_follow" addthis:userid="spurlockmuseum"></a>
</div>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-50c7a06f3c2d8de3"></script>
<!-- AddThis Follow END -->

<p><a href="http://www.spurlock.illinois.edu/info/people.html">Contact Us</a></p>

    </div>
    	
        
        <div style="padding-top:10px; padding-bottom:8px">
        <a href="http://www.spurlock.illinois.edu/index.html">Spurlock Museum</a> | 600 S. Gregory St. | Urbana, Illinois 61801 | (217) 333-2360<br>

        &copy; 
<script type="text/javascript">
document.write("2001-"+(new Date).getFullYear());
</script>
 University of Illinois Board of Trustees | <a href="http://illinois.edu">University of Illinois at Urbana-Champaign</a></div>
  <!-- end #footer --></div>
<!-- end #container --></div>
</body>
<!-- InstanceEnd --></html>

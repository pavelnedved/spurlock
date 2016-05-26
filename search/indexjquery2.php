<?php
	
	//DEBUG: SHOW ALL WARNINGS/ERRORS
	//ini_set('error_reporting', -1);
	//ini_set('display_errors', 1);
	//ini_set('html_errors', 1);
	
	require_once("searchAuxiliary.php");
	require_once("collectionsBackend.class.php");
	require_once("dashboardTileFactory.class.php");
	
	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en"><!-- InstanceBegin template="/Templates/SM-Main.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- InstanceBeginEditable name="doctitle" -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Collections Search, Spurlock Museum, University of Illinois at Urbana-Champaign</title>
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
<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.0.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.1.1.js"></script>
<script type="text/javascript" src="http://www.spurlock.illinois.edu/support/headRotating.js"></script>
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
<link href="CSS/main.css" rel="stylesheet" type="text/css">

<?php
		
	$searchRequest = new searchRequest();
	$resultSet = array();
	
	if(isset($_GET['view'])){$view = trim($_GET['view']);}else{$view = "list";}
	if(isset($_GET['noFacet'])){$noFacet = true;}else{$noFacet = false;}
	
	if($view == "grid"){
		$recordsPerPage = 50;
		echo '<script type="text/javascript">var RECORDS_PER_PAGE = ' . $recordsPerPage . ';</script>';
	}else{
		$recordsPerPage = 25;
		echo '<script type="text/javascript">var RECORDS_PER_PAGE = ' . $recordsPerPage . ';</script>';
	}
	
	$collectionsBackend = new collectionsBackend($recordsPerPage);
	
?>


<script type="text/javascript" src="JS/index.js"></script>
<script type="text/javascript" src="JS/jquery.infinitescroll.js"></script>
	
<link href="/2013/searchinterface/searchinterface-foundation-button.css" rel="stylesheet" type="text/css">
<link href="/support/fonts/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

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
 	<!-- Sidebar (Optional DIV) END [Delete entire DIV if not needed]-->

  <!-- InstanceEndEditable -->

  
  
    <!-- InstanceBeginEditable name="MainContent" -->
	<div id="crumbs">
 	 	<span><a href="http://www.spurlock.illinois.edu/index.html">Home</a></span>&nbsp;&gt;

       		<!-- Page Crumb Trail START -->
<h1><a href="../collections/index.html">Collections</a></h1>&nbsp;&gt;
	 	 	<?php if($searchRequest->isActive())
				      {echo '<h2><a href="index.php">Search Collections</a></h2>';}
			         else{ echo '<h2>Search Collections</h2>';} ?>
                     
                     	 	 	<!-- Original PHP crumb code by MTR--   php if($searchRequest->isActive()){echo '&gt;<h3> Search Results</h3>';}  -->

	 	 	
			
			<!-- Page Crumb Trail END -->
    </div>


    <div id="mainContent">

	<!-- Actual Content START -->

	<?php
	
		if(!$collectionsBackend->testConnection()){
			showError();
			die();
		}
		
		if(!$searchRequest->isActive()){
			echo '<div class="fauxpageheader">Search the Permanent Collection of the Spurlock Museum</div>
					<p>Access the public portions of the data records for the approximately 45,000 cultural and ethnographic objects in the Museum\'s artifact collection. Enter your search terms below and click search. </p>';
		}
	?>
	
    
    
    
    
    
    <div class="search" id="searchTop" style="margin-top:50px;">
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" name="search">
    <div class="inner">
        <label for="q" class="visuallyhidden">Search:</label><span><input id="q" name="q" type="text"  value="<?php echo htmlspecialchars($searchRequest->getQuery()); ?>" placeholder="Search basic info: name, description, geography, etc." /></span><input name="Search" class="small button success radius" id="searchBtn" type="submit" value="Search" />
    </div>



<div class="inner">
	<ul style="width:100%;margin-left:0px;padding-left:0px">
    <li style="display:inline;" ><button class="button small radius" style="width:61%" id="toggleAdv"><i class="icon-chevron-down"></i> Show More Options</button></li>
    <li style="display:inline;" ><button class="button small radius" style="width:38.25%" id="toggleHlp"><i class="icon-info-sign"></i> Show Search Help</button></li>
    </ul>
</div>



        <div id="examples">
			<div class="OptionsBoxTitle">
				<h3>Examples of Search Terms</h3>
			</div>
            <ul>
            	<li><span class="bold">"Red Figure"</span> --looking for particular types of artifacts...</li>                   
                <li><span class="bold">Dagger</span> --looking for specific artifact classifications...</li>                    
                <li><span class="bold">China</span> --looking for artifacts from a particular culture, country, or continent...</li>
				<li><span class="bold">-Egypt</span> --looking for artifacts excluding ones from Egypt...</li>
				<li><span class="bold">Pottery Clay -Egypt -"Red Figure"</span> --looking for clay pottery excluding ones from Egypt with red figures...</li> 
                <li><span class="bold">1913.03.0014</span> --looking for a specific artifact (known accession number)...</li>
                <li><span class="bold">1913.03</span> --looking for a specific collection (known accession number range)...</li>
                <li><span class="bold">art*</span> --looking for art, artifact, artillery, etc. ('*' acts as a wildcard)...</li>
                <li><span class="bold">Note:</span> Searching for items in their singular form will effect better results.</li>
            </ul>
        </div>





<div id="advancedSearch">
	<div class="visuallyhidden"><h3>More Search Options</h3></div>
	<div id="OptionsCol1">
    	<div class="OptionsBox BasicInfoJ">
			<div class="">
        	<div class="OptionsBoxTitle"><h4>Basic Information</h4></div>
        	<div><label for="n">Object Name:</label><span><input id="n" type="text" name="n"  value="" ></span></div>
        	<div><label for="a">Accession Number:</label><span><input id="a" type="text" name="a"  value="" ></span></div>
        </div>
		</div>



		<div class="OptionsBox GeoJ">
            <div class="">
                <div class="OptionsBoxTitle"><h4>Place of Origin</h4></div>
                <div id="selectContinent">
                        <div><label for="g1">Continent:</label>
                                    <span><select id="g1" name="g1">
                                        <option selected="selected">All
                                        <option>Africa</option>
                                        <option>America, Central</option>
                                        <option>America, North</option>
                                        <option>America, South</option>
                                        <option>Antarctica</option>
                                        <option>Asia, Central</option>
                                        <option>Asia, East</option>
                                        <option>Asia, South</option>
                                        <option>Asia, Southeast</option>
                                        <option>Asia, West</option>
                                        <option>Australia</option>
                                        <option>Europe, East</option>
                                        <option>Europe, West</option>
                                        <option>Oceania--Melanesia</option>
                                        <option>Oceania--Micronesia</option>
                                        <option>Oceania--Polynesia</option>
                                    </select></span>
                        </div>
                </div>
                <div><label for="g2">Country:</label><span><input id="g2" type="text" name="g2" value="" ></span></div>
                <div><label for="g3">Region:</label><span><input id="g3" type="text" name="g3" value="" ></span></div>
                <div><label for="g4">City:</label><span><input id="g4" type="text" name="g4" value="" ></span></div>
                <div><label for="g5">Locality:</label><span><input id="g5" type="text" name="g5" value="" ></span></div>     
            </div>
		</div>



		<div class="OptionsBox ClassNomenJ">
			<div class="">
        	<div class="OptionsBoxTitle"><h4>Classification / Nomenclature</h4></div>
        	<div><label for="c1">Category:</label><span><?php populateDropDown('dont populate', "c1", "category");?></span></div>
        	<div><label for="c2">Classification:</label><span><?php populateDropDown('dont populate', "c2", "classification");?></span></div>
        	<div><label for="c3">Sub-Classification:</label><span><?php populateDropDown('dont populate', "c3", "subclassification");?></span></div>
        </div>
		</div>
	</div>
	<div id="OptionsCol2">


		<div class="OptionsBox OtherOptionsJ">
			<div class="">
        	<div class="OptionsBoxTitle"><h4>Search Options</h4></div>
  		    <div class="inputunit"><label for="m">With Images</label><input id="m" type="checkbox" name="m" value="1" <?php if($searchRequest->getWithImages()){ echo "CHECKED"; } ?> ></div>
			<div class="inputunit"><label for="h">With Large Images</label><input id="h" type="checkbox" name="h" value="1" <?php if($searchRequest->getWithHiResImages()){ echo "CHECKED"; } ?>></div>
          	<div class="inputunit"><div><label for="d">On Display</label><input id="d" type="checkbox" name="d" value="1" <!--<?php if($searchRequest->getOnDisplay()){ echo "CHECKED"; } ?>--></div>
        	<script></script>
            	<div id="selectGallery" style="visibility: visible !important;">
			               <!--<label for="g" >in:</label>-->
				                <span><select id="g" name="g" disabled<?php if($searchRequest->getGallery() == ""){$searchRequest->setGallery("All", FALSE);}?>>
				                    <option selected="selected">All</option>
				                    <option>Africa</option>
				                    <option>Central Core</option>
				                    <option>Egypt</option>
				                    <option>Europe</option>
				                    <option>Ancient Mediterranean</option>
				                    <option>North America</option>
				                    <option>South America</option>
				                    <option>Southeast Asia and Oceania</option>
				                    <option>Mesopotamia</option>
				                    <option>East Asia</option>
				                </select></span>
				                <script >var $checkBoxz = $('#d'),
										 $selectz = $('#g');
										 $checkBoxz.on('change',function(e){
										 if ($(this).is(':checked')){
    									 $selectz.removeAttr('disabled');
										 }else{
 										$selectz.attr('disabled','disabled');}
										 });
										 </script>
				</div>                
        	</div>
        </div>
		</div>



		<div class="OptionsBox AddlDetailsJ">
			<div class="">
        	<div class="OptionsBoxTitle"><h4>Additional Details</h4></div>
			<div><label for="cl">Culture:</label><span><input id="cl" type="text" name="cl" value="" ></span></div>
			<div><label for="cr">Credit Line:</label><span><input id="cr" type="text" name="cr"  value="" ></span></div>
			<div><label for="date">Date:</label><span><input id="date" type="text" name="date"  value="" ></span></div>
			<div><label for="mat">Material:</label><span><input id="mat" type="text" name="mat"  value="" ></span></div>
			<div><label for="man">Manufacturing Process:</label><span><input id="man" type="text" name="man"  value="" ></span></div>
        </div>
		</div>
	</div>


</div></form>
</div> <!--end of search div-->
    
    
    
    
    
    
    

    
   
    

        
        
        <?php createDropDownJS($searchRequest); ?>
	
		<?php
			
			$start = microtime(true);
			
			if($noFacet){
				$searchStatus = $collectionsBackend->executeRequest($searchRequest, FALSE);
			}else{
				$searchStatus = $collectionsBackend->executeRequest($searchRequest);
			}
			
			$clearedSearchRequest = new searchRequest();
			$clearedSearchRequest->clear();
			//$clearedSearchRequest->setQuery($searchRequest->getQuery());
			
			if($searchStatus){
				
				if($view == "grid"){
					echo '<link href="CSS/jquery.qtip.css" rel="stylesheet" type="text/css">';
					echo '<script type="text/javascript" src="JS/jquery.qtip.min.js"></script>';
					echo '<script type="text/javascript" src="JS/infiniteScrollGrid.js"></script>';
				}else{
					echo '<script type="text/javascript" src="JS/infiniteScrollList.js"></script>';
				}
				
				//Save this search in the tracking table
				$collectionsBackend->saveRequest(microtime(true) - $start);
				if($collectionsBackend->getFoundSetCount() > 0){					
					
					echo '<h3 class="visuallyhidden">Search Results</h3>';
					echo '<p id="pageEnumeration">Showing ' . $collectionsBackend->getResultStart() . '-' . min($collectionsBackend->getResultEnd(), $collectionsBackend->getFoundSetCount()) . ' of ' . $collectionsBackend->getFoundSetCount() . ' records found:<br>';
					$collectionsBackend->displayNaturalEnglishQuery();
					
					if(!$searchRequest->isLiberal()){
						echo '<br><a id="clearSearch" href="' . $clearedSearchRequest->getURL() . '">Clear Search</a>';
					}
					echo '</p>';
					
					echo '<div id="SM_result_controls">
							
							<div id="viewSelect">
						  		<span>View as:</span>
						  			<ul class="button-group radius">';	
					
					if($view == "list"){
						echo '<li><a href="#" class="small button disabled"><i class="icon-th-list rotate"></i> List</a></li>
    						  <li><a href="' . changeGetVarInURL($_SERVER['REQUEST_URI'], 'view', 'grid') . '" class="small button"><i class="icon-th"></i> Grid</a></li>';
						
					}else{
						echo '<li><a href="' . changeGetVarInURL($_SERVER['REQUEST_URI'], 'view', 'list') . '" class="small button"><i class="icon-th-list rotate"></i> List</a></li>
    						  <li><a href="#" class="small button disabled"><i class="icon-th"></i> Grid</a></li>';
					}
					
					echo '</ul></div>';
					
					$collectionsBackend->displaySort();
					$collectionsBackend->displayPagination($_SERVER['REQUEST_URI']);
					
					echo '</div>';
					
					if($collectionsBackend->getFoundSetCount() > 1 && !$noFacet){
						echo '<div id="leftSidebarOuter"><h3>Narrow Your Results</h3><div id="leftSidebar" class="panel radius">';
						$collectionsBackend->displayFacets();
						echo '<div id="retTop-anchor"></div>';
						echo '</div></div>';
					}
					
					if($view == "list"){
						$collectionsBackend->outputResultsListView();
					}else if($view == "grid"){
						$collectionsBackend->outputResultsGridView();
					}else{
						$collectionsBackend->outputResultsListView();
					}
					
					echo '<div id="bottomPagination">';
					$collectionsBackend->displayPagination($_SERVER['REQUEST_URI']);
					echo '</div>';
					echo '<div id="retTop" class="normal"><a href="#">Return to Top</a></div>';
					
				}else{
					//No results for query, show the user possible alternative spellings if available
					echo '<p>No records found:<br>';
					$collectionsBackend->displayNaturalEnglishQuery();
					echo '</p>';
					if($collectionsBackend->currentQueryIsShort()){
						echo '<p>Your query may be too common, try searching with more specific terms.</p>';
					}
					echo '<a id="clearSearch" href="' . $clearedSearchRequest->getURL() . '">Clear Search</a>';
				}
				echo '<p id="searchTimer">Search took ' . (microtime(true) - $start) . ' seconds</p>';
			}else{
				echo '<script type="text/javascript" src="JS/jquery.masonry.min.js"></script>';
				echo '<script type="text/javascript" src="JS/dashboard.js"></script>';
				//Show the dash board tiles
				echo '<div style="height:2.8em;background-color:#9e98a5;margin-top:10px;margin-left:-10px; position:relative; margin-right:-10px;clear:both;"></div>';
				echo '<h2 style="margin-top:1em">Random Selections from our Permanent Collection</h2>';				
				echo '<p id="ifjse" hidden>If you don\'t have a specfic search in mind, start by exploring the small set of randomly selected objects below.  Feel free to <a id="getnewselections" href=".">get new selections</a>; the displayed samples are randomly generated from our entire collection each time.</p>';			
				$dashboardFactory = new dashboardTileFactory();
				echo '<noscript><p>Sorry, you must have Javascript enabled to view selections from our Permanent Collection.</p></noscript>';
				echo '<div id="tileCont">';
				echo '</div>';
				echo '<div class="inner">
	<ul style="width:100%;margin-left:0px;padding-left:0px;text-align:center;">
    <li style="display:inline;" ><button class="button small radius" style="width:320px;" id="getnewtilesbutton"><i class="icon-refresh"></i> Get New Selections</button></li>
    </ul>
</div>';
			}
			
			/*echo 'Profile: ' . xdebug_get_profiler_filename();
			$test = array("1" => "A", "2" => "B");
			var_dump($test);
			echo xdebug_is_enabled();*/
		?>
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
<!--Alex's Javascript for a Tile Grid Refresh Button-->
<script>$("a#getnewselections, #getnewtilesbutton").click(function(){
	$("#tileCont").empty();
	curFactoryState = null;
	numTilesLoaded = 0;
	loadNextTile();
   //$("#tileCont1").get("MasonryRefresh.html");
   return false;
});</script>
<script>$("#ifjse").show();
</script>
        
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

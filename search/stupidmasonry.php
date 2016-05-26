<?php
	
	//DEBUG: SHOW ALL WARNINGS/ERRORS
	//ini_set('error_reporting', -1);
	//ini_set('display_errors', 1);
	//ini_set('html_errors', 1);
	
	require_once("searchAuxiliary.php");
	require_once("collectionsBackend.class.php");
	require_once("dashboardTileFactory.class.php");
	
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<script type="text/javascript" src="http://www.spurlock.illinois.edu/support/jquery-1.7.1.min.js"></script>
<link href="CSS/main.css" rel="stylesheet" type="text/css">

</head>
<iframe src="http://www.spurlock.illinois.edu/search/stupidmasonry.php" width="1000" height="1000" scrolling="no"></iframe> 
<body>
<?php

echo '<script type="text/javascript" src="JS/jquery.masonry.min.js"></script>';
				echo '<script type="text/javascript" src="JS/dashboard.js"></script>';
				//Show the dash board tiles
				echo '<div style="height:2.8em;background-color:#9e98a5;margin-top:10px;margin-left:-10px; position:relative; margin-right:-10px;clear:both;"></div>';
				echo '<h2 style="margin-top:1em">Random Selections from our Permanent Collection</h2>';				
				echo '<p>If you don\'t have a specfic search in mind, start by exploring the small set of randomly selected objects below.  Feel free to reload the page to <a href=".">get new selections</a>; the displayed samples are randomly generated from our entire collection each time.</p>';			
				$dashboardFactory = new dashboardTileFactory();
				echo '<noscript><p>Sorry, you must have Javascript enabled to view selections from our Permanent Collection.</p></noscript>';
				echo '<div id="tileCont">';
				echo '</div>';

?>
</body>
</html>
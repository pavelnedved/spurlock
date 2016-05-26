<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
	
	require("http://spurlock.illinois.edu/search/searchAuxiliary.php");
	require("http://spurlock.illinois.edu/search/collectionsBackend.class.php");
	require("http://spurlock.illinois.edu/search/dashboardTileFactory.class.php");
	
	
?>
<link href="CSS/jquery.qtip.css" rel="stylesheet" type="text/css">
					<script type="text/javascript" src="JS/jquery.qtip.min.js"></script>
<script type="text/javascript" src="http://www.spurlock.illinois.edu/support/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="JS/index.js"></script>
<script type="text/javascript" src="JS/jquery.masonry.min.js"></script>
	<script type="text/javascript" src="JS/dashboard.js"></script>
				<div style="height:2.8em;background-color:#9e98a5;margin-top:10px;margin-left:-10px; position:relative; margin-right:-10px;clear:both;"></div>
				<h2 style="margin-top:1em">Random Selections from our Permanent Collection</h2>';				
				<p>If you don\'t have a specfic search in mind, start by exploring the small set of randomly selected objects below.  Feel free to reload the page to <a href=".">get new selections</a>; the displayed samples are randomly generated from our entire collection each time.</p>	
				<script>$dashboardFactory = new dashboardTileFactory();</script>
				<noscript><p>Sorry, you must have Javascript enabled to view selections from our Permanent Collection.</p></noscript>
				<div id="tileCont">
				</div>
</body>
</html>

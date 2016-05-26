
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en"><!-- InstanceBegin template="file:///Web Folder/templates/SM-Main.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- InstanceBeginEditable name="doctitle" -->
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
<link href="CSS/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php

/**
 * 	Testing playground
 */

//phpinfo();

/*const MAX_FACET_LENGTH = 50;
const BAD_FACET_REGEX = "/(unknown)|(pending)|(needs research)|(^;.*)|([?])/i";

function shouldBeSuppressed($facet){
		if(strlen($facet) > MAX_FACET_LENGTH || strlen($facet) < 1){return true;}
		if(preg_match(BAD_FACET_REGEX, $facet)){return true;}
		return false;
}

$tests = array(
	'Africa, North',
	'Africa?, Oceania?',
	'unknown',
	'pending'
);

foreach($tests as $test){
	if(shouldBeSuppressed($test)){
		echo $test." : suppressed\n";
	}else{
		echo $test." : allowed\n";
	}
}*/

	require_once("dashboardTileFactory.class.php");
	
	
	
	$dashboardFactory = new dashboardTileFactory();
	
	echo '<div id="tileCont">';
	$dashboardFactory->getSpecificDashboardTile("randomArtifact")->display(1, true);
	echo '</div>';

?>
</body>
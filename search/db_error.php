<?php 


/* 
	Database Error Include
		- This file's contents are included into the page when there is an issue connecting to the FIleMaker database.
		I decided to make it PHP so that we could add variable and such to report errors and give more information if needed later on.
		 																							- Michael Robinson
*/

//If the database name is not set before the include, make it default to collections
if (!(isset($db_name))) {
	$db_name = 'collections';
}

echo '<p class="bold">The '.$db_name.' database is currently down for maintenence. Please try again later.</p>
    <h2 class="hide">Options</h2> 
    <ul><li><a href="http://www.spurlock.illinois.edu">Return home</a></li> 
    <li><a class="email" href="mailto:jsthoma1@illinois.edu?subject=Database%20Error">Report an error to the webmaster<span> (email link)</span></a></li>
	  <li><a href="javascript:history.back(-1);">Go back to the previous page</a></li> 
    </ul>';
	
?>
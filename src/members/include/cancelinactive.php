<?php
 
/*
 * Bluethrust Clan Scripts v4
 * Copyright 2014
 *
 * Author: Nuker_Viper & Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */
 
if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}
 
 
 
$cID = $_GET['cID'];
 
  
if($member->update(array("onia"), array(0))) { 
   
	echo "
		<div style='display: none' id='successBox'>
			<p align='center'>
				Welcome Back!<br><br>You are no longer set as inactive!
			</p>
		</div>
		  
		<script type='text/javascript'>
			popupDialog('End Inactive Period', '".$MAIN_ROOT."members', 'successBox');
		</script>
	";

}
 
?>

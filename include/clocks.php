<?php 
	
	if(!defined("MAIN_ROOT")) { exit(); }

	$websiteInfo['date_format'] = (!isset($websiteInfo['date_format']) || $websiteInfo['date_format'] == "") ? "l, F j, Y" : $websiteInfo['date_format'];
	
	function displayDefaultWorldClock() {
		global $websiteInfo, $clockObj;
		echo "
			<div class='clocksDiv main'>
				<div class='formTitle'>".date($websiteInfo['date_format'])."</div>
				<p align='center'>
					";
		
		$clockObj->displayClocks();
					
		echo "
				</p>
			</div>
		";		
	}
	
	if($websiteInfo['display_date'] == 1) {
		$hooksObj->run("worldclock-display");
	}
	
?>
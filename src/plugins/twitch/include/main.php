<?php

	if(!defined("MAIN_ROOT")) { exit(); }

	global $pluginObj;
	
	$twitchObj = new Twitch($mysqli);
	$pluginInfo = $pluginObj->get_info_filtered();
	

?>

<div class='streamPageContainer'>

<?php 

	$totalTwitchUsers = $twitchObj->displayAllMemberCards(); 
	
	if($totalTwitchUsers == 0) {

		echo "
			
			<div class='shadedBox' style='margin: 20px auto; width: 45%'>
			
				<p align='center' class='main'>
					<i>There are currently no Twitch users!</i> 
				</p>
			
			</div>
		
		";
		
	}
	
?>

</div>
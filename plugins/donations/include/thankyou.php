<?php

	if(!defined("MAIN_ROOT")) { exit(); }

	global $donationPlugin;
	
	$thankYouMessage = $donationPlugin->getConfigInfo("thankyou");
	
	$pageInfo['html'] = ($thankYouMessage == "") ? "<div class='shadedBox' style='width: 50%; margin: 40px auto'><p class='largeFont' align='center'>Thank you for donating!<br><br><a href='".MAIN_ROOT."plugins/donations/?campaign_id=".$_GET['campaign_id']."'>Return to Donation Page</a></p></div>" : $thankYouMessage;
	
	
	echo $pageInfo['html'];
?>
	
	
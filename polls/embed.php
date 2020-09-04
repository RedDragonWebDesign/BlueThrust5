<?php

	// Config File
	$prevFolder = "../";
	
	include($prevFolder."_setup.php");
	
	include_once($prevFolder."classes/member.php");
	include_once($prevFolder."classes/poll.php");

	
	$consoleObj = new ConsoleOption($mysqli);
	$pollObj = new Poll($mysqli);
	$member = new Member($mysqli);
	if(!$member->select($_SESSION['btUsername']) || !$member->authorizeLogin($_SESSION['btPassword'])) {
		$member = new Member($mysqli);
	}
	

	if($pollObj->select($_POST['pID'])) {
		
		echo "<div class='shadedBox' style='margin: 20px auto; max-width: 200px; width: 40%;'>";
		$pollObj->dispPollMenu($member);
		echo "</div>";
		
	}

?>
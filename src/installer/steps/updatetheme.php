<?php
	include_once("../../_config.php");
	include_once("../../classes/btmysql.php");
	include_once("../../classes/member.php");
	
	$mysqli = new btmysql($dbhost, $dbuser, $dbpass, $dbname);
	$mysqli->set_tablePrefix($dbprefix);
	
	$member = new Member($mysqli);
	$websiteInfoObj = new Basic($mysqli, "websiteinfo", "websiteinfo_id");
	
	$member->select($_POST['user']);
	if($member->authorizeLogin($_POST['pass'], 1) && $member->get_info("rank_id") == "1") {
		
		$memberInfo = $member->get_info_filtered();
		$websiteInfoObj->select(1);
		$websiteInfoObj->update(array("theme"), array($_POST['themeName']));

		
		echo "
		
			<script type='text/javascript'>
			
				$.post('../themes/".$_POST['themeName']."/menuimport_default.php');
			
			</script>
		
		";
		
	}
	else {
		
		if(!$member->select($_POST['user'])) {
			echo "Unable to select user ".$_POST['user']."<br>";
		}
		
		
		if(!$member->authorizeLogin($_POST['pass'], 1)) {
			echo "Not Authorized<br>";
		}
		
		if(!$member->get_info("rank_id") == "1") {
			echo "Not Admin<br>";
		}
		
		
	}
	
	
?>
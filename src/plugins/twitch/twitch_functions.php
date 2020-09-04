<?php


	$btThemeObj->addHeadItem("twitch-css", "<link rel='stylesheet' type='text/css' href='".MAIN_ROOT."plugins/twitch/twitch.css.php'>");

	function setupStreamPage() {
		global $mysqli, $hooksObj;
		$memberObj = new Member($mysqli);
		$returnVal = false;
		if($memberObj->select($_GET['user'])) {
			
			$streamTitle = $memberObj->get_info_filtered("username")."'s Stream";
			$hooksObj->addHook("breadcrumb", "setStreamPageBreadcrumb", array($streamTitle));
			$returnVal = true;
		}
		
		return $returnVal;
	}
	
	
	function setStreamPageBreadcrumb($breadcrumbTitle) {
		global $breadcrumbObj;

		$breadcrumbObj->setTitle($breadcrumbTitle);
		
		$breadcrumbObj->popCrumb();
		$breadcrumbObj->addCrumb("Twitch Streams", MAIN_ROOT."plugins/twitch");
		$breadcrumbObj->addCrumb($breadcrumbTitle);
		
	}
	
?>
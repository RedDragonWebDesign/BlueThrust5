<?php

/*
 * Bluethrust Clan Scripts v4
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */

$prevFolder = "../../../../";
include_once("../../../../_setup.php");


$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$menuCatObj = new MenuCategory($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Menu Categories");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $menuCatObj->select($_POST['mcID'])) {
		
		
		
		$menuCatObj->move($_POST['cDir']);
		
		include("include/menucategorylist.php");
		/*
		$menuCatObj->select($_POST['mcID']);
		echo "
		
			<script type='text/javascript'>
		
				$(document).ready(function() {
				
					$.post('".$MAIN_ROOT."themes/_refreshmenus.php', { refreshSectionID: '".$menuCatObj->get_info("section")."' }, function(data) {
						$('#menuSection_".$menuCatObj->get_info("section")."').html(data);		
					});
				
				});
			
			</script>
		
		";
		*/
	}
	
	
}


?>
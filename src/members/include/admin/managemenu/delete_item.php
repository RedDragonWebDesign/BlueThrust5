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
include_once("../../../../classes/member.php");
include_once("../../../../classes/rank.php");
include_once("../../../../classes/consoleoption.php");
include_once("../../../../classes/menucategory.php");
include_once("../../../../classes/menuitem.php");

$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$menuCatObj = new MenuCategory($mysqli);
$menuItemObj = new MenuItem($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Menu Items");
$consoleObj->select($cID);
$_GET['cID'] = $cID;

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $menuItemObj->select($_POST['itemID'])) {
		
		$menuItemInfo = $menuItemObj->get_info_filtered();
		
		if($_POST['confirm'] == "1") {
			
			$menuCatObj->select($menuItemInfo['menucategory_id']);
			
			$refreshSection = $menuCatObj->get_info("section");
			
			$menuItemObj->delete();
			$menuItemObj->resortOrder();
			include("include/menuitemlist.php");
			/*
			echo "
			
				<script type='text/javascript'>
			
					$(document).ready(function() {
					
						$.post('".$MAIN_ROOT."themes/_refreshmenus.php', { refreshSectionID: '".$refreshSection."' }, function(data) {
							$('#menuSection_".$refreshSection."').html(data);		
						});
					
					});
				
				</script>
			
			
			";
			*/
		}
		else {
			echo "<div id='confirmDelete'><p align='center'>Are you sure you want to delete the menu item <b>".$menuItemInfo['name']."</b>?</p></div>";
			
			echo "
			<script type='text/javascript'>
				$(document).ready(function() {
					$('#confirmDelete').dialog({
						title: 'Manage Menu Items - Delete',
						width: 400,
						zIndex: 99999,
						show: 'scale',
						modal: true,
						resizable: false,
						buttons: {
							'Yes': function() {
								$('#loadingSpiral').show();
								$('#menuItemDiv').fadeOut(250);
								
								$.post('".$MAIN_ROOT."members/include/admin/managemenu/delete_item.php', { itemID: '".$menuItemInfo['menuitem_id']."', confirm: 1 }, function(data) {
								
									$('#menuItemDiv').html(data);
									$('#loadingSpiral').hide();
									$('#menuItemDiv').fadeIn(250);
								
								});
								$(this).dialog('close');
								
							},
							'Cancel': function() {
								$(this).dialog('close');
							}
						
						}
						
						
					});
				});
			</script>
			
			";
		}
		
	}
	elseif(!$menuCatObj->select($_POST['mcID'])) {
	
		echo "<div id='confirmDelete'><p align='center'>Unable find the selected menu category.  Please try again or contact the website administrator.</p></div>";
	
		
		echo "
		
			<script type='text/javascript'>
				$(document).ready(function() {
					$('#confirmDelete').dialog({
						title: 'Manage Menu Categories - Delete',
						width: 400,
						zIndex: 99999,
						show: 'scale',
						modal: true,
						resizable: false,
						buttons: {
							'OK': function() {
								$(this).dialog('close');
							}
						
						}
						
						
					});
				});
			</script>
		
		";
		
	}
	else {
		echo "hi2";	
	}
	
	
	
	
}
else {
	echo "hi";
}

?>
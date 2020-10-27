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
$_GET['cID'] = $cID;

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $menuCatObj->select($_POST['mcID'])) {
		
		$menuCatInfo = $menuCatObj->get_info_filtered();
		
		$result = $mysqli->query("SELECT menuitem_id FROM ".$dbprefix."menu_item WHERE menucategory_id = '".$menuCatInfo['menucategory_id']."'");

		if($result->num_rows > 0) {
			
			echo "<div id='newDeleteMessage' style='display: none'><p align='center'>There are currently menu items under the menu category <b>".$menuCatInfo['name']."</b>.  Please move all menu items out of this category before deleting it.</p></div>";
			
			echo "
				<script type='text/javascript'>
					$(document).ready(function() {
						$('#deleteMessage').dialog('close');
						$('#newDeleteMessage').dialog({
						
							title: 'Manage Menu Categories - Delete',
							modal: true,
							zIndex: 9999,
							resizable: false,
							show: 'scale',
							width: 400,
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
		elseif($_POST['confirm'] == "1") {
			
			$refreshSection = $menuCatObj->get_info("section");
			
			$menuCatObj->delete();
			$menuCatObj->resortOrder();
			include("include/menucategorylist.php");
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
			echo "<div id='confirmDelete'><p align='center'>Are you sure you want to delete the menu category <b>".$menuCatInfo['name']."</b>?</p></div>";
			
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
							'Yes': function() {
								$('#loadingSpiral').show();
								$('#menuCategoryDiv').fadeOut(250);
								
								$.post('".$MAIN_ROOT."members/include/admin/managemenu/delete_category.php', { manage: 1, mcID: '".$menuCatInfo['menucategory_id']."', confirm: 1 }, function(data) {
								
									$('#menuCategoryDiv').html(data);
									$('#loadingSpiral').hide();
									$('#menuCategoryDiv').fadeIn(250);
								
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
	
}

?>


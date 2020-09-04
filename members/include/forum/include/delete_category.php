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


include("../../../../_setup.php");
include_once("../../../../classes/member.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$categoryObj = new BasicOrder($mysqli, "forum_category", "forumcategory_id");
$categoryObj->set_assocTableName("forum_board");
$categoryObj->set_assocTableKey("forumboard_id");


$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Forum Categories");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword']) && $categoryObj->select($_POST['catID'])) {
	$categoryInfo = $categoryObj->get_info_filtered();
	$arrBoards = $categoryObj->getAssociateIDs();
	
	if(count($arrBoards) > 0) {
		
		echo "
		
			<div id='deleteMessage' style='display: none'>
			
				<p class='main' align='center'>
					There are currently boards with the category, <b>".$categoryInfo['name']."</b>.  You must move these boards to a different category before deleting.
				</p>
			
			</div>
			
			<script type='text/javascript'>
			
				$('#deleteMessage').dialog({
				
					title: 'Delete Forum Category',
					width: 400,
					zIndex: 99999,
					resizable: false,
					modal: true,
					show: 'scale',
					buttons: {
					
						'OK': function() {
						
							$(this).dialog('close');
						
						}
					
					}
				
				});
			
			</script>
		
		";
		
	}
	elseif(count($arrBoards) == 0 && !isset($_POST['confirm'])) {
		
		echo "
		
			<div id='deleteMessage' style='display: none'>
			
				<p class='main' align='center'>
					Are you sure you want to delete the category, <b>".$categoryInfo['name']."</b>?
				</p>
			
			</div>
			
			<script type='text/javascript'>
			
				$('#deleteMessage').dialog({
				
					title: 'Delete Forum Category',
					width: 400,
					zIndex: 99999,
					resizable: false,
					modal: true,
					show: 'scale',
					buttons: {
						
						'Yes': function() {
							
							$('#loadingSpiral').show();
							$('#categoryList').fadeOut(250);
							$.post('".$MAIN_ROOT."members/include/forum/include/delete_category.php', { catID: '".$_POST['catID']."', confirm: 1 }, function(data) {
							
								$('#categoryList').html(data);
								$('#loadingSpiral').hide();
								$('#categoryList').fadeIn(250);	
							
							});
							
							$(this).dialog('close');
						
						},
						'Cancel': function() {
						
							$(this).dialog('close');
						
						}
					
					}
				
				});
			
			</script>
		
		
		";
		
	}
	elseif(count($arrBoards) == 0 && isset($_POST['confirm'])) {
		
		$categoryObj->delete();
		$categoryObj->resortOrder();
		include("main_managecategory.php");
		
	}
	
	
}

?>
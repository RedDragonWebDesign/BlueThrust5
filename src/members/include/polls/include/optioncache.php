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


include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/poll.php");


// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Create a Poll");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$pollObj = new Poll($mysqli);

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	
	$pollObj->cacheID = $_POST['cacheID'];
	
	
	if(isset($_POST['action']) && $_POST['action'] == "move") {
		$pollObj->moveCache($_POST['direction'], $_POST['optionOrder']);		
	}
	elseif(isset($_POST['action']) && $_POST['action'] == "delete") {
		unset($_SESSION['btPollOptionCache'][$pollObj->cacheID][$_POST['pollOption']]);
		
		$pollObj->resortCacheOrder();
	}

	$optionCount = count($_SESSION['btPollOptionCache'][$pollObj->cacheID]);

	echo "<table class='formTable' style='width: 75%; margin-top: 0px'>";
	
	foreach($_SESSION['btPollOptionCache'][$pollObj->cacheID] as $key => $pollOptionInfo) {

		$pollOptionInfo = filterArray($pollOptionInfo);
		
		$dispDownArrow = "<a href='javascript:void(0)' title='Move Down'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' data-pollcache='".$key."' data-polldirection='down' class='manageListActionButton'></a>";
		$dispUpArrow = "<a href='javascript:void(0)' title='Move Up'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' data-pollcache='".$key."' data-polldirection='up' class='manageListActionButton'></a>";
		
		if($key == ($optionCount-1)) {
			$dispDownArrow = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/transparent.png' class='manageListActionButton'>";
		}
		
		if($key == 0) {
			$dispUpArrow = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/transparent.png' class='manageListActionButton'>";
		}
		
		echo "
			<tr>
				<td class='main' align='center' style='width: 50%'>".$pollOptionInfo['value']."</td>
				<td class='main' align='center' style='width: 14%'><div class='solidBox' style='background-color: ".$pollOptionInfo['color']."; padding: 0px; height: 20px; width: 40%; display: inline-block'></div></td>
				<td class='main' align='center' style='width: 9%'>".$dispUpArrow."</td>
				<td class='main' align='center' style='width: 9%'>".$dispDownArrow."</td>
				<td class='main' align='center' style='width: 9%'><a href='javascript:void(0)' title='Edit Option'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' class='manageListActionButton' data-polledit='".$key."'></a></td>
				<td class='main' align='center' style='width: 9%'><a href='javascript:void(0)' title='Delete Option'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' class='manageListActionButton' data-polldelete='".$key."'></a></td>
			</tr>
		
		";
		
	}
	echo "</table>
	
	
	
		<script type='text/javascript'>
	
			$(document).ready(function() {
			
				$('img[data-pollcache]').click(function() {
					
					$('#loadingSpiral').show();
					$('#pollOptions').hide();
					$.post('".$MAIN_ROOT."members/include/polls/include/optioncache.php', { cacheID: '".$pollObj->cacheID."', action: 'move', direction: $(this).attr('data-polldirection'), optionOrder: $(this).attr('data-pollcache') }, function(data) {
						$('#loadingSpiral').hide();
						$('#pollOptions').html(data);
						$('#pollOptions').fadeIn(250);
					});
				
				});
				
				$('img[data-polldelete]').click(function() {
					$('#loadingSpiral').show();
					$('#pollOptions').hide();
					$.post('".$MAIN_ROOT."members/include/polls/include/optioncache.php', { cacheID: '".$pollObj->cacheID."', action: 'delete', pollOption: $(this).attr('data-polldelete') }, function(data) {
						$('#loadingSpiral').hide();
						$('#pollOptions').html(data);
						$('#pollOptions').fadeIn(250);
					});
				
				});
				
				
				$('img[data-polledit]').click(function() {
				
					$.post('".$MAIN_ROOT."members/include/polls/include/editoption.php', { cacheID: '".$pollObj->cacheID."', pollOption: $(this).attr('data-polledit') }, function(data) {
						$('#addModifyOptionDiv').html(data);
	
						$('#addModifyOptionDiv').dialog({
							title: 'Edit Poll Option',
							width: 350,
							show: 'scale',
							modal: true,
							zIndex: 99999,
							resizable: false,
							buttons: {
								'Save': function() {
									
									$.post('".$MAIN_ROOT."members/include/polls/include/editoption.php', { cacheID: '".$pollObj->cacheID."', submit: 'add', optionValue: $('#optionValue').val(), optionColor: $('#optionColor').val(), optionOrder: $('#optionOrder').val(), optionOrderBeforeAfter: $('#optionOrderBeforeAfter').val(), pollOption: $('#pollOption').val() }, function(data) {
									
										postData = JSON.parse(data);

										if(postData['result'] == \"success\") {
											reloadOptionCache();
											$('#addModifyOptionDiv').dialog('close');
										}
										else {
										
											var errorHTML = \"<strong>Unable to edit poll option due to the following errors:</strong><ul>\";
											for(var i in postData['errors']) {
												errorHTML += \"<li>\"+postData['errors'][i]+\"</li>\";
											}
											errorHTML += \"</ul>\";
											
											$('#dialogErrors').html(errorHTML);
											$('#dialogErrors').show();
											
										}
									
									});
									
								
								
								},
								'Cancel': function() {
									$(this).dialog('close');
								}
							
							}
						
						});
				
					});
				
				});
			
				
				function reloadOptionCache() {
				
					$('#loadingSpiral').show();
					$('#pollOptions').hide();
					$.post('".$MAIN_ROOT."members/include/polls/include/optioncache.php', { cacheID: '".$pollObj->cacheID."' }, function(data) {
						$('#loadingSpiral').hide();
						$('#pollOptions').html(data);
						
						$('#pollOptions').fadeIn(250);
					
					});
				
				}
			});
		
		</script>
	";
	
	if(count($_SESSION['btPollOptionCache'][$pollObj->cacheID]) == 0) {

		echo "
			<p class='main' align='center'>
				<i>No options added yet!</i>
			</p>
		";
	}
}

?>
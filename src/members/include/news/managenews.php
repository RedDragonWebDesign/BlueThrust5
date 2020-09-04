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

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

$cID = $_GET['cID'];

$newsObj = new News($mysqli);

if(isset($_GET['newsID']) && $newsObj->select($_GET['newsID'])) {

	$newsInfo = $newsObj->get_info_filtered();
	
	define("POSTNEWS_FORM", true);
	include(BASE_DIRECTORY."members/include/news/postnews_form.php");
	
	$breadcrumbObj->popCrumb();

	if($newsInfo['newstype'] != 3) {
		$arrComponents['newstype']['value'] = $newsInfo['newstype'];
		$arrComponents['pintohp']['value'] = $newsInfo['hpsticky'];
		$arrComponents['subject']['value'] = $newsInfo['postsubject'];

		$breadcrumbObj->addCrumb($consoleTitle, $MAIN_ROOT."members/console.php?cID=".$cID);
		$breadcrumbObj->addCrumb("<b>Edit Post:</b> ".$newsInfo['postsubject']);
	
	}
	else {
		unset($arrComponents['newstype']);
		unset($arrComponents['pintohp']);
		unset($arrComponents['subject']);
		$arrComponents['newspost']['type'] = "textarea";
		
		$manageShoutboxCID = $consoleObj->findConsoleIDByName("Manage Shoutbox Posts");
		$consoleObj->select($manageShoutboxCID);
		$manageShoutboxName = $consoleObj->get_info_filtered("pagetitle");
		$consoleObj->select($cID);
		$breadcrumbObj->addCrumb($manageShoutboxName, $MAIN_ROOT."members/console.php?cID=".$manageShoutboxCID);
		$breadcrumbObj->addCrumb("Edit Post");
		$breadcrumbObj->setTitle($manageShoutboxName);
		
		echo "
			<script type='text/javascript'>
				$(document).ready(function() {
					$('#consoleTopBackButton').html(\"<a href='".$MAIN_ROOT."members/console.php?cID=".$manageShoutboxCID."'>Go Back</a>\");
					$('#consoleBottomBackButton').html(\"<a href='".$MAIN_ROOT."members/console.php?cID=".$manageShoutboxCID."'>Go Back</a>\");
					$('title').html(\"".$manageShoutboxName." - ".$websiteInfo['clanname']."\");
				});
			</script>
		";
		
	}
	
	$arrComponents['newspost']['value'] = $newsInfo['newspost'];
	$arrComponents['submit']['value'] = "Edit Post";
	
	
	$setupFormArgs['components'] = $arrComponents;
	$setupFormArgs['saveType'] = "update";
	$setupFormArgs['saveAdditional'] = array("lasteditmember_id" => $memberInfo['member_id'], "lasteditdate" => time());
	$setupFormArgs['saveMessage'] = "Successfully edited news post!";
	$setupFormArgs['description'] = "Use the form below to edit the selected news post.";
	$setupFormArgs['attributes']['action'] .= "&newsID=".$newsInfo['news_id'];

	
	$breadcrumbObj->updateBreadcrumb();
	
	
}
else {
	
	
	$postNewsCID = $consoleObj->findConsoleIDByName("Post News");
	
	echo "
	
		<p align='right' class='main' style='padding-right: 20px'>
			&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$postNewsCID."'>Post News</a> &laquo;
		</p>
	
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
		<div id='deleteMessage' style='display: none'></div>
		<div id='displayNewsDiv'>
	
	
	
		</div>
		
		
		<script type='text/javascript'>
		
			$(document).ready(function() {
		
			
				$('#displayNewsDiv').hide();
				$('#loadingSpiral').show();
			
				$.post('".$MAIN_ROOT."members/include/news/include/newslist.php', { }, function(data) {
				
				
					$('#displayNewsDiv').html(data);
					$('#loadingSpiral').hide();
					$('#displayNewsDiv').fadeIn(250);
				
				
				});

			});
			
			
			
			
			function deleteNews(newsID) {
			
				$(document).ready(function() {
				
					$.post('".$MAIN_ROOT."members/include/news/include/delete.php', { nID: newsID }, function(data) {
					
					
						$('#deleteMessage').dialog({
					
							title: 'Manage News - Delete Post',
							width: 400,
							modal: true,
							zIndex: 9999,
							resizable: false,
							show: 'scale',
							buttons: {
								'Yes': function() {
									
									$('#loadingSpiral').show();
									$('#displayNewsDiv').hide();
									$(this).dialog('close');
									$.post('".$MAIN_ROOT."members/include/news/include/delete.php', { nID: newsID, confirm: 1 }, function(data1) {
										$('#displayNewsDiv').html(data1);
										$('#loadingSpiral').hide();
										$('#displayNewsDiv').fadeIn(400);	
									});
								
								},
								'Cancel': function() {
								
									$(this).dialog('close');
								
								}
							}
						});
					
					
					
					
						
						$('#deleteMessage').html(data);
						
					
					
					
					});
				
				});
			
			}
		
		</script>
		
	";
	
	
	
}




?>
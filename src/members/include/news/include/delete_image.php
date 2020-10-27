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
include_once("../../../../classes/imageslider.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$imageSliderObj = new ImageSlider($mysqli);
$consoleObj = new ConsoleOption($mysqli);
$cID = $consoleObj->findConsoleIDByName("Manage Home Page Images");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $imageSliderObj->select($_POST['imgID'])) {

		$imageSliderInfo = $imageSliderObj->get_info_filtered();
		
		if(isset($_POST['confirm'])) {
			
			unlink("../../../../".$imageSliderInfo['imageurl']);
			$imageSliderObj->delete();
			
			include("imagelist.php");
			
		}
		else {
			echo "
			
				<div id='confirmDeleteImage' style='display: none'>
					<p class='main' align='center'>
						Are you sure you want to delete <b>".$imageSliderInfo['name']."</b>?
					</p>
				</div>
				<script type='text/javascript'>
					$(document).ready(function() {
						$('#confirmDeleteImage').dialog({
							title: 'Confirm Delete - Home Page Image',
							show: 'scale',
							modal: true,
							zIndex: 999999,
							resizable: false,
							width: 400,
							buttons: {
								'Yes': function() {
									
									$('#loadingSpiral').show();
									$('#imageList').fadeOut(200);
									$.post('".$MAIN_ROOT."members/include/news/include/delete_image.php', { imgID: '".$_POST['imgID']."', confirm: 1 }, function(data) {
										$('#imageList').html(data);
										$('#loadingSpiral').hide();
										$('#imageList').fadeIn(200);
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
	
	
}

?>
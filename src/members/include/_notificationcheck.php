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



include_once("../../_setup.php");
include_once("../../classes/member.php");


$memberObj = new Member($mysqli);

if(isset($_SESSION['btUsername']) && isset($_SESSION['btPassword'])) {
	
	
	if($memberObj->select($_SESSION['btUsername']) && $memberObj->authorizeLogin($_SESSION['btPassword'])) {
		
		$memberInfo = $memberObj->get_info_filtered();
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."notifications WHERE member_id = '".$memberInfo['member_id']."' AND status = '0'");
		
		$counter = 0;
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				
				
				switch($row['icontype']) {
					case "promotion":
						$imgName = "promotionnotification.png";
						break;
					case "demotion":
						$imgName = "demotionnotification.png";
						break;
					case "medal":
						$imgName = "medalnotification.png";
						break;
					default:
						$imgName = "generalnotification.png";	
				}
				
				$arrDispNotifications[] = "
					<div id='displayNotification_".$counter."' style='display: none'>
						<table class='notificationTable'>
							<tr>
								<td clas='notificationIcon' valign='top'><img src='".$MAIN_ROOT."themes/".$THEME."/images/".$imgName."' class='notificationIMG'></td>
								<td class='main' valign='top' style='padding-left: 5px'>".$row['message']."</td>
								<td class='notificationClose' valign='top' align='right'><b><a href='javascript:void(0)' onclick=\"$('#notificationDiv').fadeOut(150)\">X</a></b></td>
							</tr>
				";
				$counter++;
			}
			
			$counter--;
			
			foreach($arrDispNotifications as $key=>$value) {
				
				$addNext = "";
				$addPrev = "";
				if($key < $counter) {
					$addNext = "&nbsp;&nbsp;<a href='javascript:void(0)' onclick=\"showNotification('".($key+1)."')\"><b>Next &raquo;</b></a>";
				}
				
				if($key > 0) {
					$addPrev = "<a href='javascript:void(0)' onclick=\"showNotification('".($key-1)."')\"><b>&laquo; Previous</b></a>&nbsp;&nbsp;";
				}
				
				if($addNext != "" || $addPrev != "") {
					$arrDispNotifications[$key] .= "
						<tr>
							<td colspan='3' class='tinyFont' align='right'>
								".$addPrev.$addNext."
							</td>
						</tr>
					
					";
				}
				
				
				$arrDispNotifications[$key] .= "</table></div>";
				
				
				echo $arrDispNotifications[$key];
				
			}
			
			
			
			echo "
			
				<script type='text/javascript'>
			
					$(document).ready(function() {
					
					
						$('#notificationDiv').html($('#displayNotification_0').html());
						$('#notificationDiv').fadeIn(250);
						$('#notificationDiv').effect('bounce', 'fast');
						
						var notificationAudio = $('#notificationSound')[0];
						notificationAudio.volume = .5;
						notificationAudio.play();
						
					
					});
					
					function showNotification(intShow) {
						$(document).ready(function() {
						
							var strShowDiv = \"#displayNotification_\"+intShow;
							
							$('#notificationDiv').html($(strShowDiv).html());
						
						});
					}
				
				</script>
			
			";
			
			
			
			$mysqli->query("UPDATE ".$dbprefix."notifications SET status = '1' WHERE member_id = '".$memberInfo['member_id']."' AND status = '0'");
			
			
		}
		else {
			echo "SELECT * FROM ".$dbprefix."notifications WHERE member_id = '".$memberInfo['member_id']."' AND status = '0'";
		}
	}

	
}




?>
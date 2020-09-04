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

// Config File
$prevFolder = "../";

include($prevFolder."_setup.php");

$consoleObj = new ConsoleOption($mysqli);
$eventObj = new Event($mysqli);
$member = new Member($mysqli);

$ipbanObj = new Basic($mysqli, "ipban", "ipaddress");

if($ipbanObj->select($IP_ADDRESS, false)) {
	$ipbanInfo = $ipbanObj->get_info();

	if(time() < $ipbanInfo['exptime'] OR $ipbanInfo['exptime'] == 0) {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
	}
	else {
		$ipbanObj->delete();
	}

}

if(!$eventObj->select($_GET['eID'])) {
	echo "
		<script type='text/javascript'>
			window.location = '".$MAIN_ROOT."events';
		</script>
	";

	exit();
}

$eventInfo = $eventObj->get_info_filtered();
$eventPgMemberObj = new Member($mysqli);

$dispCreatorLink = "Unknown";

if($eventPgMemberObj->select($eventInfo['member_id'])) {
	$dispCreatorLink = $eventPgMemberObj->getMemberLink();
}

$eventMemberProfilePic = $eventPgMemberObj->get_info_filtered("profilepic");

if($eventMemberProfilePic == "") {
	$eventMemberProfilePic = $MAIN_ROOT."themes/".$THEME."/images/defaultprofile.png";
}
else {
	$eventMemberProfilePic = $MAIN_ROOT.$eventMemberProfilePic;
}


$arrInviteList = $eventObj->getInvitedMembers(true);
$arrInviteList[] = $eventInfo['member_id'];

// Start Page
$PAGE_NAME = $eventInfo['title']." - ";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");

$memberInfo = array();
if(constant("LOGGED_IN") && $member->select($_SESSION['btUsername'])) {
	$memberInfo = $member->get_info_filtered();
	
	if($eventInfo['status'] == 2 && !in_array($memberInfo['member_id'], $eventObj->getInvitedMembers(true)) && $memberInfo['member_id'] != $eventInfo['member_id']) {
		echo "
			<script type='text/javascript'>
				window.location = '".$MAIN_ROOT."events';
			</script>
		";
		exit();
	}
	
}
elseif($eventInfo['visibility'] != 0) {

	echo "
		<script type='text/javascript'>
			window.location = '".$MAIN_ROOT."events';
		</script>
	";
	exit();
	
}

$breadcrumbObj->setTitle($eventInfo['title']);
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Events", $MAIN_ROOT."events");
$breadcrumbObj->addCrumb($eventInfo['title']);
include($prevFolder."include/breadcrumb.php");
echo "

	<div class='eventPageContainer'>
		<div class='eventLeftContainer'>
			<div class='eventTitle'>Invite List:</div>
			<div class='dashedBox'>
				<table class='formTable'>
					<tr>
					<td valign='top' class='profilePic".$addCSS."'>
						<img src='".$eventMemberProfilePic."'>
					</td>
					<td class='main".$addCSS."' valign='top'>
						<span class='largeFont'>".$eventPgMemberObj->getMemberLink()."</span><br>
						<b>Position:</b> Event Creator<br>
					</td>
				</tr>
				";

	$arrSortInviteList = array();
	$arrInviteListNoPosition = array();
	foreach($arrInviteList as $value) {
		$eventMemberID = $eventObj->getEventMemberID($value, true);
		$eventMemInfo = $eventObj->objEventMember->get_info();
		
		if($eventObj->objEventPosition->select($eventMemInfo['position_id'])) {
			$arrSortInviteList[] = $value;
		}
		else {
			$arrInviteListNoPosition[] = $value;	
		}
	}
	
	
	$sqlInviteList[0] = "('".implode("','", $arrSortInviteList)."')";
	$query[0] = "SELECT m.rank_id, ep.sortnum, r.ordernum, m.member_id FROM ".$dbprefix."members m, ".$dbprefix."eventpositions ep, ".$dbprefix."events_members em, ".$dbprefix."ranks r WHERE r.rank_id = m.rank_id AND m.member_id = em.member_id AND em.event_id = '".$eventInfo['event_id']."' AND em.position_id = ep.position_id AND em.member_id IN ".$sqlInviteList[0]." ORDER BY ep.sortnum ASC, r.ordernum DESC";

	$sqlInviteList[1] = "('".implode("','", $arrInviteListNoPosition)."')";
	$query[1] = "SELECT m.rank_id, r.ordernum, m.member_id FROM ".$dbprefix."members m, ".$dbprefix."events_members em, ".$dbprefix."ranks r WHERE r.rank_id = m.rank_id AND m.member_id = em.member_id AND em.event_id = '".$eventInfo['event_id']."' AND em.member_id IN ".$sqlInviteList[1]." ORDER BY r.ordernum DESC";
	
	$counter = 1;
	for($x=0;$x<=1;$x++) {
		$result = $mysqli->query($query[$x]);
		while($row = $result->fetch_assoc()) {
			if($eventPgMemberObj->select($row['member_id'])) {
				$eventMemID = $eventObj->getEventMemberID($row['member_id'], true);
				$eventMemInfo = $eventObj->objEventMember->get_info();
				
				if($eventObj->objEventPosition->select($eventMemInfo['position_id'])) {
					$dispPositionName = $eventObj->objEventPosition->get_info_filtered("name");
				}
				else {
					$dispPositionName = "<i>None</i>";	
				}

				
				$eventMemberProfilePic = $eventPgMemberObj->get_info_filtered("profilepic");
				
				if($eventMemberProfilePic == "") {
					$eventMemberProfilePic = $MAIN_ROOT."themes/".$THEME."/images/defaultprofile.png";
				}
				else {
					$eventMemberProfilePic = $MAIN_ROOT.$eventMemberProfilePic;	
				}
				
				if($counter == 1) {
					$addCSS = " alternateBGColor";
					$counter = 0;
				}
				else {
					$addCSS = "";
					$counter = 1;
				}
				
				switch($eventMemInfo['status']) {
					case 2:
						$dispAttendStatus = "Not Attending";
						break;
					case 1:
						$dispAttendStatus = "Attending";
						break;
					default:
						$dispAttendStatus = "Invited";				
				}
				
				echo "
					<tr>
						<td valign='top' class='profilePic".$addCSS."'>
							<img src='".$eventMemberProfilePic."'>
						</td>
						<td class='main".$addCSS."' valign='top'>
							<span class='largeFont'>".$eventPgMemberObj->getMemberLink()."</span><br>
							<b>Position:</b> ".$dispPositionName."<br>
							<i>".$dispAttendStatus."</i>
						</td>
					</tr>
				";
				
			}
		}
	}
		
	
	$dispEventPositions = "";
	$arrEventPositions = $eventObj->getPositions();
	$x = 1;
	foreach($arrEventPositions as $value) {
		
		$eventObj->objEventPosition->select($value);
		
		
		$dispEventPositions .= $x.". ".$eventObj->objEventPosition->get_info_filtered("name")."<br>";
		$x++;
	}
	
	if($dispEventPositions == "") {
		$dispEventPositions = "<i>None</i>";	
	}
	
	$dateTimeObj = new DateTime();
	$dateTimeObj->setTimestamp($eventInfo['startdate']);
	$includeTimezone = "";
	$dispTimezone = "";
	
	if($eventInfo['timezone'] != "") { 
		$timeZoneObj = new DateTimeZone($eventInfo['timezone']);
		$dateTimeObj->setTimezone($timeZoneObj);
		$includeTimezone = " T"; 
		$dispOffset = ((($timeZoneObj->getOffset($dateTimeObj))/60)/60);
		$dispSign = ($dispOffset < 0) ? "" : "+";
		
		$dispTimezone = $dateTimeObj->format(" T")."<br>".str_replace("_", " ", $eventInfo['timezone'])." (UTC".$dispSign.$dispOffset.")";
	}
	$dateTimeObj->setTimezone("UTC");
	$dispStartDate = $dateTimeObj->format("M j, Y g:i A").$dispTimezone;
	
echo "
			</table>
			</div>
			
		</div>
		
		<div class='eventRightContainer'>
			<div class='eventTitle'>Event Information:</div>
			<div class='dashedBox'>
				<table class='formTable'>
					<tr>
						<td class='main alternateBGColor' style='width: 30%' valign='top'><b>Title:</b></td>
						<td class='main' style='padding-left: 5px; width: 70%' valign='top'>".$eventInfo['title']."</td>
					</tr>
					<tr>
						<td class='main alternateBGColor' style='width: 30%' valign='top'><b>Created By:</b></td>
						<td class='main' style='padding-left: 5px; width: 70%' valign='top'>".$dispCreatorLink."</td>
					</tr>
					<tr>
						<td class='main alternateBGColor' style='width: 30%' valign='top'><b>When:</b></td>
						<td class='main' style='padding-left: 5px; width: 70%'>".$dispStartDate."</td>
					</tr>
					<tr>
						<td class='main alternateBGColor' style='width: 30%' valign='top'><b>Location:</b></td>
						<td class='main' style='padding-left: 5px; width: 70%' valign='top'>".$eventInfo['location']."</td>
					</tr>
					<tr>
						<td class='main alternateBGColor' style='width: 30%' valign='top'><b>Positions:</b></td>
						<td class='main' style='padding-left: 5px; width: 70%' valign='top'>
						
							".$dispEventPositions."
						
						</td>
					</tr>
					<tr>
						<td class='main alternateBGColor' style='width: 30%' valign='top'><b>Details:</b></td>
						<td class='main' style='padding-left: 5px; width: 70%' valign='top'>".nl2br($eventInfo['description'])."</td>
					</tr>
				</table>
			
			</div>
			<br>
			<div class='eventTitle'>Event Posts:</div>
			<div class='dashedBox'>
				
				";

			$dispPostMessageJS = "";
			if($eventObj->memberHasAccess($memberInfo['member_id'], "postmessages")) {
				echo "
				
					<div style='position: relative; margin-top: 5px; margin-left: auto; margin-right: auto; width: 95%'>
						<textarea class='textBox' id='txtPostMessage' rows='2' style='width: 98%; margin-left: auto; margin-right: auto'></textarea>
						<p align='right' style='margin-top: 2px; margin-right: 3px;'><input type='button' class='submitButton' style='width: 80px' value='Post' id='btnPostMessage'></p>
					</div>
				";
				
				
				$dispPostMessageJS = "
					
						$(document).ready(function() {
						
							$('#btnPostMessage').click(function() {
							
								$('#eventMessages').fadeOut(250);
								$('#loadingSpiral').show();
							
								$.post('".$MAIN_ROOT."members/events/include/postmessage.php', { postMessage: $('#txtPostMessage').val(), eID: '".$eventInfo['event_id']."' }, function(data) {
									
									$('#eventMessages').html(data);
									$('#loadingSpiral').hide();
									$('#eventMessages').fadeIn(250);
								
								});
								
								$('#txtPostMessage').val('');
							
							});						
						
						});
						
						
						function postComment(intMessageID) {
						
							var strCommentTxtID = \"#txtComment_\"+intMessageID;
							var strCommentsULID = \"#commentsUL_\"+intMessageID;
							
							
							$(document).ready(function() {
							
								$.post('".$MAIN_ROOT."members/events/include/postcomment.php', { commentMessage: $(strCommentTxtID).val(), messageID: intMessageID }, function(data) {
									
									$(strCommentsULID).html(data);
								
								});
						
								$(strCommentTxtID).val('');
								
							});
							
						}
				
				";
				
			}
			
			
			$dispManageMessagesJS = "";
			if($eventObj->memberHasAccess($memberInfo['member_id'], "managemessages")) {
				
				$dispManageMessagesJS = "
				
					function deleteMessage(intMessageID, strMessageType) {
					
					
						$(document).ready(function() {
							var intMessageType = 0;
						
							if(strMessageType == \"c\") {
								intMessageType = 1;
							}
					
							
							$(document).css('cursor', 'wait');
							
							$.post('".$MAIN_ROOT."members/events/include/deletemessage.php', { messageID: intMessageID, comment: intMessageType }, function(data) {
								$('#eventMessages').html(data);
								$(document).css('cursor', 'default');
							});
							
						
						});
						
					
					}
				
				";
				
			}
			
			
			
			echo "
				<div id='loadingSpiral' class='loadingSpiral'>
					<p align='center'>
						<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
					</p>
				</div>
			
				<div class='eventMessages' id='eventMessages'>
					<ul>
						
					";
			
				$result = $mysqli->query("SELECT * FROM ".$dbprefix."eventmessages WHERE event_id = '".$eventInfo['event_id']."' ORDER BY dateposted DESC");
				while($row = $result->fetch_assoc()) {
			
					$eventPgMemberObj->select($row['member_id']);
					$memInfo = $eventPgMemberObj->get_info_filtered();
				
					if($memInfo['profilepic'] == "") {
						$dispProfilePic = $MAIN_ROOT."themes/".$THEME."/images/defaultprofile.png";
					}
					else {
						$dispProfilePic = $MAIN_ROOT.$memInfo['profilepic'];
					}
				
					$dispDeleteMessage = "";
					if($eventObj->memberHasAccess($memberInfo['member_id'], "managemessages")) {
						$dispDeleteMessage = " - <a href='javascript:void(0)' onclick=\"deleteMessage('".$row['eventmessage_id']."', 'm')\">Delete</a>";
					}
					
					echo "
						<li>
							<div class='profilePic'><img src='".$dispProfilePic."'></div>
							<div class='main messageDiv'><b>".$eventPgMemberObj->getMemberLink()."</b><br>
							".nl2br(parseBBCode($row['message']))."<br>
							<div class='tinyFont' style='margin-top: 5px'>".getPreciseTime($row['dateposted']).$dispDeleteMessage."</div>
							</div>
							<div style='clear: both'></div>
							<ul id='commentsUL_".$row['eventmessage_id']."'>
							
							
							";
						
							$eventObj->objEventMessage->select($row['eventmessage_id']);
							$arrMessageComments = $eventObj->objEventMessage->getComments(" ORDER BY dateposted ASC");
						
							foreach($arrMessageComments as $commentID) {
								if($eventObj->objEventMessageComment->select($commentID)) {
									$commentInfo = $eventObj->objEventMessageComment->get_info_filtered();
									$eventPgMemberObj->select($commentInfo['member_id']);
									
									$memInfo = $eventPgMemberObj->get_info_filtered();
									
									if($memInfo['profilepic'] == "") {
										$dispProfilePic = $MAIN_ROOT."themes/".$THEME."/images/defaultprofile.png";
									}
									else {
										$dispProfilePic = $MAIN_ROOT.$memInfo['profilepic'];
									}
									
									$dispDeleteMessage = "";
									if($eventObj->memberHasAccess($memberInfo['member_id'], "managemessages")) {
										$dispDeleteMessage = " - <a href='javascript:void(0)' onclick=\"deleteMessage('".$commentID."', 'c')\">Delete</a>";
									}
									
									echo "
									
									<li class='dottedLine'>
										<div class='profilePic'><img src='".$dispProfilePic."'></div>
										<div class='main messageDiv'><b>".$eventPgMemberObj->getMemberLink()."</b><br>
											".nl2br(parseBBCode($commentInfo['comment']))."<br>
											<div class='tinyFont' style='margin-top: 5px'>".getPreciseTime($commentInfo['dateposted']).$dispDeleteMessage."</div>
										</div>
										<div style='clear: both'></div>
									</li>
									
									";
									
								}
							}
							
							echo "
							
							</ul>
						</li>
						
						";
						
							
					if(constant("LOGGED_IN") && $eventObj->memberHasAccess($memberInfo['member_id'], "postmessages")) {
						echo "	
							<li class='dashedLine'>
								Comment:<br>
								
								<textarea id='txtComment_".$row['eventmessage_id']."' class='textBox'></textarea>
								<p align='right' style='margin-top: 2px; margin-right: 3px;'><input type='button' onclick=\"postComment('".$row['eventmessage_id']."')\" class='submitButton' value='Comment' style='width: 80px'></p>
							
							</li>
						";
					}
					else {
						echo "<li class='dashedLine'></li>";	
					}
			
				}
			
					
					echo "
					</ul>
				</div>
			
			</div>
		</div>
	
	</div>
";




echo "

<script type='text/javascript'>

";

echo $dispPostMessageJS;

echo $dispManageMessagesJS;

echo "

	function updateMessages() {

		$(document).ready(function() {
		
			var arrMessages = {};
		
			$('textarea[id*=\"txtComment_\"]').each(function(index) {
			
				tempVal = $(this).attr(\"id\");
			
				arrMessages[tempVal] = $(this).val();
			
			});
		
		
			$.post('".$MAIN_ROOT."members/events/include/eventmessages.php', { eID: '".$eventInfo['event_id']."', commentBox: arrMessages }, function(data) {
				$('#eventMessages').html(data);
			
			});
			
			
			
			//alert($.param(arrMessages));
	
			
			
		
		});
		
		var refreshMessages = setTimeout(\"updateMessages()\", 5000);
		
	
	}
	
	setTimeout(\"updateMessages()\", 5000);

</script>
";


include($prevFolder."themes/".$THEME."/_footer.php");
?>
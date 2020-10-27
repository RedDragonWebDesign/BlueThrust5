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



if(!isset($member) || !isset($eventObj) || substr($_SERVER['PHP_SELF'], -strlen("manage.php")) != "manage.php") {

	exit();
}
else {
	// This is a little repeatative, but for security.

	$memberInfo = $member->get_info();
	$consoleObj->select($cID);

	$eventObj->select($eID);

	if(!$member->hasAccess($consoleObj) || (!$eventObj->memberHasAccess($memberInfo['member_id'], "invitemembers") && $memberInfo['rank_id'] != 1)) {
		exit();
	}
}


echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Invite Members\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$eventInfo['event_id']."'>".$consoleTitle."</a> > <b>".$eventInfo['title'].":</b> Invite Members\");
});
</script>
";

$dispError = "";
$countErrors = 0;
$objInviteMember = new Member($mysqli);

if($_POST['submit']) {
	
	
	foreach($_SESSION['btInviteList'] as $value) {
		
		
		$checkInvite = $eventObj->inviteMember($value, $memberInfo['member_id']);
		if($objInviteMember->select($value) && $checkInvite === true) {
			$objInviteMember->postNotification("You have been invited to the event, <b>".$eventInfo['title']."</b>!.  Go to the <a href='".$MAIN_ROOT."events/info.php?eID=".$eventInfo['event_id']."'>event</a> page to view more info.", "general");
		}
		elseif($objInviteMember->select($value) && $checkInvite === false) {
			$dispInviteErrorName = $objInviteMember->get_info_filtered("username");
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to invite ".$dispInviteErrorName.".<br>";
		}
		elseif(!$objInviteMember->select($value)) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid member.<br>";
		}
		elseif($checkInvite == "dup") {
			$dispInviteErrorName = $objInviteMember->get_info_filtered("username");
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to invite ".$dispInviteErrorName.". (already invited)<br>";
		}
		
	}
	
	if($countErrors == 0) {
		
		echo "
			
			<div style='display: none' id='successBox'>
				<p align='center'>
					Successfully sent event invitations!
				</p>
			</div>
			
			<script type='text/javascript'>
				popupDialog('Invite Members', '".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$eventInfo['event_id']."', 'successBox');
			</script>
		
		";
		
	}
	
	if($countErrors > 0) {
		$_POST = filterArray($_POST);
		$_POST['submit'] = false;	
	}
	
}


if(!$_POST['submit']) {
	
	$_SESSION['btEventID'] = $eventInfo['event_id'];
	
	if($countErrors == 0) {
		$_SESSION['btInviteList'] = array();
	}
	else {
		$_SESSION['btInviteList'] = filterArray($_SESSION['btInviteList']);	
	}
	
	$arrInvitedMembers = $eventObj->getInvitedMembers(true);
	$arrInvitedMembers = array_merge($arrInvitedMembers, $_SESSION['btInviteList']);
	
	
	$sqlInvitedMembers = "('".implode("','", $arrInvitedMembers)."')";
	$memberoptions = "<option value=''>Select</option>";
	$result = $mysqli->query("SELECT m.username,m.member_id,r.ordernum,r.name FROM ".$dbprefix."members m, ".$dbprefix."ranks r WHERE m.rank_id = r.rank_id AND m.member_id NOT IN ".$sqlInvitedMembers." AND m.disabled = '0' AND m.rank_id != '1' ORDER BY r.ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$memberoptions .= "<option value='".$row['member_id']."'>".filterText($row['name'])." ".filterText($row['username'])."</option>";		
	}

	
	
	$showInviteList = "<p align='center'><i>- Empty -</i></p>";
	if(count($_SESSION['btInviteList']) > 0) {
		
		$showInviteList = "";
		foreach($_SESSION['btInviteList'] as $key => $value) {
		
			if($objInviteMember->select($value)) {
				$showInviteList .= "<div class='mttPlayerSlot' style='width: 95%'>".$objInviteMember->get_info_filtered("username")."<div class='mttDeletePlayer'><a href='javascript:void(0)' onclick=\"removeMember('".$key."')\">X</a></div></div>";
			}
			
		}
	}
	
	
	echo "
	
		<form action='".$MAIN_ROOT."members/events/manage.php?eID=".$eventInfo['event_id']."&pID=InviteMembers' method='post'>
			<div class='formDiv'>
			
			";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to send all event invitations because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	echo "
			
				Use the form below to send event invitations.<br><br>
				<table class='formTable'>
					<tr>
						<td class='main' valign='top' style='width: 15%'><b>Member: <a href='javascript:void(0)' onmouseover=\"showToolTip('You may type in a member\'s username or select it from the dropdown.  If both the dropdown and text box are filled, the dropdown selection will be used.')\" onmouseout='hideToolTip()'>(?)</a></b></td>
						<td class='main' valign='top' style='width: 40%'>
							<i>Type:</i><br><input type='text' id='typeMemberName' class='textBox'><br><p style='padding-left: 20px'><b><i>OR</i></b></p>
							<i>Select:</i><br>
							<select id='selectMemberID' class='textBox'>".$memberoptions."</select><br><br>
							<p align='center'><input type='button' id='btnAddMember' class='submitButton' style='width: 135px' value='Add Member'></p>
						</td>
						<td class='main' valign='top' style='width: 45%'>
							<b>Invite List:</b>
							<div id='loadingSpiral' class='loadingSpiral'>
								<p align='center'>
									<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
								</p>
							</div>
							<div id='inviteListDiv'>
								
								".$showInviteList."
							
							</div>
						</td>
					</tr>
					<tr>
						<td colspan='3' align='center'>
							<br><br>
							<input type='submit' name='submit' value='Send Invitations' class='submitButton' style='width: 125px'>
						</td>
					</tr>
				</table>
			
			</div>
			<input type='hidden' id='addMemberID' value=''>
		</form>
		
		<script type='text/javascript'>
		
			$(document).ready(function() {
			
				var arrMemberList = 'include/invitemember_notinvitedlist.php';
			
				$('#typeMemberName').autocomplete({
					source: arrMemberList,
					minLength: 3,
					select: function(event, ui) {
					
						$('#addMemberID').val(ui.item.id);
					
					}
				});
				
				
				$('#btnAddMember').click(function() {
				
					var intMemberID = \"\";
					
					if($('#selectMemberID').val() != \"\") {					
						intMemberID = $('#selectMemberID').val();
					}
					else if($('#addMemberID').val() != \"\") {
						intMemberID = $('#addMemberID').val();
					}
					else {
						intMemberID = $('#typeMemberName').val();
					}
					
					
					if(intMemberID != \"\") {
						
						$('#loadingSpiral').show();
						$('#inviteListDiv').fadeOut(250);
						$.post('".$MAIN_ROOT."members/events/include/invitemember_cache.php', { action: 'add', memberID: intMemberID }, function(data) {
						
							$('#inviteListDiv').html(data);
							$('#loadingSpiral').hide();
							$('#inviteListDiv').fadeIn(250);
							
							
							$('#typeMemberName').val('');
							$('#addMemberID').val('');
							
							$('#selectMemberID').val('');
							
						
						});				

					}
					
				
				});
							
			});
			
			
			function removeMember(intInviteKey) {
				$(document).ready(function() {
				
					$('#loadingSpiral').show();
					$('#inviteListDiv').fadeOut(250);
					$.post('".$MAIN_ROOT."members/events/include/invitemember_cache.php', { action: 'delete', memberID: intInviteKey }, function(data) {
						$('#inviteListDiv').html(data);
						$('#loadingSpiral').hide();
						$('#inviteListDiv').fadeIn(250);
					});
				
				});
			}
		
		</script>
		
	";
	
}


?>
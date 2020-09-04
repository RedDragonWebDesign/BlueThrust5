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
include_once("../../classes/rank.php");
include_once("../../classes/rankcategory.php");
include_once("../../classes/squad.php");
include_once("../../classes/tournament.php");


// Delete expired compose list sessions
foreach($_SESSION['btComposeList'] as $key => $arr) {
	if(time() > $arr['exptime']) {
		unset($_SESSION['btComposeList'][$key]);
	}
}



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


// Start Page
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Private Messages");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$PAGE_NAME = "Compose Message - ".$consoleTitle." - ";
$dispBreadCrumb = "<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>".$consoleTitle."</a> > Compose Message";
$EXTERNAL_JAVASCRIPT .= "
<script type='text/javascript' src='".$MAIN_ROOT."members/js/console.js'></script>
<script type='text/javascript' src='".$MAIN_ROOT."members/js/main.js'></script>

<style>
	.ui-autocomplete {
		max-height: 150px;
		overflow-y: auto;
	}
</style>
";

$prevFolder = "../../";
include("../../themes/".$THEME."/_header.php");
echo "
<div class='breadCrumbTitle' id='breadCrumbTitle'>Compose Message</div>
<div class='breadCrumb' id='breadCrumb' style='padding-top: 0px; margin-top: 0px'>
$dispBreadCrumb
</div>
";

$pmObj = new BasicOrder($mysqli, "privatemessages", "pm_id");
$rankCatObj = new RankCategory($mysqli);
$squadObj = new Squad($mysqli);
$tournamentObj = new Tournament($mysqli);
$multiMemPMObj = new Basic($mysqli, "privatemessage_members", "pmmember_id");

$pmObj->set_assocTableName("privatemessage_members");
$pmObj->set_assocTableKey("member_id");

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {

	$memberInfo = $member->get_info_filtered();

	
	$dispError = "";
	$countErrors = 0;
	
	
	if($_POST['submit']) {
		
		$pmSessionID = $_POST['pmsessionid'];
		
		// Check To
		
		$arrReceivers = array();
		
		// Check Members
		
		if(count($_SESSION['btComposeList'][$pmSessionID]['member']) > 0) {
			foreach($_SESSION['btComposeList'][$pmSessionID]['member'] as $memberID) {
				if($memberID != "" && $member->select($memberID)) {
					
					$arrReceivers[] = $memberID;
					
				}
				
			}
		}
		
		
		// Check Rank Category
		
		if(count($_SESSION['btComposeList'][$pmSessionID]['rankcategory']) > 0) {
			foreach($_SESSION['btComposeList'][$pmSessionID]['rankcategory'] as $rankCatID) {
			
				if($rankCatID != "" && $rankCatObj->select($rankCatID)) {
				
					$arrRanks = $rankCatObj->getRanks();
					$rankSQL = "('".implode("','", $arrRanks)."')";
					$filterMembers = "('".implode("','", $arrReceivers)."')";
					$result = $mysqli->query("SELECT member_id FROM ".$dbprefix."members WHERE rank_id IN ".$rankSQL." AND member_id NOT IN ".$filterMembers." AND disabled = '0'");
					while($row = $result->fetch_assoc()) {
					
						$arrReceivers[] = $row['member_id'];
						$arrGroup[$row['member_id']] = array("rankcategory", $rankCatID);
						
					}
				
				}
				
			}
		}
		
		
		// Check Ranks
		$member->select($memberInfo['member_id']);
		if(count($_SESSION['btComposeList'][$pmSessionID]['rank']) > 0) {
			foreach($_SESSION['btComposeList'][$pmSessionID]['rank'] as $rankID) {
		
				if($rankID != "" && $member->objRank->select($rankID)) {
		
					$filterMembers = "('".implode("','", $arrReceivers)."')";
					$result = $mysqli->query("SELECT member_id FROM ".$dbprefix."members WHERE rank_id = '".$rankID."' AND member_id NOT IN ".$filterMembers);
					while($row = $result->fetch_assoc()) {
		
						$arrReceivers[] = $row['member_id'];
						$arrGroup[$row['member_id']] = array("rank", $rankID);
		
					}
		
				}
		
		
			}
		
		}
		
		
		// Check Squads
		$member->select($memberInfo['member_id']);
		$arrSquads = $member->getSquadList();
		if(count($_SESSION['btComposeList'][$pmSessionID]['squad']) > 0) {
			foreach($_SESSION['btComposeList'][$pmSessionID]['squad'] as $squadID) {
				
				if($squadID != "" && in_array($squadID, $arrSquads) && $squadObj->select($squadID)) {
					
					$filterMembers = "('".implode("','", $arrReceivers)."')";
					$result = $mysqli->query("SELECT member_id FROM ".$dbprefix."squads_members WHERE squad_id = '".$squadID."' AND member_id NOT IN ".$filterMembers);
					while($row = $result->fetch_assoc()) {

						$arrReceivers[] = $row['member_id'];
						$arrGroup[$row['member_id']] = array("squad", $squadID);
						
					}
					
				}
				
			
			}
			
		}
		
		// Check Tournaments
		$arrTournaments = $member->getTournamentList(true);
		if(count($_SESSION['btComposeList'][$pmSessionID]['tournament']) > 0) {
			foreach($_SESSION['btComposeList'][$pmSessionID]['tournament'] as $tournamentID) {

				if($tournamentID != "" && in_array($tournamentID, $arrTournaments) && $tournamentObj->select($tournamentID)) {
					
					$filterMembers = "('".implode("','", $arrReceivers)."')";
					$result = $mysqli->query("SELECT member_id FROM ".$dbprefix."tournamentplayers WHERE tournament_id = '".$tournamentID."' AND member_id != '' AND member_id NOT IN ".$filterMembers);
					while($row = $result->fetch_assoc()) {
						
						$arrReceivers[] = $row['member_id'];
						$arrGroup[$row['member_id']] = array("tournament", $tournamentID);	
						
					}
					
				}
				
			}
		}
		
		
		
		if(count($arrReceivers) == 0) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You need to select at least one member to send your message to.";
		}
		elseif(count($arrReceivers) == 1) {
			$arrReceivers = $arrReceivers[0];
		}
		
		
		/*
		
		if(!$member->select($_POST['receiver']) && !$member->select($_POST['tomember'])) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> The user you want to message does not exist.";
		}
		*/
		
		//$receiverID = $member->get_info("member_id");
		
		
		// Check Subject
		if(trim($_POST['subject']) == "") {
			$_POST['subject'] = "untitled";
		}
		
		// Check Message
		if(trim($_POST['message']) == "") {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not send a blank message.";
		}
		
		
		
		if($countErrors == 0) {
			
			$member->select($memberInfo['member_id']);
			if($member->sendPM($arrReceivers, $_POST['subject'], $_POST['message'], $_POST['replypmid'], $arrGroup)) {
				
				
				echo "
				
					<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Sent Private Message!
					</p>
					</div>
				
					<script type='text/javascript'>
						popupDialog('Compose Message', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
					</script>
				
				
				";
				
			}
			else {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to database! Please contact the website administrator.<br>";
			}
			
			
		}
		
		
		if($countErrors > 0) {
			$_POST = filterArray($_POST);
			$_POST['submit'] = false;
		}
		
		
		unset($_SESSION['btComposeList'][$pmSessionID]);
		
	}

	
	
	if(!$_POST['submit']) {
		
		$pmSessionID = uniqid();
		
		$composeListJS = "";
		$_SESSION['btComposeList'][$pmSessionID]['member'] = array();
		$_SESSION['btComposeList'][$pmSessionID]['rankcategory'] = array();
		$_SESSION['btComposeList'][$pmSessionID]['rank'] = array();
		$_SESSION['btComposeList'][$pmSessionID]['squad'] = array();
		$_SESSION['btComposeList'][$pmSessionID]['tournament'] = array();
		$_SESSION['btComposeList'][$pmSessionID]['exptime'] = time()+3600;
		
		if(isset($_GET['threadID']) && $pmObj->select($_GET['threadID']) && isset($_GET['replyID']) && $pmObj->select($_GET['replyID'])) {
			
			$replyPMInfo = $pmObj->get_info();
			$arrReceivers = $pmObj->getAssociateIDs();
			
			$_POST['subject'] = "RE: ".filterText($replyPMInfo['subject']);
			
			if($replyPMInfo['receiver_id'] != 0 && ($replyPMInfo['sender_id'] == $memberInfo['member_id'] || $replyPMInfo['receiver_id'] == $memberInfo['member_id'])) {
				
				$member->select($replyPMInfo['sender_id']);
			
				$member->objRank->select($member->get_info("rank_id"));	
				
				$_SESSION['btComposeList'][$pmSessionID]['member'][] = $replyPMInfo['sender_id'];
				
				$composeListJS = "
				
				$('#composeTextBox').before(\"<div class='pmComposeSelection' data-composeid = 'member_".$replyPMInfo['sender_id']."'><div style='float: left'>".$member->objRank->get_info_filtered("name")." ".$member->get_info_filtered("username")."</div><div class='pmComposeSelectionDelete' data-deleteid = 'member_".$replyPMInfo['sender_id']."'>&times;</div></div>\");
				
				";
				
				
			}
			elseif($replyPMInfo['receiver_id'] == 0 && ($replyPMInfo['sender_id'] == $memberInfo['member_id'] || in_array($memberInfo['member_id'], $arrReceivers))) {
				
				if(isset($_GET['replyall'])) {
					
					$pmObj->set_assocTableKey("pmmember_id");
					$arrPMMID = $pmObj->getAssociateIDs();
					
					$arrGroups['list'] = array();
					$arrGroups['rank'] = array();
					$arrGroups['squad'] = array();
					$arrGroups['tournament'] = array();
					$arrGroups['rankcategory'] = array();
					
					foreach($arrPMMID as $pmmID) {
						
						$multiMemPMObj->select($pmmID);
						$multiMemPMInfo = $multiMemPMObj->get_info();
						
						
						if($multiMemPMInfo['grouptype'] != "" && !in_array($multiMemPMInfo['group_id'], $arrGroups[$multiMemPMInfo['grouptype']])) {
							$arrGroups[$multiMemPMInfo['grouptype']][] = $multiMemPMInfo['group_id'];
						
							switch($multiMemPMInfo['grouptype']) {
								case "rankcategory":
									$dispName = ($rankCatObj->select($multiMemPMInfo['group_id'])) ? $rankCatObj->get_info_filtered("name")." - Category" : "";
									$_SESSION['btComposeList'][$pmSessionID]['rankcategory'][] = $multiMemPMInfo['group_id'];
									$composeListJS .= "$('#composeTextBox').before(\"<div class='pmComposeSelection' data-composeid = 'rankcategory_".$multiMemPMInfo['group_id']."'><div style='float: left'>".$dispName."</div><div class='pmComposeSelectionDelete' data-deleteid = 'rankcategory_".$multiMemPMInfo['group_id']."'>&times;</div></div>\");
									";
									break;
								case "rank":
									$dispName = ($member->objRank->select($multiMemPMInfo['group_id'])) ? $member->objRank->get_info_filtered("name")." - Rank" : "";
									$_SESSION['btComposeList'][$pmSessionID]['rank'][] = $multiMemPMInfo['group_id'];
									$composeListJS .= "$('#composeTextBox').before(\"<div class='pmComposeSelection' data-composeid = 'rank_".$multiMemPMInfo['group_id']."'><div style='float: left'>".$dispName."</div><div class='pmComposeSelectionDelete' data-deleteid = 'rank_".$multiMemPMInfo['group_id']."'>&times;</div></div>\");
									";
									break;
								case "squad":
									$dispName = ($squadObj->select($multiMemPMInfo['group_id'])) ? $squadObj->get_info_filtered("name")." Members" : "";
									$_SESSION['btComposeList'][$pmSessionID]['squad'][] = $multiMemPMInfo['group_id'];
									$composeListJS .= "$('#composeTextBox').before(\"<div class='pmComposeSelection' data-composeid = 'squad_".$multiMemPMInfo['group_id']."'><div style='float: left'>".$dispName."</div><div class='pmComposeSelectionDelete' data-deleteid = 'squad_".$multiMemPMInfo['group_id']."'>&times;</div></div>\");
									";
									break;
								case "tournament":
									$dispName = ($tournamentObj->select($multiMemPMInfo['group_id'])) ? $tournamentObj->get_info_filtered("name")." Players" : "";
									$_SESSION['btComposeList'][$pmSessionID]['tournament'][] = $multiMemPMInfo['group_id'];
									$composeListJS .= "$('#composeTextBox').before(\"<div class='pmComposeSelection' data-composeid = 'tournament_".$multiMemPMInfo['group_id']."'><div style='float: left'>".$dispName."</div><div class='pmComposeSelectionDelete' data-deleteid = 'tournament_".$multiMemPMInfo['group_id']."'>&times;</div></div>\");
									";
									break;
							}
							
						}
						elseif($multiMemPMInfo['grouptype'] == "") {
							$member->select($multiMemPMInfo['member_id']);
							$member->objRank->select($multiMemPMInfo['rank_id']);
							$_SESSION['btComposeList'][$pmSessionID]['member'][] = $multiMemPMInfo['member_id'];
							$dispName = $member->objRank->get_info_filtered("name")." ".$member->get_info_filtered("name");
							$composeListJS .= "$('#composeTextBox').before(\"<div class='pmComposeSelection' data-composeid = 'member_".$multiMemPMInfo['group_id']."'><div style='float: left'>".$dispName."</div><div class='pmComposeSelectionDelete' data-deleteid = 'member_".$multiMemPMInfo['group_id']."'>&times;</div></div>\");
							";
						}
					}

				}
				
				// Add Sender to compose list

				if($replyPMInfo['sender_id'] != $memberInfo['member_id']) {
					$member->select($replyPMInfo['sender_id']);
					$member->objRank->select($member->get_info("rank_id"));
					$_SESSION['btComposeList'][$pmSessionID]['member'][] = $replyPMInfo['sender_id'];
					$dispName = $member->objRank->get_info_filtered("name")." ".$member->get_info_filtered("name");
					$composeListJS .= "$('#composeTextBox').before(\"<div class='pmComposeSelection' data-composeid = 'member_".$replyPMInfo['sender_id']."'><div style='float: left'>".$dispName."</div><div class='pmComposeSelectionDelete' data-deleteid = 'member_".$replyPMInfo['sender_id']."'>&times;</div></div>\");
					";
				}
				

			}
			
			
			
		}
		elseif(isset($_GET['toID']) && $member->select($_GET['toID'])) {
			
			$member->objRank->select($member->get_info("rank_id"));
			$_SESSION['btComposeList'][$pmSessionID]['member'][] = $_GET['toID'];
			$dispName = $member->objRank->get_info_filtered("name")." ".$member->get_info_filtered("name");
			$composeListJS .= "$('#composeTextBox').before(\"<div class='pmComposeSelection' data-composeid = 'member_".$_GET['toID']."'><div style='float: left'>".$dispName."</div><div class='pmComposeSelectionDelete' data-deleteid = 'member_".$_GET['toID']."'>&times;</div></div>\");
			";
				
			
		}
		
		
		
		echo "
			<p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Go Back</a></p>
	
			
			<form action='".$MAIN_ROOT."members/privatemessages/compose.php' method='post'>
				<div class='formDiv'>
			";
		
		if($dispError != "") {
			echo "
			<div class='errorDiv'>
			<strong>Unable to send private message because the following errors occurred:</strong><br><br>
			$dispError
			</div>
			";
		}
		
		echo "
					Use the form below to send a private message.<br>
					<table class='formTable'>
						<tr>
							<td class='formLabel' valign='top'>To:</td>
							<td class='main'>
								<div class='pmComposeTextBox'>
									<div id='composeList' style='float: left'>
										<div id='composeTextBox' style='float: left'><input type='text' id='tomember' name='tomember' class='textBox'></div>
									</div>
									<div style='clear: both'></div>
								</div>
							</td>
						</tr>
						<tr>
							<td class='formLabel'>Subject:</td>
							<td class='main'><input type='text' name='subject' value='".$_POST['subject']."' style='width: 250px' class='textBox'></td>
						</tr>
						<tr>
							<td class='formLabel' valign='top'>Message:</td>
							<td class='main'>
								<textarea class='textBox' cols='50' rows='8' name='message'>".$_POST['message']."</textarea>
							</td>
						</tr>
						<tr>
							<td class='main' colspan='2' align='center'><br>
								<input type='submit' name='submit' class='submitButton' id='btnSubmit' value='Send Message'>
							</td>
						</tr>
					</table>
					<input type='hidden' value='".$pmSessionID."' name='pmsessionid'>
				</div>
				
			";
		
		if(isset($_GET['threadID']) && is_numeric($_GET['threadID'])) {
			echo "<input type='hidden' name='replypmid' value='".$_GET['threadID']."'>";
		}
		else {
			echo "<input type='hidden' name='replypmid' value='0'>";
		}
		
		echo "
			</form>
		
		
			<div style='clear: both'><p align='right' style='margin-bottom: 20px; margin-right: 20px;'><br><br>&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Go Back</a></p></div>
		
		
			<script type='text/javascript'>

					$(document).ready(function() {
						
						$('#tomember').blur(function() {
							$(this).val('');
						}).keypress(function(event) {
							if(event.which == 8) {
								
								if($('#tomember').val() == \"\") {
									$('#btnSubmit').attr('disabled', true);
									$('#btnSubmit').attr('value', 'Please wait...');
									$.post('".$MAIN_ROOT."members/privatemessages/include/compose_tolist.php', { composeID: $('.pmComposeSelection:last').attr('data-composeid'), pmSessionID: '".$pmSessionID."', remove: 1 }, function() {
										$('#btnSubmit').attr('disabled', false);
										$('#btnSubmit').attr('value', 'Send Message');
									});
									
									$('.pmComposeSelection:last').remove();
								}
								
							}
						}).autocomplete({
							source: 'include/compose_search.php?pmsessionid=".$pmSessionID."',
							minLength: 3,
							select: function(event, ui) {

								$('#composeTextBox').before(\"<div class='pmComposeSelection' data-composeid = '\"+ui.item.id+\"'><div style='float: left'>\"+ui.item.value+\"</div><div class='pmComposeSelectionDelete' data-deleteid = '\"+ui.item.id+\"'>&times;</div></div>\");
								
								$('#btnSubmit').attr('disabled', true);
								$('#btnSubmit').attr('value', 'Please wait...');
								$.post('".$MAIN_ROOT."members/privatemessages/include/compose_tolist.php', { composeID: ui.item.id, pmSessionID: '".$pmSessionID."' }, function() {
									$('#btnSubmit').attr('disabled', false);
									$('#btnSubmit').attr('value', 'Send Message');
								});
							
								$('#tomember').val('');
								return false;
							}
						
						});
						
						
						
						
						
						$(document).delegate('.pmComposeSelectionDelete', 'click', function() {
							
							var selector = \"div[data-composeid='\"+$(this).attr('data-deleteid')+\"']\";
						
							$('#btnSubmit').attr('disabled', true);
							$('#btnSubmit').attr('value', 'Please wait...');
							$.post('".$MAIN_ROOT."members/privatemessages/include/compose_tolist.php', { composeID: $(this).attr('data-deleteid'),  remove: 1, pmSessionID: '".$pmSessionID."' }, function() {
								$('#btnSubmit').attr('disabled', false);
								$('#btnSubmit').attr('value', 'Send Message');
							});
							
							$(selector).remove();
							
						
						});
						
						
						".$composeListJS."
					});
				
				</script>
		
		
		
		
		
		";


	}
	
	
	
}
else {

	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");

}



include("../../themes/".$THEME."/_footer.php");

?>
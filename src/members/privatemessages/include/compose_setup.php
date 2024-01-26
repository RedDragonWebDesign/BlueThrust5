<?php

if (!defined("MAIN_ROOT")) {
	exit();
}


if ( empty($_POST['submit']) ) {
	$pmSessionID = uniqid();

	$composeListJS = "";
	$_SESSION['btComposeList'][$pmSessionID]['member'] = [];
	$_SESSION['btComposeList'][$pmSessionID]['rankcategory'] = [];
	$_SESSION['btComposeList'][$pmSessionID]['rank'] = [];
	$_SESSION['btComposeList'][$pmSessionID]['squad'] = [];
	$_SESSION['btComposeList'][$pmSessionID]['tournament'] = [];
	$_SESSION['btComposeList'][$pmSessionID]['exptime'] = time()+3600;

	if (isset($_GET['threadID']) && $pmObj->select($_GET['threadID']) && isset($_GET['replyID']) && $pmObj->select($_GET['replyID'])) {
		$replyPMInfo = $pmObj->get_info();
		$arrReceivers = $pmObj->getAssociateIDs();

		$_POST['subject'] = "RE: ".filterText($replyPMInfo['subject']);


		if ($replyPMInfo['receiver_id'] != 0 && ($replyPMInfo['sender_id'] == $memberInfo['member_id'] || $replyPMInfo['receiver_id'] == $memberInfo['member_id'])) {
			$member->select($replyPMInfo['sender_id']);

			$member->objRank->select($member->get_info("rank_id"));

			$_SESSION['btComposeList'][$pmSessionID]['member'][] = $replyPMInfo['sender_id'];

			$composeListJS = "
			
			$('#composeTextBox').before(\"<div class='pmComposeSelection' data-composeid = 'member_".$replyPMInfo['sender_id']."'><div style='float: left'>".$member->objRank->get_info_filtered("name")." ".$member->get_info_filtered("username")."</div><div class='pmComposeSelectionDelete' data-deleteid = 'member_".$replyPMInfo['sender_id']."'>&times;</div></div>\");
			
			";
		} elseif ($replyPMInfo['receiver_id'] == 0 && ($replyPMInfo['sender_id'] == $memberInfo['member_id'] || in_array($memberInfo['member_id'], $arrReceivers))) {
			if (isset($_GET['replyall'])) {
				$pmObj->set_assocTableKey("pmmember_id");
				$arrPMMID = $pmObj->getAssociateIDs();

				$arrGroups['list'] = [];
				$arrGroups['rank'] = [];
				$arrGroups['squad'] = [];
				$arrGroups['tournament'] = [];
				$arrGroups['rankcategory'] = [];

				foreach ($arrPMMID as $pmmID) {
					$multiMemPMObj->select($pmmID);
					$multiMemPMInfo = $multiMemPMObj->get_info();


					if ($multiMemPMInfo['grouptype'] != "" && !in_array($multiMemPMInfo['group_id'], $arrGroups[$multiMemPMInfo['grouptype']])) {
						$arrGroups[$multiMemPMInfo['grouptype']][] = $multiMemPMInfo['group_id'];

						switch ($multiMemPMInfo['grouptype']) {
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
					} elseif ($multiMemPMInfo['grouptype'] == "") {
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

			if ($replyPMInfo['sender_id'] != $memberInfo['member_id']) {
				$member->select($replyPMInfo['sender_id']);
				$member->objRank->select($member->get_info("rank_id"));
				$_SESSION['btComposeList'][$pmSessionID]['member'][] = $replyPMInfo['sender_id'];
				$dispName = $member->objRank->get_info_filtered("name")." ".$member->get_info_filtered("name");
				$composeListJS .= "$('#composeTextBox').before(\"<div class='pmComposeSelection' data-composeid = 'member_".$replyPMInfo['sender_id']."'><div style='float: left'>".$dispName."</div><div class='pmComposeSelectionDelete' data-deleteid = 'member_".$replyPMInfo['sender_id']."'>&times;</div></div>\");
				";
			}
		}
	} elseif (isset($_GET['toID']) && $member->select($_GET['toID'])) {
		$member->objRank->select($member->get_info("rank_id"));
		$_SESSION['btComposeList'][$pmSessionID]['member'][] = $_GET['toID'];
		$dispName = $member->objRank->get_info_filtered("name")." ".$member->get_info_filtered("name");
		$composeListJS .= "$('#composeTextBox').before(\"<div class='pmComposeSelection' data-composeid = 'member_".$_GET['toID']."'><div style='float: left'>".$dispName."</div><div class='pmComposeSelectionDelete' data-deleteid = 'member_".$_GET['toID']."'>&times;</div></div>\");
		";
	}
}

$composePageJS = "

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


";

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



include_once("../../../_setup.php");
include_once("../../../classes/member.php");
include_once("../../../classes/rank.php");
include_once("../../../classes/consoleoption.php");
include_once("../../../classes/event.php");

if(!isset($eventObj)) {
	
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	$memberInfo = $member->get_info_filtered();
	
	$objMember = new Member($mysqli);
	
	$eventObj = new Event($mysqli);
	
	$consoleObj = new ConsoleOption($mysqli);
	

	$eventID = $_POST['eID'];
	
}

if(!$eventObj->select($eventID)) {

	exit();	
}


echo "<ul>";
$focusID = "";
$result = $mysqli->query("SELECT * FROM ".$dbprefix."eventmessages WHERE event_id = '".$eventID."' ORDER BY dateposted DESC");
while($row = $result->fetch_assoc()) {

	$objMember->select($row['member_id']);
	$memInfo = $objMember->get_info_filtered();

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
	<div class='main messageDiv'><b>".$objMember->getMemberLink()."</b><br>
	".nl2br(parseBBCode($row['message']))."<br>
	<div class='tinyFont' style='margin-top: 5px'>".getPreciseTime($row['dateposted']).$dispDeleteMessage."</div>
	</div>
	<div style='clear: both'></div>
	<ul id='commentsUL_".$row['eventmessage_id']."'>

	";


	
	$eventObj->objEventMessage->select($row['eventmessage_id']);
	$arrMessageComments = $eventObj->objEventMessage->getComments(" ORDER BY dateposted ASC");

	foreach($arrMessageComments as $commentID) {
		if($eventObj->objEventMessageComment->select($commentID) && $objMember->select($row['member_id'])) {
			$commentInfo = $eventObj->objEventMessageComment->get_info_filtered();
			
			$objMember->select($commentInfo['member_id']);
			
			$memInfo = $objMember->get_info_filtered();

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
				<div class='main messageDiv'><b>".$objMember->getMemberLink()."</b><br>
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
	
	if($eventObj->memberHasAccess($memberInfo['member_id'], "postmessages")) {
		
		$tempTextAreaID = "txtComment_".$row['eventmessage_id'];
		
		$dispComment = "";
		if($_POST['commentBox'][$tempTextAreaID] != "") {
			$dispComment = filterText($_POST['commentBox'][$tempTextAreaID]);
			
			$focusID = "#".$tempTextAreaID;
			
		}
		
		echo "
		<li class='dashedLine'>
		Comment:<br>
	
		<textarea id='txtComment_".$row['eventmessage_id']."' class='textBox'>".$dispComment."</textarea>
		<p align='right' style='margin-top: 2px; margin-right: 3px;'><input type='button' onclick=\"postComment('".$row['eventmessage_id']."')\" class='submitButton' value='Comment' style='width: 80px'></p>
	
		</li>
		";
	}
	else {
		echo "<li class='dashedLine'></li>";	
	}

}

echo "</ul>";

if($focusID != "") {

	echo "
		<script type='text/javascript'>
		
			$(document).ready(function() {
			
				tempVal = $('".$focusID."').val();
				$('".$focusID."').val('');
				$('".$focusID."').focus();
				$('".$focusID."').val(tempVal);
			
			});
		
		</script>
	";
}

?>
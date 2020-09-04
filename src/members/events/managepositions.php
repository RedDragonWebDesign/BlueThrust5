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

	if(!$member->hasAccess($consoleObj) || (!$eventObj->memberHasAccess($memberInfo['member_id'], "eventpositions") && $memberInfo['rank_id'] != 1)) {

		exit();
	}
}


echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Manage Event Positions\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$eventInfo['event_id']."'>".$consoleTitle."</a> > <b>".$eventInfo['title'].":</b> Manage Event Positions\");
});
</script>
";


if(!isset($_GET['posID']) || (isset($_GET['posID']) && !$eventObj->objEventPosition->select($_GET['posID']))) {


	echo "
		<table class='formTable' style='border-spacing: 1px'>
			<tr>
				<td class='main' colspan='2' align='right'>
					&raquo; <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$_GET['eID']."&pID=AddPosition'>Add Event Position</a> &laquo;<br><br>
				</td>
			</tr>
			<tr>
				<td class='formTitle' width=\"76%\">Position Name:</td>
				<td class='formTitle' width=\"24%\">Actions:</td>
			</tr>
		</table>
		
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
		
		<div id='positionListDiv' style='margin: 0px; padding: 0px'>
	";
	
	include("include/manageposition_main.php");
	
	echo "
		</div>
		<div id='deletePositionMessage'></div>
		<script type='text/javascript'>
		
			function movePosition(strDir, intPositionID) {
				$(document).ready(function() {
					$('#loadingSpiral').show();
					$('#positionListDiv').hide();
					$.post('".$MAIN_ROOT."members/events/include/manageposition_move.php', {
						pDir: strDir, posID: intPositionID }, function(data) {
							$('#positionListDiv').html(data);
							$('#loadingSpiral').hide();
							$('#positionListDiv').fadeIn(250);
						});
			
				});
			}
			
			function deletePosition(intPositionID) {
				$(document).ready(function() {
					$.post('".$MAIN_ROOT."members/events/include/manageposition_delete.php', { posID: intPositionID }, function(data) {
							$('#deletePositionMessage').html(data);
						});
				});
			}
		
		</script>
	";
	
}
elseif(isset($_GET['posID']) && $_GET['action'] == "edit") {
	include("include/manageposition_edit.php");
}


?>
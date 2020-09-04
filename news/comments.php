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


echo "
	<div id='commentsDiv'>
";
$blnShowDeleteComment = false;
$manageNewsCID = $consoleObj->findConsoleIDByName("Manage News");
$consoleObj->select($manageNewsCID);



if(isset($memberInfo['member_id']) && $member->hasAccess($consoleObj)) {
	$blnShowDeleteComment = true;
}


foreach($arrComments as $commentID) {

	$newsObj->objComment->select($commentID);
	$commentInfo = $newsObj->objComment->get_info_filtered();
	
	$member->select($commentInfo['member_id']);
	$dispDelete = "";
	if($blnShowDeleteComment) {
		$dispDelete = " - <a href='javascript:void(0)' onclick=\"deleteComment('".$commentID."')\">DELETE</a>";
	}
	
	
	echo "
		
		
		<p class='main' style='margin-bottom: 40px'>
		".$commentInfo['message']."<br>
		".$member->getMemberLink()." &nbsp; ".getPreciseTime($commentInfo['dateposted']).$dispDelete."
		</p>
		
	
	";

}

if(count($arrComments) == 0) {
	echo "
		<p class='main' align='center'><br><i>No Comments!</i></p>
	";
}


echo "</div>";


?>
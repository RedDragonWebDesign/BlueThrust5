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

include("../classes/pmfolder.php");

$pmFolderObj = new PMFolder($mysqli);
$cID = $_GET['cID'];



if(isset($_GET['fID']) && !in_array($_GET['fID'], $arrSpecialFolders) && $pmFolderObj->select($_GET['fID'])) {
	// Edit Folder page
	define("EDIT_FOLDER", true);
	include("include/edit.php");
}
else {
	$addFolderCID = $consoleObj->findConsoleIDByName("Add PM Folder");
	echo "
	
		<p align='right' style='margin-bottom: 20px; margin-right: 20px;'>
			<b>&raquo;</b> <a href='".$MAIN_ROOT."members/console.php?cID=".$addFolderCID."'>Add New Folder</a> <b>&laquo;</b>
		</p>
		
		<table class='formTable'>
			<tr>
				<td class='formTitle' style='width: 76%'>Folder Name:</td>
				<td class='formTitle' style='width: 24%'>Actions:</td>
			</tr>
		</table>
	<div id='loadingSpiral' class='loadingSpiral'>
		<p align='center'>
			<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
		</p>
	</div>
	<div id='folderList'>
	";
	define("SHOW_FOLDERLIST", true);
	include("include/folderlist.php");
	echo "</div>
	
		<script type='text/javascript'>
			

			function moveFolder(upOrDown, intFolderID) {
				$(document).ready(function() {
					$('#loadingSpiral').show();
					$('#folderList').fadeOut(250);
					$.post('".$MAIN_ROOT."members/include/privatemessages/include/move.php', { folderDir: upOrDown, folder: intFolderID }, function(data) {
						$('#folderList').html(data);
						$('#folderList').fadeIn(250);
						$('#loadingSpiral').hide();
					});
				});
			}
			
			function deleteFolder(intFolderID) {
				$(document).ready(function() {
			
					$('#loadingSpiral').show();
					$('#folderList').fadeOut(250);
					$.post('".$MAIN_ROOT."members/include/privatemessages/include/delete.php', { folder: intFolderID }, function(data) {
						$('#folderList').html(data);
						$('#folderList').fadeIn(250);
						$('#loadingSpiral').hide();
					});
				
				});
			}

		</script>
	
	";
}



?>
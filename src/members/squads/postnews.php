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

if(!isset($member) || !isset($squadObj) || substr($_SERVER['PHP_SELF'], -strlen("managesquad.php")) != "managesquad.php") {
	exit();
}
else {
	// This is a little repeatative, but for security.
	
	$memberInfo = $member->get_info();
	$consoleObj->select($cID);
	
	$squadObj->select($sID);
	
	
	if(!$member->hasAccess($consoleObj) || !$squadObj->memberHasAccess($memberInfo['member_id'], "postnews")) {
		exit();
	}
}



echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Post News\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$squadInfo['squad_id']."'>".$consoleTitle."</a> > <b>".$squadInfo['name'].":</b> Post News\");
});
</script>
";

$countErrors = 0;
$dispError = "";
if($_POST['submit']){
	
	// Check News Type
	//	1 - Public
	// 	2 - Private
	
	if($_POST['newstype'] != 1 && $_POST['newstype'] != 2) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid news type.<br>";
	}
	
	
	// Check Subject
	
	if(trim($_POST['subject']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a news subject.<br>";
	}
	
	// Check Message
	
	if(trim($_POST['message']) == "") {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not make a blank news post.<br>";
	}
	
	
	if($countErrors == 0) {
		$time = time();
		$arrColumns = array("squad_id", "member_id", "newstype", "dateposted", "postsubject", "newspost");
		$arrValues = array($squadInfo['squad_id'], $memberInfo['member_id'], $_POST['newstype'], $time, $_POST['subject'], $_POST['message']);
		
		$newsPost = new Basic($mysqli, "squadnews", "squadnews_id");
		if($newsPost->addNew($arrColumns, $arrValues)) {
			
			echo "
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully Posted Squad News!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Post Squad News', '".$MAIN_ROOT."members/console.php?cID=".$cID."', 'successBox');
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
	
	
	
}


if(!$_POST['submit']) {
	
	echo "
		<form action='managesquad.php?sID=".$_GET['sID']."&pID=PostNews' method='post'>
			<div class='formDiv'>
			
			";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to post squad news because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	echo "
			
				Use the form below to post squad news.<br><br>
				
				<table class='formTable'>
					<tr>
						<td class='formLabel'>News Type:</td>
						<td class='main'><select name='newstype' class='textBox' id='newsType' onchange='updateTypeDesc()'><option value='1'>Public</option><option value='2'>Private</option></select><span class='tinyFont' style='padding-left: 10px' id='typeDesc'></span></td>
					</tr>
					<tr>
						<td class='formLabel'>Subject:</td>
						<td class='main'><input type='text' name='subject' value='".$_POST['subject']."' class='textBox' style='width: 250px'></td>
					</tr>
					<tr>
						<td class='formLabel' valign='top'>Message:</td>
						<td class='main'>
							<textarea rows='10' cols='50' class='textBox' name='message'>".$_POST['message']."</textarea>
						</td>
					</tr>
					<tr>
						<td class='main' align='center' colspan='2'><br><br>
							<input type='submit' name='submit' value='Post News' class='submitButton' style='width: 125px'>
						</td>
					</tr>
				</table>
				
			</div>
		</form>
		
		<script type='text/javascript'>
			function updateTypeDesc() {
				$(document).ready(function() {
					$('#typeDesc').hide();
					if($('#newsType').val() == \"1\") {
						$('#typeDesc').html('<i>Share this news for the world to see!</i>');
					}
					else {
						$('#typeDesc').html('<i>Only show this post to squad members!</i>');
					}
					$('#typeDesc').fadeIn(250);
				
				});
			}
			
			updateTypeDesc();
		</script>
	";
	
}
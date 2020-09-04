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
	
	if(!$member->hasAccess($consoleObj) || !$squadObj->memberHasAccess($memberInfo['member_id'], "managenews")) {

		exit();
	}
}

$squadNewsObj = new Basic($mysqli, "squadnews", "squadnews_id");

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Manage News\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$squadInfo['squad_id']."'>".$consoleTitle."</a> > <b>".$squadInfo['name'].":</b> Manage News\");
});
</script>
";



if($_GET['nID'] == "") {
	
	echo "
	
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
		<div id='deleteMessage' style='display: none'></div>
		<div id='contentDiv'></div>
	
	
		<script type='text/javascript'>
			$(document).ready(function() {
				$('#loadingSpiral').show();
				$('#contentDiv').hide();
				$.post(\"".$MAIN_ROOT."members/squads/include/newslist.php\", { sID: '".$_GET['sID']."', pID: '".$pID."' }, function(data) {
					$('#contentDiv').html(data);
					$('#loadingSpiral').hide();
					$('#contentDiv').fadeIn(250);
				});
			});
			
			
			function editNews(squadID, newsID) {
				$(document).ready(function() {
					
					divID = \"#newsDiv_\"+newsID;
					$(divID).html(\"<p align='center'><img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading</p>\");
					
					$.post('".$MAIN_ROOT."members/squads/include/editpost.php', { sID: squadID, nID: newsID }, function(data) {
						$(divID).hide();
						$(divID).html(data);
						$(divID).fadeIn(250);
					});
					
				});
			
			}
			
			function saveNewsPost(squadID, newsID) {
			
				$(document).ready(function() {
				
					var strSubject = '#subject_'+newsID;
					var strMessage = '#message_'+newsID;
					var intNewsType = '#newsType_'+newsID;
				
					$.post('".$MAIN_ROOT."members/squads/include/editpost.php', { sID: squadID, nID: newsID, submit: 1, subject: $(strSubject).val(), message: $(strMessage).val(), newstype: $(intNewsType).val() }, function(data) {
						$(divID).hide();
						$(divID).html(data);
						$(divID).fadeIn(250);
					});
					
				});
			
			
			}
			
			function cancelEdit(squadID, newsID) {
				$(document).ready(function() {
				
					divID = \"#newsDiv_\"+newsID;
					$(divID).html(\"<p align='center'><img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading</p>\");
					
				
					$.post('".$MAIN_ROOT."members/squads/include/editpost.php', { sID: squadID, nID: newsID, cancel: 1 }, function(data) {
						$(divID).hide();
						$(divID).html(data);
						$(divID).fadeIn(250);
					});
				});
			
			}
			
			
			function deleteNews(squadID, newsID) {
			
				$(document).ready(function() {				
			
				$.post('".$MAIN_ROOT."members/squads/include/deletepost.php', { sID: squadID, nID: newsID }, function(data) {
			
					
					$('#deleteMessage').dialog({
				
						title: 'Manage Squad News - Delete Post',
						width: 400,
						modal: true,
						zIndex: 9999,
						resizable: false,
						show: 'scale',
						buttons: {
							'Yes': function() {
								
								$('#loadingSpiral').show();
								$('#contentDiv').hide();
								$(this).dialog('close');
								$.post('".$MAIN_ROOT."members/squads/include/deletepost.php', { sID: squadID, nID: newsID, confirm: 1 }, function(data1) {
									$('#contentDiv').html(data1);
									$('#loadingSpiral').hide();
									$('#contentDiv').fadeIn(400);	
								});
							
							},
							'Cancel': function() {
							
								$(this).dialog('close');
							
							}
						}
					});
					
					$('#deleteMessage').html(data);
				
				});

			});		
			
			
			}
			
		</script>
	";
	
}
elseif($_GET['nID'] != "" && $squadNewsObj->select($_GET['nID'])) {
	
	
	echo "
	
	<script type='text/javascript'>
	$(document).ready(function() {
	$('#breadCrumbTitle').html(\"Manage News\");
	$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$squadInfo['squad_id']."'>".$consoleTitle."</a> > <a href='".$MAIN_ROOT."members/squads/managesquad.php?sID=".$_GET['sID']."&pID=ManageNews'><b>".$squadInfo['name'].":</b> Manage News</a> > Edit Post\");
	});
	</script>
	";
	
	
	
	if($_POST['submit']) {
		
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
			$arrColumns = array("newstype", "postsubject", "newspost", "lasteditmember_id", "lasteditdate");
			$arrValues = array($_POST['newstype'], $_POST['subject'], $_POST['message'], $memberInfo['member_id'], $time);
		
			if($squadNewsObj->update($arrColumns, $arrValues)) {
		
				echo "
				<div style='display: none' id='successBox'>
				<p align='center'>
				Successfully Edited News Post!
				</p>
				</div>
		
				<script type='text/javascript'>
				popupDialog('Manage Squad News', '".$MAIN_ROOT."members/managesquad.php?sID=".$_GET['sID']."&pID=ManageNews', 'successBox');
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
		
		$squadNewsInfo = $squadNewsObj->get_info_filtered();
		
		$privateSelected = "";
		if($squadNewsInfo['newstype'] == 2) {
			$privateSelected = "selected";
		}
		
		echo "
			<form action='managesquad.php?sID=".$_GET['sID']."&pID=ManageNews&nID=".$_GET['nID']."' method='post'>
			<div class='formDiv'>
		
		";
		
		if($dispError != "") {
			echo "
			<div class='errorDiv'>
			<strong>Unable to edit squad news because the following errors occurred:</strong><br><br>
			$dispError
			</div>
			";
		}
		
		
		echo "
		
			Use the form below to edit the selected squad news post.<br><br>
					
					<table class='formTable'>
						<tr>
							<td class='formLabel'>News Type:</td>
							<td class='main'><select name='newstype' class='textBox' id='newsType' onchange='updateTypeDesc()'><option value='1'>Public</option><option value='2' ".$privateSelected.">Private</option></select><span class='tinyFont' style='padding-left: 10px' id='typeDesc'></span></td>
						</tr>
						<tr>
							<td class='formLabel'>Subject:</td>
							<td class='main'><input type='text' name='subject' value='".$squadNewsInfo['postsubject']."' class='textBox' style='width: 250px'></td>
						</tr>
						<tr>
							<td class='formLabel' valign='top'>Message:</td>
							<td class='main'>
								<textarea rows='10' cols='50' class='textBox' name='message'>".$squadNewsInfo['newspost']."</textarea>
							</td>
						</tr>
						<tr>
							<td class='main' align='center' colspan='2'><br><br>
								<input type='submit' name='submit' value='Edit Post' class='submitButton' style='width: 125px'>
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
	
	
}



?>
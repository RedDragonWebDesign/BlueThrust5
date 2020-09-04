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
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

echo "

	<div class='formDiv'>

		Use this page to manage member's only pages.  You can also turn on the member's only page tagger.  Once the tagger is on, a window in the bottom left portion of your browser will appear that will allow you to add new member's only pages.  Simply navigate to the page you want to be member's only and mark the page in the tagger window.
	
		<div class='shadedBox' style='width: 45%; margin: 30px auto 20px auto'>
		
			<div id='loadingSpiral' style='display: none'>
				<p align='center' class='main'>
					<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
				</p>
			</div>
		
			<p align='center' style='margin-top: 10px' id='membersOnlyTaggerStatus'>
		
		";


		if(isset($_SESSION['btMembersOnlyTagger']) && $_SESSION['btMembersOnlyTagger'] == 1) {
			echo "
				The member's only page tagger is currently <b>on</b>.<br><br>
			
				<a href='javascript:void(0)' onclick='setMembersOnlyTaggerStatus()'>Turn Off Member's Only Page Tagger</a>
			";			
		}
		else {
			
			echo "
				The member's only page tagger is currently <b>off</b>.<br><br>
			
				<a href='javascript:void(0)' onclick='setMembersOnlyTaggerStatus()'>Turn On Member's Only Page Tagger</a>
			";
			
		}
		
		$selectPrivateForum = "";
		if($websiteInfo['privateforum'] == 1) {
			$selectPrivateForum = " selected";	
		}
		
		$selectPrivateProfile = "";
		if($websiteInfo['privateprofile'] == 1) {
			$selectPrivateProfile = " selected";
		}


		echo "
		
			</p>
		</div>
		
		<div class='dottedLine main' style='width: 95%; margin: 40px auto 0px auto'>
			<b>Member's Only Sections:</b>
		</div>
		<table class='formTable' style='margin-top: 5px'>
			<tr>
				<td class='formLabel'>Member Profiles:</td>
				<td class='main'><select id='profilepages' class='textBox'><option value='0'>Public</option><option value='1'".$selectPrivateProfile.">Private</option></select><span style='padding-left: 10px' id='loadprofile'></span></td>
			</tr>
			<tr>
				<td class='formLabel'>Forum:</td>
				<td class='main'><select id='forumpages' class='textBox'><option value='0'>Public</option><option value='1'".$selectPrivateForum.">Private</option></select><span style='padding-left: 10px' id='loadforum'></span></td>
			</tr>
		</table>
		
		
		<div class='dottedLine main' style='width: 95%; margin: 40px auto 20px auto'>
			<b>Member's Only Pages:</b>
		</div>
		
		<div id='loadingSpiralPageList' style='display: none'>
			<p align='center' class='main'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
			</p>
		</div>
		
		<div id='membersOnlyPageList'>
		";
		
		include("membersonlypageslist.php");
		
		echo "
		</div>
	</div>	
	
	<script type='text/javascript'>
	
	
		function setMembersOnlyTaggerStatus() {
			$(document).ready(function() {
	
				$('#membersOnlyTaggerStatus').hide();
				$('#loadingSpiral').show();
			
				$.post('".$MAIN_ROOT."members/include/admin/membersonlypagetagger.php', { setTaggerStatus: '1' }, function(data) {
	
					$('#membersOnlyTaggerStatus').html(data);
					$('#loadingSpiral').hide();
					$('#membersOnlyTaggerStatus').fadeIn(250);
				
				});
				
			});
		}
		
		
		function untagPage(intPageID) {
		
			$(document).ready(function() {
				
				$('#loadingSpiralPageList').show();
				$('#membersOnlyPageList').hide();
				$.post('".$MAIN_ROOT."members/include/admin/membersonlypagetagger.php', { setPageStatus: '1', pageID: intPageID }, function(data) {

					$('#membersOnlyPageList').html(data);
					$('#loadingSpiralPageList').hide();
					$('#membersOnlyPageList').fadeIn(250);
				
				});
			
			});

		}
		
		function setSectionStatus(strPage, strValue) {
			var jqDivID = '#load'+strPage;
			$.post('".$MAIN_ROOT."members/include/admin/membersonlypagetagger.php', { setSectionStatus: '1', pageID: strPage, pageStatusValue: strValue }, function(data) {
				$(jqDivID).html(data).delay(5000).fadeOut(400);
			});
		
			
		}
		
		$(document).ready(function() {
		
			$('#profilepages').change(function() {
				
				$('#loadprofile').show();
				$('#loadprofile').html('<i>please wait...</i>');
				setSectionStatus('profile', $('#profilepages').val());

			});
		
			$('#forumpages').change(function() {
				
				$('#loadforum').show();
				$('#loadforum').html('<i>please wait...</i>');
				setSectionStatus('forum', $('#forumpages').val());

			});
		
		});
	
	
	</script>	
	
";



?>
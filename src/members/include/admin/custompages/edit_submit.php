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


include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/rank.php");
include_once("../../../../classes/consoleoption.php");


$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("Manage Custom Pages");
$consoleObj->select($cID);

$customPageObj = new Basic($mysqli, "custompages", "custompage_id");


if($member->authorizeLogin($_SESSION['btPassword'])) {
	
	$memberInfo = $member->get_info_filtered();
	
	if($member->hasAccess($consoleObj) && $customPageObj->select($_POST['cpID'])) {
		
		$countErrors = 0;
		// Check Page Name
		
		if(trim($_POST['pagename']) == "") {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You must enter a page name for your custom page.<br>";
		}
		
		
		
		if($countErrors == 0) {
		
			$_POST['wysiwygHTML'] = str_replace("<?", "", $_POST['wysiwygHTML']);
			$_POST['wysiwygHTML'] = str_replace("?>", "", $_POST['wysiwygHTML']);
			$_POST['wysiwygHTML'] = str_replace("&lt;?", "", $_POST['wysiwygHTML']);
			$_POST['wysiwygHTML'] = str_replace("?&gt;", "", $_POST['wysiwygHTML']);
		
			if($customPageObj->update(array("pagename", "pageinfo"), array($_POST['pagename'], $_POST['wysiwygHTML']))) {
			
				
				$dispTime = date("l F j, Y g:i:s A");
				
				$customPageInfo = $customPageObj->get_info();
				echo "
				<script type='text/javascript'>
					
						$('#loadingspiral').hide();
						$('#saveMessage').html(\"<b><span class='successFont'>Custom Page Saved:</span> ".$dispTime."</b>\");
					
				</script>
				";
		
		
		
			}
			else {
				$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to edit custom page.  Please try again!<br>";
				$countErrors++;
			}
		
		}
			
		
	}
	else {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to edit custom page.  Invalid Custom Page ID!<br>";
		$countErrors++;
	}
	
}
else {
	$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to edit custom page.  You are not authorized to edit custom pages!<br>";
	$countErrors++;
}



if($countErrors > 0) {
	echo "
	<script type='text/javascript'>
	
		$('#loadingspiral').hide();
		$('#errorInfo').html(\"".$dispError."\");
		$('#errorDiv').fadeIn(400);
		$('#saveMessage').html(\"<b><span class='failedFont'>Custom Page Not Saved!</span></b>\");
	
	</script>
	";
}



?>
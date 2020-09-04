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

include("../../../_setup.php");
include_once("../../../classes/member.php");
include_once("../../../classes/rank.php");
include_once("../../../classes/consoleoption.php");

$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("Modify Current Theme");

$ipbanObj = new Basic($mysqli, "ipban", "ipaddress");
$failbanObj = new Basic($mysqli, "failban", "failban_id");
$intMaxAttempts = 3;
$submitSuccess = false;
$scrollTop = true;

$consoleObj->select($cID);
if(!$member->hasAccess($consoleObj)) {
	exit();
}

foreach($_POST as $key=>$value) {
	$_POST[$key] = utf8_decode($_POST[$key]);
}


if($member->authorizeLogin($_SESSION['btPassword'])) {
	
	
	$memberInfo = $member->get_info();
	
		
	// Check Security Code
	
	if($_POST['checkadmin'] != constant('ADMIN_KEY')) {
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."failban WHERE ipaddress = '".$IP_ADDRESS."' AND pagename = 'edittheme'");
		$countFails = $result->num_rows;
		$adminKeyFails = $intMaxAttempts-$countFails;
		
		$failbanObj->addNew(array("ipaddress", "pagename"), array($IP_ADDRESS, "edittheme"));
		
		if($adminKeyFails <= 0) {
			$ipbanObj->set_tableKey("ipban_id");
			$ipbanObj->addNew(array("ipaddress"), array($IP_ADDRESS));
		
		
			$banMessage = "You have been permanently banned!  If you are the true website admin, you will be able to unban yourself.  If not... GTFO!";
			echo "
			<div id='acoBan' style='display: none'><p align='center'>".$banMessage."</p></div>
			<script type='text/javascript'>
			$(document).ready(function() {
		
			$('#acoBan').dialog({
			title: 'Banned!',
			modal: true,
			resizable: false,
			width: 400,
			zIndex: 9999,
			buttons: {
			'OK': function() {
			$(this).dialog('close');
		}
		},
		beforeClose: function() {
		window.location = '".$MAIN_ROOT."banned.php';
		}
		
		});
		$('.ui-dialog :button').blur();
		});
		</script>
		
		";
		
			$scrollTop = false;
		}
		
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You entered an invalid admin key.  Please check the config file for the correct admin key.  You have ".$adminKeyFails." more trys before being IP Banned. ".$IP_ADDRESS."<br>";
		
		
		
	}
	
	
	// Update Header
	
	if(!is_writable("../../../themes/".$THEME."/_header.php")) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save header information.<br>";
	}
	else {
		
		$headerCode = $_POST['headerCode'];
		$headerCode = str_replace("&lt;", "<", $headerCode);
		$headerCode = str_replace("&gt;", ">", $headerCode);
		$headerCode = str_replace("&#38;", "&", $headerCode);
		
		
		$themeFile = fopen("../../../themes/".$THEME."/_header.php", "w");
		if(!fwrite($themeFile, $headerCode)) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save header information.<br>";
		}
		
	}
	
	// Update Footer
	
	if(!is_writable("../../../themes/".$THEME."/_footer.php")) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save footer information.<br>";
	}
	else {
		
		$footerCode = $_POST['footerCode'];
		$footerCode = str_replace("&lt;", "<", $footerCode);
		$footerCode = str_replace("&gt;", ">", $footerCode);
		$footerCode = str_replace("&38#;", ">", $footerCode);
		
		$themeFile = fopen("../../../themes/".$THEME."/_footer.php", "w");
		if(!fwrite($themeFile, $footerCode)) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save footer information.<br>";
		}
		
	}
	
	// Update Theme CSS
	
	if(!is_writable("../../../themes/".$THEME."/style.css")) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save theme css information.<br>";
	}
	else {
		$themeFile = fopen("../../../themes/".$THEME."/style.css", "w");
		if(!fwrite($themeFile, htmlspecialchars_decode($_POST['themeCSSCode']))) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save theme css information.<br>";
		}
		
	}
	
	/*
	// Update Global CSS
	
	if(!is_writable("../../../themes/".$THEME."/btcs4.css")) {
		$countErrors++;
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save global css information.<br>";
	}
	else {
		$themeFile = fopen("../../../btcs4.css", "w");
		if(!fwrite($themeFile, htmlspecialchars_decode($_POST['globalCSSCode']))) {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save global css information.<br>";
		}
		
	}
	*/
	
	
	if($countErrors == 0) {
		$submitSuccess = true;	
	}
		
	
}


if($submitSuccess) {
	$dispTime = date("l F j, Y g:i:s A");
	echo "
		<script type='text/javascript'>
			$('#saveMessage').html(\"<b><span class='successFont'>Theme Information Saved: </span> ".$dispTime."</b><br>Refresh the page to view changes.\");
			$('#saveMessage').fadeIn(400);
			$('#errorDiv').hide();
		</script>
	";
	
}
else {
	
	echo "
		<script type='text/javascript'>
			$(document).ready(function() {
				
				$('#errorMessage').html('".$dispError."');
				$('#errorDiv').fadeIn(400);
				$('#saveMessage').html(\"<span class='failedFont'><b>Theme Information Not Saved!</b></span>\");
				$('#saveMessage').fadeIn(400);
				";
				
		if($scrollTop) {
			echo "$('html, body').animate({ scrollTop: 0 });";
		}
				
				echo "
			});
		</script>
	";
	
}


?>
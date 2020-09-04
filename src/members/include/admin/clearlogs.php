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


$cID = $_GET['cID'];



$dispError = "";
$countErrors = 0;


if($_POST['submit']) {
	
	$checkArr = array(1,2,3,4,5,6);
	
	if(!in_array($_POST['clearlogs'], $checkArr)) {
		$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an delete option.<br>";
		$countErrors++;
	}

	if($countErrors == 0) {
	
		switch($_POST['clearlogs']) {
			case 1:
				$deleteDate = time()-(60*60*24*15);
				$query = "DELETE FROM ".$dbprefix."logs WHERE logdate < '".$deleteDate."'";
				$successMessage = "cleared logs older than 15 days";
				break;
			case 2:
				$deleteDate = time()-(60*60*24*30);
				$query = "DELETE FROM ".$dbprefix."logs WHERE logdate < '".$deleteDate."'";
				$successMessage = "cleared logs older than 30 days";
				break;
			case 3:
				$deleteDate = time()-(60*60*24*45);
				$query = "DELETE FROM ".$dbprefix."logs WHERE logdate < '".$deleteDate."'";
				$successMessage = "cleared logs older than 45 days";
				break;	
			case 4:
				$deleteDate = time()-(60*60*24*60);
				$query = "DELETE FROM ".$dbprefix."logs WHERE logdate < '".$deleteDate."'";
				$successMessage = "cleared logs older than 60 days";
				break;
			case 5:
				$deleteDate = time()-(60*60*24*90);
				$query = "DELETE FROM ".$dbprefix."logs WHERE logdate < '".$deleteDate."'";
				$successMessage = "cleared logs older than 90 days";
				break;
			default:
				$query = "TRUNCATE TABLE ".$dbprefix."logs";
				$successMessage = "cleared all logs";
				break;
		}
		
		if($mysqli->query($query)) {
			
			echo "
			
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully ".$successMessage."</b>!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Clear Logs', '".$MAIN_ROOT."members', 'successBox');
				</script>
			
			";
			
			$mysqli->query("OPTIMIZE TABLE `".$dbprefix."logs`");
			$logMessage = ucfirst($successMessage).".";
			$member->logAction($logMessage);
			
		}
		else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";			
		}
		
		
		
	}
	
	if($countErrors > 0) {
		$_POST['submit'] = false;	
	}
	
}

if(!$_POST['submit']) {
	
	echo "
		<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
			<div class='formDiv'>
			
			";
	
	if($dispError != "") {
		echo "
		<div class='errorDiv'>
		<strong>Unable to clear logs because the following errors occurred:</strong><br><br>
		$dispError
		</div>
		";
	}
	
	
	echo "
				Use the form below to clear the website's logs.
				<table class='formTable'>
					<tr>
						<td class='formLabel'>Delete:</td>
						<td class='main'>
							<select class='textBox' name='clearlogs'>
								<option value='select'>Select</option>
								<option value='1'>Older than 15 days</option>
								<option value='2'>Older than 30 days</option>
								<option value='3'>Older than 45 days</option>
								<option value='4'>Older than 60 days</option>
								<option value='5'>Older than 90 days</option>
								<option value='6'>Clear All</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class='main' colspan='2' align='center'><br>
							<input type='submit' name='submit' value='Clear Logs' class='submitButton'>
						</td>
					</tr>
				</table>
			</div>
		</form>
	";
}



?>
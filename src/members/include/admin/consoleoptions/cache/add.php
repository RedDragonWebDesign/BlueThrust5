<?php

/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */

require_once("../../../../../_setup.php");
require_once("../../../../../classes/member.php");
require_once("../../../../../classes/rank.php");
require_once("../../../../../classes/consoleoption.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$consoleObj = new ConsoleOption($mysqli);

$intAddConsoleCID = $consoleObj->findConsoleIDByName("Add Console Option");
$consoleObj->select($intAddConsoleCID);
$checkAccess1 = $member->hasAccess($consoleObj);


$intManageConsoleCID = $consoleObj->findConsoleIDByName("Manage Console Options");
$consoleObj->select($intManageConsoleCID);
$checkAccess2 = $member->hasAccess($consoleObj);

$checkAccess = ($checkAccess1 || $checkAccess2);

$blnSuccess = false;
if ($member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();

	if ($checkAccess && is_numeric($_POST['mID'])) {
		if ($member->select($_POST['mID']) && ($_POST['accessrule'] == "allow" || $_POST['accessrule'] == "deny")) {
			$intAlreadyAdded = "no";
			$counter = 0;
			foreach ($_SESSION['btAccessRules'] as $key => $accessInfo) {
				if ($accessInfo['mID'] == $_POST['mID']) {
					$intAlreadyAdded = $key;
				}
			}



			$arrSaveInfo = [
					'mID' => $_POST['mID'],
					'accessRule' => $_POST['accessrule']
					];

			if (is_numeric($intAlreadyAdded)) {
				$_SESSION['btAccessRules'][$intAlreadyAdded] = $arrSaveInfo;
			} else {
				$_SESSION['btAccessRules'][] = $arrSaveInfo;
			}
			$blnSuccess = true;
		}
	}

	if ($checkAccess) {
		require_once("view.php");
	}


	if (!$blnSuccess && $checkAccess) {
		echo "
			<div id='addErrorMessage' style='display: none'><p align='center'>Unable to add special access rule!  Please Try Again.</p></div>
			<script type='text/javascript'>
				$(document).ready(function() {
				
					$('#addErrorMessage').dialog({
						title: 'Add Console Option - Error',
						modal: true,
						zIndex: 9999,
						resizable: false,
						show: 'scale',
						width: 400,
						buttons: {
							'OK': function() {
								$(this).dialog('close');
							}
						}
					
					
					});
				
				});
			</script>
			";
	}
}

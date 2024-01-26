<?php

if (!defined("CAMPAIGN_FORM")) {
	exit();
}

$arrPaypalCurrencyCodes = $campaignObj->getCurrencyCodes();
$arrPaypalCurrencyInfo = $campaignObj->getCurrencyCodeInfo();

$donationPlugin = new btPlugin($mysqli);
$donationPlugin->selectByName("Donations");
$checkRecurringBox = ($setRecurringBox == 1) ? 1 : 0;
$campaignJS = "
	
		$(document).ready(function() {
			var recurring = ".$checkRecurringBox.";
		
			$('#chkRecurring').click(function() {
				if(recurring == 1) {
					$('#repeatPeriodAmount').attr('disabled', 'disabled');
					$('#repeatPeriodUnit').attr('disabled', 'disabled');
					recurring = 0;
				}
				else {
					$('#repeatPeriodAmount').attr('disabled', false);
					$('#repeatPeriodUnit').attr('disabled', false);
					recurring = 1;
				}
			});
			
			$('#runUntil').change(function() {
			
				if($(this).val() == \"choose\") {
					$('#jsEndDate').show();
				}
				else {
					$('#jsEndDate').hide();
				}
			
			});
			
			
			$('#runUntil').change();
		});
	";


$maxYear = date("Y")+10;
$maxDate = "new Date(".$maxYear.",12,31)";


$i=0;

$arrComponents = [
		"mainsection" => [
			"type" => "section",
			"options" => ["section_title" => "General Information"],
			"sortorder" => $i++
		],
		"title" => [
			"type" => "text",
			"attributes" => ["class" => "textBox bigTextBox formInput"],
			"sortorder" => $i++,
			"display_name" => "Title",
			"db_name" => "title",
			"validate" => ["NOT_BLANK"]
		],
		"description" => [
			"type" => "textarea",
			"display_name" => "Description",
			"attributes" => ["class" => "textBox formInput bigTextBox", "rows" => 5],
			"sortorder" => $i++,
			"db_name" => "description"
		],
		"rununtil" => [
			"type" => "select",
			"display_name" => "Run Until",
			"options" => ["forever" => "Forever", "choose" => "Choose Date"],
			"attributes" => ["class" => "textBox formInput", "id" => "runUntil"],
			"sortorder" => $i++
		],
		"enddate" => [
			"type" => "datepicker",
			"sortorder" => $i++,
			"attributes" => ["style" => "cursor: pointer; display: none", "id" => "jsEndDate", "class" => "textBox formInput"],
			"db_name" => "dateend",
			"before_html" => "<label class='formLabel' style='display: inline-block'></label>
			",
			"options" => ["changeMonth" => "true",
							   "changeYear" => "true",
							   "dateFormat" => "M d, yy",
							   "minDate" => "new Date(50, 1, 1)",
							   "maxDate" => $maxDate,
							   "yearRange" => "1950:".$maxYear,
							   "altField" => "realEndDate"],
			"validate" => ["NUMBER_ONLY"],
			"value" => 0
		],
		"allowname" => [
			"type" => "checkbox",
			"display_name" => "Allow Names",
			"tooltip" => "Check this box to allow donators to leave their name.",
			"sortorder" => $i++,
			"value" => 1,
			"options" => [1 => ""],
			"attributes" => ["class" => "formInput"],
			"db_name" => "allowname"
		],
		"allowmessage" => [
			"type" => "checkbox",
			"display_name" => "Allow Messages",
			"tooltip" => "Check this box to allow donators to leave a message.",
			"sortorder" => $i++,
			"value" => 1,
			"options" => [1 => ""],
			"attributes" => ["class" => "formInput"],
			"db_name" => "allowmessage"
		],
		"allowhiddenamount" => [
			"type" => "checkbox",
			"display_name" => "Allow Hidden Amounts",
			"tooltip" => "Check this box to allow donators to hide the amount they donated on the donation profile page.  You will still be able to view the amount in the donation logs.",
			"sortorder" => $i++,
			"value" => 0,
			"options" => [1 => ""],
			"attributes" => ["class" => "formInput"],
			"db_name" => "allowhiddenamount"
		],
		"goalamount" => [
			"type" => "text",
			"attributes" => ["class" => "formInput textBox smallTextBox"],
			"sortorder" => $i++,
			"display_name" => "Donation Goal",
			"db_name" => "goalamount"
		],
		"minimumamount" => [
			"type" => "text",
			"attributes" => ["class" => "formInput textBox smallTextBox"],
			"sortorder" => $i++,
			"display_name" => "Minimum Donation",
			"value" => "1.00",
			"db_name" => "minimumamount"
		],
		"currency" => [
			"type" => "select",
			"attributes" => ["class" => "formInput textBox"],
			"sortorder" => $i++,
			"display_name" => "Currency",
			"db_name" => "currency",
			"options" => $arrPaypalCurrencyCodes,
			"value" => $donationPlugin->getConfigInfo("currency")
		]

	];

	// Check for award medal console access

$awardMedalCID = $consoleObj->findConsoleIDByName("Award Medal");
$consoleObj->select($awardMedalCID);
$hasAwardMedalAccess = false;
if ($member->hasAccess($consoleObj)) {
	$hasAwardMedalAccess = true;
	$medalOptions[0] = "None";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."medals ORDER BY ordernum DESC");
	while ($row = $result->fetch_assoc()) {
		$medalOptions[$row['medal_id']] = filterText($row['name']);
	}


	$arrComponents['awardmedal'] = [
		"type" => "select",
		"display_name" => "Award Medal",
		"sortorder" => $i++,
		"tooltip" => "Auto-award a medal to a member who donates.",
		"attributes" => ["class" => "formInput textBox"],
		"db_name" => "awardmedal",
		"options" => $medalOptions
	];
}

$consoleObj->select($cID);

if (!is_array($arrSelectRecur)) {
	$arrSelectRecur['months'] = "selected";
}

$arrRecurUnits = ["days"=>"Days", "weeks"=>"Weeks", "months"=>"Months", "years"=>"Years"];
foreach ($arrRecurUnits as $key => $value) {
	$recurOptions .= "<option value='".$key."'".$arrSelectRecur[$key].">".$value."</option>";
}

$disabledRecurring = ($checkRecurringBox == 1) ? "" : " disabled='disabled'";

$arrRecurringComponents = [
		"recurringsection" => [
			"type" => "section",
			"options" => ["section_title" => "Recurring Options", "section_description" => "Use this section to setup a campaign that restarts after a certain period of time."],
			"sortorder" => $i++
		],
		"recurring" => [
			"type" => "checkbox",
			"display_name" => "Recurring Campaign",
			"options" => [1 => ""],
			"sortorder" => $i++,
			"attributes" => ["class" => "formInput", "id" => "chkRecurring"],
			"value" => $checkRecurringBox,
			"db_name" => "currentperiod"
		],
		"repeatperiod" => [
			"type" => "custom",
			"display_name" => "Repeat Every",
			"sortorder" => $i++,
			"html" => "<input type='text' id='repeatPeriodAmount' class='textBox smallTextBox formInput' name='recurringamount' value='1'".$disabledRecurring."> <select name='recurringunit' class='textBox formInput' id='repeatPeriodUnit'".$disabledRecurring.">".$recurOptions."</select>",
			"validate" => ["validateCreateCampaignForm"]
		]
	];


$arrComponents['submit'] = [
		"type" => "submit",
		"value" => "Create Campaign",
		"attributes" => ["class" => "submitButton formSubmitButton"],
		"sortorder" => $i++
	];

$arrComponents = array_merge($arrComponents, $arrRecurringComponents);
$setupFormArgs = [
		"name" => "console-".$cID,
		"components" => $arrComponents,
		"saveObject" => $campaignObj,
		"saveType" => "add",
		"saveMessage" => "Successfully created new donation campaign!",
		"attributes" => ["action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"],
		"description" => "Use the form below to create a new donation campaign.",
		"embedJS" => $campaignJS,
		"saveAdditional" => ["member_id" => $memberInfo['member_id'], "datestarted" => time(), "recurringamount" => $_POST['recurringamount'], "recurringunit" => $_POST['recurringunit']]
	];


function validateCreateCampaignForm() {
	global $hasAwardMedalAccess, $formObj, $arrRecurUnits;

	if (!$hasAwardMedalAccess) {
		$formObj->errors[] = "You don't have access to the award medal privilege.";
	}

	$validRecurringUnits = array_keys($arrRecurUnits);
	if (!in_array($_POST['recurringunit'], $validRecurringUnits) && $_POST['recurring'] == 1) {
		$formObj->errors[] = "You selected an invalid recurring unit.";
	}

	if ($_POST['recurringamount'] <= 0 && $_POST['recurring'] == 1) {
		$formObj->errors[] = "The recurring amount must be greater than zero.";
	}

	if ($_POST['recurring'] != 1) {
		$_POST['recurringunit'] = "";
		$_POST['recurringamount'] = 0;
		$_POST['recurring'] = 0;
	} else {
		switch ($_POST['recurringunit']) {
			case "days":
				$_POST['recurring'] = date($formObj->objSave->DAY);
				break;
			case "weeks":
				$_POST['recurring'] = date($formObj->objSave->WEEK);
				break;
			case "months":
				$_POST['recurring'] = date($formObj->objSave->MONTH);
				break;
			case "years":
				$_POST['recurring'] = date($formObj->objSave->YEAR);
				break;
		}
	}

	if ($_POST['rununtil'] == "forever") {
		$_POST['enddate'] = 0;
	}

	if ($formObj->saveType == "update") {
		global $campaignInfo;

		if ($campaignInfo['recurringunit'] == $_POST['recurringunit'] && $campaignInfo['recurringamount'] == $_POST['recurringamount']) {
			$_POST['recurring'] = $campaignInfo['currentperiod'];
		}
	}
}

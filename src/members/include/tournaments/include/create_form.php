<?php

if (!defined("MAIN_ROOT")) {
	exit();
}

if (!isset($tournamentObj)) {
	$tournamentObj = new Tournament($mysqli);
}

$gameObj = new Game($mysqli);

$i=0;


	// Date/Time Options
$oneYear = 31536000;

$minDate = "new Date(".date("Y").", ".(date("n")-1).", ".date("j").")";
$maxDate = "new Date(".date("Y, n, j", time()+($oneYear*8)).")";
$defaultDate = date("M j, Y");
$yearRange = date("Y").":".date("Y", time()+($oneYear*8));


	// Game Options
$gameOptions = [];
foreach ($gameObj->getGameList() as $gameID) {
	$gameObj->select($gameID);

	$gameOptions[$gameID] = $gameObj->get_info_filtered("name");
}

if (count($gameOptions) == 0) {
	$gameOptions[0] = "No Games";
}

	// Tournament Structure
$seedTypeOptions = [
		1 => "Manual",
		2 => "Random",
		3 => "Pools"
	];
$seedExplaination = "<span style=\'text-decoration:underline; font-weight: bold\'>Manual:</span> Seeds go in numeric order as you add players to the tournament.<br><br><span style=\'text-decoration:underline; font-weight: bold\'>Random:</span> Seeds are randomly set to players as you add them to the tournament.<br><br><span style=\'text-decoration:underline; font-weight: bold\'>Pools:</span> Teams/Players are separated into groups before the main tournament starts.  Each team/player plays one another in their group.  Seeds are determined by the win/loss record within that group.<br><br>With each seed option, you will have the ability to change the first round matches.  The matches will be set up with the top seed facing the lowest seed, second top seed facing the second lowest seed, and so on.";

$eliminationOptions = [1 => "Single Elimination"];

$maxTeamsPlayers = [4 => 4, 8 => 8, 16 => 16, 32 => 32, 64 => 64];
$playersPerTeam = [];
for ($i=1; $i<=16; $i++) {
	$playersPerTeam[$i] = $i;
}

$arrComponents = [

		"generalinfo" => [
			"type" => "section",
			"options" => ["section_title" => "General Information"],
			"sortorder" => $i++
		],
		"tournamentname" => [
			"type" => "text",
			"sortorder" => $i++,
			"attributes" => ["class" => "formInput textBox"],
			"display_name" => "Tournament Name",
			"validate" => ["NOT_BLANK"],
			"db_name" => "name"
		],
		"startdate" => [
			"type" => "datepicker",
			"sortorder" => $i++,
			"display_name" => "Start Date",
			"attributes" => ["class" => "formInput textBox", "id" => "jsStartDate"],
			"options" => ["changeMonth" => "true",
						   "changeYear" => "true",
						   "dateFormat" => "M d, yy",
						   "minDate" => $minDate,
						   "maxDate" => $maxDate,
						   "yearRange" => $yearRange,
						   "defaultDate" => $defaultDate,
						   "altField" => "realStartDate"],
			"validate" => ["NUMBER_ONLY"],
			"usetime" => "starttime",
			"db_name" => "startdate",
			"value" => time()*1000
		],
		"starttime" => [
			"type" => "timepicker",
			"sortorder" => $i++,
			"display_name" => "Start Time",
			"attributes" => ["class" => "textBox"],
			"options" => ["show_timezone" => 1]
		],
		"game" => [
			"type" => "select",
			"sortorder" => $i++,
			"display_name" => "Game",
			"attributes" => ["class" => "textBox formInput"],
			"options" => $gameOptions,
			"db_name" => "gamesplayed_id"
		],
		"requirereplay" => [
			"type" => "checkbox",
			"sortorder" => $i++,
			"display_name" => "Require Replay",
			"attributes" => ["class" => "textBox formInput"],
			"value" => 1,
			"db_name" => "requirereplay"
		],
		"extrainfo" => [
			"type" => "textarea",
			"display_name" => "Extra Info",
			"attributes" => ["class" => "textBox formInput", "rows" => 5, "cols" => 35],
			"sortorder" => $i++,
			"db_name" => "description"
		],
		"tournamentstructure" => [
			"type" => "section",
			"options" => ["section_title" => "Tournament Structure"],
			"sortorder" => $i++
		],
		"seedtype" => [
			"type" => "select",
			"display_name" => "Seed Type",
			"sortorder" => $i++,
			"options" => $seedTypeOptions,
			"validate" => ["RESTRICT_TO_OPTIONS"],
			"db_name" => "seedtype",
			"attributes" => ["class" => "textBox formInput"],
			"tooltip" => $seedExplaination
		],
		"eliminations" => [
			"type" => "select",
			"display_name" => "Eliminations",
			"sortorder" => $i++,
			"options" => $eliminationOptions,
			"db_name" => "eliminations",
			"attributes" => ["class" => "textBox formInput"],
			"validate" => ["RESTRICT_TO_OPTIONS"]
		],
		"maxteams" => [
			"type" => "select",
			"display_name" => "Max Teams/Players",
			"sortorder" => $i++,
			"options" => $maxTeamsPlayers,
			"db_name" => "maxteams",
			"attributes" => ["class" => "textBox formInput"],
			"validate" => ["RESTRICT_TO_OPTIONS"]
		],
		"players" => [
			"type" => "select",
			"display_name" => "Players Per Team",
			"sortorder" => $i++,
			"options" => $playersPerTeam,
			"db_name" => "playersperteam",
			"attributes" => ["class" => "textBox formInput"],
			"validate" => ["RESTRICT_TO_OPTIONS"]
		],
		"tournamentaccesssection" => [
			"type" => "section",
			"options" => ["section_title" => "Tournament Access"],
			"sortorder" => $i++
		],
		"access" => [
			"type" => "select",
			"display_name" => "Access",
			"options" => [1 => "Clan Only", 3 => "Everyone"],
			"sortorder" => $i++,
			"db_name" => "access",
			"validate" => ["RESTRICT_TO_OPTIONS"],
			"attributes" => ["class" => "formInput textBox"]
		],
		"tournamentpw" => [
			"type" => "password",
			"display_name" => "Password",
			"tooltip" => "Leave blank for no password",
			"sortorder" => $i++,
			"attributes" => ["class" => "formInput textBox", "id" => "tournamentpw"],
			"validate" => [["name" => "EQUALS_VALUE", "value" => $_POST['tournamentpw_check']]]
		],
		"tournamentpw_check" => [
			"type" => "password",
			"display_name" => "Re-type Password",
			"sortorder" => $i++,
			"attributes" => ["class" => "formInput textBox", "id" => "tournamentpw_check"],
			"html" => "<span id='checkPassword' class='formInput formInputSideText'></span>"
		],
		"submit" => [
			"type" => "submit",
			"attributes" => ["class" => "formSubmitButton submitButton"],
			"sortorder" => $i++,
			"value" => "Create Tournament"
		]



	];



$setupFormArgs = [
		"name" => "console-".$cID,
		"components" => $arrComponents,
		"description" => "Use the form below to create a tournament.",
		"saveObject" => $tournamentObj,
		"saveMessage" => "Successfully Created New Tournament!",
		"saveType" => "add",
		"attributes" => ["action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"],
		"saveAdditional" => ["password" => md5($_POST['tournamentpw']), "timezone" => $_POST['starttime_timezone'], "member_id" => $memberInfo['member_id']]
	];

	?>


<script type='text/javascript'>
			
	$(document).ready(function() {
	
		$('#tournamentpw_check').keyup(function() {
			
			if($('#tournamentpw').val() != "") {
			
				if($('#tournamentpw_check').val() == $('#tournamentpw').val()) {
					$('#checkPassword').toggleClass('successFont', true);
					$('#checkPassword').toggleClass('failedFont', false);
					$('#checkPassword').html('ok!');
				}
				else {
					$('#checkPassword').toggleClass('successFont', false);
					$('#checkPassword').toggleClass('failedFont', true);
					$('#checkPassword').html('error!');
				}
			
			}
			else {
				$('#checkPassword').html('');
			}
		
		});
	
	});

</script>	

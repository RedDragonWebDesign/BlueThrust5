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



	if(!isset($member)|| substr($_SERVER['PHP_SELF'], -strlen("console.php")) != "console.php") {
	
		exit();
	}
	else {
		// This is a little repeatative, but for security.
	
		$memberInfo = $member->get_info();
		$consoleObj->select($cID);
	
		if(!$member->hasAccess($consoleObj)) {
	
			exit();
		}
	}
	require_once("../classes/tournament.php");

	$tMemberObj = new Member($mysqli);
	
	$countErrors = 0;
	$dispError = "";

	$tournamentObj = new Tournament($mysqli);
	
	$arrTournaments = $member->getTournamentList();


	$tournamentSQL = "('".implode("','", $arrTournaments)."')";
	
	$tournamentOptions[''] = "Select";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."tournaments WHERE tournament_id NOT IN ".$tournamentSQL." ORDER BY name");
	while($row = $result->fetch_assoc()) {		
		$tournamentOptions[$row['tournament_id']] = filterText($row['name']);
	}
	
	if($result->num_rows > 0) {
	
		
		$arrComponents = array(
			"tournament" => array(
				"display_name" => "Tournament",
				"type" => "select",
				"options" => $tournamentOptions,
				"attributes" => array("class" => "textBox formInput", "id" => "tournamentID"),
				"sortorder" => 1,
				"validate" => array("RESTRICT_TO_OPTIONS", "joinTournamentChecks"),
				"value" => (isset($_GET['tID'])) ? $_GET['tID'] : "",
				"db_name" => "tournament_id"
			),
			"loading" => array(
				"type" => "custom",
				"html" => "<div id='loadingSpiral' class='loadingSpiral'>
								<p align='center' class='main'>
									<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
								</p>
							</div>",
				"sortorder" => 2,
				"hidden" => true
			),
			"fakeSubmit" => array(
				"type" => "button",
				"value" => "Join Tournament",
				"attributes" => array("class" => "submitButton formSubmitButton", "id" => "btnFakeSubmit"),
				"sortorder" => 3
			),
			"submit" => array(
				"type" => "submit",
				"value" => "submit",
				"attributes" => array("style" => "display: none", "id" => "btnSubmit"),
				"sortorder" => 4,
				"hidden" => true				
			),
			"tournamentpassword" => array(
				"type" => "hidden",
				"attributes" => array("id" => "tournamentPassword"),
				"sortorder" => 99,
				"hidden" => true
			)
		
		
		);
		
		
		$setupFormArgs = array(
			"name" => "console-".$cID,
			"components" => $arrComponents,
			"saveMessage" => "Successfully joined tournament!",
			"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"),
			"description" => "Use the form below to join a tournament.",
			"saveObject" => $tournamentObj->objPlayer,
			"saveType" => "add",
			"saveAdditional" => array("member_id" => $memberInfo['member_id'])
		);

		echo "
			
			<div id='checkPasswordDump'></div>
			<script type='text/javascript'>
		
				$(document).ready(function() {
				
					$('#btnFakeSubmit').click(function() {
						
						$('#loadingSpiral').show();
						$.post('".$MAIN_ROOT."members/include/tournaments/include/checkpassword.php', { tID: $('#tournamentID').val() }, function(data) {
						
							$('#checkPasswordDump').html(data);
							
						
						});
					
					});
				
				});
			
			</script>
		
		";
		
	
	}
	else {

		echo "
			<div class='shadedBox' style='width: 40%; margin: 25px auto'>
				<p class='main' align='center'>
					<i>There are no tournaments for you to join!</i>
				</p>
			</div>
		";
	}
	

	require_once(BASE_DIRECTORY."members/include/tournaments/include/jointournamentfunctions.php");
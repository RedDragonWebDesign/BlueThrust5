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


if(!isset($member) || !isset($tournamentObj) || substr($_SERVER['PHP_SELF'], -strlen("managetournament.php")) != "managetournament.php") {

	exit();
}
else {
	// This is a little repeatative, but for security.

	$memberInfo = $member->get_info();
	$consoleObj->select($cID);

	$tournamentObj->select($tID);


	if(!$member->hasAccess($consoleObj)) {

		exit();
	}
}

echo "

<script type='text/javascript'>
$(document).ready(function() {
$('#breadCrumbTitle').html(\"Manage Matches\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$tournamentInfo['tournament_id']."'>".$consoleTitle."</a> > <b>".$tournamentInfo['name'].":</b> Manage Matches\");

});
</script>
";


$dispError = "";
$countErrors = 0;


// Get Rounds with Matches that are settable

$result = $mysqli->query("SELECT * FROM ".$dbprefix."tournamentmatch WHERE tournament_id='".$tournamentInfo['tournament_id']."' AND (team1_id != '0' OR team2_id != '0') ORDER BY round");
while($row = $result->fetch_assoc()) {
	$arrRounds[] = $row['round'];
}

$arrRounds = array_unique($arrRounds);

foreach($arrRounds as $roundNum) {
	$roundoptions .= "<option value='".$roundNum."'>Round ".$roundNum."</option>";
}


?>


<div class='formDiv main' style='overflow: auto; border: 0px; background: none'>

	<p class='main'>
		
		Select a round below to view and manage the tournament matches.  Clicking on a player/team's name will allow you to change the their seed.

	</p>

	<b>Select Round:</b> <select id='roundSelect' class='textBox'><?php echo $roundoptions; ?></select><br><br>
	<b>Round <span id='roundNumSpan'>1</span> Matches:</b>
	<div class='dottedLine' style='width: 100%; margin-bottom: 10px; margin-top: 3px'></div>
	<div style='clear: both'></div>
	<div class='loadingSpiral' id='loadingSpiral'>
		<p align='center'>
			<img src='<?php echo $MAIN_ROOT; ?>themes/<?php echo $THEME; ?>/images/loading-spiral.gif'><br>Loading
		</p>
	</div>
	<div id='matchDiv'>
	<?php
	
		include("include/listmatches.php");
	
	?>
	</div>
</div>
<div id='changeSeedDiv' class='main' style='display: none'></div><div id='successBox' class='main' style='display: none'></div>
<script type='text/javascript'>


	$(document).ready(function() {

		$('#roundSelect').change(function() {

			$('#roundNumSpan').html($('#roundSelect').val());

			$('#matchDiv').hide();
			$('#loadingSpiral').show();
			$.post('<?php echo $MAIN_ROOT;?>members/tournaments/include/listmatches.php', { tID: <?php echo $tID; ?>, roundSelected: $('#roundSelect').val() }, function(data) {
				$('#matchDiv').html(data);
				$('#loadingSpiral').hide();
				$('#matchDiv').fadeIn(250);
			});

		});
		
	});


	<?php
	
		if($tournamentInfo['playersperteam'] == 1) {
			$dispTeamOrPlayer = "Player";			
		}
		else {
			$dispTeamOrPlayer = "Team";
		}
	
			
		echo "

			function setPlayerSeed(intTeamID) {
			
				$(document).ready(function() {
			
					$.post('".$MAIN_ROOT."members/tournaments/include/changeteamseed.php', {
						tID: '".$tournamentInfo['tournament_id']."', teamID: intTeamID },
						function(data) {
			
							$('#changeSeedDiv').html(data);
							$('#changeSeedDiv').dialog({
			
								title: 'Manage ".$dispTeamOrPlayer."s - Change Seed',
								modal: true,
								width: 400,
								show: 'scale',
								resizable: false,
								zIndex: 9999,
								buttons: {
			
									'Save': function() {
			
									$.post('".$MAIN_ROOT."members/tournaments/include/changeteamseed.php', {
										tID: '".$tournamentInfo['tournament_id']."', teamID: intTeamID, newSeed: $('#newSeedSelect').val() },
										function(data1) {
			
			
											$('#successBox').html(data1);
											$('#successBox').dialog({
			
			
												title: 'Manage ".$dispTeamOrPlayer."s - Change Seed',
												modal: true,
												width: 400,
												show: 'scale',
												resizable: false,
												zIndex: 9999,
												buttons: {
													'OK': function() {
													$('#roundSelect').change();
													$(this).dialog('close');
			
												}
												}
											});
			
										});
			
										$(this).dialog('close');
			
								},
								'Cancel': function() {
			
								$(this).dialog('close');
			
								}
			
								}
			
							});
			
						});
				});
			
			}

		";
			
			
		
	
	
	?>
	

</script>


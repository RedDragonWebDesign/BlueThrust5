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
$('#breadCrumbTitle').html(\"Set Tournament Managers\");
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$tournamentInfo['tournament_id']."'>".$consoleTitle."</a> > <b>".$tournamentInfo['name'].":</b> Set Tournament Managers\");
});
</script>
";

?>

<div class='formDiv'>
	Use the form below to add and remove tournament managers.  A tournament manager will have all privileges in managing the tournament except for deleting the tournament and adding new tournament managers.
	<p><b><u>NOTE:</u></b> In order to be a manager, the user must have access to the <a href='<?php echo $MAIN_ROOT; ?>members/console.php?cID=<?php echo $cID; ?>'>Manage Tournaments</a> console option.
	<div class='manageTournamentTeams'>
		<div class='mttLeftColumn'>
			<div class='dottedLine' style='padding-bottom: 3px'><b>Current Managers:</b></div>
			<div class='loadingSpiral' id='loadingSpiral'>
				<p align='center'>
					<img src='<?php echo $MAIN_ROOT; ?>themes/<?php echo $THEME; ?>/images/loading-spiral.gif'><br>Loading
				</p>
			</div>
			<div id='managerList'>
			<?php 
				
				define("SHOW_MANAGERLIST", true);
				include("include/managerlist.php");
			
			?>
			</div>
		</div>
		<div class='mttRightColumn'>
			<div class='dottedLine' style='padding-bottom: 3px'><b>Assign Managers:</b></div>
			<p><b>Enter Username:</b></p>
			<input type='text' class='textBox' id='newManager'>
			<p><i>- OR -</i></p>
			<p><b>Select from player list:</b></p>
			<select id='newManagerSelect' class='textBox'>
				<option value=''>Select</option>
				<?php 
				
					$tournamentConsoleCheck = new ConsoleOption($mysqli);
					$tournamentConsoleCheck->select($cID);
					$result = $mysqli->query("SELECT ".$dbprefix."tournamentplayers.member_id FROM ".$dbprefix."tournamentplayers, ".$dbprefix."members, ".$dbprefix."ranks WHERE ".$dbprefix."members.member_id = ".$dbprefix."tournamentplayers.member_id AND ".$dbprefix."members.rank_id = ".$dbprefix."ranks.rank_id AND ".$dbprefix."tournamentplayers.tournament_id = '".$tID."' AND ".$dbprefix."tournamentplayers.member_id != '0' ORDER BY ".$dbprefix."ranks.ordernum DESC"); 
					while($row = $result->fetch_assoc()) {
						$member->select($row['member_id']);	
						if($member->hasAccess($tournamentConsoleCheck)) {
							echo "<option value='".$row['member_id']."'>".$member->getMemberLink()."</option>";
						}
					}
					
					$member->select($memberInfo['member_id']);
				?>
			</select>
			<br><br>
			<p align='center'>
				<input type='button' class='submitButton' value='Add Manager' id='btnAddManager'>
				
			</p>
		</div>
	</div>
</div>
<input type='hidden' id='newManagerID'>
<?php 

	// Get auto-complete list
	$arrMembers = array();

	$result = $mysqli->query("SELECT ".$dbprefix."members.member_id, ".$dbprefix."members.username FROM ".$dbprefix."members, ".$dbprefix."ranks WHERE ".$dbprefix."members.rank_id = ".$dbprefix.".ranks.rank_id AND ".$dbprefix."members.disabled = '0' ORDER BY ".$dbprefix."ranks.ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$member->select($row['member_id']);
		
		if($member->hasAccess($tournamentConsoleCheck)) {
			$arrMembers[] = array("id" => $row['member_id'], "value" => filterText($row['username']));
		}
		
	}
	$member->select($memberInfo['member_id']);
	$arrJSONMembers = json_encode($arrMembers);
?>

<script type='text/javascript'>
	$(document).ready(function() {

		var arrMemberList = <?php echo $arrJSONMembers; ?>;
		
		$('#newManager').autocomplete({
			source: arrMemberList,
			minLength: 3,
			select: function(event, ui) {
			
				$('#newManagerID').val(ui.item.id);
				$('#newManagerSelect').val("");
				
			}
		});

		$('#newManagerSelect').change(function() {
			if($('#newManagerSelect').val() != "") {
				$('#newManagerID').val($(this).val());
				$('#newManager').val("");
			}
		});

		$('#btnAddManager').click(function() {

			$('#loadingSpiral').show();
			$('#managerList').hide();
			$.post('<?php echo $MAIN_ROOT; ?>members/tournaments/include/addmanager.php', { tournamentID: '<?php echo $tID; ?>', managerID: $('#newManagerID').val() }, function(data) {
				$('#managerList').html(data);
				$('#loadingSpiral').hide();
				$('#managerList').fadeIn(250);

				$('#newManagerID').val("");
				$('#newManager').val("");
				$('#newManagerSelect').val("");
				
			});
		});
		

	});


	function deleteManager(mID) {

		$(document).ready(function() {
			$('#loadingSpiral').show();
			$('#managerList').hide();
			$.post('<?php echo $MAIN_ROOT; ?>members/tournaments/include/deletemanager.php', { tournamentID: '<?php echo $tID; ?>', managerID: mID }, function(data) {
				$('#managerList').html(data);
				$('#loadingSpiral').hide();
				$('#managerList').fadeIn(250);
			});
		});
		
	}
</script>
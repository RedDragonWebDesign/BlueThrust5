<?php

if(!defined("SHOW_POLLLIST")) {
	
	include_once("../../../../_setup.php");
	include_once("../../../../classes/member.php");
	include_once("../../../../classes/poll.php");
	
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	$consoleObj = new ConsoleOption($mysqli);
	
	$cID = $consoleObj->findConsoleIDByName("Manage Polls");
	$consoleObj->select($cID);
	
	
	// Check Login
	if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
		$memberInfo = $member->get_info();
	}
	else {
		exit();
	}	
	
}

echo "
	<table class='formTable' style='border-spacing: 0px; margin-top: 0px'>
";

$counter = 0;
$result = $mysqli->query("SELECT * FROM ".$dbprefix."polls ORDER BY dateposted DESC");
while($row = $result->fetch_assoc()) {

	if($counter == 0) {
		$addCSS = "";
		$counter = 1;	
	}
	else {
		$addCSS = " alternateBGColor";
		$counter = 0;
	}
	
	$dispQuestion = (strlen($row['question']) > 75) ? substr($row['question'], 0, 75) : $row['question'];
	$dispQuestion = filterText($dispQuestion);	
	
	echo "	
		<tr>
			<td class='main manageList".$addCSS."' style='padding-left: 10px; width: 76%'><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&pID=".$row['poll_id']."&action=edit'>".$dispQuestion."</a></td>
			<td class='main manageList".$addCSS."' style='width: 12%' align='center'><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&pID=".$row['poll_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' class='manageListActionButton'></a></td>
			<td class='main manageList".$addCSS."' style='width: 12%' align='center'><a href='javascript:void(0)'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' class='manageListActionButton' data-deletePoll='".$row['poll_id']."'></a></td>
		</tr>
	";
	
}

echo "</table>";

if($result->num_rows == 0) {

	echo "
		<div class='shadedBox' style='margin: 20px auto; width: 40%'>
			<p class='main' align='center'>
				There are currently no polls!
			</p>
		</div>
	";
}

?>

<script type='text/javascript'>
	$(document).ready(function() {
		$('img[data-deletePoll]').click(function() {

			$('#loadingSpiral').show();
			$('#pollList').fadeOut(250);
			
			$.post('<?php echo $MAIN_ROOT; ?>members/include/polls/include/delete.php', { pollID: $(this).attr('data-deletePoll') }, function(data) {

				$('#loadingSpiral').hide();
				$('#pollList').html(data);
				$('#pollList').fadeIn(250);

			});

		});
	});
</script>


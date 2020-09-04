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

include_once($prevFolder."classes/squad.php");
$cID = $_GET['cID'];

echo "
<div id='actionMessage' class='shadedBox' style='display: none; width: 300px; margin-left: auto; margin-right: auto'></div>
<div id='loadingSpiral' class='loadingSpiral'>
	<p align='center'>
		<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
	</p>
</div>
<div id='contentDiv'>
";

include("include/invitelist.php");

echo "</div>


<script type='text/javascript'>

	function inviteClicked(intSquadInviteID, strAction) {
	
		$(document).ready(function() {
			
			$('#contentDiv').hide();
			$('#loadingSpiral').show();
			$.post('".$MAIN_ROOT."members/include/squads/include/inviteaction.php', { siID: intSquadInviteID, action: strAction }, function(data) {
				$('#contentDiv').html(data);
				$('#loadingSpiral').hide();
				$('#contentDiv').fadeIn(250);
				$('#actionMessage').show().delay(3000).fadeOut(250);
			});
		
		
		
		});
	
	}

</script>

";

?>
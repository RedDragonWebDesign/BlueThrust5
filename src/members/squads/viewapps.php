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

if(!isset($member) || !isset($squadObj) || substr($_SERVER['PHP_SELF'], -strlen("managesquad.php")) != "managesquad.php") {

	exit();
}
else {
	// This is a little repeatative, but for security.

	$memberInfo = $member->get_info();
	$consoleObj->select($cID);

	$squadObj->select($sID);


	if(!$member->hasAccess($consoleObj) || !$squadObj->memberHasAccess($memberInfo['member_id'], "acceptapps")) {

		exit();
	}
}

echo "

<script type='text/javascript'>
	$(document).ready(function() {
		$('#breadCrumbTitle').html(\"View Applications\");
		$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$squadInfo['squad_id']."'>".$consoleTitle."</a> > <b>".$squadInfo['name'].":</b> View Applications\");
	});
</script>
";



echo "
<div id='actionMessage' class='shadedBox' style='display: none; width: 300px; margin-left: auto; margin-right: auto'></div>
<div id='loadingSpiral' class='loadingSpiral'>
	<p align='center'>
		<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
	</p>
</div>
<div id='contentDiv'>
";

include("include/applist.php");

echo "</div>


<script type='text/javascript'>

	function decisionClicked(intSquadAppID, strAction) {
	
		$(document).ready(function() {
		
			$('#contentDiv').hide();
			$('#loadingSpiral').show();
			$.post('".$MAIN_ROOT."members/squads/include/appdecision.php', { saID: intSquadAppID, action: strAction, sID: '".$squadInfo['squad_id']."' }, function(data) {
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
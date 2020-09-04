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
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


echo "
<script type='text/javascript'>

function deleteRank(intRankID) {

	$(document).ready(function() {
		$('#loadingSpiral').show();
		$.post('".$MAIN_ROOT."members/include/admin/manageranks/delete.php', { rID: intRankID }, function(data) {
			$('#deleteMessage').html(data);
			
			$('#deleteDiv').dialog({
				title: 'Manage Ranks - Delete Rank',
				modal: true,
				resizeable: false,
				width: 400,
				show: 'scale',
				zIndex: 99999,
				buttons: {
					'Yes': function() {
						
						$.post('".$MAIN_ROOT."members/include/admin/manageranks/delete.php', { rID: intRankID, confirm: '1' }, function(data) {
							$(this).dialog('close');
							$('#deleteConfirm').html(data);
							
							
						});
						
					},
					'No': function() {
						$(this).dialog('close');
					}
				}
			
			});
			
			
		});
		$('#loadingSpiral').hide();
	});

}

</script>
";

include_once($prevFolder."classes/btupload.php");
$cID = $_GET['cID'];

echo "
<div class='loadingSpiral' id='loadingSpiral'>
	<p align='center'>
		<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
	</p>
</div>
<div style='display: none' id='deleteDiv'>
	<p align='center' id='deleteMessage'></p>
</div>
<div style='display: none' id='deleteConfirm'>

</div>
";

if(!isset($_GET['rID']) || $_GET['rID'] == "") {
	include("manageranks/main.php");
}
elseif($_GET['rID'] != "" && $_GET['action'] == "edit") {
	include("manageranks/edit.php");
}



?>
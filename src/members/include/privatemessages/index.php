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

include("../classes/pmfolder.php");
$cID = $_GET['cID'];

$addFolderCID = $consoleObj->findConsoleIDByName("Add PM Folder");

$pmFolderObj = new PMFolder($mysqli);
$arrFolders = $pmFolderObj->listFolders($memberInfo['member_id']);

$folderList = "";
foreach($arrFolders as $folderID => $folderName) {
	$folderList .= "<option value='".$folderID."'>".filterText($folderName)."</option>";	
}

?>



<div style='position: relative; text-align: right; padding: 0px 20px'>
		<b>&raquo;</b> <a href='<?php echo $MAIN_ROOT; ?>members/console.php?cID=<?php echo $addFolderCID; ?>'>Add New Folder</a> <b>&laquo;</b><br><br>Folder: <select id='selectFolder' class='textBox'><option value='0'>Inbox</option><option value='-1'>Sent Messages</option><option value='-2'>Trash</option><?php echo $folderList; ?></select>
</div>

<div class='formDiv' style='margin-top: 10px; border-width: 0px; background-color: transparent; padding-bottom: 0px; padding-bottom: 5px; padding-left: 10px; padding-right: 10px; height: 30px'>

	<div style='float: left; height: 30px; line-height: 30px'>
		<b>&raquo;</b> <a href='javascript:void(0)' id='deleteButton'>Delete Selected</a> <b>&laquo;</b> 
	</div>
	
	<div style='float: left; margin-left: 10px; height: 30px; line-height: 30px'>
		<b>&raquo;</b> <a href='javascript:void(0)' id='moveButton'>Move Selected</a>: <select id='folderName' class='textBox'><option value='0'>Inbox</option><option value='-1'>Sent Messages</option><option value='-2'>Trash</option><?php echo $folderList; ?></select> <b>&laquo;</b> 
	</div>

	<div style='float: right; height: 30px; line-height: 30px'>
		<b>&raquo;</b> <a href='<?php echo $MAIN_ROOT; ?>members/privatemessages/compose.php'>Compose Message</a> <b>&laquo;</b><!-- &nbsp; &raquo; <a href='<?php echo $MAIN_ROOT; ?>members/privatemessages/viewsent.php'>View Sent Messages</a> &laquo; -->
	</div>
	
</div>


<table class='formTable' style='border-spacing: 0px'>
	<tr>
		<td class='main' width="5%"><img src='<?php echo $MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png"; ?>' width='18' height='18' id='checkAllX' style='cursor: pointer'></td>
		<td class='formTitle' width="30%" id="msgFrom">From:</td>
		<td class='formTitle' width="35%" style='border-left-width: 0px'>Subject:</td>
		<td class='formTitle' width="30%" style='border-left-width: 0px'>Date Sent</td>
	</tr>	
</table>
<div id='inboxDiv'>



</div>
<div class='loadingSpiral' id='loadingSpiral'>
	<p align='center'>
		<img src='<?php echo $MAIN_ROOT; ?>themes/<?php echo $THEME; ?>/images/loading-spiral.gif'><br>Loading
	</p>
</div>

<script type='text/javascript'>

	$(document).ready(function() {

		var intCheckAll = 0;


		<?php 

			if(isset($_GET['folder']) && is_numeric($_GET['folder'])) {
				echo "$('#selectFolder').val('".$_GET['folder']."');";
			}
				
		?>
				
		

		$('#selectFolder').change(function() {

			$('#inboxDiv').hide();
			$('#loadingSpiral').show();
			$.post('<?php echo $MAIN_ROOT; ?>members/privatemessages/include/inbox.php', { folder: $(this).val() }, function(data) {

				$('#inboxDiv').html(data);
				$('#loadingSpiral').hide();
				$('#inboxDiv').fadeIn(250);

			});

			if($(this).val() == "-1") {
				$("#msgFrom").html("To:");
			}
			else {
				$("#msgFrom").html("From:");
			}
			
		});

		$('#selectFolder').change();
		
		$('#checkAllX').click(function() {


			if(intCheckAll == 0) {
				$('input:checkbox').attr('checked', true);
				intCheckAll = 1;
			}
			else {
				$('input:checkbox').attr('checked', false);
				intCheckAll = 0;
			}
			


		});

		$('#deleteButton').click(function() {


			var arrDeletePMID = [];
			
			$('input:checked').each(function() {
				arrDeletePMID.push(this.value);
			});


			arrDeletePMID = JSON.stringify(arrDeletePMID);

			$.post("<?php echo $MAIN_ROOT; ?>members/privatemessages/include/delete.php", { 'deletePMs': arrDeletePMID }, function() {


				$('#inboxDiv').hide();
				$('#loadingSpiral').show();
				
				$.post('<?php echo $MAIN_ROOT; ?>members/privatemessages/include/inbox.php', { }, function(data) {

					$('#inboxDiv').html(data);
					$('#loadingSpiral').hide();
					$('#inboxDiv').fadeIn(250);

				});
				
			});

		});


		$('#moveButton').click(function() {


			var arrMovePMID = [];
			
			$('input:checked').each(function() {
				arrMovePMID.push(this.value);
			});


			arrMovePMID = JSON.stringify(arrMovePMID);

			$.post("<?php echo $MAIN_ROOT; ?>members/privatemessages/include/move.php", { 'movePMs': arrMovePMID, newFolder: $('#folderName').val() }, function() {


				$('#inboxDiv').hide();
				$('#loadingSpiral').show();
				
				$.post("<?php echo $MAIN_ROOT; ?>members/privatemessages/include/inbox.php", { folder: $('#selectFolder').val() }, function(data) {

					$('#inboxDiv').html(data);
					$('#loadingSpiral').hide();
					$('#inboxDiv').fadeIn(250);

				});
				
			});

		});

	});


</script>

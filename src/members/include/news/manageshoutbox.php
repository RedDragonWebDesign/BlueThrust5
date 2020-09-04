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


$dispError = "";
$countErrors = 0;


echo "
	<div class='formDiv' style='background: transparent; border: 0px'>
		<b>&raquo;</b> <a href='javascript:void(0)' id='deleteButton'>Delete Selected</a> <b>&laquo;</b>
	</div>
	<table class='formTable' style='border-spacing: 0px'>
		<tr>
			<td class='main' style='width: 5%'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' width='18' height='18' id='checkAllX' style='cursor: pointer'></td>
			<td class='formTitle' style='width: 30%'>Poster:</td>
			<td class='formTitle' style='width: 35%; border-left-width: 0px'>Message:</td>
			<td class='formTitle'  style='width: 30%; border-left-width: 0px'>Date Posted</td>
		</tr>	
	</table>

	<div class='loadingSpiral' id='loadingSpiral'>
		<p align='center'>
			<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
		</p>
	</div>
	<div id='shoutboxList'>
";

define("SHOW_SHOUTBOXLIST", true);

include("include/manageshoutbox_list.php");


echo "
	</div>
<script type='text/javascript'>

	$(document).ready(function() {

		var intCheckAll = 0;
		
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


			var arrDeletePostID = [];
			
			$('input:checked').each(function() {
				arrDeletePostID.push(this.value);
			});


			arrDeletePostID = JSON.stringify(arrDeletePostID);
			$('#shoutboxList').hide();
			$('#loadingSpiral').show();
			$.post('".$MAIN_ROOT."members/include/news/include/manageshoutbox_delete.php', { 'deletePosts': arrDeletePostID }, function(data) {
				
				$('#shoutboxList').html(data);
				$('#loadingSpiral').hide();
				$('#shoutboxList').fadeIn(250);
				
			});

		});
		
	});
</script>
";



?>
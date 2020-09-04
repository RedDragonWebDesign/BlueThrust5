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

$arrShowPerPage = array(25, 50, 100);

if(!isset($_GET['page']) || !is_numeric($_GET['page']) || $_GET['page'] < 1) {
	$_GET['page'] = 1;
}

if(!isset($_GET['show']) || !in_array($_GET['show'], $arrShowPerPage)) {
	$_GET['show'] = 25;	
}


$mysqli->query("OPTIMIZE TABLE logs");

$result = $mysqli->query("SELECT * FROM ".$dbprefix."logs");
$numOfPages = ceil($result->num_rows/$_GET['show']);

if($numOfPages == 0) {
	$numOfPages = 1;	
}

if($numOfPages < $_GET['page']) {
	$_GET['page'] = $numOfPages;	
}


for($i=1; $i<=$numOfPages; $i++) {
	
	$dispSelected = "";
	if($_GET['page'] == $i) {
		$dispSelected = " selected";
	}
	
	$pageoptions .= "<option value='".$i."'".$dispSelected.">".$i."</option>";	
}


if($_GET['page'] == 1) {
	$startLimit = 0;	
}
else {
	$startLimit = ($_GET['page']-1)*$_GET['show'];	
}


$dispLinks = "";
if($_GET['page'] == 1 && $numOfPages > 1) {
	$dispLinks = "<a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&page=".($_GET['page']+1)."&show=".$_GET['show']."'>Next</a> &raquo;";	
}
elseif($_GET['page'] != 1 && $numOfPages > $_GET['page']) {
	$dispLinks = "&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&page=".($_GET['page']-1)."&show=".$_GET['show']."'>Previous</a> | <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&page=".($_GET['page']+1)."&show=".$_GET['show']."'>Next</a> &raquo;";
}
elseif($_GET['page'] != 1 && $numOfPages == $_GET['page']) {
	$dispLinks = "&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&page=".($_GET['page']-1)."&show=".$_GET['show']."'>Previous</a>";
}


foreach($arrShowPerPage as $numShowPerPage) {
	$dispSelected = "";
	if($numShowPerPage == $_GET['show']) {
		$dispSelected = " selected";	
	}
	
	$showoptions .= "<option value='".$numShowPerPage."'".$dispSelected.">".$numShowPerPage." entries per page</option>";	
}

echo "
	<div class='formDiv'>
	
		<div style='float: left'>
			<p class='main' style='padding-left: 10px'>
				Display: <select id='showselect' class='textBox'>".$showoptions."</select> <input type='button' id='showselectButton' class='submitButton' value='GO'>
			</p>
		</div>
		<div style='float: right'>
			<p class='main' align='right' style='padding-right: 10px'>
				<b>Page:</b> <select id='pageselect' class='textBox'>".$pageoptions."</select> <input type='button' id='pageselectButton' class='submitButton' value='GO'> &nbsp;&nbsp;&nbsp; ".$dispLinks." 
			</p>
		</div>
		
		<table class='formTable'>
";

$result = $mysqli->query("SELECT * FROM ".$dbprefix."logs ORDER BY logdate DESC LIMIT ".$startLimit.",".$_GET['show']);
while($row = $result->fetch_assoc()) {

	$member->select($row['member_id']);	
	$formatDate = getPreciseTime($row['logdate']);
	
	echo "
		<tr>
			<td class='formLabel'>Log ID#:</td>
			<td class='main'>".$row['log_id']."</td>
		</tr>
		<tr>
			<td class='formLabel'>Log Date:</td>
			<td class='main'>".$formatDate."</td>
		</tr>
		<tr>
			<td class='formLabel'>Member:</td>
			<td class='main'>".$member->getMemberLink()."</td>
		</tr>
		<tr>
			<td class='formLabel' valign='top'>Action:</td>
			<td class='main' valign='top'>".$row['message']."</td>
		</tr>
		<tr>
			<td colspan='2' align='center'><br><div class='dottedLine' style='width: 90%'></div><br></td>
		</tr>
	";
	
}

echo "</table>

<p align='right' style='padding-right: 10px' class='main'>
	<a href='javascript:void(0)' id='goUpLink'>^^ Go Up</a>
</p>
<div style='clear: both'></div>
</div>


<script type='text/javascript'>
	
	$(document).ready(function() {
		
		$('#pageselectButton').click(function() {
			var pageNum = $('#pageselect').val();
			window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."&page='+pageNum+'&show=".$_GET['show']."';
		});
	
		$('#showselectButton').click(function() {
			
			var showPerPage = $('#showselect').val();
			window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."&page=1&show='+showPerPage;
		
		});
	
	
		
		$('#goUpLink').click(function() {
			$('html, body').animate({ scrollTop: 0 });
		});
	
	});

</script>

";
?>
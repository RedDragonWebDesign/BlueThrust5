<?php

if (!defined("SHOW_FORUMSEARCH")) {
	exit();
}

$setFilterTopic = empty($filterTopic) ? "" : "data-topic='".$filterTopic."'";
$setFilterBoard = empty($filterBoard) ? "" : "data-board='".$filterBoard."'";

$arrSearchFilterURL = [];
if ($setFilterTopic != "") {
	$arrSearchFilterURL[] = "topic=".$filterTopic;
}

if ($setFilterBoard != "") {
	$arrSearchFilterURL[] = "filterboards[]=".$filterBoard;
}

$addToURL = addslashes(implode("&", $arrSearchFilterURL));
if ($addToURL != "") {
	$addToURL = "&".$addToURL;
}

echo "
	
		<div class='formDiv' style='border: 0px; background: none; overflow: auto'>
			<div class='largeFont' style='float: right'>
				<b>".$searchLabel.":</b> <input type='text' class='textBox' id='searchTerm'> <input type='button' class='submitButton' id='btnSearchForum' ".$setFilterTopic.$setFilterBoard." style='padding: 3px 8px' value='GO'>
				<br><span class='tinyFont'><a href='".$MAIN_ROOT."forum/search.php'>Advanced Search</a></span>
			</div>
		</div>
	
		<script type='text/javascript'>
			
			$(document).ready(function() {
			
				$('#btnSearchForum').click(function() {
				
					window.location = '".$MAIN_ROOT."forum/search.php?keyword='+$('#searchTerm').val()+'".$addToURL."';
				
				});
			
			});
		
		</script>
	";

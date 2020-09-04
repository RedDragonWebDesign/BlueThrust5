<?php

	if(!defined("SHOW_FORUMSEARCH")) {
		exit();	
	}
	
	$setFilterTopic = ($filterTopic == 0) ? "" : "data-topic='".$filterTopic."'";
	$setFilterBoard = ($filterBoard == 0) ? "" : "data-board='".$filterBoard."'";
	
	$arrSearchFilterURL = array();
	if($setFilterTopic != "") {
		$arrSearchFilterURL[] = "topic=".$filterTopic;
	}
	
	if($setFilterBoard != "") {
		$arrSearchFilterURL[] = "filterboards[]=".$filterBoard;	
	}
	
	$addToURL = addslashes(implode("&", $arrSearchFilterURL));
	if($addToURL != "") { $addToURL = "&".$addToURL; }
	
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
?>
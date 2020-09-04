<?php

	$prevFolder = "../";
	include($prevFolder."_setup.php");
	include($THEME."/_logindisplay.php");
	include("include_header.php");
	include($THEME."/_menus.php");
	
	if(isset($_POST['refreshSectionID'])) {
		dispMenu($_POST['refreshSectionID']);
		
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."tableupdates WHERE tablename LIKE '".$dbprefix."menu%' OR tablename LIKE '".$dbprefix."forum%' OR tablename = '".$dbprefix."members'");
		while($row = $result->fetch_assoc()) {
			$tableName = $row['tablename']."_update";
			$updateTime = $row['updatetime'];
			$_SESSION[$tableName] = $updateTime;
		}
	}
	else {
		$updateMenu = 0;
		
		$result = $mysqli->query("SELECT itemtype FROM ".$dbprefix."menu_item WHERE itemtype = 'forumactivity' OR itemtype = 'newestmembers'");
		$addSQL = "";
		if($result->num_rows > 0) {
			$addSQL = " OR tablename LIKE '".$dbprefix."forum%' OR tablename = '".$dbprefix."members'";
		}
	
		
		echo "SELECT * FROM ".$dbprefix."tableupdates WHERE tablename LIKE '".$dbprefix."menu%'".$addSQL."<br><br>";
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."tableupdates WHERE tablename LIKE '".$dbprefix."menu%'".$addSQL);
		while($row = $result->fetch_assoc()) {
		
			$tableName = $row['tablename']."_update";
			$updateTime = $row['updatetime'];
			
			if(!isset($_SESSION[$tableName]) || (isset($_SESSION[$tableName]) && $_SESSION[$tableName] != $updateTime)) {
				$updateMenu++;

				$_SESSION[$tableName] = $updateTime;
				
			}

		}

		
		if($updateMenu > 0) {
			echo "
			
				<script type='text/javascript'>
			
					$(document).ready(function() {
					
						";
					
					for($i=0; $i<=$menuXML->info->section->count(); $i++) {
						
						echo "
						
							$.post('".$MAIN_ROOT."themes/_refreshmenus.php', { refreshSectionID: ".$i." }, function(data) {
								$('#menuSection_".$i."').html(data);
							});
	
						";
						
					}
			
			
			echo "
					
					});
				
			
				</script>
				
			";
		
		}
		
	}
	
	
	
?>
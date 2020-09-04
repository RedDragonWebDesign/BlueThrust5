<?php
	
	if(!defined("SHOW_PROFILE_MAIN")) {
		exit();	
	}
	
	
	// SQUADS
			
	$arrSquads = $member->getSquadList();
	$squadObj = new Basic($mysqli, "squads", "squad_id");
	$dispSquads = "";
	
	foreach($arrSquads as $squadID) {
		
		$squadObj->select($squadID);
		$squadInfo = $squadObj->get_info_filtered();
		
		if($squadInfo['logourl'] != "") {
			$dispSquads .= "<a href='".$MAIN_ROOT."squads/profile.php?sID=".$squadID."'><img src='".$squadInfo['logourl']."' class='squadLogo'></a><div class='dottedLine' style='width: 90%; margin-top: 20px; margin-bottom: 20px'></div>";
		}
		else {
			$dispSquads .= "<span class='largeFont'><b><a href='".$MAIN_ROOT."squads/profile.php?sID=".$squadID."'>".$squadInfo['name']."</a></b><div class='dottedLine' style='width: 90%; margin-top: 20px; margin-bottom: 20px'></div>";
		}
	}
	
	if($dispSquads != "") {
		
		echo "
			<div class='formTitle' style='text-align: center; margin-top: 20px'>Squads</div>
			<table class='profileTable' style='border-top-width: 0px'>
				<tr>
					<td class='main' align='center'>
						<p>
							".$dispSquads."
						</p>
					</td>
				</tr>
			</table>
		";
		
		
	}

?>
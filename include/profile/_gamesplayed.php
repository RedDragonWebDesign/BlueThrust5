<?php
	if(!defined("SHOW_PROFILE_MAIN")) {
		exit();	
	}

	
	
	// GAMES PLAYED
			
			$gameObj = new Game($mysqli);
			$gameStatObj = new Basic($mysqli, "gamestats", "gamestats_id");
			$dispGamesPlayed = "";
			$arrGames = $gameObj->getGameList();
			foreach($arrGames as $gameID) {
				if($member->playsGame($gameID)) {
					$gameObj->select($gameID);
					
					$dispGameStats = "";
					$arrGameStats = $gameObj->getAssociateIDs("ORDER BY ordernum");
					foreach($arrGameStats as $gameStatID) {
						$gameStatObj->select($gameStatID);
						if($gameStatObj->get_info_filtered("hidestat") == 0) {
							
							
							if($gameStatObj->get_info_filtered("stattype") == "calculate") {
								$dispGameStats .= "<b>".$gameStatObj->get_info_filtered("name").":</b> ".$gameObj->calcStat($gameStatID, $member)."<br>";
							}
							else {
								$dispGameStats .= "<b>".$gameStatObj->get_info_filtered("name").":</b> ".$member->getGameStatValue($gameStatID)."<br>";
							}
							
						}
					}
					
					$dispGamesPlayed .= "
						<tr>
							<td class='profileLabel alternateBGColor' valign='top'>
								".$gameObj->get_info_filtered("name").":
							</td>
							<td class='main' style='padding-left: 10px' valign='top'>
								".$dispGameStats."<br>						
							</td>
						</tr>
					";
				
				}
			}
			
			
			if($dispGamesPlayed != "") {
				
				echo "

					<div class='formTitle' style='text-align: center; margin-top: 20px'>Game Statistics</div>
					<table class='profileTable' style='border-top-width: 0px'>
					".$dispGamesPlayed."</table>";

			}
			
	?>
<?php 
	if(!defined("SHOW_PROFILE_MAIN")) {
		exit();	
	}

	$gamerTagID = $member->getProfileValue("11");
	
	$dispUserbar = "";
	switch($memberInfo['maingame_id']) {
		case "12":
			$dispUserbar = "<a href='http://bf3stats.com/stats_360/".$gamerTagID."' target='_blank'><img src='http://g.bf3stats.com/360/OlyoXN4d/".$gamerTagID.".png'></a>";
			break;
		case "14":
			$dispUserbar = "<a href='http://bf4stats.com/xbox/".$gamerTagID."' target='_blank'><img src='http://g.bf4stats.com/o1TEpx5V/xbox/".$gamerTagID.".png'></a>";
			break;
		case "15":
			$dispUserbar = "<a href='http://bf4stats.com/xone/".$gamerTagID."' target='_blank'><img src='http://g.bf4stats.com/o1TEpx5V/xone/".$gamerTagID.".png'></a>";
			break;
	}
	
	
	if($gamerTagID != "Not Set" && $dispUserbar != "") {

		echo "
		
			<div class='formTitle' style='position: relative; text-align: center; margin-top: 20px'>Gamer Tag</div>
			<table class='profileTable'>
				<tr>
					<td align='center' style='padding: 20px 0px'>
						".$dispUserbar."
					</td>
				</tr>
			</table>
		
		";
		
	}

?>
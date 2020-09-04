<?php 
	if(!defined("SHOW_PROFILE_MAIN")) {
		exit();	
	}

	$minecraftUsername = $member->getGameStatValue(21);

	if($minecraftUsername != "") {

		echo "
		
			<div class='formTitle' style='position: relative; text-align: center; margin-top: 20px'>Minecraft Skin</div>
			<table class='profileTable'>
				<tr>
					<td align='center' style='padding: 20px 0px'>
						<img src='http://mcsk.in/avatar/".$minecraftUsername."'>
					</td>
				</tr>
			</table>
		
		";
		
	}

?>
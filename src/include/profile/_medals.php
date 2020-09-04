<?php

	if(!defined("SHOW_PROFILE_MAIN")) {
		exit();	
	}

	// MEDALS
			
	$arrMedals = $member->getMedalList(false, $websiteInfo['medalorder']);
	$medalObj = new Medal($mysqli);
	
	if(count($arrMedals) > 0) {
		
		foreach($arrMedals as $medalID) {
			
			$medalObj->select($medalID);
			$medalInfo = $medalObj->get_info_filtered();
			
			if($medalInfo['imagewidth'] == 0) {
				$imgInfo = getimagesize($medalObj->getLocalImageURL());
				$medalInfo['imagewidth'] = $imgInfo[0];
			}
			
			if($medalInfo['imageheight'] == 0) {
				$imgInfo = getimagesize($medalObj->getLocalImageURL());
				$medalInfo['imageheight'] = $imgInfo[1];
			}
			
			$result = $mysqli->query("SELECT * FROM ".$dbprefix."medals_members WHERE member_id = '".$memberInfo['member_id']."' AND medal_id = '".$medalInfo['medal_id']."'");
			$row = $result->fetch_assoc();
			
			$dispDateAwarded = "<b>Date Awarded:</b><br>".getPreciseTime($row['dateawarded']);
			
			$dispReason = "";
			if($row['reason'] != "") {
				$dispReason = "<br><br><b>Awarded for:</b><br>".filterText($row['reason']);	
			}
			
			$dispMedalMessage = "<b>".$medalInfo['name']."</b><br><br>".$dispDateAwarded.$dispReason;
			
			$tempArr = array("width" => $medalInfo['imagewidth'], "height" => $medalInfo['imageheight'], "url" => $medalInfo['imageurl'], "message" => $dispMedalMessage);
			$arrDispMedals[] = $tempArr;
		}
		
		
		
		
		$jsonMedals = json_encode($arrDispMedals);
		
		echo "
		<div class='formTitle' style='position: relative; text-align: center; margin-top: 20px'>Medals</div>
			<table class='profileTable' id='medalTable' style='border-top-width: 0px'>
				<tr>
					<td class='main' align='center'>
						<p>
							<div id='medalDiv'>
							
							</div>
						</p>
					</td>
				</tr>
			</table>
		
			
			<script type='text/javascript'>
				var arrMedals = ".$jsonMedals."
				var medalHTML = \"\";
				var divWidth = $('#medalTable').width();
				var countWidth = 0;
				var arrMessage = [];
				
				$(document).ready(function() {
					
					$.each(arrMedals, function(i, val) {
						
						countWidth += parseInt(val.width);
						if(countWidth > divWidth) {
							medalHTML += \"<br><br>\";
							countWidth = 0;
						}
						
						
						
						arrMessage[i] = val.message;
						//alert(arrMessage[i]);
						medalHTML += \"<img src='\"+val.url+\"' width='\"+val.width+\"' height='\"+val.height+\"' style='margin: 0px 20px' onmouseover='showToolTip(arrMessage[\"+i+\"])' onmouseout='hideToolTip()'>\";

					});
				
					$('#medalDiv').html(medalHTML);
					
				});
				
			</script>
		";
		
	}

?>
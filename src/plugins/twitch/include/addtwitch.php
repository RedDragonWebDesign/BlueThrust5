<?php

	$prevFolder = "../../../";
	include_once($prevFolder."_setup.php");
	
	// Start Page
	$consoleObj = new ConsoleOption($mysqli);
	
	$cID = $consoleObj->findConsoleIDByName("Add Social Media Icon");
	$consoleObj->select($cID);
	$consoleInfo = $consoleObj->get_info_filtered();
	
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	
	
	// Check Login
	$LOGIN_FAIL = true;
	if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
		
		$socialObj = new Social($mysqli);
		if(!$_POST['confirm'] && $socialObj->selectByMulti(array("name" => "Twitch"))) {
			
			echo "
				<div id='addTwitchInfo'></div>
				<div id='confirmAddTwitch' style='display: none'>
					<p align='center' class='main'>
						There is already a Social Media Icon named Twitch.  Are you sure you want to add a new Twitch Social Media Icon?
					</p>
				</div>
				<script type='text/javascript'>
					$(document).ready(function() {
						$('#addTwitchLoading').hide();

						$('#confirmAddTwitch').dialog({
							title: 'Confirm',
							modal: true,
							width: 400,
							zIndex: 99999,
							show: 'scale',
							resizable: false,
							buttons: {
								'Yes': function() {
	
									$.post('".MAIN_ROOT."plugins/twitch/include/addtwitch.php', { confirm: '1' }, function(data) {
										$('#addTwitchInfo').html(data);
									});
									
									$(this).dialog('close');
								},
								'Cancel': function() {
								
									$(this).dialog('close');
								}
							}
							
						});
						
					});
				</script>
			";
			
		}
		else {
			
			$saveName = "Twitch";
			if($socialObj->selectByMulti(array("name" => "Twitch"))) {

				$result = $mysqli->query("SELECT social_id FROM ".$dbprefix."social WHERE name LIKE '%Twitch%'");
				
				$totalTwitch = $result->num_rows;
				
				$saveName = "Twitch - ".($totalTwitch+1);
				
			}
			
			$arrColumns = array("name", "icon", "iconwidth", "iconheight", "url", "tooltip", "ordernum");
			$arrValues = array($saveName, "plugins/twitch/images/twitch.png", "24", "24", "http://twitch.tv/", "Enter your Twitch username", $socialObj->getHighestOrderNum()+1);
			$socialObj->addNew($arrColumns, $arrValues);
			
			$socialOptions = "";
			$result = $mysqli->query("SELECT social_id,name FROM ".$dbprefix."social ORDER BY ordernum DESC");
			while($row = $result->fetch_assoc()) {
				$socialOptions .= "<option value='".$row['social_id']."'>".$row['name']."</option>";
			}
			
			echo "
				<script type='text/javascript'>
					$(document).ready(function() {
						$('#addTwitchLoading').hide();						
						$('#twitchsocial_id').html(\"".$socialOptions."\");
					
					});
				</script>
			";
		}
		
		
	}
	else {

		echo "
			<div id='errorTwitch' style='display: none'>
				<p align='center' class='main'>
					You do not have access to Add Social Media Icons.
				</p>
			</div>
			<script type='text/javascript'>
				$(document).ready(function() {
					$('#errorTwitch').dialog({
						title: 'Confirm',
						modal: true,
						width: 400,
						zIndex: 99999,
						show: 'scale',
						buttons: {
							'Ok': function() {
								$(this).dialog('close');
							}
						}
						
					});
					
				});
			</script>
		";
		
	}
?>

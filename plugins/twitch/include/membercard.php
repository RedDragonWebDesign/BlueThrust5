<?php
	
	if(!isset($this->twitchObj->data['memberCard']['memberID'])) { exit(); }
	
	$twitchObj = $this->twitchObj;	
	
?>


<div class='twitchCardContainer'>
	<div class='twitchPreview'>
	<?php 
		
		if($twitchObj->data['memberCard']['online']) {
			
			echo "
				<div class='twitchLiveIcon'></div>
				<div class='twitchGameOverlay'><img src='".$twitchObj->getGameImageURL($twitchObj->data['memberCard']['game'])."' onmouseover=\"showToolTip('".filterText($twitchObj->data['memberCard']['game'])."')\" onmouseout=\"hideToolTip()\"></div>
				<div class='twitchViewers'>".number_format($twitchObj->data['memberCard']['viewers'])." ".pluralize("viewer", $twitchObj->data['memberCard']['viewers'])."</div>
				<a href='".MAIN_ROOT."plugins/twitch/?user=".$twitchObj->data['memberCard']['memberInfo']['username']."'><img src='".$twitchObj->data['memberCard']['rawData']['stream']['preview']['medium']."'></a>
			";
			
		}
		else {

			echo "<a href='".MAIN_ROOT."plugins/twitch/?user=".$twitchObj->data['memberCard']['memberInfo']['username']."'><img src='".MAIN_ROOT."plugins/twitch/images/offlinepreview.png'></a>";
			
		}
	
	?>
	
	</div>
	<div class='twitchChannelDescription ellipsis' title='<?php echo filterText($twitchObj->data['memberCard']['streamTitle']); ?>'><?php echo $twitchObj->data['memberCard']['streamTitle']; ?></div>
	<div class='twitchChannelDescription'><?php echo $twitchObj->data['memberCard']['memberLink']; ?> streaming as <a href='http://twitch.tv/<?php echo $twitchObj->data['memberCard']['twitchName']; ?>/profile' target='_blank'><?php echo $twitchObj->data['memberCard']['twitchName']; ?></a></div>
</div>
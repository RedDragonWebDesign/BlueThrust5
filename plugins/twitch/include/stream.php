<?php

	if(!defined("MAIN_ROOT")) { exit(); }
	
	$member = new Member($mysqli);
	$twitchObj = new Twitch($mysqli);
	$pluginObj = new btPlugin($mysqli);
	$pluginObj->selectByName("Twitch");
	
	if(!$member->select($_GET['user']) || !$twitchObj->hasTwitch($_GET['user'])) {
		echo "
			<script type='text/javascript'>window.location='".MAIN_ROOT."plugins/twitch'</script>
		";
		exit();
	}
	
	$twitchName = $twitchObj->getTwitchName($twitchObj->data['memberID']);
	
	$autoPlay = ($pluginObj->getConfigInfo("autoplay") == "1") ? "true" : "false";
	
?>


<div class='twitchPlayerContainer'>
	<object class='twitchPlayer' type='application/x-shockwave-flash' id='live_embed_player_flash' data='http://www.twitch.tv/widgets/live_embed_player.swf?channel=<?php echo $twitchName; ?>' bgcolor='#000000'>
		<param name='allowFullScreen' value='true' /><param name='wmode' value='opaque' />
		<param name='allowScriptAccess' value='always' /><param name='allowNetworking' value='all' />
		<param name='movie' value='http://www.twitch.tv/widgets/live_embed_player.swf' />
		<param name='flashvars' value='hostname=www.twitch.tv&channel=<?php echo $twitchName; ?>&auto_play=<?php echo $autoPlay; ?>&start_volume=25' />
	</object>
	
	<iframe frameborder="0" scrolling="no" src="http://twitch.tv/<?php echo $twitchName; ?>/chat?popout=" class='twitchChat'></iframe>
	<p align='right'><a href='javascript:void(0)' id='hideChatButton'>Hide Chat</a></p>
	<p align='right'><a href='<?php echo MAIN_ROOT; ?>plugins/twitch'>Go Back</a></p>
</div>



<script type='text/javascript'>

	$(document).ready(function() {

		var showChat = true;
		
		$('#hideChatButton').click(function() {

			if(showChat) {
				$("#hideChatButton").html("Show Chat");
				$(".twitchChat").hide();
				showChat = false;
			}
			else {
				$("#hideChatButton").html("Hide Chat");
				$(".twitchChat").show();
				showChat = true;
			}

		});


		<?php 
		
			if($pluginObj->getConfigInfo("autohidechat") == "1") {
				echo "$('#hideChatButton').click();";
			}
		
		?>
		
	});

</script>
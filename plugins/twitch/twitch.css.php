<?php 
	header("Content-type: text/css");
	include("../../_setup.php");
	include(BASE_DIRECTORY."themes/".$THEME."/css.php");
	
	if($arrCSSInfo['box-bg-image'] != "none") {
		
		$arrCSSInfo['box-bg-image'] = substr($arrCSSInfo['box-bg-image'], strlen("url('"));
		$arrCSSInfo['box-bg-image'] = substr($arrCSSInfo['box-bg-image'], 0, strlen($arrCSSInfo['box-bg-image'])-2);
		
	}
	
	$pluginObj = new btPlugin($mysqli);
	$pluginObj->selectByName("Twitch");
	
	$streamWidth = ($pluginObj->getConfigInfo("stream_width") != "") ? $pluginObj->getConfigInfo("stream_width") : 640;
	$streamHeight = ($pluginObj->getConfigInfo("stream_height") != "") ? $pluginObj->getConfigInfo("stream_height") : 360;
	
	$streamChatHeight = ($pluginObj->getConfigInfo("streamchat_width") != "") ? $pluginObj->getConfigInfo("streamchat_height") : 300;
	
?>
.streamPageContainer {
	position: relative;
	overflow: auto;
	margin: 0px auto;
	width: 98%;
}

.twitchCardContainer {
	max-width: 320px;
	width: 26%;
	display: inline-block;
	padding: 10px;
	border: solid <?php echo $arrCSSInfo['default-border-color']; ?> 1px;
	min-height: 200px;
	margin: 10px;
	vertical-align: top;
	text-align: left;
	background-color: <?php echo $arrCSSInfo['box-bg-color']; ?>;
	background-image:  url('../../themes/<?php echo $arrCSSInfo['box-bg-image']; ?>');
}

.twitchPreview {
	position: relative;
}

.twitchPreview img {
	width: 100%;
	height: 100%;
}

.twitchLiveIcon {
	position: absolute;
	top: 5px;
	right: 5px;
	width: 70px;
	height: 30px;
	background-image: url('images/live.png');
}

.twitchGameOverlay {
	position: absolute;
	bottom: 0px;
	right: 0px;
	border-top: solid white 2px;
	border-left: solid white 2px;
}

.twitchChannelDescription {
	font-size: 14px;
	text-align: center;
	margin-top: 15px;
	min-height: 15px;
}

.twitchViewers {
	position: absolute;
	bottom: 5px;
	left: 5px;
	background-color: rgba(0,0,0,.7);
	color: white;
	font-size: 11px;
	border-radius: 2px;
	border: solid <?php echo $arrCSSInfo['default-border-color']; ?> 1px;
	padding: 3px;
}

.twitchPlayerContainer {
	position: relative;
	text-align: center;
	margin: 10px auto;
	width: <?php echo $streamWidth; ?>px;
}

.twitchPlayer {
	position: relative;
	margin: 10px auto;
	width: <?php echo $streamWidth; ?>px;
	height: <?php echo $streamHeight; ?>px;
}

.twitchChat {
	width: <?php echo $streamWidth; ?>px;
	height: <?php echo $streamChatHeight; ?>px;
}

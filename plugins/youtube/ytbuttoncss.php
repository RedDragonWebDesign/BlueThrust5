<style>


	.ytThumbnail {
	
		float: left;
		margin-right: 10px;
		height: 48px;
		width: 48px;
	
	}

	.ytThumbnail img {
		border: 0px;
		height: 48px;
		width: 48px;
	}


	.ytInfoContainer {
		
		float: left;
	
	}
	
	.ytChannelTitle {
	
		position: relative;
		height: 25px;
	
	}
	
	.ytVideoViews {
	
		float: right;
	
		text-align: left;

	
	}
	
	.ytSubscribeButtonWrapper {
	
		width: 107px;
		height: 23px;
		position: relative;
		border: 0px;
		padding: 0px;

	
	}

	.ytSubscribeButton {
		
		width: 77px;
		height: 23px;
		background: url('<?php echo $MAIN_ROOT; ?>plugins/youtube/ytbutton.png') 0px 0px;
		position: relative;
		cursor: pointer;
		font-size: 12px;
		color: white;
		font-family: arial;
		padding-left: 30px;
		line-height: 23px;
			
		
	}
	
	.ytSubscribeButton:hover {
		
		background: url('<?php echo $MAIN_ROOT; ?>plugins/youtube/ytbutton.png') 0px -23px;
		
	}
	
	.ytBubble {
	
		position: absolute;
		right: -20px;
		top: 0px;
		padding: 0px 5px;
		height: 21px;
		border: solid #d5d5d5 1px;
		border-radius: 2px;
		background-color: white;
		color: #494949;
		font-size: 10px;
		line-height: 21px;
		
	}
	
	.ytBubbleArrow {
	
		left: -6px;
		top: 4px;
		background: url('<?php echo $MAIN_ROOT; ?>plugins/youtube/bubblearrow.png');
		position: absolute;
		width: 6px;
		height: 13px;
			
	}
	
	
	.ytProfileVideos {
	
		position: relative;
		margin-left: auto;
		margin-right: auto;
		margin-bottom: 20px;
		width: 85%;
		overflow: hidden;
		padding: 0px;
		white-space: nowrap;
	
	}
	
	.ytVideo {
		display: inline-block;
		width: 185px;
		margin-right: 15px;
		white-space: normal;
		vertical-align: top;
	}
	
	
	.videoScroller {
	
		position: relative;
		margin-left: auto;
		margin-right: auto;
		width: 85%;
		cursor: pointer;
	
	}
	
	.videoScroller .ui-slider-horizontal {
		height: 50px;
	}
	
	.videoScroller .ui-slider-handle {
		cursor: pointer;
		width: 50px;
		margin-left: -25px;
	}
	
</style>
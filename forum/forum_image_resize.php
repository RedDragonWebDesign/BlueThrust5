<?php

	if(!defined("RESIZE_FORUM_IMAGES")) {
		exit();	
	}

	// Image and Signuature Size Settings
	$setMaxImageWidthUnit = ($websiteInfo['forum_imagewidthunit'] == "%") ? "%" : "px";
	$setMaxImageWidth = ($websiteInfo['forum_imagewidth'] > 0) ? "max-width: ".$websiteInfo['forum_imagewidth'].$setMaxImageWidthUnit : "";
	
	$setMaxImageHeightUnit = ($websiteInfo['forum_imageheightunit'] == "%") ? "%" : "px";
	$setMaxImageHeight = ($websiteInfo['forum_imageheight'] > 0) ? "max-height: ".$websiteInfo['forum_imageheight'].$setMaxImageHeightUnit : "";
	
	$setMaxSigWidthUnit = ($websiteInfo['forum_sigwidthunit'] == "%") ? "%" : "px";
	$setMaxSigWidth = ($websiteInfo['forum_sigwidth'] > 0) ? "max-width: ".$websiteInfo['forum_sigwidth'].$setMaxSigWidthUnit : "";
	
	$setMaxSigHeightUnit = ($websiteInfo['forum_sigheightunit'] == "%") ? "%" : "px";
	$setMaxSigHeight = ($websiteInfo['forum_sigheight'] > 0) ? "max-height: ".$websiteInfo['forum_sigheight'].$setMaxSigHeightUnit : "";
	
	$editForumCSS = "";
	
	if($setMaxImageWidth != "" || $setMaxImageHeight != "") {
		$editForumCSS .= "
			.boardPostInfo img {
				".$setMaxImageWidth.";
				".$setMaxImageHeight.";
			}
			
			.forumPostMessageInfo img {
				".$setMaxImageWidth.";
				".$setMaxImageHeight.";
			}	
		";
	}
	
	if($setMaxSigWidth != "" || $setMaxSigHeight != "") {
		$editForumCSS .= "
			.forumSignatureContainer img {
				".$setMaxSigWidth.";
				".$setMaxSigHeight.";
			}	
		";
	}
	
	
	if($editForumCSS != "") {
		$EXTERNAL_JAVASCRIPT .= "	
			<style>
				".$editForumCSS."		
			</style>
		";
	}
	
	$EXTERNAL_JAVASCRIPT .= "
	<style>
		.forumCode {
			max-width: 600px;
		}
	</style>
	";


?>
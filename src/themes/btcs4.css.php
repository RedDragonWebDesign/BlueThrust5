<?php 
	header("Content-type: text/css");
	include("../_setup.php");
	include($THEME."/css.php");

	// Image and Signuature Size Settings
	$setMaxImageWidthUnit = ($websiteInfo['forum_imagewidthunit'] == "%") ? "%" : "px";
	$setMaxForumImageWidth = ($websiteInfo['forum_imagewidth'] > 0) ? "max-width: ".$websiteInfo['forum_imagewidth'].$setMaxImageWidthUnit.";" : "";
	
	$setMaxImageHeightUnit = ($websiteInfo['forum_imageheightunit'] == "%") ? "%" : "px";
	$setMaxForumImageHeight = ($websiteInfo['forum_imageheight'] > 0) ? "max-height: ".$websiteInfo['forum_imageheight'].$setMaxImageHeightUnit.";" : "";
	
	$setMaxSigWidthUnit = ($websiteInfo['forum_sigwidthunit'] == "%") ? "%" : "px";
	$setMaxSigWidth = ($websiteInfo['forum_sigwidth'] > 0) ? "max-width: ".$websiteInfo['forum_sigwidth'].$setMaxSigWidthUnit.";" : "";
	
	$setMaxSigHeightUnit = ($websiteInfo['forum_sigheightunit'] == "%") ? "%" : "px";
	$setMaxSigHeight = ($websiteInfo['forum_sigheight'] > 0) ? "max-height: ".$websiteInfo['forum_sigheight'].$setMaxSigHeightUnit.";" : "";
	

echo "/*
THE CSS CLASSES BELOW MUST BE IN ALL THEMES!  MODIFY THESE TO SUIT YOUR NEEDS
*/

img {
	
	border: 0px;
	
}

input[type=checkbox] {
	cursor: pointer;
}

h1,h2,h3,h4,h5,h6 {
	margin: 0px;
	padding: 0px;
}

input[type=file] {
	border: 0px;	
}

.main {
	color: ".$arrCSSInfo['font-color'].";
	font-size: ".$arrCSSInfo['default-font-size'].";
	font-family: ".$arrCSSInfo['font-family'].";
}

.ui-dialog .main {
	color: ".$arrCSSInfo['dialog-font-color'].";
	font-family: ".$arrCSSInfo['dialog-font-family'].";
}

.ellipsis {
	white-space:nowrap; 
	overflow-x: hidden;
	text-overflow: ellipsis;
}

.largeFont {
	color: ".$arrCSSInfo['font-color'].";
	font-size: ".$arrCSSInfo['large-font-size'].";
	font-family: ".$arrCSSInfo['font-family'].";
}

.tinyFont {
	color: ".$arrCSSInfo['font-color'].";
	font-size: ".$arrCSSInfo['small-font-size'].";
	font-family: ".$arrCSSInfo['font-family'].";
}

.textBox {
	font-family: ".$arrCSSInfo['font-family'].";
	font-size: ".$arrCSSInfo['default-font-size']." !important;
	border: ".$arrCSSInfo['form-component-border'].";
	padding: 3px;
	color: ".$arrCSSInfo['form-component-font-color'].";
}

.smallTextBox {
	width: ".$arrCSSInfo['small-textbox-width'].";
}

.bigTextBox {
	width: ".$arrCSSInfo['large-textbox-width'].";
}

.disabledSelectItem {
	font-weight: bold;
	color: black;	
	font-size: 12px;
}


.checkBox {
	cursor: pointer;
	color: black;
	width: 11px;
	height: 11px;
}

.submitButton {
	font-family: ".$arrCSSInfo['font-family'].";
	font-size: ".$arrCSSInfo['default-font-size']." !important;
	background-color: ".$arrCSSInfo['submit-button-bg-color'].";
	border: ".$arrCSSInfo['form-component-border'].";
	color: ".$arrCSSInfo['form-component-font-color'].";
	padding: 3px 20px;
	cursor: pointer;
}

.submitButton:hover {
	background-color: ".$arrCSSInfo['submit-button-hover-bg-color'].";
}

.shadedBox {
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	background-color: ".$arrCSSInfo['box-bg-color'].";
	background-image: ".$arrCSSInfo['box-bg-image'].";
	padding: 5px;
}


.breadCrumbTitle {
	font-size: ".$arrCSSInfo['breadcrumb-title-font-size'].";
	font-weight: ".$arrCSSInfo['breadcrumb-title-font-weight'].";
	padding-left: 10px;
	padding-top: 10px;
	position: relative;
	font-family: ".$arrCSSInfo['font-family'].";
}


.breadCrumb {
	font-size: ".$arrCSSInfo['breadcrumb-font-size'].";
	font-weight ".$arrCSSInfo['breadcrumb-font-weight'].";
	font-family: ".$arrCSSInfo['font-family'].";
	margin-top: 5px;
	margin-left: 15px;
	margin-bottom: 1px;
}

.successFont {
	color: ".$arrCSSInfo['success-font-color'].";
	font-family: ".$arrCSSInfo['font-family'].";		
}

.failedFont {
	color: ".$arrCSSInfo['fail-font-color'].";
	font-family: ".$arrCSSInfo['font-family'].";
}

.pendingFont {
	
	color: ".$arrCSSInfo['pending-font-color'].";
	font-family: ".$arrCSSInfo['font-family'].";
	
}


.consoleCategory {
	border: ".$arrCSSInfo['console-category-button-border'].";
	background: ".$arrCSSInfo['console-category-button-bg-color'].";
	padding: 5px;
	cursor: pointer;
	width: ".$arrCSSInfo['console-category-button-width'].";
	margin: 3px;
}

.consoleCategory_clicked {
	border: ".$arrCSSInfo['console-category-button-border'].";
	background: ".$arrCSSInfo['console-category-button-hover-bg-color'].";
	padding: 5px;
	cursor: pointer;
	width: ".$arrCSSInfo['console-category-button-width'].";
	margin: 3px;
}


.dashedLine {
	border-bottom: dashed ".$arrCSSInfo['default-border-color']." 1px;
}

.dottedLine {
	border-bottom: dotted ".$arrCSSInfo['default-border-color']." 1px;
}

.solidLine {
	border-bottom: solid ".$arrCSSInfo['default-border-color']." 1px;
}

.dottedBox {
	
	border: dotted ".$arrCSSInfo['default-border-color']." 1px;
	padding: 3px;
	margin: 3px;
	
}

.dashedBox {
	border: dashed ".$arrCSSInfo['default-border-color']." 1px;
	padding: 3px;
	margin: 3px;
}

.solidBox {
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	padding: 3px;
	margin: 3px;
}


.formTable {
	border: 0px;
	padding: 0px;
	width: 95%;
	margin: 15px auto 0px auto;
	border-spacing: 2px;
}

.formTable td {
	padding: 2px;
}

.formLabel {
	width: ".$arrCSSInfo['form-label-width'].";
	font-family: ".$arrCSSInfo['font-family'].";
	font-size: ".$arrCSSInfo['default-font-size'].";
	font-weight: bold;
	vertical-align: top;
	margin-top: 10px;
}

.formInput {
	display: inline-block;
	vertical-align: top;
	margin-top: 10px;
	width: auto;
}

.formInputSideText {
	vertical-align: middle !important; 
	padding-left: 3px; 
	padding-top: 3px	
}

.formSubmitButton {
	display: block; 
	position: relative; 
	margin: 20px auto; 
	margin-bottom: 0px;
}


.formTable .manageList {
	height: 24px;
}

.formTable .manageListActionButton {
	width: 24px;
	height: 24px;
}

.formTitle {
	background-color: ".$arrCSSInfo['table-title-bg-color'].";
	background-image: ".$arrCSSInfo['table-title-bg-image'].";
	font-weight: ".$arrCSSInfo['table-title-font-weight'].";
	color: ".$arrCSSInfo['table-title-font-color'].";
	font-family: ".$arrCSSInfo['default-font-family'].";
	padding: 3px;
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	font-size: ".$arrCSSInfo['table-title-font-size'].";
	height: ".$arrCSSInfo['table-title-height'].";
}

.formTable .manageList {
	height: 24px;
}

/* Box to surround forms */
.formDiv {
	font-family: ".$arrCSSInfo['default-font-family'].";
	width: 95%;
	padding: 5px; 
	margin: 20px auto;
	border: solid ".$arrCSSInfo['default-border-color']." 1px; 
	background-color: ".$arrCSSInfo['box-bg-color'].";
	background-image: ".$arrCSSInfo['box-bg-image'].";
	font-size: ".$arrCSSInfo['default-font-size'].";
}

.errorDiv {
	font-family: ".$arrCSSInfo['default-font-family'].";
	width: 90%;
	margin: 15px auto;
	padding: 10px;
	background: ".$arrCSSInfo['error-bg-color'].";
	border: ".$arrCSSInfo['error-border'].";
	font-size: ".$arrCSSInfo['default-font-size'].";
}

.errorDiv strong {
	color: ".$arrCSSInfo['error-bold-color'].";
}

.newsDiv {
	font-family: ".$arrCSSInfo['default-font-family'].";
	width: 95%;
	padding: 5px;
	position: relative;
	margin: 20px auto;
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	background-color: ".$arrCSSInfo['box-bg-color'].";
	background-image: ".$arrCSSInfo['box-bg-image'].";
}


.newsDiv .avatarImg {
	
	width: ".$arrCSSInfo['avatar-width'].";
	height: ".$arrCSSInfo['avatar-height'].";
	border: ".$arrCSSInfo['avatar-border'].";
	padding: 0px;
	top: 5px;
	left: 5px;
	
}

.newsDiv .postInfo {
	position: relative;
	left: 5px;
	top: 5px;
	font-size: ".$arrCSSInfo['default-font-size'].";
	overflow: auto;
}


.newsDiv .subjectText {
	font-size: ".$arrCSSInfo['news-subject-font-size'].";
	font-weight: ".$arrCSSInfo['news-subject-font-weight'].";
}

.newsDiv .postMessage {
	position: relative;
	font-size: ".$arrCSSInfo['default-font-size'].";
	margin-top: 10px;
	margin-bottom: 10px;
	padding-left: 10px;
	padding-right: 10px;
}


#toolTip {
	position: absolute;
	display: none;
	font-family: ".$arrCSSInfo['default-font-family'].";
	color: ".$arrCSSInfo['tooltip-font-color'].";
	font-size: ".$arrCSSInfo['small-font-size'].";
	z-Index: 99999;
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	background-color: ".$arrCSSInfo['tooltip-bg-color'].";
	padding: 5px;
	border-radius: 4px;
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	text-align: left;

}



#toolTipWidth {
	position: absolute;
	display: none;
	font-family: ".$arrCSSInfo['default-font-family'].";
	color: white;
	font-size: ".$arrCSSInfo['small-font-size'].";
	z-Index: 99999;
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	background-color: ".$arrCSSInfo['tooltip-bg-color'].";
	padding: 5px;
	border-radius: 4px;
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
}


.alternateBGColor {
	background: ".$arrCSSInfo['alternate-bg-color'].";
}

.loadingSpiral {
	display: none;
	width: 100%;
	padding-top: 25px;
	color: ".$arrCSSInfo['font-color'].";
	font-size: 10px;
	font-style: italic;
}

.loadingSpiral img {
	margin-bottom: 10px;	
}

.denyText {
	color: ".$arrCSSInfo['deny-font-color'].";
}

.allowText {
	color: ".$arrCSSInfo['allow-font-color'].";
}

.publicNewsColor {
	color: ".$arrCSSInfo['public-news-color'].";	
}

.privateNewsColor {
	color: ".$arrCSSInfo['private-news-color'].";
}

.youtubeEmbed {
	width: ".$arrCSSInfo['youtube-embed-width'].";
	height: ".$arrCSSInfo['youtube-embed-height'].";
	border: 0px;
	position: relative;
	z-index: 0;
}


.shoutBox {
	
	position: relative;
	overflow-y: auto;
	background-color: ".$arrCSSInfo['shoutbox-bg-color'].";
	background-image: ".$arrCSSInfo['shoutbox-bg-image'].";
	margin: 5px auto;
	padding: 10px;
	vertical-align: bottom;
	font-size: ".$arrCSSInfo['shoutbox-font-size'].";	
}


.shoutBoxPost {
	
	position: relative;
	margin: 5px auto;
	padding: 0px;
	
}





/* Squads Page CSS */


.squadContainer {
	position: relative;
	width: 97%;
	margin: 10px;
	padding: 0px;
}

.squadLogo {
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	width: ".$arrCSSInfo['squad-logo-width'].";
	height: ".$arrCSSInfo['squad-logo-height'].";
	margin-left: 0px;
	padding-left: auto;
}




.squadContainer .squadLeftColumn {
	float: left;
	width: 60%;
	padding: 3px;
	margin-bottom: 25px;
	text-align: center;
}


.squadContainer .squadInfoTitle {
	position: relative;
	font-weight: bold;
	font-size: ".$arrCSSInfo['large-font-size'].";
	margin: 15px auto 3px auto;
	padding: 0px;
	padding-left: 1px;
	text-align: left;
	width: 92%;
}


.squadContainer .squadNews {
	width: 100%;
	border: 0px;
	padding: 2px;
	overflow-y: auto;
	height: 300px;
}

.squadContainer .squadNews .squadNewsPost {
	width: 98%;
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	padding: 3px;
	margin-bottom: 10px;
	position: relative;
	margin-left: auto;
	margin-right: auto;
}


.squadContainer .squadNews .squadNewsPost .avatarImg {
	width: ".$arrCSSInfo['avatar-width'].";
	height: ".$arrCSSInfo['avatar-height'].";
	border: ".$arrCSSInfo['avatar-border'].";
	float: left;
}

.squadContainer .squadNews .squadNewsPost .squadNewsInfo {
	font-size: ".$arrCSSInfo['small-font-size'].";
	font-family: ".$arrCSSInfo['font-family'].";
	float: left;
	margin-left: 5px;
	top: 3px;
	margin-bottom: 10px;	
}


.squadContainer .squadNews .squadNewsPost .squadNewsSubject {
	font-size: ".$arrCSSInfo['large-font-size'].";
	font-weight: bold;
}

.squadContainer .squadLeftColumn .squadInfoBox {
	position: relative;
	font-family: ".$arrCSSInfo['font-family'].";
	border: dashed ".$arrCSSInfo['default-border-color']." 1px;
	width: 90%;
	padding: 3px;
	font-size: ".$arrCSSInfo['small-font-size'].";
	text-align: left;
	margin-left: auto;
	margin-right: auto;	
}

.squadContainer .formTable {
	width: 98%;
	margin: 0px;
}

.squadContainer .profilePic img {
	border: ".$arrCSSInfo['avatar-border'].";
	width: 100px;
	height: 133px;
}

.squadContainer .profilePic {
	width: 100px;
}

.squadContainer .squadRightColumn {
	float: right;
	width: 38%;
	text-align: center;
	padding: 3px;
	margin-bottom: 25px;
}


.squadContainer .squadRightColumn .squadInfoTitle {
	width: 82%;	
}

.squadContainer .squadRightColumn .squadInfoBox {
	position: relative;
	font-family: ".$arrCSSInfo['font-family'].";
	border: dashed ".$arrCSSInfo['default-border-color']." 1px;
	width: 80%;
	padding: 3px;
	font-size: ".$arrCSSInfo['small-font-size'].";
	text-align: left;
	margin-left: auto;
	margin-right: auto;
}

	
.squadContainer .squadNews .squadNewsPost iframe {
	width: 330px;
	height: 250px;
}



/*
 - END SQUAD SECTION
*/


.manageTournamentTeams {
	
	position: relative;
	width: 90%;
	margin: 10px;
	padding: 0px;
	margin-left: auto;
	margin-right: auto;
	overflow: auto;
	
}

.mttLeftColumn {
	
	width: 307px;
	float: left;
	padding: 5px;
	
	
}

.mttRightColumn {
	
	width: 307px;
	float: right;
	padding: 5px;
	
	
}

.mttPlayerSlot {
	
	width: 275px;
	position: relative;
	margin-top: 5px;
	padding: 10px 3px 10px 5px;
	background-image: ".$arrCSSInfo['box-bg-image'].";
	background-color: ".$arrCSSInfo['box-bg-color'].";
	border: dotted ".$arrCSSInfo['default-border-color']." 1px;
	
}

.mttDeletePlayer {
	
	width: 20px;
	height: 20px;
	background-color: ".$arrCSSInfo['alternate-bg-color'].";
	background-image: ".$arrCSSInfo['box-bg-image'].";
	padding: 2px;
	margin: 2px;
	position: absolute;
	right: 3px;
	top: 2px;
	text-align: center;
	line-height: 20px;
	font-weight: bold;
	cursor: pointer;
	
	
}

.mttDeletePlayer a {
	
	color: ".$arrCSSInfo['link-color'].";
	text-decoration: none;
}

.mttDeletePlayer a:hover {
	
	color: ".$arrCSSInfo['link-hover-color'].";
	text-decoration: none;
}


.tournamentBracket {
	position: relative;
}

.tournamentBracket a {
	color: ".$arrCSSInfo['tournament-bracket-link-color'].";	
}

.tournamentBracket a:hover {
	color: ".$arrCSSInfo['tournament-bracket-link-hover-color'].";
}

.tournamentBracket .main {
	color: ".$arrCSSInfo['tournament-bracket-font-color'].";
}

.tournamentBracket .tinyFont {
	color: ".$arrCSSInfo['tournament-bracket-font-color'].";	
}

.tournamentBracket .bracket {
	
	position: absolute;
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	padding: 3px;
	background: ".$arrCSSInfo['alternate-bg-color'].";
}

.tournamentBracket .seed {
	position: absolute;
	width: 20px;
}

.tournamentBracket .bracketConnector {
	position: absolute;
	border: solid ".$arrCSSInfo['tournament-bracket-connector-color']." 2px;
	border-left-width: 0px;
}

.tournamentBracket .bracketConnectorDash {
	
	position: absolute;
	border: 0px;
	border-top: solid ".$arrCSSInfo['tournament-bracket-connector-color']." 2px;
	width: 25px;
	
	
}


.tournamentProfileContainer {
	
	position: relative;
	width: 95%;
	margin-left: auto;
	margin-right: auto;
	margin-top: 15px;
	
}

.tournamentProfileTitle {
	
	font-size: ".$arrCSSInfo['large-font-size'].";
	font-weight: bold;
	
}

.tournamentProfileLeft {
	width: 50%;
	float: left;
	padding: 3px;
}

.tournamentProfileRight {
	width: 41%;
	float: right;
	padding: 3px;
}



.userProfileLeft {
	width: ".$arrCSSInfo['profile-left-width'].";
	float: left;
	padding: 3px;
}

.userProfileLeftBoxWidth {
	width: 100%
}


.userProfileRight {
	width: ".$arrCSSInfo['profile-right-width'].";
	float: right;
	padding: 3px;
}

.profileTable {
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	padding: 0px;
	width: 100%;
	margin: 0px;
	border-spacing: 0px;
}

.profileLabel {
	width: ".$arrCSSInfo['profile-label-width'].";
	font-family: ".$arrCSSInfo['font-family'].";
	font-size: ".$arrCSSInfo['default-font-size'].";
	font-weight: bold;
}


.notificationTable {
	width: 350px;
	height: 50px;
	padding: 3px;
	border-spacing: 0px;
	border: 0px;
}

.notificationTable .notificationIcon {
	width: 50px;
}

.notificationTable .main {
	color: ".$arrCSSInfo['notification-font-color'].";	
}


.notificationTable .notificationIMG {
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
}

.notificationTable .notificationClose {
	width: 20px;
	font-size: 10px;
}

#notificationDiv {
	position: absolute;
	top: 20px;
	right: 20px;
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	background-color: ".$arrCSSInfo['notification-bg-color'].";
	width: 350px;
	height: 80px;
	padding: 2px;
	margin: 0px;
	display: none;
	z-index: 999999999;
	color: ".$arrCSSInfo['notification-font-color'].";
}

#notificationContainer {
	display: none;
}


.eventPageContainer {
	postion: relative;
	margin-top: 20px;
	margin-left: auto;
	margin-right: auto;
	width: 97%;
}

.eventPageContainer .formTable {

	border-spacing: 0px; 
	margin-top: 0px;
	width: 99%;
	margin-left: 1px;
	
}

.eventLeftContainer {
	float: left;
	width: 40%;
	
}

.eventTitle {
	font-weight: bold;
	font-size: ".$arrCSSInfo['large-font-size'].";
	position: relative;
}


.eventRightContainer {
	float: right;
	width: 55%;
}


.eventLeftContainer .profilePic {
	width: 65px;
}

.eventLeftContainer .profilePic img {
	border: ".$arrCSSInfo['avatar-border'].";
	width: 60px;
	height: 80px;
}


.eventPageContainer .eventMessages ul {
	list-style-type: none;
	padding: 0px;
	margin: 0px 10px;
	margin-bottom: 10px;
}

.eventPageContainer .tinyFont {
	
}

.eventPageContainer .eventMessages li {
	background-color: ".$arrCSSInfo['alternate-bg-color'].";
	padding: 5px;
}

.eventPageContainer .eventMessages .profilePic {
	width: 40px;
	float: left;
}

.eventPageContainer .eventMessages .profilePic img {
	width: 40px;
	height: 53px;
	margin-bottom: 5px;
}

.eventPageContainer .eventMessages .messageDiv {
	float: left;
	margin-left: 5px;
}

.eventPageContainer .eventMessages .commentUL li {
	margin: 1px 0px;
	padding: 3px;
	border-top: dotted ".$arrCSSInfo['default-border-color']." 1px;
}

.eventPageContainer .eventMessages .textBox {
	height: 20px;
	width: 98%;
}

.eventPageContainer .eventMessages .commentUL {
	margin-bottom: 10px;
}

.codeEditor {
	 position: absolute;
     top: 0;
     right: 0;
     bottom: 0;
     left: 0;
     width: 100%;
     height: 300px;
     background-color: white;
}

#hpNewsTicker {
	width: 400px;
	overflow: hidden;
	margin-left: auto;
	margin-right: auto;
	text-align: right;
	position: relative;
}


#membersOnlyTagger {
	
	position: fixed;
	_position: absolute;
	bottom: 0px;
	left: 25px;
	z-index: 99999;
	width: 380px;
	height: 80px;
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	background-color: ".$arrCSSInfo['notification-bg-color'].";
	padding: 10px;
	font-family: ".$arrCSSInfo['font-family'].";
	color: ".$arrCSSInfo['notification-font-color']."
}

#membersOnlyTagger .taggerBottomLeft {
	position: absolute;
	left: 5px;
	bottom: 5px;
}

#membersOnlyTagger .taggerBottomRight {
	position: absolute;
	right: 5px;
	bottom: 5px;
}

/* Forum CSS */


.forumTable {
	
	width: 98%; 
	margin: 15px auto;
	padding: 0px;
	border-spacing: 0px;
}

.boardCategory {
	font-size: ".$arrCSSInfo['forum-category-font-size'].";
	font-family: ".$arrCSSInfo['font-family'].";
	font-weight: bold;
}

.boardTitles {
	background-image: ".$arrCSSInfo['forum-title-bg-image'].";
	background-color: ".$arrCSSInfo['forum-title-bg-color'].";
	font-weight: bold;
	color: ".$arrCSSInfo['forum-title-font-color'].";
	font-family: ".$arrCSSInfo['font-family'].";
	padding-left: 3px;
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	font-size: ".$arrCSSInfo['forum-title-font-size'].";
	height: 18px;
}

.forumTopicCount {
	width: 100px;
	border-left: 0px;
}

.forumLastPost {
	width: 150px;
	border-left: 0px;
}

.boardIcon {
	width: 52px;
	text-align: center;
	height: 52px;
}

.boardName {
	font-size: ".$arrCSSInfo['forum-title-font-size'].";
	font-weight: bold;
	background-color: ".$arrCSSInfo['box-bg-color'].";
	background-image: ".$arrCSSInfo['box-bg-image'].";
	padding: 5px;
}

.boardDescription {
	font-size: ".$arrCSSInfo['default-font-size'].";
	font-weight: normal;
}

.boardLastPost {
	width: 150px;
	background-color: ".$arrCSSInfo['box-bg-color'].";
	background-image: ".$arrCSSInfo['box-bg-image'].";
	padding: 5px;
	font-size: ".$arrCSSInfo['default-font-size'].";
}


.boardTopicCount {
	width: 100px;
	background-color: ".$arrCSSInfo['box-bg-color'].";
	background-image: ".$arrCSSInfo['box-bg-image'].";
	padding: 5px;
	font-size: ".$arrCSSInfo['default-font-size'].";
}

.boardNewPostBG {
	background-color: ".$arrCSSInfo['forum-new-post-bg-color'].";
	background-image: ".$arrCSSInfo['forum-new-post-bg-image'].";
}

.boardRows td {
	height: 45px;
}

.boardPosterInfo {
	width: 150px;
	background-color: ".$arrCSSInfo['box-bg-color'].";
	background-image: ".$arrCSSInfo['box-bg-image'].";
	font-size: ".$arrCSSInfo['default-font-size'].";
	height: 180px;
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	padding: 5px;
	border-bottom: 0px;
}

.boardPosterName {
	font-size: ".$arrCSSInfo['large-font-size'].";
}


.boardLastPostTitle {
	width: 140px;
	text-overflow: ellipsis;
	overflow: hidden;
	font-weight: bold;
}

.forumQuote {
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	background-color: ".$arrCSSInfo['forum-quote-bg-color'].";
	background-image: ".$arrCSSInfo['forum-quote-bg-image'].";
	padding: 5px;
	margin-bottom: 10px;
}

.forumQuote p {
	margin: 0px;
	padding: 0px;
}

.forumCode {
	font-family: 'courier new', courier;
	font-size: ".$arrCSSInfo['default-font-size'].";
	background-color: ".$arrCSSInfo['forum-code-bg-color'].";
	background-image: ".$arrCSSInfo['forum-code-bg-image'].";
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	padding: 3px;
	overflow: auto;
	white-space: nowrap;
	max-height: 100px;
	color: ".$arrCSSInfo['forum-code-font-color'].";
	margin-bottom: 10px;
	max-width: ".$arrCSSInfo['forum-code-max-width'].";
}

#forumShowAvatar img {
	margin-top: 5px;
	margin-bottom: 5px;	
}

.forumPostContainer {
	width: 98%;
	margin: 15px auto;
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	position: relative;
	display: table;
}

.forumPostPosterInfo {
	width: 18%;
	max-width: 150px;
	border-right: solid ".$arrCSSInfo['default-border-color']." 1px;
	background-color: ".$arrCSSInfo['box-bg-color'].";
	background-image: ".$arrCSSInfo['box-bg-image'].";
	padding: 5px;
	display: table-cell;
}

.forumPostNewSection {
	display: table-row;
}

.forumPostMessageInfo {
	padding: 5px;
	width: 80%;
	padding-bottom: 25px;
	overflow: auto;
	position: relative;
	display: table-cell;
}

.forumPostMessageInfo img {
	".$setMaxForumImageWidth."
	".$setMaxForumImageWidth."
}

.forumPostMessageExtras {
	width: 80%;
	padding: 5px;
	display: table-cell;
}

.forumManageLinks {
	text-align: right;
	font-weight: bold;
	padding-top: 15px;
}

.forumSignatureContainer {
	border-top: dotted ".$arrCSSInfo['default-border-color']." 1px;
	padding: 5px;
	margin-bottom: 10px;
	margin-left: auto;
	margin-right: auto;
	max-width: ".$arrCSSInfo['forum-signature-max-width'].";
	width: 95%;
	overflow: auto;
	position: relative;
}

.forumSignatureContainer img {
	".$setMaxSigWidth."
	".$setMaxSigHeight."
}


.forumSignatureContainer p {
	margin: 0px;
}

.forumAttachmentsContainer {
	
	margin-top: 20px;
	padding: 5px;
	font-size: 10px;
	
}

.pmComposeTextBox {
	padding: 5px;
	background-color: white;
	border: solid black 1px;
	width: 90%;
	position: relative;
}

.pmComposeTextBox input[type=text] {
	border: 0px;
	width: 100px;
	padding: 0px;
}

.pmComposeSelection {
	display: inline-block;
	border: solid black 1px;
	background-color: ".$arrCSSInfo['pm-compose-selection-bg-color'].";
	padding: 3px;
	float: left;
	margin-right: 5px;
	margin-bottom: 5px;
	color: black;
	line-height: 14px;
	border-radius: 2px;
	cursor: default;
}

.pmComposeSelection:hover {
	background-color: ".$arrCSSInfo['pm-compose-selection-bg-hover-color'].";
}

.pmComposeSelectionDelete {
	font-family: \"Helvetica\", helvetica, arial, sans-serif;
	font-size: 14px;
	font-weight: bold;
	text-shadow: 0 1px 1px #fff;
	padding-left: 10px;
	padding-right: 3px;
	padding-top: 1px;
	float: right;
	cursor: pointer;
}

.pmComposeSelectionDelete:hover {
	text-decoration: underline;
}

/* Home Page Image Scroller */

.hp_imgScrollContainer {
	position: relative;
	width: ".$arrCSSInfo['default-hp-image-width'].";
	height: ".$arrCSSInfo['default-hp-image-height'].";
	padding: 0px;
	margin: 0px;
	margin-left: auto;
	margin-right: auto;
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	margin-top: 20px;
}

.hp_imagescroller {
	
	position: absolute;
	top: 0px;
	left: 0px;
	display: none;
	padding: 0px;
	margin: 0px;
	border: 0px;
	
}

.hp_imagescroller img {
	border: 0px;
}

.hp_dotsContainer {
	position: relative;
	width: ".$arrCSSInfo['default-hp-image-width'].";
	text-align: center;
	height: 20px;
	margin: 0px;
	margin-left: auto;
	margin-right: auto;
	margin-top: 10px;
	padding: 0px;
}

.hp_imgScrollerDot {
	
	position: absolute;
	margin: 0px;
	padding: 0px;
	border: 0px;
	cursor: pointer;
	top: 0px;
	left: 0px;
	width: 14px;
	height: 14px;
	text-align: center;
	
}

.hp_imageScrollerOverlay {
	position: absolute;
	bottom: 0px;
	width: 100%;
	height: 20%;
	background: ".$arrCSSInfo['hp-image-overlay-bg'].";
	color: ".$arrCSSInfo['hp-image-overlay-font-color'].";
}

.hp_imageScrollerOverlayTitle {
	font-size: ".$arrCSSInfo['hp-image-overlay-title-font-size'].";
	font-weight: bold;
	position: relative;
	text-align: left;
	padding: 3px;
}

.hp_imageScrollerOverlayMessage {
	font-size: ".$arrCSSInfo['default-font-size'].";
	text-overflow: ellipsis;
	position: relative;
	text-align: left;
	padding: 3px;
	white-space:nowrap; 
	overflow-x: hidden;
}


.downloadDiv {
	font-family: verdana, sans-serif;
	width: 75%;
	padding: 5px;
	position: relative;
	margin: 20px auto;
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	background-color: ".$arrCSSInfo['box-bg-color'].";
	background-image: ".$arrCSSInfo['box-bg-image'].";	
}


.downloadDiv .avatarImg {
	
	width: ".$arrCSSInfo['avatar-width'].";
	height: ".$arrCSSInfo['avatar-height'].";
	border: ".$arrCSSInfo['avatar-border'].";
	padding: 0px;
	top: 5px;
	left: 5px;
	
}

.downloadDiv .downloadInfo {
	position: relative;
	left: 5px;
	top: 5px;
	font-size: ".$arrCSSInfo['default-font-size'].";
	overflow: auto;
}


.downloadDiv .nameText {
	font-size: ".$arrCSSInfo['download-title-font-size'].";
	font-weight: bold;
}

.downloadDiv .downloadDescription {
	position: relative;
	font-size: ".$arrCSSInfo['default-font-size'].";
	margin-top: 10px;
	margin-bottom: 10px;
	padding-left: 10px;
	padding-right: 10px;
}


/*
	Side Menus CSS
*/

.menusForumActivityWrapper {
	margin-left: 3px;
	margin-right: 3px; 
	margin-top: 5px; 
	margin-bottom: 20px;
}

.menusForumActivityItemWrapper {
	padding: 5px 5px;
}

.menusForumActivityAvatarDiv {
	float: left; 
	width: ".$arrCSSInfo['sidemenu-avatar-div-width'].";
}

.menusForumActivityAvatarDiv img {
	width: ".$arrCSSInfo['sidemenu-avatar-width']."; 
	height: ".$arrCSSInfo['sidemenu-avatar-height']."; 
	border: ".$arrCSSInfo['sidemenu-avatar-border'].";
}

.menusForumActivityTextWrapper {
	float: left; 
	width: ".$arrCSSInfo['sidemenu-forumactivity-width'].";
	font-size: ".$arrCSSInfo['default-font-size'].";
}

.menusForumActivityPostTitle {
	font-size: ".$arrCSSInfo['sidemenu-forumactivity-title-font-size']."; 
	overflow: hidden; 
	text-overflow: ellipsis;
	white-space: nowrap;
}

.menusForumActivityPoster {
	overflow: hidden; 
	text-overflow: ellipsis;
	white-space: nowrap;
}

.menusNewestMembersWrapper {
	margin-left: 3px; 
	margin-right: 3px; 
	margin-top: 5px; 
	margin-bottom: 20px;
}

.menusNewestMembersItemWrapper {
	padding: 5px 5px;	
}

.menusNewestMembersAvatarDiv {
	float: left; 
	width: ".$arrCSSInfo['sidemenu-profilepic-div-width'].";	
}

.menusNewestMembersAvatarDiv img {
	width: ".$arrCSSInfo['sidemenu-profilepic-width']."; 
	height: ".$arrCSSInfo['sidemenu-profilepic-height']."; 
	border: ".$arrCSSInfo['sidemenu-avatar-border'].";	
}

.menusNewestMembersTextWrapper {
	float: left; 
	padding-left: 5px; 
	width: ".$arrCSSInfo['sidemenu-newmembers-width'].";
}

.menusNewestMembersName {
	font-size: ".$arrCSSInfo['sidemenu-newmembers-font-size'].";
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}

.menusNewestMembersRank {
	overflow: hidden; 
	text-overflow: ellipsis;
	white-space: nowrap;
	font-size: ".$arrCSSInfo['default-font-size'].";
}


.menusImageItemWrapper {
	margin-top: 15px; 
	margin-bottom: 15px;
}

.pollMenuDiv {

	position: relative;	
	overflow: auto;
	
}

.pollMenuOptionsDiv {
	position: relative;
	overflow: auto;
	margin: 10px;
}

.pollMenuOption {
	display: inline-block;
	margin-right: 10px;
	margin-bottom: 5px;
	height: 20px;
}

.pollMenuOption input {
	margin: 0px;
	vertical-align: bottom;
}

.pollMenuOption label {
	margin-left: 10px;
	font-size: ".$arrCSSInfo['default-font-size'].";
	vertical-align: bottom;
}

.pollContainer {
	position: relative;
	margin: 0px auto;
	width: 95%;
	border: 0px;
	overflow: auto;
	margin-top: 30px;
}

.pollChart  {
	float: left;
	width: ".$arrCSSInfo['poll-chart-width'].";
	height: ".$arrCSSInfo['poll-chart-height'].";
	margin-top: 0px;
	padding: 0px;
}

.pollLegend {
	float: right;
	width: ".$arrCSSInfo['poll-legend-width'].";
}

.pollLegendSquare {
	width: ".$arrCSSInfo['poll-legend-square-width'].";
	height: ".$arrCSSInfo['poll-legend-square-height'].";
	border: ".$arrCSSInfo['poll-legend-square-border'].";
	display: inline-block;
	margin: 0px;
	margin-right: 10px;
	margin-bottom: 10px;
}

.pollLegendText {
	height: ".$arrCSSInfo['poll-legend-square-height'].";
	display: inline-block;
	margin: 0px;
	margin-bottom: 10px;
	vertical-align: top;
	line-height: ".$arrCSSInfo['poll-legend-square-height'].";
}

.pollInfoWrapper {
	position: relative;
	margin-left: auto;
	margin-right: auto;
	width: ".$arrCSSInfo['poll-info-width'].";
	font-size: ".$arrCSSInfo['poll-info-title-font-size'].";
}

.pollInfoDiv {
	font-size: ".$arrCSSInfo['default-font-size'].";
}

.pollInfoDiv .formTable {
	width: 100%; 
	margin: 0px; 
	border-spacing: 0px;
}

.pollInfoLabel {
	font-weight: bold;
	width: ".$arrCSSInfo['poll-info-label-width'].";
	background: ".$arrCSSInfo['poll-info-label-bg'].";
}

.pollProfilePic {
	width: ".$arrCSSInfo['poll-profilepic-width'].";	
}

.pollProfilePic img {
	width: ".$arrCSSInfo['poll-profilepic-width'].";
	height: ".$arrCSSInfo['poll-profilepic-height'].";
	border: ".$arrCSSInfo['avatar-border'].";
}


.pageSelectorDiv {
	float: right;
	margin: 10px 0px;
	font-size: ".$arrCSSInfo['default-font-size'].";
}

.pageSelectorDiv .pageArrowButton {
	display: inline-block;
	padding: 0px 5px;
}

.pageSelectorDiv .pageNum {
	display: inline-block;
	padding: 0px 5px;
	border: solid ".$arrCSSInfo['default-border-color']." 1px;
	background-color: ".$arrCSSInfo['box-bg-color'].";
	background-image: ".$arrCSSInfo['box-bg-image'].";
	margin: 0px 3px;
}

.pageSelectorDiv .currentPage {
	font-weight: bold;
	background: ".$arrCSSInfo['page-selector-current-page-bg'].";
}

.clocksDiv {
	position: relative;
	margin-bottom: 15px;
}

.clocksDiv p {
	margin: 0px;
	margin-top: 3px;
	font-weight: bold;
	font-size: ".$arrCSSInfo['default-font-size'].";
}
";
?>
<?php

/*
 * Bluethrust Clan Scripts v4
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */


// Config File
$prevFolder = "";

include($prevFolder."_setup.php");

$member = new Member($mysqli);

if($member->select($_GET['mID'])) {
	
	$memberInfo = $member->get_info_filtered();
	$member->addProfileView();
	$member->autoAwardMedals();
	$member->autoPromote();
	
}
else {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."';</script>");	
}



// Start Page
$PAGE_NAME = $memberInfo['username']."'s Profile - ";
$dispBreadCrumb = "";
include($prevFolder."themes/".$THEME."/_header.php");

// Check Private Profiles

if($websiteInfo['privateprofile'] == 1 && !constant("LOGGED_IN")) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");
}



$member->select($_GET['mID']);
$memberInfo = $member->get_info_filtered();

$rankObj = new Rank($mysqli);
$rankObj->select($memberInfo['rank_id']);

$rankInfo = $rankObj->get_info_filtered();

if($memberInfo['profilepic'] == "") {
	$dispProfileImage = $MAIN_ROOT."themes/".$THEME."/images/defaultprofile.png";
}
else {
	$dispProfileImage = $memberInfo['profilepic'];
}

$dispSendPM = "";
if(constant("LOGGED_IN")) {
	$dispSendPM = "
		<div class='dottedLine' style='padding: 3px 0px'></div>
		
		<p align='center'>
			<b><a href='".$MAIN_ROOT."members/privatemessages/compose.php?toID=".$memberInfo['member_id']."'>Send Private Message</a></b>
		</p>
	
	";
}

$dispSocialMedia = "";


$memberSocialInfo = $member->objSocial->getMemberSocialInfo(true);

foreach($memberSocialInfo as $socialID => $socialInfo) {
	
	$dispSocialIconDimensions = "";
	$member->objSocial->select($socialID);
	$tempSocialInfo = $member->objSocial->get_info_filtered();
	
	if($tempSocialInfo['iconwidth'] != 0) {
		$dispSocialIconDimensions .= "width: ".$tempSocialInfo['iconwidth']."px;";
	}
	
	if($tempSocialInfo['iconheight'] != 0) {
		$dispSocialIconDimensions .= "height: ".$tempSocialInfo['iconheight']."px;";
	}
	
	
	
	$dispSocialMedia .= "<a href='".$socialInfo."' target='_blank'><img class='socialMediaProfileIcons' src='".$MAIN_ROOT.$tempSocialInfo['icon']."' style='margin-right: 5px; ".$dispSocialIconDimensions."'></a>";
	
}



if($dispSocialMedia != "") {
	
	$addBar = "<div class='dottedLine' style='padding: 3px 0px; margin-bottom: 3px'></div><b>Follow Me:</b><br><p align='center'>";
	$dispSocialMedia = $addBar.$dispSocialMedia."</p>";
	
}


$dispRecruiter = "Unknown";
if($member->select($memberInfo['recruiter'])) {
	$dispRecruiter = $member->getMemberLink();
}
elseif($memberInfo['recruiter'] == 0) {
	$dispRecruiter = "Website Admin";
}

$member->select($memberInfo['member_id']);
$arrRecruits = $member->countRecruits(true);
$totalRecruits = count($arrRecruits);

foreach($arrRecruits as $recruitID) {
	
	$member->select($recruitID);
	$arrDispRecruits[] = $member->getMemberLink();
	
	
}

$dispRecruits = implode(", ", $arrDispRecruits);


$dispLastLogin = "Never Logged In";


if($memberInfo['lastlogin'] != 0) {
	$dispLastLogin = getPreciseTime($memberInfo['lastlogin']);
}

$dispLastSeen = "Never Logged In";
if($memberInfo['lastseen'] != 0) {
	$dispLastSeen = getPreciseTime($memberInfo['lastseen']);	
}

$dispLastPromotion = "Never Promoted";
if($memberInfo['lastpromotion'] != 0) {
	$dispLastPromotion = getPreciseTime($memberInfo['lastpromotion']);
}

$dispLastDemotion = "Never Demoted";
if($memberInfo['lastdemotion'] != 0) {
	$dispLastDemotion = getPreciseTime($memberInfo['lastdemotion']);
}


$dispDaysInClan = round((time()-$memberInfo['datejoined'])/86400);


if((time()-$memberInfo['lastseen']) < 600) {
	$dispOnlineStatus = "<span style='margin-top: 1px'><img src='".$MAIN_ROOT."themes/".$THEME."/images/onlinedot.png' title='Online!'></span>";
}
else {
	$dispOnlineStatus = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/offlinedot.png' title='Offline'>";
	
	if($memberInfo['loggedin'] == 1) {
		$member->select($memberInfo['member_id']);
		$member->update(array("loggedin"), array(0));	
	}
	
}

$dispRankImg = "";
if($rankInfo['imageurl'] != "") {
	$dispRankImg = "
		<div id='profilePageRankPic' class='main' style='margin-left: auto; margin-right: auto; text-align: center; margin-top: 5px; width: 150px; padding: 0px'>
			<img src='".$rankInfo['imageurl']."' width='".$rankInfo['imagewidth']."' height='".$rankInfo['imageheight']."'>
		</div>
	";
}


$dispBirthday = "";
if($memberInfo['birthday'] != 0) {
	
	$bdayDate = new DateTime();
	$bdayDate->setTimestamp($memberInfo['birthday']);
	$bdayDate->setTimezone(new DateTimeZone("UTC"));
	
	$formatBirthday = $bdayDate->format("M j, Y");
	$calcAge = floor((time()-$memberInfo['birthday'])/(31536000));
	$dispBirthday = "<br><br>
	<b>Birthday:</b><br>
	".$formatBirthday."<br><br>
	<b>Age:</b> ".$calcAge;
	
}

if($memberInfo['lastseenlink'] == "") {
	$dispLastSeenLink = "No Where";	
}
else {
	$member->select($memberInfo['member_id']);
	$dispLastSeenLink = $member->get_info("lastseenlink");	
}

$dispInactive = "";
if($memberInfo['onia'] == 1) {
	$dispInactive = "<div style='display: inline-block; vertical-align: middle' class='failedFont tinyFont'>&nbsp;&nbsp;&nbsp;INACTIVE</div>";
}

$breadcrumbObj->setTitle("<div style='display: inline-block'>".$memberInfo['username']."'s Profile</div><div id='profilePageOnlineStatus' style='display: inline-block; margin-left: 8px; vertical-align: middle'>".$dispOnlineStatus."</div>".$dispInactive);
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Members", $MAIN_ROOT."members.php");
$breadcrumbObj->addCrumb($memberInfo['username']."'s Profile");
include($prevFolder."include/breadcrumb.php");
?>

<div style='position: relative; margin-left: auto; margin-right: auto; width: 95%; margin-top: 15px'>
	<div class='main userProfileLeft'>
	
		<div id='profilePagePicImage'>
			<p align='center'><img src='<?php echo $dispProfileImage; ?>' width='150' height='200' class='solidBox' style='padding: 0px; margin: 0px auto'></p>
			<?php echo $dispRankImg; ?>
		</div>
		<div id='profilePageProfileInfoSection'>
			<div class='formTitle userProfileLeftBoxWidth' style='margin-top: 5px'>
				<b>Profile Information:</b>
			</div>
			<div class='solidBox tinyFont userProfileLeftBoxWidth' style='margin: 0px; border-top-width: 0px'>
				<b>Last Seen:</b><br>
				<?php echo $dispLastSeen; ?> on<br>
				<?php echo $dispLastSeenLink; ?>
				<br><br>
				<b>Date Recruited:</b><br>
				<?php echo getPreciseTime($memberInfo['datejoined']); ?><br><br>
				<b>Profile Views:</b> <?php echo number_format($memberInfo['profileviews']); ?>
				<?php echo $dispBirthday; ?>
				<?php echo $dispSocialMedia; ?>
				<?php echo $dispSendPM; ?>
				
			
			</div>
		</div>
	
	
	</div>


	<div class='main userProfileRight'>
		<?php 
			define("SHOW_PROFILE_MAIN", true);
			$arrSections[] = "include/profile/_main.php";
			$arrSections[] = "include/profile/_customoptions.php";
			$arrSections[] = "include/profile/_gamesplayed.php";
			$arrSections[] = "include/profile/_squads.php";
			$arrSections[] = "include/profile/_medals.php";
			
			$pluginObj = new btPlugin($mysqli);
			
			$arrPlugins = $pluginObj->getPluginPage("profile");
			
			$hooksObj->run("profile_sections");
			
			
			$arrSections[] = "";
			
			$x = 0;
			
			foreach($arrSections as $section) {
				
				foreach($arrPlugins as $pluginInfo) {

					if($pluginInfo['sortnum'] == $x) {
						include($pluginInfo['pagepath']);	
					}
					
				}

				if($section != "") {
					include($section);
				}
								
				$x++;
				
			}
			
			
			
			
		?>
	
	</div>
	
</div>

<?php

include($prevFolder."themes/".$THEME."/_footer.php");


?>
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

 
//Installer Code - Can Be Deleted After Install For Security
function deleteDir($path) {
	$i = new DirectoryIterator($path);
	
	foreach($i as $f) {
		if($f->isFile()) {
			unlink($f->getRealPath());
		}
		elseif(!$f->isDot() && $f->isDir()) {
			deleteDir($f->getRealPath());
			rmdir($f->getRealPath());
		}
	}
	
	rmdir($path);
}

if ((!file_exists("_config.php")) && (file_exists("installer/lock.txt"))) {
	die("Installer Lock File Exists. Please delete if you wish installation to continue.");
}
elseif (((!file_exists("_config.php")) && (!file_exists("installer/lock.txt"))) || ((file_exists("installer/_installrunning.txt")) && (file_get_contents("installer/_installrunning.txt") != "done"))) {
	file_put_contents("installer/_installrunning.txt", "");
	
	echo "
		<script type='text/javascript'>
			window.location = 'installer/index.php'
		</script>
	";
	
	die;
}

//End Installer Code - Can Be Deleted After Install For Security
 
// Config File
$prevFolder = "";

include("_setup.php");

// Start Page
$dispBreadCrumb = "";


include("themes/".$THEME."/_header.php");


$member = new Member($mysqli);
$rankObj = new Rank($mysqli);
$rankCatObj = new RankCategory($mysqli);


// Update members table to log out inactive members
$mysqli->query("UPDATE ".$dbprefix."members SET loggedin = '0' WHERE loggedin = '1' AND lastseen < '".(time()-600)."'");

$result = $mysqli->query("SELECT member_id FROM ".$dbprefix."members WHERE loggedin = '1' AND rank_id != '1' AND disabled != '1'");

if($result->num_rows > $websiteInfo['mostonline']) {
	$webInfoObj->multiUpdate(array("mostonline", "mostonlinedate"), array($result->num_rows, time()));	
}

$membersOnlineCount = $result->num_rows;

$arrMembersOnline = array();
$arrRankCatCount = array();
$arrDispRankCat = array();
$result2 = $mysqli->query("SELECT rankcategory_id FROM ".$dbprefix."rankcategory WHERE hidecat = '0' ORDER BY ordernum DESC");
while($row = $result2->fetch_assoc()) {
	$arrDispRankCat[$row['rankcategory_id']] = "";
	$arrRankCatCount[$row['rankcategory_id']] = 0;
}

while($row = $result->fetch_assoc()) {
	$member->select($row['member_id']);
	$arrMembersOnline[] = $member->getMemberLink();
if(constant('LOGGED_IN')) {
	
	$rankObj->select($member->get_info("rank_id"));
	$rankCat = $rankObj->get_info("rankcategory_id");
	
	$arrRankCatCount[$rankCat] += 1;
	
	}

	
}

$membersOnlineList = implode(", ", $arrMembersOnline);

// Get Page View Info
$totalPageViews = $mysqli->query("SELECT SUM(totalhits) FROM ".$dbprefix."hitcounter");
$totalPageViews = $totalPageViews->fetch_array(MYSQLI_NUM);

$totalUniqueViews = $mysqli->query("SELECT DISTINCT ipaddress FROM ".$dbprefix."hitcounter");

$totalYourViews = $mysqli->query("SELECT totalhits FROM ".$dbprefix."hitcounter WHERE ipaddress = '".$IP_ADDRESS."'");
$totalYourViews = $totalYourViews->fetch_assoc();

$result = $mysqli->query("SELECT dateposted FROM ".$dbprefix."hitcounter WHERE ipaddress = '".$IP_ADDRESS."' ORDER BY dateposted DESC LIMIT 1");
$lastVisitDate = $result->fetch_assoc();

if($result->num_rows == 1) {
	$dispLastVisitDate = "Your last visit was ".getPreciseTime($lastVisitDate['dateposted']).".";
}
else {
	$dispLastVisitDate = "This is your first visit!";	
}



// Display News Ticker

if($websiteInfo['newsticker'] != "") {
	$blnDisplayNewsTicker = true;
	$setNewsTickerStyle = "";
	if($websiteInfo['newstickercolor'] != "") {
		$setNewsTickerStyle .= "; color: ".$websiteInfo['newstickercolor'].";";	
	}
	$setMarqueeTickerStyle = "";
	if($websiteInfo['newstickersize'] != 0) {
		$setNewsTickerStyle .= "; font-size: ".$websiteInfo['newstickersize']."px; height: ".$websiteInfo['newstickersize']."px;";
		$setMarqueeTickerStyle = " style ='height: ".($websiteInfo['newstickersize']+5)."px;'";
	}
	
	if($websiteInfo['newstickerbold'] == 1) {
		$setNewsTickerStyle .= "; font-weight: bold;";
	}
	
	if($websiteInfo['newstickeritalic'] == 1) {
		$setNewsTickerStyle .= "; font-style: italic;";
	}
	
	
	echo "
	

			<div id='hpNewsTicker'>
			
				<marquee scrollamount='3'".$setMarqueeTickerStyle."><div id='tickerSpan' style='".$setNewsTickerStyle." position: relative; margin-left: auto; margin-right: auto;'>".$websiteInfo['newsticker']."</div></marquee>
			
			</div>
			
			
		";
}

echo "
	<div id='hpImageSliderWrapper' style='text-align: center; position: relative; margin-left: auto; margin-right: auto'>
			";
	
	$imageSliderObj = new ImageSlider($mysqli);
	$imageSliderObj->strDisplayStyle = $websiteInfo['hpimagetype'];
	$imageSliderObj->intDisplayWidth = $websiteInfo['hpimagewidth'];
	$imageSliderObj->intDisplayHeight = $websiteInfo['hpimageheight'];
	$imageSliderObj->strDisplayWidthUnit = $websiteInfo['hpimagewidthunit'];
	$imageSliderObj->strDisplayHeightUnit = $websiteInfo['hpimageheightunit'];
	$imageSliderObj->blnLoggedIn = constant('LOGGED_IN');
	$imageSliderObj->strTheme = $websiteInfo['theme'];
	
	$imageSliderObj->dispHomePageImage();
	
		//echo "<div id='hpImageScroller'></div>";
		
	
	echo "	
	</div>
		
	";


$dispAnnouncements = "";
$dispHPNews = "";
// Get Pinned News Posts

//<p class='main' style='font-size: 18px; font-weight: bold; padding-left: 15px'>Latest News</p>

$result = $mysqli->query("SELECT * FROM ".$dbprefix."news WHERE (newstype = '1' OR newstype = '2') AND hpsticky = '1' ORDER BY dateposted DESC");

$checkHTMLConsoleObj = new ConsoleOption($mysqli);
$htmlNewsCID = $checkHTMLConsoleObj->findConsoleIDByName("HTML in News Posts");
$checkHTMLConsoleObj->select($htmlNewsCID);
$checkHTMLAccess = "";
while($row = $result->fetch_assoc()) {
	unset($checkHTMLAccess);
	$newsObj = new News($mysqli);
	$newsInfo = filterArray($row);
	
	if($newsInfo['newstype'] == 1 || ($newsInfo['newstype'] == 2 && constant('LOGGED_IN'))) {
	
		$newsObj->select($newsInfo['news_id']);
		
		$member->select($newsInfo['member_id']);
		$posterInfo = $member->get_info_filtered();
		
		if($posterInfo['avatar'] == "") {
			$posterInfo['avatar'] = $MAIN_ROOT."themes/".$THEME."/images/defaultavatar.png";
		}
		else {
			$posterInfo['avatar'] = $MAIN_ROOT.$posterInfo['avatar'];
		}
		
		$dispNewsType = " - <span class='publicNewsColor' style='font-style: italic'>public</span>";
		if($newsInfo['newstype'] == 2) {
			$dispNewsType = " - <span class='privateNewsColor' style='font-style: italic'>private</span>";
		}
		
		$dispLastEdit = "";
		if($member->select($newsInfo['lasteditmember_id'])) {
		
			$dispLastEditTime = getPreciseTime($newsInfo['lasteditdate']);
			$dispLastEdit = "<span style='font-style: italic'>last edited by ".$member->getMemberLink()." - ".$dispLastEditTime."</span>";
		}
		
		$member->select($newsInfo['member_id']);
		
		if(!isset($checkHTMLAccess)) { $checkHTMLAccess = $member->hasAccess($checkHTMLConsoleObj); }
		
		$dispNews = ($checkHTMLAccess) ? parseBBCode($newsObj->get_info("newspost")) : nl2br(parseBBCode(filterText($newsInfo['newspost'])));
		
		
		$dispAnnouncements .= "
			<div class='newsDiv' id='newsDiv_".$newsInfo['news_id']."'>
				<div class='postInfo'>
					<div id='newsPostAvatar' style='float: left'><img src='".$posterInfo['avatar']."' class='avatarImg'></div>
					<div id='newsPostInfo' style='float: left; margin-left: 15px'>posted by ".$member->getMemberLink()." - ".getPreciseTime($newsInfo['dateposted']).$dispNewsType."<br>
					<span class='subjectText'>".$newsInfo['postsubject']."</span></div>
				</div>
				<br>
				<div class='dottedLine' style='margin-top: 5px'></div>
				<div class='postMessage'>
					".$dispNews."
				</div>
				<div class='dottedLine' style='margin-top: 5px; margin-bottom: 5px'></div>
				<div class='main' style='margin-top: 0px; margin-bottom: 10px; padding-left: 5px'>".$dispLastEdit."</div>
				<p style='padding: 0px; margin: 0px' align='right'><b><a href='".$MAIN_ROOT."news/viewpost.php?nID=".$newsInfo['news_id']."#comments'>Comments (".$newsObj->countComments().")</a></b></p>
			</div>
		
		
		";
		
	
	}
	
}


// Get Most Recent News Post

$numOfNewsPosts = ($websiteInfo['hpnews'] == -1) ? "" : " LIMIT ".$websiteInfo['hpnews'];
$result = $mysqli->query("SELECT * FROM ".$dbprefix."news WHERE newstype = '1' AND hpsticky = '0' ORDER BY dateposted DESC".$numOfNewsPosts);
$checkHTMLAccess = "";
if($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		unset($checkHTMLAccess);
		$newsObj = new News($mysqli);
		
		$newsInfo = filterArray($row);
		$newsObj->select($newsInfo['news_id']);
		
		$member->select($newsInfo['member_id']);
		$posterInfo = $member->get_info_filtered();
		
		if($posterInfo['avatar'] == "") {
			$posterInfo['avatar'] = $MAIN_ROOT."themes/".$THEME."/images/defaultavatar.png";
		}
		
	
		$dispNewsType = " - <span class='publicNewsColor' style='font-style: italic'>public</span>";
	
		$dispLastEdit = "";
		if($member->select($newsInfo['lasteditmember_id'])) {
		
			$dispLastEditTime = getPreciseTime($newsInfo['lasteditdate']);
			$dispLastEdit = "<span style='font-style: italic'>last edited by ".$member->getMemberLink()." - ".$dispLastEditTime."</span>";
		}
		
		$member->select($newsInfo['member_id']);
		
		if(!isset($checkHTMLAccess)) { $checkHTMLAccess = $member->hasAccess($checkHTMLConsoleObj); }
		
		$dispNews = ($checkHTMLAccess) ? parseBBCode($newsObj->get_info("newspost")) : nl2br(parseBBCode(filterText($newsInfo['newspost'])));
		
		
		
		$dispHPNews .= "		
			<div class='newsDiv' id='newsDiv_".$newsInfo['news_id']."'>
				<div class='postInfo'>
					<div id='newsPostAvatar' style='float: left'><img src='".$posterInfo['avatar']."' class='avatarImg'></div>
					<div id='newsPostInfo' style='float: left; margin-left: 15px'>posted by ".$member->getMemberLink()." - ".getPreciseTime($newsInfo['dateposted']).$dispNewsType."<br>
					<span class='subjectText'>".$newsInfo['postsubject']."</span></div>
				</div>
				<br>
				<div class='dottedLine' style='margin-top: 5px'></div>
				<div class='postMessage'>
					".$dispNews."
				</div>
				<div class='dottedLine' style='margin-top: 5px; margin-bottom: 5px'></div>
				<div class='main' style='margin-top: 0px; margin-bottom: 10px; padding-left: 5px'>".$dispLastEdit."</div>
				<p style='padding: 0px; margin: 0px' align='right'><b><a href='".$MAIN_ROOT."news/viewpost.php?nID=".$newsInfo['news_id']."#comments'>Comments (".$newsObj->countComments().")</a></b></p>
			</div>
		
		
		";
	}
}


if($dispAnnouncements != "") {
	
echo "<p class='main' style='font-size: 18px; font-weight: bold; padding-left: 15px'>Announcements</p>";
echo $dispAnnouncements;

	if($dispHPNews != "") {
		echo "<br>";	
	}

}


if($dispHPNews != "") {
	
echo "<p class='main' style='font-size: 18px; font-weight: bold; padding-left: 15px'>Latest News</p>";
echo $dispHPNews;	
	
}

echo "
<div>

<table class='formTable' style='width: 65%; margin-left: auto; margin-right: auto; border-spacing: 0px' align='center'>
	<tr>
		<td class='formTitle' align='center'>Members Online Statistics</td>
	</tr>
	<tr>
		<td class='main solidBox' style='border-top-width: 0px' align='center'>
			<b>Members Online:</b> ".$membersOnlineCount."<br>
			<p>
				"; if(constant('LOGGED_IN')) { echo $membersOnlineList; } else { echo "You must be logged in to view members online"; } echo"
			</p>
		</td>
	</tr>
	<tr>
		<td class='main solidBox' style='border-top-width: 0px' align='center'>
";



foreach($arrRankCatCount as $key=>$value) {

	$rankCatObj->select($key);
	$rankCatColor = $rankCatObj->get_info_filtered("color");
	$rankCatName = $rankCatObj->get_info_filtered("name");

	$arrDispRankCat[$key] = "<span style='color: ".$rankCatColor."'><b>".$value."</b> ".$rankCatName."</span>";


}

$dispRankCatCount = implode(", ", $arrDispRankCat);


echo "<p>".$dispRankCatCount."</p>
		</td>
	</tr>
</table>
<table class='formTable' style='width: 65%; margin-left: auto; margin-right: auto; border-spacing: 0px' align='center'>
	<tr>
		<td class='formTitle' align='center'>Website Statistics</td>
	<tr>
		<td class='main solidBox' style='border-top-width: 0px' align='center'>
			<p>
				<b>Most Members Online:</b> ".$websiteInfo['mostonline']." - ".getPreciseTime($websiteInfo['mostonlinedate'])."<br><br>
				<b>Total Page Views:</b> ".$totalPageViews[0]."<br>
				<b>Total Unique Views:</b> ".$totalUniqueViews->num_rows."<br><br>
				You have viewed this site <b>".$totalYourViews['totalhits']."</b> times.<br><br>
				".$dispLastVisitDate."
			</p>
		</td>
	</tr>
</table>

</div>
<br><br>

	";




echo "<!-- ".phpversion()." -->";

include("themes/".$THEME."/_footer.php");


?>
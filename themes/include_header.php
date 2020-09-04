<?php

$arrLoginInfo = array();

$taggerObj = new Basic($mysqli, "membersonlypage", "pageurl");
$siteDomain = $_SERVER['SERVER_NAME'];

if(trim($_SERVER['HTTPS']) == "" || $_SERVER['HTTPS'] == "off") {
	$dispHTTP = "http://";
}
else {
	$dispHTTP = "https://";
}


if((!isset($_COOKIE['btUsername']) || !isset($_COOKIE['btPassword'])) && isset($_SESSION['btRememberMe']) && $_SESSION['btRememberMe'] == 1 && isset($_SESSION['btUsername']) && isset($_SESSION['btPassword'])) {
	$cookieExpTime = time()+((60*60*24)*3);
	setcookie("btUsername", $_SESSION['btUsername'], $cookieExpTime, $MAIN_ROOT);
	setcookie("btPassword", $_SESSION['btPassword'], $cookieExpTime, $MAIN_ROOT);
}


$menuXML = new SimpleXMLElement(BASE_DIRECTORY."themes/".$THEME."/themeinfo.xml", NULL, true);
if(isset($_SESSION['btUsername']) && isset($_SESSION['btPassword'])) {

	$memberObj = new Member($mysqli);
	if($memberObj->select($_SESSION['btUsername'])) {

		if($memberObj->authorizeLogin($_SESSION['btPassword'])) {
			define("LOGGED_IN", true);

			$memberInfo = $memberObj->get_info();
			$memberUsername = $memberInfo['username'];
			$memberID = $memberInfo['member_id'];

			if($memberInfo['loggedin'] == 0) {
				$memberObj->update(array("loggedin"), array(1));
			}


			$actualPageNameLoc = strrpos($PAGE_NAME," - ");
			$actualPageName = substr($PAGE_NAME, 0, $actualPageNameLoc);

			if($PAGE_NAME == "") {
				$actualPageName = "Home Page";
			}


			$lastSeenLink = "<a href='".$dispHTTP.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."'>".$actualPageName."</a>";
			$arrUpdateColLastSeen = array("lastseen", "lastseenlink");
			$arrUpdateValLastSeen = array(time(), $lastSeenLink);

			if((time()-$memberInfo['lastlogin']) > 3600) {
				$arrUpdateColLastSeen[] = "lastlogin";
				$arrUpdateValLastSeen[] = time();
			}

			$memberObj->update($arrUpdateColLastSeen, $arrUpdateValLastSeen);


			$rankObj = new Rank($mysqli);
			$rankObj->select($memberInfo['rank_id']);
			$rankInfo = $rankObj->get_info();
			$memberRank = $rankInfo['name'];

			$consoleOptionObj = new ConsoleOption($mysqli);

			
			// Members Only Tagger			
			
			$dispMembersOnlyTagger = "";
			if(isset($_SESSION['btMembersOnlyTagger']) && $_SESSION['btMembersOnlyTagger'] == 1 && substr($_SERVER['PHP_SELF'], -11) != "console.php") {
				
				$pageTaggerURL = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
				
				$taggerCID = $consoleOptionObj->findConsoleIDByName("Member's Only Pages");
				
				if($taggerObj->select($pageTaggerURL, false)) {
					$pageTagStatus = "<span class='pendingFont'>Member's Only</span>";
					$dispTagOrUntag = "Untag";
				}
				else {
					$pageTagStatus = "<span class='publicNewsColor'>Public</span>";
					$dispTagOrUntag = "Tag";
				}
				
				$dispMembersOnlyTagger = "
				<div id='membersOnlyTagger'>
				
				
					<div id='membersOnlyLoadingSpiral' style='display: none'>
						<p align='center' class='main'>
							<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral2.gif'><br>Loading
						</p>
					</div>
				
				
					<div id='membersOnlyTaggerHTML'>
						<p align='center' style='margin: 0px; margin-bottom: 15px'><b>Members Only Tagger: ".$actualPageName."</b></p>
					
						<p align='center'>Current Status: ".$pageTagStatus."<br>Return to <a href='".$MAIN_ROOT."members/console.php?cID=".$taggerCID."'>Member's Only Pages</a></p>
					
						
						<div class='taggerBottomLeft'><a href='javascript:void(0)' onclick='setMembersOnlyTaggerStatus()'>Turn Off</a></div>
						<div class='taggerBottomRight'><a href='javascript:void(0)' onclick='setMembersOnlyPageStatus()'>".$dispTagOrUntag." Page</a></div>
					</div>
					
				</div>
				
				<script type='text/javascript'>
							
					function setMembersOnlyTaggerStatus() {
						$(document).ready(function() {
							$.post('".$MAIN_ROOT."members/include/admin/membersonlypagetagger.php', { setTaggerStatus: '1' }, function(data) {
								$('#membersOnlyTagger').fadeOut(250);							
							});
						});
					}
					
					function setMembersOnlyPageStatus() {
					
						$(document).ready(function() {
							$('#membersOnlyTaggerHTML').hide();
							$('#membersOnlyLoadingSpiral').show();
							$.post('".$MAIN_ROOT."members/include/admin/membersonlypagetagger.php', { setPageStatus: '1', pageName: '".filterText($actualPageName)."', tagURL: '".$pageTaggerURL."' }, function(data) {
											
								$('#membersOnlyTaggerHTML').html(data);
								$('#membersOnlyLoadingSpiral').hide();
								$('#membersOnlyTaggerHTML').fadeIn(250);
							
							});
						});
					
					}
					
				
				</script>
				
				";
			}			
			
		}

	}


}

if(!defined("LOGGED_IN")) {
	define("LOGGED_IN", false);
}


if($taggerObj->select($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'], false) && constant('LOGGED_IN') == false) {

	echo "
	
		<script type='text/javascript'>
		
			window.location='".$MAIN_ROOT."login.php';
		
		</script>
	
	";
	
	exit();
	
}


$hitCountObj = new Basic($mysqli, "hitcounter", "hit_id");
$result = $mysqli->query("SELECT * FROM ".$dbprefix."hitcounter WHERE ipaddress = '".$IP_ADDRESS."'");
if($result->num_rows > 0) {
	$hitCountRow = $result->fetch_assoc();
	$hitCountObj->select($hitCountRow['hit_id']);
	$updateHits = $hitCountObj->get_info("totalhits")+1;
	
	
	$updateColumns = array("totalhits", "pagename");
	$updateValues = array($updateHits, $PAGE_NAME);
	
	if(time() > ($hitCountObj->get_info("dateposted")+1800)) {
		$updateColumns[] = "dateposted";
		$updateValues[] = time();	
	}
	
	$hitCountObj->update($updateColumns, $updateValues);

}
else {
	$hitCountObj->addNew(array("ipaddress", "dateposted", "pagename", "totalhits"), array($IP_ADDRESS, time(), $PAGE_NAME, 1));
}


$blnDisplayNewsTicker = false;

$breadcrumbObj = new BreadCrumb();
$hooksObj->addHook("worldclock-display", "displayDefaultWorldClock");
?>
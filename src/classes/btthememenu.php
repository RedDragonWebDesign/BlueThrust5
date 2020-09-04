<?php


	class btThemeMenu {
		
		
		protected $name;
		public $MySQL;
		public $dir;
		public $memberObj;
		public $menuItemObj;
		protected $menuItemInfo;
		public $data = array();
		protected $blnLoggedIn;
		public $menuCatObj;
		protected $intMenuSection;
		protected $arrMenuItems;
		protected $intAccessType;
		public $defaultShoutboxWidth = 140;
		public $defaultShoutboxHeight = 400;
		public $arrShoutBoxIDs = array();
		
		public function __construct($dir, $sqlConnection) {
			

			$themeName = file_get_contents(BASE_DIRECTORY."themes/".$dir."/THEMENAME.txt");
			$this->name = ($themeName) ? $themeName : "Unknown";
			
			$this->dir = $dir;
			$this->MySQL = $sqlConnection;

			$this->memberObj = new Member($this->MySQL);
			
			$this->blnLoggedIn = $this->memberObj->select($_SESSION['btUsername']) && $this->memberObj->authorizeLogin($_SESSION['btPassword']);
			
			$this->menuCatObj = new MenuCategory($this->MySQL);
			$this->menuItemObj = new MenuItem($this->MySQL);
			
			
			$this->prepareAdditionalMemberInfo();
			
		}
		
		
		public function getForumActivity($amountToShow=5) {
			
			$forumObj = new ForumBoard($this->MySQL);
			$arrReturn = array();
			
			
			
			$memberInfo = $this->memberObj->get_info();
			
			$result = $this->MySQL->query("SELECT forumpost_id FROM ".$this->MySQL->get_tablePrefix()."forum_post ORDER BY dateposted DESC");
			while(count($arrReturn) < $amountToShow && $row = $result->fetch_assoc()) {
				$forumPostID = $row['forumpost_id'];
				$forumObj->objPost->select($forumPostID);
				
				$topicID = $forumObj->objPost->get_info("forumtopic_id");
				$forumObj->objTopic->select($topicID);
				$boardID = $forumObj->objTopic->get_info("forumboard_id");
				
				$forumObj->select($boardID);
				$boardAccessType = $forumObj->get_info("accesstype");
				
				if(!in_array($forumPostID, $arrReturn) && $boardAccessType == 0) {
					$arrReturn[] = $forumPostID;
				}
				elseif(!in_array($forumPostID, $arrReturn) && $boardAccessType == 1 && $this->blnLoggedIn && $forumObj->memberHasAccess($memberInfo)) {
					$arrReturn[] = $forumPostID;
				}
				
			}
			
			
			
			return $arrReturn;
			
		}
		
		public function displayForumActivity($amountToShow=5) {
		
			$forumObj = new ForumBoard($this->MySQL);
			$arrForumActivity = $this->getForumActivity($amountToShow);
			
			$member = new Member($this->MySQL);
			
			if(file_exists(BASE_DIRECTORY."themes/".$this->dir."/menus/forumactivity.php")) {
				if(!defined("FORUMACTIVITY_MENUITEM")) { define("FORUMACTIVITY_MENUITEM", true); }
				include(BASE_DIRECTORY."themes/".$this->dir."/menus/forumactivity.php");
			}
			else {
				
				echo "
					<div class='menusForumActivityWrapper'>
				";
				
				
				$altColorSwitch = 0;
				foreach($arrForumActivity as $forumPostID) {
				
					$forumObj->objPost->select($forumPostID);
					$postInfo = $forumObj->objPost->get_info_filtered();
				
					$forumObj->objTopic->select($postInfo['forumtopic_id']);
					$topicInfo = $forumObj->objTopic->get_info_filtered();
					
					$topicPostInfo = $forumObj->objPost->getTopicInfo(true);
					
					$member->select($postInfo['member_id']);
					
					
					if($altColorSwitch == 1) {
						$addCSS = "";
						$altColorSwitch = 0;
					}
					else {
						$addCSS = " alternateBGColor";
						$altColorSwitch = 1;
					}
				
					echo "
						<div class='menusForumActivityItemWrapper dottedLine ".$addCSS."'>
							<div class='menusForumActivityAvatarDiv'>
								".$member->getAvatar()."
							</div>
							<div class='menusForumActivityTextWrapper'>
								<div class='menusForumActivityPostTitle'>
									<a href='".$forumObj->objPost->getLink()."' title='".$topicPostInfo['title']."'>".$topicPostInfo['title']."</a>
								</div>
								<div class='menusForumActivityPoster'>
									by ".$member->getMemberLink()."
								</div><span class='menusForumActivityDate'>".getPreciseTime($postInfo['dateposted'])."</span>
							</div>
							<div style='clear: both'></div>
						</div>
					";
				
				}
				
				
				echo "
					</div>
				";
				

			}
			
			
		}
		
		public function getNewMembers($amountToShow=5) {
			$result = $this->MySQL->query("SELECT member_id FROM ".$this->MySQL->get_tablePrefix()."members WHERE rank_id != '1' ORDER BY datejoined DESC LIMIT 5");
			return $result->fetch_all(MYSQLI_ASSOC);				
		}
		
		public function displayNewMembers($amountToShow=5) {
			
			$member = new Member($this->MySQL);
			$rank = new Rank($this->MySQL);
			
			
			if(file_exists(BASE_DIRECTORY."themes/".$this->dir."/menus/newmembers.php")) {
				if(!defined("NEWMEMBERS_MENUITEM")) { define("NEWMEMBERS_MENUITEM", true); }
				include(BASE_DIRECTORY."themes/".$this->dir."/menus/newmembers.php");
			}
			else {
				echo "
					<div class='menusNewestMembersWrapper'>
				";
				
				$altColorSwitch = 0;
				
				$result = $this->MySQL->query("SELECT member_id FROM ".$this->MySQL->get_tablePrefix()."members WHERE rank_id != '1' ORDER BY datejoined DESC LIMIT ".$amountToShow);
				while($row = $result->fetch_assoc()) {
					$member->select($row['member_id']);
					$rank->select($member->get_info("rank_id"));
					
					if($altColorSwitch == 1) {
						$addCSS = "";
						$altColorSwitch = 0;
					}
					else {
						$addCSS = " alternateBGColor";
						$altColorSwitch = 1;
					}
				
					
					
					echo "
						<div class='menusNewestMembersItemWrapper dottedLine ".$addCSS."'>
							<div class='menusNewestMembersAvatarDiv'>
								".$member->getProfilePic()."
							</div>
							<div class='menusNewestMembersTextWrapper'>
								<div class='menusNewestMembersName'>
									".$member->getMemberLink()."
								</div>
								<div class='menusNewestMembersRank'>
									".$rank->get_info_filtered("name")."
								</div>
							</div>
							<div style='clear: both'></div>
						</div>
					";
				}
				
				
				
				echo "
					</div>
				";
			}
			
		}
		
	
		public function displayTopPlayers() {

			echo "
				<span class='menuLinks'>
					<b>&middot;</b> <a href='".MAIN_ROOT."top-players/recruiters.php'>Recruiters</a>
				</span><br>
			";
			$hpGameObj = new Game($this->MySQL);
			$arrGames = $hpGameObj->getGameList();
			foreach($arrGames as $gameID) {
				$hpGameObj->select($gameID);
				echo "
					<span class='menuLinks'>
						<b>&middot;</b> <a href='".MAIN_ROOT."top-players/game.php?gID=".$gameID."'>".$hpGameObj->get_info_filtered("name")."</a>
					</span><br>
				";
			}
			
			
		}
		
		public function displayLink() {
			$menuLinkInfo = $this->menuItemObj->objLink->get_info();
			$checkURL = parse_url($menuLinkInfo['link']);
			
			if(!isset($checkURL['scheme']) || $checkURL['scheme'] = "") {
				$menuLinkInfo['link'] = MAIN_ROOT.$menuLinkInfo['link'];
			}
			
			
			echo "
				<div class='menuLinks' style='text-align: ".$menuLinkInfo['textalign']."'>
					".$menuLinkInfo['prefix']."<a href='".$menuLinkInfo['link']."' target='".$menuLinkInfo['linktarget']."'>".$this->menuItemInfo['name']."</a>
				</div>
			";
		}
		
		
		public function displayShoutbox() {

			$shoutboxInfo = $this->menuItemObj->objShoutbox->get_info();
			
			$shoutboxInfo['width'] = ($shoutboxInfo['width'] <= 0) ? $this->defaultShoutboxWidth : $shoutboxInfo['width'];
			$blnShoutboxWidthPercent = ($shoutboxInfo['percentwidth']) ? true : false;

			$shoutboxInfo['height'] = ($shoutboxInfo['height'] <= 0) ? $this->defaultShoutboxHeight : $shoutboxInfo['height'];
			$blnShoutboxHeightPercent = ($shoutboxInfo['percentheight']) ? true : false;
			
			$shoutboxObj = new Shoutbox($this->MySQL, "news", "news_id");
			$newShoutboxID = uniqid("mainShoutBox_");
			$this->data['shoutboxIDs'][] = $newShoutboxID;
			
			$shoutboxObj->strDivID = $newShoutboxID;
			
			$this->arrShoutBoxIDs[] = $newShoutboxID;
			
			$shoutboxObj->prepareLinks($this->memberObj);
			echo $shoutboxObj->dispShoutbox($shoutboxInfo['width'], $shoutboxInfo['height'], $blnShoutboxWidthPercent, $shoutboxInfo['textboxwidth'], $blnShoutboxHeightPercent);
			echo $shoutboxObj->getShoutboxJS();
			
		}
		
		public function displayCustomFormLink() {
			$customFormObj = new CustomForm($this->MySQL);
			$menuCustomFormInfo = $this->menuItemObj->objCustomPage->get_info();
			$customFormObj->select($menuCustomFormInfo['custompage_id']);
			echo "
				<div class='menuLinks' style='text-align: ".$menuCustomFormInfo['textalign']."'>
					".$menuCustomFormInfo['prefix']."<a href='".MAIN_ROOT."customform.php?pID=".$menuCustomFormInfo['custompage_id']."' target='".$menuCustomFormInfo['linktarget']."'>".$customFormObj->get_info_filtered("name")."</a>
				</div>
			";
			
		}
		
		
		public function displayCustomPageLink() {
			$customPageObj = new Basic($this->MySQL, "custompages", "custompage_id");
			$menuCustomPageInfo = $this->menuItemObj->objCustomPage->get_info();
			$customPageObj->select($menuCustomPageInfo['custompage_id']);
			echo "
				<div class='menuLinks' style='text-align: ".$menuCustomPageInfo['textalign']."'>
					".$menuCustomPageInfo['prefix']."<a href='".MAIN_ROOT."custompage.php?pID=".$menuCustomPageInfo['custompage_id']."' target='".$menuCustomPageInfo['linktarget']."'>".$customPageObj->get_info_filtered("pagename")."</a>
				</div>
			";
				
		}
		
		
		public function displayDownloadPageLink() {
			
			$downloadCatObj = new DownloadCategory($this->MySQL);
			$menuDownloadLinkInfo = $this->menuItemObj->objCustomPage->get_info();
			$downloadCatObj->select($menuDownloadLinkInfo['custompage_id']);
			echo "
				<div class='menuLinks' style='text-align: ".$menuDownloadLinkInfo['textalign']."'>
					".$menuDownloadLinkInfo['prefix']."<a href='".MAIN_ROOT."downloads/index.php?catID=".$menuDownloadLinkInfo['custompage_id']."' target='".$menuDownloadLinkInfo['linktarget']."'>".$downloadCatObj->get_info_filtered("name")."</a>
				</div>
			";
			
		}
		
		
		public function displayCustomCodeBlock() {
			
			$menuCustomBlockInfo = $this->menuItemObj->objCustomBlock->get_info();
			
			
			$menuCustomBlockInfo['code'] = $this->replaceKeywords($menuCustomBlockInfo['code']);

			echo $menuCustomBlockInfo['code'];
			
		}
		
		
		public function displayImage() {
			
			$menuImageInfo = $this->menuItemObj->objImage->get_info();
			$checkURL = parse_url($menuItemInfo['imageurl']);
			if(!isset($checkURL['scheme']) || $checkURL['scheme'] = "") {
				$menuImageInfo['imageurl'] = MAIN_ROOT.$menuImageInfo['imageurl'];
			}
			

			$dispSetWidth = ($menuImageInfo['width'] != 0) ? "width: ".$menuImageInfo['width']."px; " : "";
			$dispSetHeight = ($menuImageInfo['height'] != 0) ? "height: ".$menuImageInfo['height']."px; " : "";
			
			echo "
				<div style='text-align: ".$menuImageInfo['imagealign'].";' class='menusImageItemWrapper'>
			";
			
			
			if($menuImageInfo['link'] != "") {
			
				$checkURL = parse_url($menuImageInfo['link']);
				if(!isset($checkURL['scheme']) || $checkURL['scheme'] = "") {
					$menuImageInfo['link'] = MAIN_ROOT.$menuImageInfo['link'];
				}
			
				echo "
					<a href='".$menuImageInfo['link']."' target='".$menuImageInfo['linktarget']."'><img src='".$menuImageInfo['imageurl']."' style='".$dispSetWidth.$dispSetHeight."' title='".$menuItemInfo['name']."'></a>
				";
			}
			else {
				echo "
					<img src='".$menuImageInfo['imageurl']."' title='".$menuItemInfo['name']."' style='".$dispSetWidth.$dispSetHeight."'>
				";
			}
			
			
			echo "
				</div>
			";
			
			
		}
		
		
		public function displayMenuItem() {
			global $hooksObj;
			$this->menuItemInfo['itemtype'] = ($this->menuItemInfo['itemtype'] == "customcode" || $this->menuItemInfo['itemtype'] == "customformat") ? "customblock" : $this->menuItemInfo['itemtype'];
		
			
			switch($this->menuItemInfo['itemtype']) {
				case "link":
					$this->menuItemObj->objLink->select($this->menuItemInfo['itemtype_id']);
					$this->displayLink();
			
					break;
				case "top-players":
					$this->displayTopPlayers();
					break;
				case "customform":
					$this->menuItemObj->objCustomPage->select($this->menuItemInfo['itemtype_id']);
					$this->displayCustomFormLink();
					break;
				case "custompage":
					$this->menuItemObj->objCustomPage->select($this->menuItemInfo['itemtype_id']);
					$this->displayCustomPageLink();
					break;
				case "downloads":
					$this->menuItemObj->objCustomPage->select($this->menuItemInfo['itemtype_id']);
					$this->displayDownloadPageLink();
					break;
				case "customblock":
					$this->menuItemObj->objCustomBlock->select($this->menuItemInfo['itemtype_id']);
					$this->displayCustomCodeBlock();
					break;
				case "image":
					$this->menuItemObj->objImage->select($this->menuItemInfo['itemtype_id']);
					$this->displayImage();
					break;
				case "shoutbox":
					$this->menuItemObj->objShoutbox->select($this->menuItemInfo['itemtype_id']);

					$this->displayShoutbox();
					$arrShoutBoxIDs = $theme->data['shoutboxIDs'];
			
					break;
				case "newestmembers":
					$this->displayNewMembers();
			
					break;
				case "forumactivity":
					$this->displayForumActivity();
			
					break;
				case "login":
					$this->displayLogin();
					break;
				case "poll":
					$pollObj = new Poll($this->MySQL);
					$pollObj->select($this->menuItemInfo['itemtype_id']);
					$pollObj->dispPollMenu($this->memberObj);
					break;
				default:
					$GLOBALS['menu_item_info'] = $this->menuItemInfo;
					$hooksObj->run("menu_item");
					unset($GLOBALS['menu_item_info']);
					break;
			}
			
		}
		
		
		public function displayMenu($menuSection) {
			$this->intMenuSection = $menuSection;
			$this->determineAccessType();
			
			
			$arrCategories = $this->menuCatObj->getCategories($menuSection, $this->intAccessType);
			
			echo "
				<div id='menuSection_".$menuSection."'>
			";
			
			foreach($arrCategories as $menuCatID) {
				$this->menuCatObj->select($menuCatID);
				$this->arrMenuItems = $this->menuItemObj->getItems($menuCatID, $this->intAccessType);
				
				$this->displayMenuCategory("top");
				
				foreach($this->arrMenuItems as $menuItemID) {
					$this->menuItemObj->select($menuItemID);
					$this->menuItemInfo = $this->menuItemObj->get_info();
					$this->displayMenuItem();
				}
				
				$this->displayMenuCategory("bottom");
				
			}
			
			
			echo "
				</div>
			";
		}
		
		
		public function displayMenuCategory($loc="top") {
			// Placeholder function - This likely needs to be overwritten
			
			// top = top portion of menu
			// bottom = bottom portion of menu
			
			$menuCatInfo = $this->menuCatObj->get_info();
			if($loc == "top") {
				echo $this->getHeaderCode($menuCatInfo);				
			}
			else {
				echo "<br>";
			}
			
			
		}
		
		
		public function displayLoggedOut() {
			// Placeholder function
			
		}
		
		public function displayLoggedIn() {
			// Placeholder function
			
		}
		
		public function getHeaderCode($info) {
			
			if($info['headertype'] == "image") {
				$returnVal = "<img src='".MAIN_ROOT.$info['headercode']."' class='menuHeaderImg'>";
			}
			else {
				$info['headercode'] = $this->replaceKeywords($info['headercode']);
				$returnVal = $info['headercode'];
			}
			
			return $returnVal;
		}
		
		
		public function displayLogin() {
			
			if($this->blnLoggedIn) {
				$this->displayLoggedIn();
			}
			else {
				$this->displayLoggedOut();
			}
			
		}
				
		
		public function prepareAdditionalMemberInfo() {
			
			if($this->blnLoggedIn) {
			
				// Private Message Info
				$consoleOptionObj = new ConsoleOption($this->MySQL);
				$pmCID = $consoleOptionObj->findConsoleIDByName("Private Messages");
			
			
				$totalPMs = $this->memberObj->countPMs();
				$totalNewPMs = $this->memberObj->countPMs(true);
			
				$alertPM = 0;
			
				if($totalNewPMs > 0) {
					$dispPMCount = "<b>(".$totalNewPMs.")</b> <img src='".MAIN_ROOT."themes/".THEME."/images/pmalert.gif'>";
					$intPMCount = $totalNewPMs;
					$alertPM = 1;
				}
				else {
					$dispPMCount = "(".$totalPMs.")";
					$intPMCount = $totalPMs;
				}
			
			
				$this->data['pmCID'] = $pmCID;
				$this->data['pmCount'] = $intPMCount;
				$this->data['pmCountDisp'] = $dispPMCount;
				$this->data['pmAlert'] = $alertPM;
			
				$this->data['pmLink'] = "<a href='".MAIN_ROOT."members/console.php?cID=".$pmCID."' id='pmLoggedInLink'>PM Inbox ".$dispPMCount."</a>";
			
				// Member Info
				$rank = new Rank($this->MySQL);
				$rank->select($this->memberObj->get_info("rank_id"));
				$this->data['memberRank'] = $rank->get_info_filtered("name");
				$this->data['memberInfo'] = $this->memberObj->get_info_filtered();
				
				
			}
			
		}
		
		
		public function replaceKeywords($value) {
			
			$arrFilter = array(
			
				"[MAIN_ROOT]" => MAIN_ROOT,
				"[MEMBER_ID]" => $this->memberObj->get_info("member_id"),
				"[MEMBERUSERNAME]" => $this->memberObj->get_info_filtered("username"),
				"[MEMBERRANK]" => $this->data['memberRank'],
				"[PMLINK]" => $this->data['pmLink']
			
			);
			
			foreach($arrFilter as $find => $replace) {
				$value = str_replace($find, $replace, $value);
			}
			
			return $value;
		}
		
		
		public function determineAccessType() {

			$this->intAccessType = ($this->blnLoggedIn) ? 1 : 2;
			return $this->intAccessType;
			
		}
		
		
		public function getPMInboxLink() {
			
			return $this->data['pmLink'];
			
		}
		
		
		public function getThemeName() {
			return $this->name;	
		}
		
	}


?>
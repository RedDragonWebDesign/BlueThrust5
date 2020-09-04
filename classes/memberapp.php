<?php


	class MemberApp extends Basic {
		
		public $objAppComponent;
		public $objSignUpForm;
		private $objMember;
		
		public function __construct($sqlConnection) {
		
			$this->MySQL = $sqlConnection;
			$this->strTableName = $this->MySQL->get_tablePrefix()."memberapps";
			$this->strTableKey = "memberapp_id";
			
			
			$this->objAppComponent = new AppComponent($sqlConnection);
			$this->objSignUpForm = new Form();
			
			$this->objMember = new Member($sqlConnection);
		}
		
		
		public function save() {
			global $IP_ADDRESS;
			$returnVal = false;
			if($this->objSignUpForm->validate()) {
			
				$newPassword = encryptPassword($_POST['password']);
				
				$arrColumns = array("username", "password", "password2", "email", "applydate", "ipaddress");
				$arrValues = array($_POST['username'], $newPassword['password'], $newPassword['salt'], $_POST['email'], time(), $IP_ADDRESS);
		
				if($this->addNew($arrColumns, $arrValues)) {
					
					$result = $this->MySQL->query("SELECT appcomponent_id FROM ".$this->MySQL->get_tablePrefix()."app_components ORDER BY ordernum DESC");
					while($row = $result->fetch_assoc()) {
						$this->objAppComponent->select($row['appcomponent_id']);
						
						$this->objAppComponent->saveAppValue($this->intTableKeyValue);
						
					}
					
					$returnVal = true;
					$this->notifyManagers();
				}
				
			}
			else {
			
				$_POST = filterArray($_POST);
				if($this->objSignUpForm->prefillValues) {
					$this->objSignUpForm->prefillPostedValues();
				}

				$_POST['submit'] = false;
			
			}
			
			return $returnVal;
		}		
		
		public function getAppValues($profileOnly=false) {
			
			$addSQL = "componenttype != 'captcha' AND componenttype != 'captchaextra'";
			if($profileOnly) {
				$addSQL = "componenttype = 'profile'";
			}
			
			$returnArr = array();
			$componentIDs = $this->MySQL->query("SELECT appcomponent_id,componenttype FROM ".$this->MySQL->get_tablePrefix()."app_components WHERE ".$addSQL." ORDER BY ordernum DESC");
			while($row = $componentIDs->fetch_assoc()) {

				$appValues = $this->MySQL->query("SELECT appvalue FROM ".$this->MySQL->get_tablePrefix()."app_values WHERE appcomponent_id = '".$row['appcomponent_id']."' AND memberapp_id = '".$this->intTableKeyValue."'");
				
				$this->objAppComponent->select($row['appcomponent_id']);
				
				$arrAppValues = array();
				$arrAppDisplayValues = array();
				while($row2 = $appValues->fetch_assoc()) {

					$arrAppValues[] = $row2['appvalue'];
					$arrAppDisplayValues[] = $this->objAppComponent->getDisplayValue($row2['appvalue']);
					
				}
				
				$returnArr[$row['appcomponent_id']] = array(
					"type" => $row['componenttype'],
					"values" => $arrAppValues,
					"display_values" => $arrAppDisplayValues
				);
			
			}
			
			return $returnArr;
			
		}
		
		
		
		public function addMember() {
			
			$rankObj = new Rank($this->MySQL);
			$rankObj->selectByOrder(2);

			$newMemRank = $rankObj->get_info("rank_id");
			
			$appInfo = $this->get_info();
			
			$arrColumns = array("username", "password", "password2", "rank_id", "email", "datejoined", "lastlogin", "lastseen");
			$arrValues = array($appInfo['username'], $appInfo['password'], $appInfo['password2'], $newMemRank, $appInfo['email'], time(), time(), time());
			
			if($this->objMember->addNew($arrColumns, $arrValues)) {
				$this->setMemberProfile();
				
				$returnVal = $this->update(array("memberadded"), array(1));
				
				$this->notifyNewMember();
				
			}
			
			return $returnVal;
		}
		
		
		public function setMemberProfile() {

			$arrProfileValues = $this->getAppValues(true);
			if(count($arrProfileValues) > 0) {
				
				foreach($arrProfileValues as $componentID => $profileItem) {	
					$this->objAppComponent->select($componentID);
					$arrSelectValueID = $this->objAppComponent->getAssociateIDs("ORDER BY componentvalue");
					$this->objAppComponent->appSelectValueObj->select($arrSelectValueID[0]);
					$componentValue = $this->objAppComponent->appSelectValueObj->get_info("componentvalue");
					switch($componentValue) {
						case "birthday":
						case "maingame":
						case "recruiter":
							$columnName = ($componentValue == "maingame") ? "maingame_id" : $componentValue;
							$this->objMember->update(array($columnName), array($profileItem['values'][0]));
							break;
						case "gamesplayed":
							$gameMemberObj = new Basic($this->MySQL, "gamesplayed_members", "gamemember_id");
							foreach($profileItem['values'] as $gameID) {
								$gameMemberObj->addNew(array("member_id", "gamesplayed_id"), array($this->objMember->get_info("member_id"), $gameID));							
							}
							
							break;
						default:
							$this->objMember->setProfileValue($componentValue, $profileItem['values'][0]);							
							break;
					}
					
				}
				
			}
			
		}
		
		
		
		public function notifyManagers() {
			
			$webInfoObj = new WebsiteInfo($this->MySQL);
			$memberObj = new Member($this->MySQL);
			$consoleObj = new ConsoleOption($this->MySQL);
			
			$webInfoObj->select(1);
			$webInfo = $webInfoObj->get_info_filtered();
			
			$viewMemberAppCID = $consoleObj->findConsoleIDByName("View Member Applications");
			$consoleObj->select($viewMemberAppCID);
			
			$arrBCC = array();
			
			$result = $this->MySQL->query("SELECT member_id FROM ".$this->MySQL->get_tablePrefix()."members WHERE disabled = '0'");
			while($row = $result->fetch_assoc()) {
				$memberObj->select($row['member_id']);
				if($memberObj->hasAccess($consoleObj)) {
					
					if($memberObj->get_info("email") != "") {
						$arrBCC[] = array(
							"email" => $memberObj->get_info("email"),
							"name" => $memberObj->get_info("username")
						);
					}
					
					$memberObj->postNotification("A new member has signed up!  Go to the <a href='".MAIN_ROOT."members/console.php?cID=".$viewMemberAppCID."'>View Member Applications</a> page to review the application.");
			
				}

			
			}
			
			$subject = $webInfo['clanname'].": New Member Application";
			$message = "A new member, ".$this->arrObjInfo['username'].", has signed up at your website: <a href='".FULL_SITE_URL."'>".$webInfo['clanname']."</a>!";
			
			
			$webInfoObj->objBTMail->sendMail("", $subject, $message, array("bcc" => $arrBCC));
			
		}
		
		public function notifyNewMember($accepted=true) {
			$webInfoObj = new WebsiteInfo($this->MySQL);
			$webInfoObj->select(1);
			$webInfo = $webInfoObj->get_info_filtered();
			
			
			$to = $this->arrObjInfo['email'];
			
			if($accepted) {
				$subject = $webInfo['clanname'].": Member Application Accepted";
				$message = "You have been accepted to become a full member of ".$webInfo['clanname']."!  Go to <a href='".FULL_SITE_URL."'>".FULL_SITE_URL."</a> to log in to your account.";
			}
			else {
				$subject = $webInfo['clanname'].": Member Application Declined";
				$message = "Your application to become a member of ".$webInfo['clanname']." has been declined.  You may try signing up again by going to <a href='".FULL_SITE_URL."'>".FULL_SITE_URL."</a>.";
			}
			
			$webInfoObj->objBTMail->sendMail($to, $subject, $message);
		}
		
		public function getNewMemberInfo() {
		
			return $this->objMember->get_info_filtered();
		
		}
		
		public function setRecruiter($memberID) {
			$returnVal = false;
			if(is_numeric($memberID)) {
				$returnVal = $this->objMember->update(array("recruiter"), array($memberID));
			}
			
			return $returnVal;
		}
		
		
		public function delete() {
			$returnVal = false;
			if($this->intTableKeyValue != "") {
			
				$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."app_values WHERE memberapp_id = '".$this->intTableKeyValue."'");
				$returnVal = parent::delete();	
			
			}
			
			return $returnVal;
		}
		
	}


?>
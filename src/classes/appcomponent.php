<?php

	class AppComponent extends BasicOrder {
		
		public $appSelectValueObj;
		public $arrSelectValues;
		public $profileOptionObj;
		public $objAppValue;
		public $defaultCounter = 0;
		public $intMemberAppID;
		
		public function __construct($sqlConnection) {
		
			$this->MySQL = $sqlConnection;
			$this->strTableName = $this->MySQL->get_tablePrefix()."app_components";
			$this->strTableKey = "appcomponent_id";
			
			$this->strAssociateKeyName = "appselectvalue_id";
			$this->strAssociateTableName = $this->MySQL->get_tablePrefix()."app_selectvalues";
			
			$this->appSelectValueObj = new Basic($sqlConnection, "app_selectvalues", "appselectvalue_id");
		
			$this->profileOptionObj = new ProfileOption($sqlConnection);
			$this->objAppValue = new Basic($sqlConnection, "app_values", "appvalue_id");
			
		}
		
		
		public function select($intIDNum, $numericIDOnly = true) {

			$returnVal = parent::select($intIDNum, $numericIDOnly);

			if($returnVal) {

				$this->arrSelectValues = $this->getAssociateIDs("ORDER BY componentvalue");
				
			}
			
			
			return $returnVal;
			
		}
		
		public function getDefaultInputCode() {
			$i=$this->defaultCounter;
			$arrComponents = array(
				"generalinfo" => array(
					"type" => "section",
					"options" => array("section_title" => "General Information"),
					"sortorder" => $i++
				),
				"username" => array(
					"type" => "text",
					"attributes" => array("class" => "formInput textBox"),
					"sortorder" => $i++,
					"display_name" => "Username",
					"validate" => array("NOT_BLANK", "appCheckUsername")
				),
				"password" => array(
					"type" => "password",
					"attributes" => array("class" => "formInput textBox"),
					"sortorder" => $i++,
					"display_name" => "Password",
					"validate" => array("NOT_BLANK", array("name" => "GREATER_THAN", "value" => 5), array("name" => "EQUALS_VALUE", "value" => $_POST['password2'], "customMessage" => "Your passwords did not match."))
				),
				"password2" => array(
					"type" => "password",
					"attributes" => array("class" => "formInput textBox"),
					"sortorder" => $i++,
					"display_name" => "Re-type Password"
				),
				"email" => array(
					"type" => "text",
					"attributes" => array("class" => "formInput textBox"),
					"sortorder" => $i++,
					"display_name" => "E-mail",
					"validate" => array("NOT_BLANK", "appCheckEmail", "appCheckIP", "appCheckCaptchas")
				)	
			);
			
			$this->defaultCounter = $i;
			
			return $arrComponents;
		}
		
		
		public function getBirthdayInputCode() {
			
			$maxYear = date("Y")-8;
			$maxDate = "new Date(".$maxYear.",12,31)";
			
			$arrComponent = array(
				"display_name" => $this->get_info_filtered("name"),
				"type" => "datepicker",
				"attributes" => array("style" => "cursor: pointer", "id" => "jsBirthday_".$this->intTableKeyValue, "class" => "textBox formInput"),
				"options" => array("changeMonth" => "true", 
								   "changeYear" => "true", 
								   "dateFormate" => "M d, yy", 
								   "minDate" => "new Date(50, 1, 1)", 
								   "maxDate" => $maxDate, 
								   "yearRange" => "1950:".$maxYear, 
								   "altField" => "realBirthday_".$this->intTableKeyValue),
				"validate" => array("NUMBER_ONLY"),
				"value" => mktime(0,0,0,12,31,$maxYear)*1000
			);
			
			return $arrComponent;
		}
		
		public function getGamesplayedInputCode() {
			
			$gameObj = new Game($this->MySQL);
			$arrGames = $gameObj->getGameList();
			$arrSelectOptions = array();
			foreach($arrGames as $gameID) {
				$gameObj->select($gameID);
				$arrSelectOptions[$gameID] = $gameObj->get_info_filtered("name");
				
			}

			return $this->getMultiSelectInputCode($arrSelectOptions);
		}
		
		
		public function getSelectOptionArray() {
		
			$arrSelectOptions = array();
			if($this->arrObjInfo['componenttype'] != "profile") {
				
				$arrSelectOptionIDs = $this->getAssociateIDs("ORDER BY componentvalue");
				foreach($arrSelectOptionIDs as $selectOptionID) {
					$this->appSelectValueObj->select($selectOptionID);
					$componentValue = $this->appSelectValueObj->get_info_filtered("componentvalue");
					$arrSelectOptions[$componentValue] = $componentValue;
				}
				
			}
			
			return $arrSelectOptions;
		}
		
		public function getMultiSelectInputCode($customSelectOptions=array()) {
			
			if(count($customSelectOptions) > 0) {
				$arrSelectOptions = $customSelectOptions;	
			}
			else {
				$arrSelectOptions = $this->getSelectOptionArray();				
			}
			
			$arrComponent = array(
				"type" => "checkbox",
				"display_name" => $this->get_info_filtered("name"),
				"attributes" => array("class" => "formInput textBox"),
				"options" => $arrSelectOptions,
				"validate" => array("RESTRICT_TO_OPTIONS")
			);
			
			
			return $arrComponent;
		}
		
		public function getSelectInputCode($customSelectOptions=array()) {
			if(count($customSelectOptions) > 0) {
				$arrSelectOptions = $customSelectOptions;	
			}
			else {
				$arrSelectOptions = $this->getSelectOptionArray();				
			}
						
			$arrComponent = array(
				"type" => "select",
				"display_name" => $this->get_info_filtered("name"),
				"attributes" => array("class" => "formInput textBox"),
				"options" => $arrSelectOptions,
				"validate" => array("RESTRICT_TO_OPTIONS")
			);
			
			return $arrComponent;
		}
		
		
		public function getMainGameInputCode() {
			$gameObj = new Game($this->MySQL);
			$arrGames = $gameObj->getGameList();
			$arrSelectOptions = array();
			foreach($arrGames as $gameID) {
				$gameObj->select($gameID);
				$arrSelectOptions[$gameID] = $gameObj->get_info_filtered("name");
				
			}
			
			return $this->getSelectInputCode($arrSelectOptions);
		}
		
		public function getRecruiterInputCode() {
			$arrMemberList = array();
			$dbprefix = $this->MySQL->get_tablePrefix();
			$result = $this->MySQL->query("SELECT * FROM ".$dbprefix."members WHERE disabled = '0' AND rank_id != '1' ORDER BY username");
			while($row = $result->fetch_assoc()) {
				$arrMemberList[] = array("id" => $row['member_id'], "value" => filterText($row['username']));	
			}
			
			$memberList = json_encode($arrMemberList);
			
			$arrComponent = array(
				"type" => "autocomplete",
				"attributes" => array("class" => "formInput textBox"),
				"options" => array("real_id" => "realAppComp_".$this->intTableKeyValue, "fake_id" => "fakeAppComp_".$this->intTableKeyValue, "list" => $memberList)
			);
			
			return $arrComponent;
		}
		
		public function getProfileOptionInputCode() {
			
			$arrComponent = array();
			if($this->profileOptionObj->get_info("optiontype") == "select") {
				$arrSelectOptions = $this->profileOptionObj->getSelectValues();
				
				$arrComponent = $this->getSelectInputCode($arrSelectOptions);
				
				
			}
			
			return $arrComponent;
		}
		
		public function getComponentInputCode() {
			global $hooksObj;
			$returnArr = array();
	
			
			$formInputName = "appcomponent_".$this->intTableKeyValue;
			if($this->arrObjInfo['componenttype'] == "profile") {
				$this->appSelectValueObj->select($this->arrSelectValues[0]);
				switch($this->appSelectValueObj->get_info("componentvalue")) {
					case "birthday":
						$returnArr = $this->getBirthdayInputCode();
						break;
					case "gamesplayed":
						$returnArr = $this->getGamesplayedInputCode();
						break;
					case "maingame":
						$returnArr = $this->getMainGameInputCode();
						break;
					case "recruiter":
						$returnArr = $this->getRecruiterInputCode();
						break;
					default:
						$this->profileOptionObj->select($this->appSelectValueObj->get_info("componentvalue"));
						$returnArr = $this->getProfileOptionInputCode();
						break;
				}
			}
			else {
				switch($this->arrObjInfo['componenttype']) {	
					case "multiselect":
						$returnArr = $this->getMultiSelectInputCode();
						break;
					case "select":
						$returnArr = $this->getSelectInputCode();
						break;
					case "largeinput":
						$returnArr = array("type" => "textarea");
						break;
					case "captcha":
					case "captchaextra":
						$returnArr['type'] = "custom";
						$returnArr['html'] = "<div class='formInput'><input type='text' name='".$formInputName."' class='textBox'>&nbsp;&nbsp;&nbsp<a href='javascript:void(0)' data-refresh='1' data-image='".$formInputName."_image' data-appid='".$this->intTableKeyValue."'>Refresh Image</a><br><br><div id='".$formInputName."_image' style='margin-bottom: 25px'><img src='".MAIN_ROOT."images/captcha.php?appCompID=".$this->intTableKeyValue."' width='440' height='90'></div></div>";
						break;
				}
			}
			
			if($this->get_info("required") == 1) {
				$returnArr['validate'][] = "NOT_BLANK";
			}
			
			$GLOBALS['returnArr'] = $returnArr;
			$hooksObj->run("display-member-app-components");
			$returnArr = $GLOBALS['returnArr'];
			unset($GLOBALS['returnArr']);
			
			return $returnArr;
		}
		
		public function checkCaptcha() {
			global $IP_ADDRESS;
			$dbprefix = $this->MySQL->get_tablePrefix();
			$filterIP = $this->MySQL->real_escape_string($IP_ADDRESS);
			$result = $this->MySQL->query("SELECT * FROM ".$dbprefix."app_components WHERE componenttype = 'captcha' OR componenttype = 'captchaextra'");
			while($row = $result->fetch_assoc()) {
		
				$result2 = $mysqli->query("SELECT * FROM ".$dbprefix."app_captcha WHERE ipaddress = '".$filterIP."' AND appcomponent_id = '".$row['appcomponent_id']."'");
				if($result2->num_rows > 0) {
					$checkArr = $result2->fetch_assoc();
					$postName = "appcomponent_".$row['appcomponent_id'];
					if($checkArr['captchatext'] != strtolower($_POST[$postName])) {
						$countErrors++;
						$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You entered an incorrect value for ".filterText($row['name']).".<br>";
					}
				}
				
			}
			
		}
	
		
		public function saveAppValue($memberAppID) {
			
			$this->intMemberAppID = $memberAppID;
			$arrSingleInputs = array("input", "largeinput", "select");
	
			if(in_array($this->arrObjInfo['componenttype'], $arrSingleInputs)) {
				$this->saveSingleValue();				
			}
			elseif($this->arrObjInfo['componenttype'] == "multiselect") {
				// Multi-select values
				$this->saveMultiValues();
			}
			elseif($this->arrObjInfo['componenttype'] == "profile") {
				
				$this->saveProfileValue();
				
			}
			
		}
		
		
		public function getProfileOptionType() {

			$arrType = $this->getAssociateIDs("ORDER BY componentvalue");
			$this->appSelectValueObj->select($arrType[0]);
			
			return $this->appSelectValueObj->get_info("componentvalue");
		}
		
		public function getDisplayValue($value) {
			
			if($this->arrObjInfo['componenttype'] == "profile") {
				$profileOptionType = $this->getProfileOptionType();
				switch($profileOptionType) {
					case "birthday":
						$bdayDate = new DateTime();
						$bdayDate->setTimestamp($value);
						$bdayDate->setTimezone(new DateTimeZone("UTC"));
						$returnVal = $bdayDate->format("M j, Y");
						break;
					case "maingame":
					case "gamesplayed":
						$gameObj = new Game($this->MySQL);
						$returnVal = "Unknown Game";
						if($gameObj->select($value)) {							
							$returnVal = $gameObj->get_info_filtered("name");
						}
						break;
					case "recruiter":
						$memberObj = new Member($this->MySQL);
						$returnVal = "Not Set";
						if($memberObj->select($value)) {
							$returnVal = $memberObj->getMemberLink();
						}
						break;
					default:
						$this->profileOptionObj->select($profileOptionType);
						if($this->profileOptionObj->get_info("optiontype") == "input") {
							$returnVal = $value;
						}
						else {
							$returnVal = "Unknown Value";
							if($this->profileOptionObj->objProfileOptionSelect->select($value)) {
								$returnVal = $this->profileOptionObj->objProfileOptionSelect->get_info_filtered("selectvalue");
							}
						}
						
				}
				
				
			}
			else {

				$returnVal = $value;
				
			}
			
			return $returnVal;
		}
		
		
		private function saveSingleValue() {
			
			//$postName = ($setPostName == "") ? "appcomponent_".$this->intTableKeyValue : $setPostName;
			$postName = "appcomponent_".$this->intTableKeyValue;
			
			$arrColumns = array("appcomponent_id", "memberapp_id", "appvalue");
			$arrValues = array($this->intTableKeyValue, $this->intMemberAppID, $_POST[$postName]);
			
			return $this->objAppValue->addNew($arrColumns, $arrValues);
			
		}
		
		
		private function saveMultiValues($arrCustomValues = array()) {

			$arrColumns = array("appcomponent_id", "memberapp_id", "appvalue");
			$arrSelectValues = (count($arrCustomValues) > 0) ? $arrCustomValues : $this->getAssociateIDs("ORDER BY componentvalue");
			$componentCounter = 1;
			$returnVal = true;
			foreach($arrSelectValues as $value) {
				$postName = "appcomponent_".$this->intTableKeyValue."_".$componentCounter;
				$componentCounter++;
				
				if(isset($_POST[$postName])) {
					$arrValues = array($this->intTableKeyValue, $this->intMemberAppID, $_POST[$postName]);
					
					if(!$this->objAppValue->addNew($arrColumns, $arrValues)) {
						$returnVal = false;
					}
				}
				
			}
			
			return $returnVal;
		}
		
		private function saveProfileValue() {
			if($this->getProfileOptionType() == "gamesplayed") {
				
				$gameObj = new Game($this->MySQL);
				$arrGameList = $gameObj->getGameList();
				
				$returnVal = $this->saveMultiValues($arrGameList);
				
			}
			else {
				$returnVal = $this->saveSingleValue();
			}

			return $returnVal;
		}
		
				
	}
		
	
	// Form Validation Functions
	
	function appCheckUsername() {
		global $signUpForm, $mysqli;
		$memberObj = new Member($mysqli);
		
		if($memberObj->select($_POST['username'])) {
			$signUpForm->errors[] = "There is already a member with that username.";			
		}
		
	}
	
	function appCheckEMail() {
		global $signUpForm, $mysqli, $dbprefix;
		if(trim(str_replace("@", "", $_POST['email'])) == "" || strpos($_POST['email'], "@") === false) {
			$signUpForm->errors[] = "You entered an invalid e-mail address.";
		}		
		
		$filterEmail = $mysqli->real_escape_string($_POST['email']);
		$result = $mysqli->query("SELECT email FROM ".$dbprefix."members WHERE email = '".$filterEmail."'");
		if($result->num_rows > 0) {
			$signUpForm->errors[] = "There is already a member registered with that e-mail address.";
		}
		
		$result = $mysqli->query("SELECT email FROM ".$dbprefix."memberapps WHERE email = '".$filterEmail."'");
		if($result->num_rows > 0) {
			$signUpForm->errors[] = "There is already a member application with that e-mail address.";
		}
		
	}
	
	
	function appCheckIP() {
		global $signUpForm, $mysqli, $dbprefix, $IP_ADDRESS, $websiteInfo;
		
		if($websiteInfo['allow_multiple_ips'] == 0) {
			$checkIP = $mysqli->query("SELECT ipaddress FROM ".$dbprefix."memberapps WHERE ipaddress = '".$IP_ADDRESS."'");
			if($checkIP->num_rows > 0) {
				$signUpForm->errors[] = "You have already applied to join.";
			}
		}
	}
	
	function appCheckCaptchas() {
		global $signUpForm, $mysqli, $dbprefix, $IP_ADDRESS;
		
		$filterIP = $mysqli->real_escape_string($IP_ADDRESS);
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."app_components WHERE componenttype = 'captcha' OR componenttype = 'captchaextra'");
		while($row = $result->fetch_assoc()) {
	
			$result2 = $mysqli->query("SELECT * FROM ".$dbprefix."app_captcha WHERE ipaddress = '".$filterIP."' AND appcomponent_id = '".$row['appcomponent_id']."'");
			if($result2->num_rows > 0) {
				$checkArr = $result2->fetch_assoc();
				$postName = "appcomponent_".$row['appcomponent_id'];
				if($checkArr['captchatext'] != strtolower($_POST[$postName])) {
					$signUpForm->errors[] = "You entered an incorrect value for ".filterText($row['name']).".";
				}
			}
			
		}
			
		
	}
	

?>
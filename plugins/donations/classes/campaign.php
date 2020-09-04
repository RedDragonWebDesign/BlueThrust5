<?php

	include_once(BASE_DIRECTORY."plugins/donations/classes/donation.php");

	class DonationCampaign extends Basic {
		
		
		public $donationObj;
		
		// Recurring Period Date Codes
		const DAY = "Ymd";
		const WEEK = "YW";
		const MONTH = "Ym";
		const YEAR = "Y";
		
		protected $arrPeriodDateCodes;
		private $blnUpdateCurrentPeriod = false;
		public $donationInfo;
		public $donationAmounts;
		private $arrDonatorList;
		
		public function __construct($sqlConnection) {

			$this->MySQL = $sqlConnection;
			$this->strTableName = $this->MySQL->get_tablePrefix()."donations_campaign";
			$this->strTableKey = "donationcampaign_id";

			$this->donationObj = new Donation($sqlConnection);
			$this->arrPeriodDateCodes = array("days" => self::DAY, "weeks" => self::WEEK, "months" => self::MONTH, "years" => self::YEAR);

		}
		
		
		public function select($intIDNum, $numericIDOnly=true) {
			
			$returnVal = parent::select($intIDNum, $numericIDOnly);
			
			$this->populateDonationInfo();
			
			return $returnVal;
		}
		
		public function getLink() {
			
			return MAIN_ROOT."plugins/donations/?campaign_id=".$this->intTableKeyValue;	
			
		}
		
		public function getCurrentPeriodRange($returnTimestamps=false) {
			
			$returnVal = array();
			if($this->intTableKeyValue != "" && $this->arrObjInfo['currentperiod'] != 0) {	

				$recurAmount = $this->arrObjInfo['recurringamount'];
				$currentPeriod = $this->arrObjInfo['currentperiod'];
				
				$year = substr($currentPeriod, 0, 4);
				$month = substr($currentPeriod, 4, 2);
				$day = substr($currentPeriod, 6, 2);
				
				switch($this->arrObjInfo['recurringunit']) {
					case "days":
						$currentPeriodDate = mktime(0,0,0,$month,$day,$year);
						$nextPeriodDate = mktime(0,0,0,$month,$day+$recurAmount,$year);
						$nextPeriod = date(self::DAY, $nextPeriodDate);
						break;
					case "weeks":
						$currentPeriodDate = strtotime($year."W".$month);
						$nextPeriodDate = strtotime($year."W".($month+$recurAmount));
						$nextPeriod = date(self::WEEK, $nextPeriodDate);
						break;
					case "months":
						$currentPeriodDate = mktime(0,0,0,$month,01,$year);
						$nextPeriodDate = mktime(0,0,0,$month+$recurAmount,01,$year);
						$nextPeriod = date(self::MONTH, $nextPeriodDate);
						break;
					case "years":
						$currentPeriodDate = mktime(0,0,0,01,$day,$year);
						$nextPeriodDate = mktime(0,0,0,01,01,$year+$recurAmount);
						$nextPeriod = date(self::YEAR, $nextPeriodDate);
						break;
				}

				
				$returnVal = (!$returnTimestamps) ? array("current" => $currentPeriod, "next" => $nextPeriod) : array("current" => $currentPeriodDate, "next" => $nextPeriodDate);
				
			}
			elseif($this->intTableKeyValue != "" && $this->arrObjInfo['currentperiod'] == 0) {
				// Default Prior 30 days range
				$x30Days = 60*60*24*30;
				
				$returnVal = (!$returnTimestamps) ? array() : array("current" => (time()-$x30Days), "next" => time());
				
			}
			
			
			return $returnVal;
		}
		
		
		public function updateCurrentPeriod() {
			
			if($this->intTableKeyValue != "" && $this->arrObjInfo['currentperiod'] != 0) {	
				
				$recurUnit = $this->arrObjInfo['recurringunit'];
				$todayPeriod = date($this->arrPeriodDateCodes[$recurUnit]);

				$currentPeriodRange = $this->getCurrentPeriodRange();
				if($todayPeriod >= $currentPeriodRange['next']) {
					$this->arrObjInfo['currentperiod'] = $currentPeriodRange['next'];
					$this->blnUpdateCurrentPeriod = true;
					$this->updateCurrentPeriod();
				}
				elseif($this->blnUpdateCurrentPeriod) {
					$this->update(array("currentperiod"), array($this->arrObjInfo['currentperiod']));
					$this->blnUpdateCurrentPeriod = false;
				}
		
			}
	
		}
		
		
		public function calcPeriodsSinceStart() {
		
			$returnVal = 0;
			if($this->intTableKeyValue != "" && $this->arrObjInfo['currentperiod'] != 0) {	
			
				$startDate = $this->arrObjInfo['datestarted'];
				$recurAmount = $this->arrObjInfo['recurringamount'];
				$recurUnit = $this->arrObjInfo['recurringunit'];
				
				$startPeriod = date($this->arrPeriodDateCodes[$recurUnit], $startDate);
				
				$todayPeriod = date($this->arrPeriodDateCodes[$recurUnit]);
				
				$returnVal = floor(($todayPeriod-$startPeriod)/$recurAmount);
				
			}
			
			return $returnVal;
		}
		
		
		public function populateDonationInfo($total=false, $currentPeriod=0, $nextPeriod=0) {
		
			$donationInfo = array();
			if($this->intTableKeyValue != "") {
					
				$arrPeriod = $this->getCurrentPeriodRange(true);
				$sqlCurrentPeriod = ($currentPeriod == 0) ? $arrPeriod['current'] : $currentPeriod;
				$sqlNextPeriod = ($nextPeriod == 0) ? $arrPeriod['next'] : $nextPeriod;
				
				$addSQL = (count($arrPeriod) == 0 || $total) ? "" : " AND datesent >= '".$sqlCurrentPeriod."' AND datesent < '".$sqlNextPeriod."'";
				
				$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."donations WHERE donationcampaign_id = '".$this->intTableKeyValue."' ".$addSQL."ORDER BY datesent DESC");
				while($row = $result->fetch_assoc()) {
					$donationInfo[] = filterArray($row);
					$totalDonationAmount += $row['amount'];	
				}
				
				$this->donationAmounts = $totalDonationAmount;
			}
			
			$this->donationInfo = $donationInfo;
		}
		
		
		public function getTotalDonationAmount() {
			return $this->donationAmounts;
		}

		public function getDonationInfo() {
			return $this->donationInfo;
		}
		
		
		
		public function getDonators($allTime=false) {
			
			$returnVal = array();
			if($this->intTableKeyValue != "") {	
			
				$addSQL = "";
				if(!$allTime) {
					
					$period = $this->getCurrentPeriodRange(true);

					$addSQL = " AND (datesent >= '".$period['current']."' AND datesent < '".$period['next']."')";
					
				}
				
				$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."donations WHERE donationcampaign_id = '".$this->intTableKeyValue."'".$addSQL." ORDER BY datesent DESC");
				while($row = $result->fetch_assoc()) {
					$returnVal[] = $row;	
				}
				
			}
			
			return $returnVal;
		}

		public function condenseDonators($arrDonators) {
			
			$returnVal = array();
			
			foreach($arrDonators as $arr) {
				if($arr['member_id'] != 0) {
					$returnVal[$arr['member_id']]['amount'] += $arr['amount'];
					$returnVal[$arr['member_id']]['timesdonated'] += 1;
					
					if($arr['datesent'] > $returnVal[$arr['member_id']]['lastdate']) {
						$returnVal[$arr['member_id']]['lastdate'] = $arr['datesent'];
						$returnVal[$arr['member_id']]['lastdonation'] = $arr['amount'];
					}
				}
			}
			
			$this->arrDonatorList = $returnVal;
			
			return $returnVal;
		}
		
		
		public function showDonatorList($allTime=false, $limit=0) {
			
			$counter = 0;
			
			$arrDonators = $this->getDonators($allTime);
			$this->condenseDonators($arrDonators);
			$arrList = array();
			$i = 0;
			foreach($arrDonators as $arr) {
				
				if(!in_array($arr['member_id'], $arrList)) {
				
					$isMember = $arr['member_id'] != 0;
					$selectID = $isMember ? $arr['member_id'] : $arr['donation_id'];
					
					if($i == 0) {
						$addCSS = "";
						$i = 1;
					}
					else {
						$addCSS = " alternateBGColor";
						$i = 0;
					}
					
					
					$this->displayDonator($selectID, $isMember, $addCSS);					
					
					
					if($arr['member_id'] != 0) {
						$arrList[] = $arr['member_id'];
					}
					
					$counter++;
				}
				
				if($limit != 0 && $counter == $limit) {
					break;
				}
				
			}
			
		}
		
		public function displayDonator($selectID, $isMember=true, $css="") {
			$member = new Member($this->MySQL);
			
			$arrSymbols = $this->getCurrencySymbol();
			if($isMember && $member->select($selectID)) {
				$dispDonatorInfo['name'] = $member->getMemberLink();
				$dispDonatorInfo['amount'] = $this->arrDonatorList[$selectID]['amount'];
				$dispDonatorInfo['lastdate'] = getPreciseTime($this->arrDonatorList[$selectID]['lastdate']);
				$dispDonatorInfo['lastdonation'] = ($this->arrDonatorList[$selectID]['timesdonated'] > 1) ? "Last Donation: <span class='donatorAmount'>".$this->formatAmount($this->arrDonatorList[$selectID]['lastdonation'])."</span><br>" : "";
			}
			else {
				
				$this->donationObj->select($selectID);
				$donationInfo = $this->donationObj->get_info_filtered();
				$dispDonatorInfo['name'] = ($donationInfo['name'] == "") ? "Anonymous" : $donationInfo['name'];
				$dispDonatorInfo['amount'] = $donationInfo['amount'];
				$dispDonatorInfo['lastdate'] = getPreciseTime($donationInfo['datesent']);
				$dispDonatorInfo['lastdonation'] = "";
			}
			
			include(BASE_DIRECTORY."plugins/donations/include/donator_template.php");
			
		}
		
		
		
		public function showMessagesList($allTime=false) {
		
			if($this->intTableKeyValue != "") {
				$i = 0;
				$arrDonators = $this->getDonators($allTime);
				$count = 0;
				foreach($arrDonators as $donationInfo) {
					if($donationInfo['message'] != "") {
						if($i == 0) {
							$addCSS = "";
							$i = 1;
						}
						else {
							$addCSS = " alternateBGColor";
							$i = 0;
						}
	
						$this->displayMessage($donationInfo['donation_id'], $addCSS);
						$count++;
					}
				}
			
			
				if($count == 0) {
					
					echo "	
						<p align='center' class='main'>
							<i>No Messages!</i>
						</p>
					";
					
				}
			
			}
			
		}
		
		
		public function displayMessage($donationID, $css="") {
			if($this->donationObj->select($donationID)) {
				$member = new Member($this->MySQL);
				$donationInfo = $this->donationObj->get_info_filtered();
				
				if($member->select($donationInfo['member_id'])) {
					$extraName = $donationInfo['name'] != "" ? " <i>(".$donationInfo['name'].")</i>" : "";
					$dispDonatorName = $member->getMemberLink().$extraName;	
				}
				else {
					$dispDonatorName = ($donationInfo['name'] == "") ? "Anonymous" : $donationInfo['name'];
				}				
				
				include(BASE_DIRECTORY."plugins/donations/include/messages_template.php");
				
			}
		}
		
		
		public function formatAmount($amount) {
			$arrSymbols = $this->getCurrencySymbol();	
			return $arrSymbols['left'].number_format($amount, 2).$arrSymbols['right'];
		}
		
		
		public function getCurrencySymbol() {

			$returnVal = array();
			if($this->intTableKeyValue != "") {
				include(BASE_DIRECTORY."plugins/donations/include/currency_codes.php");
				
				$blnSymbolLeft = $arrPaypalCurrencyInfo[$this->arrObjInfo['currency']]['position'] == "left";
				$blnSymbolRight = $arrPaypalCurrencyInfo[$this->arrObjInfo['currency']]['position'] == "right";
				$returnVal['left'] = ($blnSymbolLeft) ? $arrPaypalCurrencyInfo[$this->arrObjInfo['currency']]['symbol'] : "";
				$returnVal['right'] = ($blnSymbolRight) ? $arrPaypalCurrencyInfo[$this->arrObjInfo['currency']]['symbol'] : "";
			}
			
			return $returnVal;
		}
		
		public function getCurrentEndDate() {
			
			$currentEndDate = 0;
			if($this->intTableKeyValue != "") {
			
				if($this->arrObjInfo['dateend'] != 0) {
					$periodRange = $this->getCurrentPeriodRange(true);
					$currentEndDate = ($periodRange['next'] > $this->arrObjInfo['dateend']) ? $this->arrObjInfo['dateend'] : $periodRange['next']-(60*60*24);
				}
				elseif($this->arrObjInfo['dateend'] == 0 && $this->arrObjInfo['currentperiod'] != 0) {
					$periodRange = $this->getCurrentPeriodRange(true);
					$currentEndDate = $periodRange['next']-(60*60*24);
				}
				
				
			}
			
			return $currentEndDate;
		}
		
		public function getFormattedEndDate() {

			$currentEndDate = $this->getCurrentEndDate();
			$returnVal = "";
			if($currentEndDate != 0) {
				
				$timeDiff = $currentEndDate-time();
				if($timeDiff < 0) {
					$returnVal = "Campaign Ended";
				}
				elseif($timeDiff < 3600) {
					$timeLeft = round($timeDiff/60);
					$returnVal = $timeLeft." ".pluralize("minute", $timeLeft);
				}
				elseif($timeDiff < 86400) {
					$timeLeft = round($timeDiff/3600);
					$returnVal = $timeLeft." ".pluralize("hour", $timeLeft);
				}
				else {
					$timeLeft = round($timeDiff/86400);
					$returnVal = $timeLeft." ".pluralize("day", $timeLeft);
				}
				
			}
			
			return $returnVal;
		}
		
		public function getDaysLeft() {
			
			$currentEndDate = $this->getCurrentEndDate();
			$returnVal = false;
			if($currentEndDate != 0) {
				$secondsLeft = $currentEndDate-time();
				$returnVal = ($secondsLeft > 0) ? round($secondsLeft/(60*60*24)) : 0;				
			}
			
			return $returnVal;
		}
		
		public function __get($name) {
			
			$arrConstants = array("DAY", "WEEK", "MONTH", "YEAR");	
			if(in_array($name, $arrConstants)) {
				return constant("self::$name");	
			}
			
		}
		
		public function getCurrencyCodes() {

			include(BASE_DIRECTORY."plugins/donations/include/currency_codes.php");
			return $arrPaypalCurrencyCodes;
		}
		
		public function getCurrencyCodeInfo() {
			include(BASE_DIRECTORY."plugins/donations/include/currency_codes.php");
			return $arrPaypalCurrencyInfo;
		}
		
	}

?>
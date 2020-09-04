<?php


	class Clock extends BasicOrder {
		
		public $clockSeparator = "||";
		
		public function __construct($sqlConnection) {
			
			$this->MySQL = $sqlConnection;
			$this->strTableName = $this->MySQL->get_tablePrefix()."clocks";
			$this->strTableKey = "clock_id";

		}
		
		
		public function getUTCTime() {
			$currentTimezone = date_default_timezone_get();
			date_default_timezone_set("UTC");	
			$utcTime = time();
			
			date_default_timezone_set($currentTimezone);
			
			return $utcTime;
		}
		
		public function getClockInfo($forceOffset=false) {
			
			$returnVal = false;
			if($this->intTableKeyValue != "") {
				$dateObj = new DateTime(date("Y-m-d"), new DateTimeZone($this->arrObjInfo['timezone']));
				$dateOffset = $dateObj->getOffset();
				$dateTime = ($this->getUTCTime()+$dateOffset);
				$dateHour = gmdate("G", $dateTime);
				$dateMinutes = gmdate("i", $dateTime);
				
				if(!$forceOffset && date("nj") == gmdate("nj", $dateTime)) {
					$dateOffset = "''";	
				}
				
				$returnVal = array(
					"offset" => $dateOffset,
					"time" => $dateTime,
					"hour" => $dateHour,
					"minutes" => $dateMinutes
				);
				
			}
			
			return $returnVal;
		}
		
		public function displayClocks($return=false) {
			
			$clockArray = array();
			$clockJS = "";
			$result = $this->MySQL->query("SELECT clock_id FROM ".$this->strTableName." ORDER BY ordernum DESC");	
			while($row = $result->fetch_assoc()) {
				$this->select($row['clock_id']);	
				$clockInfo = $this->getClockInfo();
				$info = $this->get_info_filtered();
				
				$clockArray[] = "<span style='color: ".$info['color']."'>".$info['name'].": <span id='clock_".$row['clock_id']."'></span></span>";

				$clocksJS .= "displayClock(".$clockInfo['offset'].", ".$clockInfo['hour'].", ".$clockInfo['minutes'].", 'clock_".$row['clock_id']."');
				";
				
			}
			
			if(!$return) {
				echo implode(" ".$this->clockSeparator." ", $clockArray)."
				
					<script type='text/javascript'>
				
						".$clocksJS."
					
					</script>
				";
			}
			else {
				
				return implode(" ".$this->clockSeparator." ", $clockArray)."
				
					<script type='text/javascript'>
				
						".$clocksJS."
					
					</script>
				";
				
			}
			
		}
		
		public function getTimezones() {
			
			$arrTimezoneOptions = array();
			$arrTimezones = DateTimeZone::listIdentifiers();
			foreach($arrTimezones as $timeZone) {
				
				$tz = new DateTimeZone($timeZone);
				$dispOffset = ((($tz->getOffset(new DateTime("now", $tz)))/60)/60);
				$dispSign = ($dispOffset < 0) ? "" : "+";
				
				$arrTimezoneOptions[$timeZone] = str_replace("_", " ", $timeZone)." (UTC".$dispSign.$dispOffset.")";
			}	

			return $arrTimezoneOptions;
		}
		
		
		/*
		 * This class doesn't use associate id's so cancelling out these functions
		 * 
		 */
		public function getAssociateIDs($sqlOrderBY = "", $bypassFilter=false) {
			return false;
		}
		
		
		public function set_assocTableName($tableName) {
			return false;
		}
		
		public function set_assocTableKey($tableKey) {
			return false;
		}
			
		
	}


?>
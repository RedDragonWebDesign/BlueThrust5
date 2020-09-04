<?php

	include_once("basic.php");
	
	class IPBan extends Basic {
		
		
		public function __construct($sqlConnection) {	

			$this->MySQL = $sqlConnection;
			$this->strTableKey = "ipaddress";
			$this->strTableName = $this->MySQL->get_tablePrefix()."ipban";
			
		}
		

		
		public function isBanned($ip) {
			
			$returnVal = false;
	
			if($this->select($ip, false)) {
			
				if($this->arrObjInfo['exptime'] == -1) {
					$this->arrObjInfo['exptime'] = 0;	
				}
				
				
				if(time() < $this->arrObjInfo['exptime'] || $this->arrObjInfo['exptime'] == 0) {
					$returnVal = true;
				}
				else {
					$this->delete();
				}
			
			}
			else {
				
				$arrCheckIP = explode(".", $ip);
				
				$result = $this->MySQL->query("SELECT * FROM ".$this->MySQL->get_tablePrefix()."ipban WHERE ipaddress LIKE '%*%' AND (exptime > '".time()."' OR exptime = '0')");
				
				if($result !== false) {
					while($row = $result->fetch_assoc()) {
					
						$arrBannedIP = explode(".", $row['ipaddress']);
						$checkIP = 0;
						foreach($arrBannedIP as $key=>$ipPart) {
							if($arrCheckIP[$key] == $ipPart || $ipPart == "*") {
								$checkIP++;
							}
						}
						
						
						if($checkIP == count($arrBannedIP)) {
							$returnVal = true;	
						}
						
					}
				}
				
				$this->MySQL->query("DELETE FROM ".$this->MySQL->get_tablePrefix()."ipban WHERE exptime != '0' AND exptime < '".time()."'");
				$this->MySQL->query("OPTIMIZE TABLE `".$this->MySQL->get_tablePrefix()."ipban`");
			}
			

			return $returnVal;
		}
				
	}


?>
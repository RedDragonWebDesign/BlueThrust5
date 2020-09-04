<?php

	// Class to make some things simpler with PHPMailer


	class btMail {

		private $objPHPMailer;
		
		// General e-mail function using PHPMailer
		public function sendMail($to, $subject="", $message, $additional=array()) {
				
			$mail = new PHPMailer();
			$this->objPHPMailer = $mail;
			
			
			$from = $this->getFrom($additional);
			
			// Check if the from has both email and name
			if(is_array($from)) {
				$mail->setFrom($from['email'], $from['name']);
			}
			else {
				$mail->setFrom($from);
			}
			
			
			$this->addEmail(array("to" => $to));
			
			$mail->Subject = $subject;
			
			$mail->msgHTML($message);
			
			
			$this->addEmail($additional, "bcc");
			$this->addEmail($additional, "cc");
	
			
			return $mail->send();
			
		}
		
		private function getFrom($args) {
			
			if(!isset($args['from'])) {			
				$siteDomain = $_SERVER['SERVER_NAME'];
				if(substr($siteDomain,0,strlen("www.")) == "www.") {
					$siteDomain = substr($siteDomain, strlen("www."));
				}
				
				$from = "admin@".$siteDomain;
			}
			else {
				$from = $args['from'];	
			}
			
			return $from;
		}
		
		
		private function addEmail($args, $type="to") {
			
			$mail = $this->objPHPMailer;
			
			switch($type) {
				case "bcc":
					$func = "addBCC";
					break;
				case "cc":
					$func = "addCC";
					break;
				default:
					$func = "addAddress";				
			}
			
			if(isset($args[$type]) && is_array($args[$type])) {
	
				foreach($args[$type] as $info) {
					if(is_array($info)) {	
						call_user_func_array(array($mail, $func), array($info['email'], $info['name']));
					}
					else {
						call_user_func_array(array($mail, $func), array($info));
					}
				}
				
			}
			elseif(isset($args[$type])) {
				call_user_func_array(array($mail, $func), array($args[$type]));		
			}
			
		}
		
	}


?>
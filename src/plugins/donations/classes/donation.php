<?php

	include_once(BASE_DIRECTORY."classes/basic.php");

	class Donation extends Basic {
		
		
		protected $objError;
		
		public function __construct($sqlConnection) {
		
			$this->MySQL = $sqlConnection;
			$this->strTableName = $this->MySQL->get_tablePrefix()."donations";
			$this->strTableKey = "donation_id";

			
			$this->objError = new Basic($sqlConnection, "donations_errorlog", "donationerror_id");
			
		}
		
		
		public function logError($response) {

			$this->objError->addNew(array("datesent", "response"), array(time(), $response));
			
		}
		

	}

?>
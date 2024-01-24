<?php

	class EmailNotification extends Basic {

		protected $mailObj;
		protected $memberObj;

		public function __construct($sqlConnection) {

			$this->MySQL = $sqlConnection;
			$this->strTableName = $this->MySQL->get_tablePrefix()."emailnotifications_queue";
			$this->strTableKey = "emailnotificationsqueue_id";
			$this->mailObj = new btMail();

			$this->memberObj = new Member($sqlConnection);

		}


		public function select($intIDNum, $numericIDOnly = true) {

			$result = parent::select($intIDNum, $numericIDOnly);
			if($result && !$this->memberObj->select($this->arrObjInfo['member_id'])) {
				$this->intTableKeyValue = "";
				$result = false;
			}

			return $result;
		}


		public function send() {

			if($this->intTableKeyValue != "") {

				$this->memberObj->email($this->arrObjInfo['subject'], $this->arrObjInfo['message']);

				$this->update(array("sent"), array(1));

			}

		}

	}
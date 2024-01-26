<?php


class EmailNotificationSetting extends Basic {

	protected $memberID;


	public function __construct($sqlConnection) {

		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."emailnotifications_settings";
		$this->strTableKey = "emailnotificationsetting_id";
		$this->memberID = 0;
	}


	public function update($arrTableColumns, $arrColumnValues) {

		if ($this->intTableKeyValue == "" && $this->memberID != 0) {
			$this->addNew(["member_id"], [$this->memberID]);
		}

		return parent::update($arrTableColumns, $arrColumnValues);
	}



	public function setMemberID($member_id) {
		$this->memberID = $member_id;

		if (!$this->selectByMulti(["member_id" => $member_id])) {
			$this->addNew(["member_id"], [$this->memberID]);
		}
	}


}

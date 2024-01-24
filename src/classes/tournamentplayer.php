<?php

class TournamentPlayer extends Basic {

	protected $tournamentObj;

	public function __construct($sqlConnection, &$tObj) {

		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."tournamentplayers";
		$this->strTableKey = "tournamentplayer_id";

		$this->tournamentObj = $tObj;

	}


	public function setTournament(&$tObj) {
		$this->tournamentObj = $tObj;
	}

	public function addNew($arrColumns, $arrValues) {

		$returnVal = parent::addNew($arrColumns, $arrValues);

		$this->tournamentObj->select($this->get_info("tournament_id"));

		if ($returnVal && $this->tournamentObj->get_info("playersperteam") == 1) {

			$arrUnfilledTeams = $this->tournamentObj->getUnfilledTeams();
			if (count($arrUnfilledTeams) > 0) {
				$newTeam = $arrUnfilledTeams[0];
				$this->update(array("team_id"), array($newTeam));
			}

		}

		// Check for email notification
		$this->setReminder();

		return $returnVal;

	}


	public function getTournamentReminderID() {

		$returnVal = false;

		if ($this->intTableKeyValue != "") {

			$tournamentReminderTable = $this->MySQL->get_tablePrefix()."tournament_reminder";
			$emailQueueTable = $this->MySQL->get_tablePrefix()."emailnotifications_queue";

			$subQuery = "SELECT emailnotificationsqueue_id FROM ".$tournamentReminderTable." WHERE tournament_id = '".$this->tournamentObj->get_info("tournament_id")."'";
			$mainQuery = "SELECT emailnotificationsqueue_id, sent FROM ".$emailQueueTable." WHERE member_id = '".$this->arrObjInfo['member_id']."' AND emailnotificationsqueue_id IN (".$subQuery.")";

			$result = $this->MySQL->query($mainQuery);
			$row = $result->fetch_assoc();

			if ($result->num_rows > 0 && $row['sent'] == 0) {
				$returnVal = $row['emailnotificationsqueue_id'];
			}
		}

		return $returnVal;
	}

	public function setReminder() {

		$member = new Member($this->MySQL);

		if ($member->select($this->arrObjInfo['member_id']) && $member->getEmailNotificationSetting("tournament_time") != 0) {

			$timeBefore = $member->getEmailNotificationSetting("tournament_time");
			$unitBefore = $member->getEmailNotificationSetting("tournament_unit");

			switch ($unitBefore) {
				case "minutes":
					$timeDiff = $timeBefore*60;
					break;
				case "hour":
					$timeDiff = $timeBefore*60*60;
					break;
				case "days":
					$timeDiff = $timeBefore*60*60*24;
					break;
			}

			$sendReminder = $this->tournamentObj->get_info("startdate")-$timeDiff;

			$tournamentLink = "<a href='".$this->tournamentObj->getLink()."'>View Tournament Page</a>";
			$message = "This is a reminder that the tournament, ".$this->tournamentObj->get_info_filtered("name")." will be starting at ".getDateUTC($sendReminder).".<br><br>".$tournamentLink;

			$tournamentReminderID = $this->getTournamentReminderID();
			if ($tournamentReminderID !== false) {
				// A reminder has already been set, need to update the time!
				// Update reminder
				$member->setEmailReminder($sendReminder, "Tournament Starting!", $message, $tournamentReminderID);
			}
			else {
				// Add new reminder
				$emailReminderID = $member->setEmailReminder($sendReminder, "Tournament Starting!", $message);
				$tournamentReminder = new Basic($this->MySQL, "tournament_reminder", "tournamentreminder_id");
				$tournamentReminder->addNew(array("emailnotificationsqueue_id", "tournament_id"), array($emailReminderID, $this->arrObjInfo['tournament_id']));

			}

		}

	}

	public function deleteReminder() {

		$tournamentReminderID = $this->getTournamentReminderID();

		if ($tournamentReminderID !== false) {

			$tournamentReminderTable = $this->MySQL->get_tablePrefix()."tournament_reminder";
			$emailQueueTable = $this->MySQL->get_tablePrefix()."emailnotifications_queue";

			$this->MySQL->query("DELETE FROM ".$emailQueueTable." WHERE emailnotificationsqueue_id = '".$tournamentReminderID."'");
			$this->MySQL->query("DELETE FROM ".$tournamentReminderTable." WHERE emailnotificationsqueue_id = '".$tournamentReminderID."'");

		}

	}


	public function delete() {

		$this->deleteReminder();

		return parent::delete();

	}

}
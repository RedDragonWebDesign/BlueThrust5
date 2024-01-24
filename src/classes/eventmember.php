<?php


	class EventMember extends Basic {

		protected $eventObj;

		public function __construct($sqlConnection, &$eObj) {

			$this->MySQL = $sqlConnection;
			$this->strTableName = $this->MySQL->get_tablePrefix()."events_members";
			$this->strTableKey = "eventmember_id";

			$this->eventObj = $eObj;

		}

		public function update($arrTableColumns, $arrTableValues) {

			$result = parent::update($arrTableColumns, $arrTableValues);

			if($result) {

				if(in_array("status", $arrTableColumns)) {

					$eventReminderID = $this->getEventReminderID();
					$statusKey = array_search("status", $arrTableColumns);

					if($arrTableValues[$statusKey] == 1) {
						// Set Reminder

						$this->setReminder();

					}
					elseif($eventReminderID !== false) {

						$eventReminderTable = $this->MySQL->get_tablePrefix()."event_reminder";
						$emailQueueTable = $this->MySQL->get_tablePrefix()."emailnotifications_queue";
						$this->MySQL->query("DELETE FROM ".$emailQueueTable." WHERE emailnotificationsqueue_id = '".$eventReminderID."'");
						$this->MySQL->query("DELETE FROM ".$eventReminderTable." WHERE emailnotificationsqueue_id = '".$eventReminderID."'");
					}

				}

			}

			return $result;
		}

		public function getEventReminderID() {

			$returnVal = false;

			if($this->intTableKeyValue != "") {
				$eventReminderTable = $this->MySQL->get_tablePrefix()."event_reminder";
				$emailQueueTable = $this->MySQL->get_tablePrefix()."emailnotifications_queue";

				$subQuery = "SELECT emailnotificationsqueue_id FROM ".$eventReminderTable." WHERE event_id = '".$this->eventObj->get_info("event_id")."'";
				$mainQuery = "SELECT emailnotificationsqueue_id, sent FROM ".$emailQueueTable." WHERE member_id = '".$this->arrObjInfo['member_id']."' AND emailnotificationsqueue_id IN (".$subQuery.")";

				$result = $this->MySQL->query($mainQuery);
				$row = $result->fetch_assoc();

				if($result->num_rows > 0 && $row['sent'] == 0) {
					$returnVal = $row['emailnotificationsqueue_id'];
				}
			}

			return $returnVal;
		}


		public function setReminder() {

			$member = new Member($this->MySQL);

			if($this->arrObjInfo['status'] == 1 && $member->select($this->arrObjInfo['member_id']) && $member->getEmailNotificationSetting("event_time") != 0) {

				$timeBefore = $member->getEmailNotificationSetting("event_time");
				$unitBefore = $member->getEmailNotificationSetting("event_unit");

				switch($unitBefore) {
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

				$sendReminder = $this->eventObj->get_info("startdate")-$timeDiff;

				$eventLink = "<a href='".$this->eventObj->getLink()."'>View Event Page</a>";
				$message = "This is a reminder that the event, ".$this->eventObj->get_info_filtered("title")." will be starting at ".getDateUTC($sendReminder).".<br><br>".$eventLink;

				$eventReminderID = $this->getEventReminderID();

				if($eventReminderID !== false) {
					// A reminder has already been set, need to update the time!
					// Update reminder
					$member->setEmailReminder($sendReminder, "Event Starting!", $message, $eventReminderID);
				}
				else {
					// Add new reminder
					$emailReminderID = $member->setEmailReminder($sendReminder, "Event Starting!", $message);
					$eventReminder = new Basic($this->MySQL, "event_reminder", "eventreminder_id");
					$eventReminder->addNew(array("emailnotificationsqueue_id", "event_id"), array($emailReminderID, $this->arrObjInfo['event_id']));

				}

			}

		}


		public function deleteReminder() {

			$eventReminderID = $this->getEventReminderID();

			if($eventReminderID !== false) {

				$eventReminderTable = $this->MySQL->get_tablePrefix()."event_reminder";
				$emailQueueTable = $this->MySQL->get_tablePrefix()."emailnotifications_queue";

				$this->MySQL->query("DELETE FROM ".$emailQueueTable." WHERE emailnotificationsqueue_id = '".$eventReminderID."'");
				$this->MySQL->query("DELETE FROM ".$eventReminderTable." WHERE emailnotificationsqueue_id = '".$eventReminderID."'");

			}

		}

		public function delete() {

			$this->deleteReminder();

			return parent::delete();

		}

	}
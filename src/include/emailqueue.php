<?php

	if (!defined("MAIN_ROOT")) {
exit();
    }

	function sendQueuedEmail() {
		global $mysqli, $websiteInfo, $webInfoObj, $dbprefix;

		$date = new DateTime();
		$date->setTimezone(new DateTimeZone("UTC"));

		$time = $date->getTimestamp();

		if (!isset($websiteInfo['emailqueue_lastsent']) || ($websiteInfo['emailqueue_lastsent']+($websiteInfo['emailqueue_delay']*60)) <= $time) {
			$emailNotification = new EmailNotification($mysqli);

			$query = "SELECT emailnotificationsqueue_id FROM ".$dbprefix."emailnotifications_queue WHERE sent = '0' AND senddate <= '".$time."'";
			$result = $mysqli->query($query);
			while ($row = $result->fetch_assoc()) {
				if ($emailNotification->select($row['emailnotificationsqueue_id'])) {
					$emailNotification->send();
				}
			}

			$webInfoObj->multiUpdate(array("emailqueue_lastsent"), array($time));
		}
	}

	sendQueuedEmail();

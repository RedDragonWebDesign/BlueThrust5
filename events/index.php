<?php

/*
 * Bluethrust Clan Scripts v4
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */


// Config File
$prevFolder = "../";

include($prevFolder."_setup.php");

$consoleObj = new ConsoleOption($mysqli);
$eventObj = new Event($mysqli);
$member = new Member($mysqli);


// Start Page
$PAGE_NAME = "Events - ";
include($prevFolder."themes/".$THEME."/_header.php");


$memberInfo = array();




$LOGGED_IN = false;
if($member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();
	$LOGGED_IN = true;

}

$breadcrumbObj->setTitle("Events");
$breadcrumbObj->addCrumb("Home", $MAIN_ROOT);
$breadcrumbObj->addCrumb("Events");
include($prevFolder."include/breadcrumb.php");
?>


<div style='margin: 0px auto; '>
	<table class='formTable' style='margin-left: auto; margin-right: auto'>
	
		<tr>
			<td class='formTitle' width="45%">Event Title:</td>
			<td class='formTitle' width="30%">Creator:</td>
			<td class='formTitle' width="25%">Start Date:</td>
		</tr>
		
		<?php
		
			$eventObj = new Event($mysqli);
			$objMember = new Member($mysqli);
			$counter = 0;
			$countEvents = 0;
			$result = $mysqli->query("SELECT event_id FROM ".$dbprefix."events ORDER BY startdate");
			while($row = $result->fetch_assoc()) {
				
				$eventObj->select($row['event_id']);
				$eventInfo = $eventObj->get_info_filtered();

				$showEvent = false;
				if($eventInfo['visibility'] == 2 && (in_array($memberInfo['member_id'], $eventObj->getInvitedMembers(true)) || $memberInfo['member_id'] == $eventInfo['member_id'] || $memberInfo['rank_id'] == 1)) {
					$showEvent == true;
				}
				elseif($eventInfo['visibility'] == 1 && $LOGGED_IN) {
			
					$showEvent = true;					
				}
				elseif($eventInfo['visibility'] == 0) {
					$showEvent = true;					
				}
				
				
				
				if($showEvent) {
					
					$countEvents++;
					$addCSS = "";
					if($counter%2 == 0) {
						$addCSS = " alternateBGColor";
					}
					$counter++;
					
					$objMember->select($eventInfo['member_id']);
					
					$dateTimeObj = new DateTime();
					$dateTimeObj->setTimestamp($eventInfo['startdate']);
					$includeTimezone = "";
					
					if($eventInfo['timezone'] != "") { 
						$dateTimeObj->setTimezone(new DateTimeZone($eventInfo['timezone']));
						$dispTimezone = $dateTimeObj->format(" T"); 
					}
					$dateTimeObj->setTimezone("UTC");
					$dispStartDate = $dateTimeObj->format("M j, Y g:i A").$dispTimezone;
					
					echo "
					<tr>
						<td class='main".$addCSS."' style='padding: 3px'><a href='info.php?eID=".$eventInfo['event_id']."'>".$eventInfo['title']."</a></td>
						<td class='main".$addCSS."' style='padding: 3px' align='center'>".$objMember->getMemberLink()."</td>
						<td class='main".$addCSS."' style='padding: 3px' align='center'>".$dispStartDate."</td>
					</tr>
					
					";
					
					
				}
				
			}
			
		?>
		
	</table>
	
	<?php
	
		if($countEvents == 0) {
			

			echo "
			
				<div class='shadedBox' style='width: 30%; margin-top: 20px; margin-left: auto; margin-right: auto'>
					<p class='main' align='center'>
						<i>No visible events have been created!</i>
					</p>
				</div>
			
			";
			
		}
	
	?>
	
</div>

<?php
include($prevFolder."themes/".$THEME."/_footer.php");
?>
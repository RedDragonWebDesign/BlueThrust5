<?php

	if(!defined("MAIN_ROOT")) { exit(); }
	
	
	$arrColumns = array();
	$arrValues = array();
	
	
	if($eventObj->memberHasAccess($memberInfo['member_id'], "mangeinvites")) {
		$arrColumns[] = "position_id";
		$arrValues[] = $_POST['updatePositionID'];
		$checkSelectPosition = $eventObj->objEventPosition->select($_POST['updatePositionID']);
		// Check Position ID
		if($_POST['updatePositionID'] != 0 && (!$checkSelectPosition || ($checkSelectPosition && $eventObj->objEventPosition->get_info("event_id") != $eventID))) {
			$formObj->errors[] = "You selected an invalid position.";
		}			
		
	}
	
	
	if($eventObj->memberHasAccess($memberInfo['member_id'], "attendenceconfirm")) {
		$arrAcceptableValues = array(0,1,2,3);
		if($eventInfo['startdate'] <= time() && in_array($_POST['updateConfirm'], $arrAcceptableValues)) {
			$arrColumns[] = "attendconfirm_admin";
			$arrValues[] = $_POST['updateConfirm'];
		}	
		
	}
	

	
	if(count($formObj->errors) == 0) {
		if(!$eventObj->objEventMember->update($arrColumns, $arrValues)) {
			$formObj->errors[] = "Unable to save information to the database.  Please contact the website administrator.";
		}
	}
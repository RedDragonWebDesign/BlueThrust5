<?php

	include("../_setup.php");
	
	include("../classes/member.php");
	
	$member = new Member($mysqli);
	$consoleObj = new ConsoleOption($mysqli);
	
	$websiteSettingsCID = $consoleObj->findConsoleIDByName("Website Settings");
	$consoleObj->select($websiteSettingsCID);
	
	if(!isset($_SESSION['btUsername']) || !isset($_SESSION['btPassword']) || !$member->select($_SESSION['btUsername']) || ($member->select($_SESSION['btUsername']) && !$member->authorizeLogin($_SESSION['btPassword'])) || ($member->select($_SESSION['btUsername']) && $member->authorizeLogin($_SESSION['btPassword']) && !$member->hasAccess($consoleObj))) {
		header("HTTP/1.0 404 Not Found");
		exit();
	}
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."menu_category ORDER BY section, sortnum");
	while($catInfo = $result->fetch_assoc()) {
		
		
		foreach($catInfo as $key=>$value) {
			$catInfo[$key] = $mysqli->real_escape_string($value);	
		}

		$saveMenuSQL .= "INSERT INTO `".$dbprefix."menu_category` (`menucategory_id`, `section`, `name`, `sortnum`, `headertype`, `headercode`, `accesstype`, `hide`) VALUES ('".$catInfo['menucategory_id']."', '".$catInfo['section']."', '".$catInfo['name']."', '".$catInfo['sortnum']."', '".$catInfo['headertype']."', '".$catInfo['headercode']."', '".$catInfo['accesstype']."', '".$catInfo['hide']."');";
		$saveMenuSQL .= "\n";
		
	}
	
	$saveMenuSQL .= "\n";
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."menu_item ORDER BY menucategory_id, sortnum");
	while($row = $result->fetch_assoc()) {
		
		foreach($row as $key=>$value) {
			$row[$key] = $mysqli->real_escape_string($value);
		}
		
		$saveMenuSQL .= "INSERT INTO `".$dbprefix."menu_item` (`menuitem_id`, `menucategory_id`, `name`, `itemtype`, `itemtype_id`, `accesstype`, `hide`, `sortnum`) VALUES ('".$row['menuitem_id']."', '".$row['menucategory_id']."', '".$row['name']."', '".$row['itemtype']."', '".$row['itemtype_id']."', '".$row['accesstype']."', '".$row['hide']."', '".$row['sortnum']."');";
		$saveMenuSQL .= "\n";
	}
	$saveMenuSQL .= "\n";
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."menuitem_customblock");
	while($row = $result->fetch_assoc()) {
	
		foreach($row as $key=>$value) {
			$row[$key] = $mysqli->real_escape_string($value);
		}
	
		$saveMenuSQL .= "INSERT INTO `".$dbprefix."menuitem_customblock` (`menucustomblock_id`, `menuitem_id`, `blocktype`, `code`) VALUES ('".$row['menucustomblock_id']."', '".$row['menuitem_id']."', '".$row['blocktype']."', '".$row['code']."');";
		$saveMenuSQL .= "\n";
	}
	$saveMenuSQL .= "\n";
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."menuitem_custompage");
	while($row = $result->fetch_assoc()) {
	
		foreach($row as $key=>$value) {
			$row[$key] = $mysqli->real_escape_string($value);
		}
	
		$saveMenuSQL .= "INSERT INTO `".$dbprefix."menuitem_custompage` (`menucustompage_id`, `menuitem_id`, `custompage_id`, `prefix`, `linktarget`, `textalign`) VALUES ('".$row['menucustompage_id']."', '".$row['menuitem_id']."', '".$row['custompage_id']."', '".$row['prefix']."', '".$row['linktarget']."', '".$row['textalign']."');";
		$saveMenuSQL .= "\n";
	}
	$saveMenuSQL .= "\n";
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."menuitem_image");
	while($row = $result->fetch_assoc()) {
	
		foreach($row as $key=>$value) {
			$row[$key] = $mysqli->real_escape_string($value);
		}
	
		$saveMenuSQL .= "INSERT INTO `".$dbprefix."menuitem_image` (`menuimage_id`, `menuitem_id`, `imageurl`, `width`, `height`, `link`, `linktarget`, `imagealign`) VALUES ('".$row['menuimage_id']."', '".$row['menuitem_id']."', '".$row['imageurl']."', '".$row['width']."', '".$row['height']."', '".$row['link']."', '".$row['linktarget']."', '".$row['imagealign']."');";
		$saveMenuSQL .= "\n";
	}
	$saveMenuSQL .= "\n";
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."menuitem_link");
	while($row = $result->fetch_assoc()) {
	
		foreach($row as $key=>$value) {
			$row[$key] = $mysqli->real_escape_string($value);
		}
	
		$saveMenuSQL .= "INSERT INTO `".$dbprefix."menuitem_link` (`menulink_id`, `menuitem_id`, `link`, `linktarget`, `prefix`, `textalign`) VALUES ('".$row['menulink_id']."', '".$row['menuitem_id']."', '".$row['link']."', '".$row['linktarget']."', '".$row['prefix']."', '".$row['textalign']."');";
		$saveMenuSQL .= "\n";
	}
	$saveMenuSQL .= "\n";
	
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."menuitem_shoutbox");
	while($row = $result->fetch_assoc()) {
	
		foreach($row as $key=>$value) {
			$row[$key] = $mysqli->real_escape_string($value);
		}
	
		$saveMenuSQL .= "INSERT INTO `".$dbprefix."menuitem_shoutbox` (`menushoutbox_id`, `menuitem_id`, `width`, `height`, `percentwidth`, `percentheight`, `textboxwidth`) VALUES ('".$row['menushoutbox_id']."', '".$row['menuitem_id']."', '".$row['width']."', '".$row['height']."', '".$row['percentwidth']."', '".$row['percentheight']."', '".$row['textboxwidth']."');";
		$saveMenuSQL .= "\n";
	}


	if(file_put_contents($websiteInfo['theme']."/savemenu.sql", $saveMenuSQL)) {
		echo "1";	
	}
	else {
		echo "2";
	}
	
	
?>
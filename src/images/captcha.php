<?php
include("../_setup.php");
include_once("../classes/member.php");
include_once("../classes/basicorder.php");

$member = new Member($mysqli);

$appComponentObj = new BasicOrder($mysqli, "app_components", "appcomponent_id");
$appComponentObj->set_assocTableName("app_selectvalues");
$appComponentObj->set_assocTableKey("appselectvalue_id");

if(($_GET['appCompID'] != -1 && !$appComponentObj->select($_GET['appCompID'])) || ($_GET['appCompID'] != -1 && ($appComponentObj->get_info("componenttype") != "captcha" && $appComponentObj->get_info("componenttype") != "captchaextra"))) {
	exit();	
}


if(isset($_GET['display'])) {
	echo "<img src='".$MAIN_ROOT."images/captcha.php?appCompID=".$_GET['appCompID']."&new=".time()."' width='440' height='90'>";
}
else {
	header('Content-Type: image/png');
	
	if($_GET['appCompID'] == -1) {
		$appCompInfo['appcomponent_id'] = -1;
	}
	else {
		$appCompInfo = $appComponentObj->get_info_filtered();
	}
	
	
	$captchaObj = new Basic($mysqli, "app_captcha", "appcaptcha_id");
	$filterIP = $mysqli->real_escape_string($IP_ADDRESS);
	$mysqli->query("DELETE FROM ".$dbprefix."app_captcha WHERE appcomponent_id = '".$appCompInfo['appcomponent_id']."' AND ipaddress = '".$filterIP."'");
	
	// Create the image
	$im = imagecreatetruecolor(440, 90);
	
	
	// Colors
	$black = imagecolorallocate($im, 9, 9, 9);
	$blackAlpha = imagecolorallocatealpha($im, 9, 9, 9,25);
	$redAlpha = imagecolorallocatealpha($im, 255, 0, 0, 15);
	$white = imagecolorallocate($im, 255, 255, 255);
	
	imagefilledrectangle($im, 0, 0, 439, 89, $white);
	
	
	// Generate Captcha Text
	
	$randString = md5(uniqid("", true));
	$randNum = rand(0,22);
	
	$arrABC = range("a", "z");
	shuffle($arrABC);
	$captcha = substr($randString.$randString, $randNum, 8);
	
	$arrCaptcha = str_split($captcha);
	$finalCaptchaText = "";
	
	$distortFont = "captcha-fonts/RFX_Splatz.ttf";
	$arrDistort = range("a", "i");
	$counter = 0;
	foreach($arrCaptcha as $value) {
	
		// Font Locations
		$arrFonts = array();
		$arrFonts[0] = "captcha-fonts/AnonymousClippings.ttf";
		$arrFonts[1] = "captcha-fonts/Pulse_virgin.ttf";
		$arrFonts[2] = "captcha-fonts/Staubiges_Verg.ttf";
		$arrFonts[3] = "captcha-fonts/Woodcutter_Anonymous.ttf";
		
		
		$char = $value;
	
		if(is_numeric($value) && rand(1, 10) > 2) {
			$char = (rand(1,10) > 5) ? strtoupper($arrABC[$value]) : $value;
		}
		elseif(!is_numeric($value) && rand(1, 10) > 5) {
			$char = strtoupper($value);
		}
		
		if(is_numeric($char)) {
			unset($arrFonts[1]);
		
			if($char == 0) {
				unset($arrFonts[0]);	
			}
			elseif($char == 3) {
				unset($arrFonts[3]);	
			}
			elseif($char == 9) {
				unset($arrFonts[2]);	
			}
			
		}
		elseif(strtolower($char) == "s") {
			unset($arrFonts[3]);
		}
		
		shuffle($arrFonts);
		
		
		$randFontNum = rand(0,(count($arrFonts)-1));
		$randFont = $arrFonts[$randFontNum];
		
		$randNum2 = rand(0,8);
		
		if($appComponentObj->get_info("componenttype") == "captchaextra") {
			$xCoord = ($counter == 0) ? 10 : (55*$counter);
			imagettftext($im, 70, rand(0,20), $xCoord, 80, $blackAlpha, $distortFont, $arrDistort[$randNum2]);
		}
		
		$xCoord = ($counter == 0) ? 10 : (55*$counter);
		
		imagettftext($im, 50, rand(-5,5), $xCoord, 70, $black, $randFont, $char);
		$counter++;
		$finalCaptchaText .= $char;
	}
	
	$captchaObj->addNew(array("appcomponent_id", "ipaddress", "captchatext"), array($appCompInfo['appcomponent_id'], $IP_ADDRESS, strtolower($finalCaptchaText)));
	imagepng($im);
	imagedestroy($im);

}


?>
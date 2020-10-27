<?php

include($prevFolder."themes/include_header.php");
include($prevFolder."themes/destiny/destinymenu.php");
$themeMenusObj = new DestinyMenu($mysqli);

$btThemeObj->setThemeName("Destiny");

$btThemeObj->menusObj = $themeMenusObj;
$btThemeObj->addHeadItem("destinyjs", "<script type='text/javascript' src='".MAIN_ROOT."themes/destiny/destiny.js'></script>");
$btThemeObj->addHeadItem("google-font", "<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<?php $btThemeObj->displayHead(); ?>
	</head>
<body>


	<div class='wrapper'>	
		
	
		<div class='headerDiv'>
			<div class='logoDiv'>
				<a href='<?php echo MAIN_ROOT; ?>'><img src='<?php echo $websiteInfo['logourl']; ?>'></a>
			</div>
		</div>
	
		<div class='bodyDiv'>
		
			<div class='leftMenuDiv'><?php $themeMenusObj->displayMenu(0); ?></div>
			<div class='rightMenuDiv'><?php $themeMenusObj->displayMenu(1); ?></div>
			<div class='centerContentDiv'>
			<?php include(BASE_DIRECTORY."include/clocks.php"); ?>
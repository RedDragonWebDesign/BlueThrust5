<?php

include($prevFolder."themes/include_header.php");
include($prevFolder."themes/ribbonwow/ribbonwowmenu.php");
$themeMenusObj = new RibbonWoWMenu($mysqli);

$btThemeObj->setThemeName("Ribbon WoW");

$btThemeObj->menusObj = $themeMenusObj;
$btThemeObj->updateHeadItem("jquery-ui-css", "<link rel='stylesheet' type='text/css' href='".MAIN_ROOT."themes/ribbonwow/jqueryui/jquery-ui-1.9.2.custom.min.css'>");
$btThemeObj->addHeadItem("google-font", "<link href='https://fonts.googleapis.com/css?family=Asul' rel='stylesheet' type='text/css'>");


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<?php $btThemeObj->displayHead(); ?>
</head>
<body>

	<div class='wrapper'>
		<div class='headerDiv'>
		
			<a href='<?php echo $MAIN_ROOT; ?>'><img src='<?php echo $websiteInfo['logourl']; ?>'></a>
		
		</div>
	
		<div class='topMenuDiv'>
			<div class='topMenuLeftGradient'></div>
			<div class='topMenuRightGradient'></div>
			<div class='topMenuLeftLineGradient'></div>
			<div class='topMenuRightLineGradient'></div>
			<div class='topMenuBottomLeft'></div>
			<div class='topMenuBottomRight'></div>
			<div class='topMenuTopDashes'></div>
			<div class='topMenuBottomDashes'></div>
		
			<div class='topMenuItemsDiv'>
				
				<?php $themeMenusObj->displayMenu(2); ?>
			
			
			</div>
		
		
		</div>

		<div class='bodyDiv'>
		
			<div class='bodyContentDiv'>
			
			
				<div class='leftMenuDiv'>
					<?php $themeMenusObj->displayMenu(0); ?>
				</div>
				<div class='rightMenuDiv'>
					
					<?php $themeMenusObj->displayMenu(1); ?>
					
				</div>
				<div class='centerContentDiv'>
				<?php include(BASE_DIRECTORY."include/clocks.php"); ?>
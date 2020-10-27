</div>
		<div style='clear: both'></div>
		</div>
		
		<div class='push'></div>
	</div>
	
	<div class='footerDiv'>
		
		<?php $btThemeObj->displayCopyright(); ?>
		
		<div class='bottomLeftCharacter'></div>
		<div class='bottomRightCharacter'></div>
		<div class='bottomBG'></div>
	</div>
	

	<div id='destinyBG'>
	
		<div class='destinyBGBottomLeft'>
			<div class='destinyBGBottomLeftIndent'></div>
		</div>
		<div class='destinyBGBottomRight'>
			<div class='destinyBGBottomRightIndent'></div>
		</div>
	</div>
	
	<div class='topBarBG'>
	
		<div class='topBar'>
		
			<div class='destinyLogo'></div>
			<div id='logoSmall'><img src='<?php echo $MAIN_ROOT; ?>themes/destiny/images/logo-small.png'></div>
			
			<?php $themeMenusObj->displayMenu(2); ?>			
			
		</div>
		
	</div>
	<div id='topBarBGImg'></div>
	
	<?php require_once(BASE_DIRECTORY."themes/include_footer.php"); ?>
</body>
</html>
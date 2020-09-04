<?php
	
	if(!defined("MAIN_ROOT")) { exit(); }

?>


<div class='donatorDiv dottedLine<?php echo $css; ?>'>

	<div class='donatorPic'>
	
		<?php echo $member->getProfilePic(); ?>
	
	</div>
	<div class='donatorInfo'>
	
		<?php echo $dispDonatorInfo['name']; ?>
		<br>
		<span class='donatorAmount'><?php echo $arrSymbols['left'].number_format($dispDonatorInfo['amount'],2).$arrSymbols['right']; ?></span>
		<br>
		<span class='main'><br><?php echo $dispDonatorInfo['lastdonation']."<span class='donateDate'>".$dispDonatorInfo['lastdate']; ?></span></span>
	</div>

</div>
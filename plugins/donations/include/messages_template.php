<?php

	if(!defined("MAIN_ROOT")) { exit(); }

?>

<div class='donationMessage dottedLine<?php echo $css; ?>'>

	<div class='donationMessagePic'>
	
		<?php echo $member->getProfilePic(); ?>
	
	</div>

	<div class='donationMessageText'>
	
		<?php 
		
			echo 
				
				nl2br(parseBBCode($donationInfo['message'])). "
			
				<br><br>
				
				<div class='tinyFont'><span class='donatorAmount'>".$this->formatAmount($donationInfo['amount'])."</span> donated by ".$dispDonatorName."
				<br>
				".getPreciseTime($donationInfo['datesent'])."</div>
			
			";
			
		?>
	
	</div>

</div>
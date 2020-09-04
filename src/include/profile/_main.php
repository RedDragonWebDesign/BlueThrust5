<?php 
	if(!defined("SHOW_PROFILE_MAIN")) {
		exit();	
	}
?>

	<div class='formTitle' style='text-align: center'>User Information</div>
	
	<table class='profileTable' style='border-top-width: 0px'>
		<tr>
			<td class='profileLabel alternateBGColor'>Username:</td>
			<td class='main' style='padding-left: 10px'><?php echo $memberInfo['username']; ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor'>Rank:</td>
			<td class='main' style='padding-left: 10px'><?php echo $rankInfo['name']; ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor'>Recruited By:</td>
			<td class='main' style='padding-left: 10px'><?php echo $dispRecruiter; ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor'>Recruits: <?php echo $totalRecruits; ?></td>
			<td class='main' style='padding-left: 10px'><marquee scrollamount='3'><?php echo $dispRecruits; ?></marquee></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor'>Last Log In:</td>
			<td class='main' style='padding-left: 10px'><?php echo $dispLastLogin; ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor'>Times Logged In:</td>
			<td class='main' style='padding-left: 10px'><?php echo $memberInfo['timesloggedin']; ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor'>Last Promotion:</td>
			<td class='main' style='padding-left: 10px'><?php echo $dispLastPromotion; ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor'>Last Demotion:</td>
			<td class='main' style='padding-left: 10px'><?php echo $dispLastDemotion; ?></td>
		</tr>
		<tr>
			<td class='profileLabel alternateBGColor'>Days In Clan:</td>
			<td class='main' style='padding-left: 10px'><?php echo $dispDaysInClan; ?></td>
		</tr>
	</table>

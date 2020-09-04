<?php


if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php" || !isset($_GET['cID'])) {

	include_once("../../../../_setup.php");
	include_once("../../../../classes/member.php");
	include_once("../../../../classes/customform.php");

	// Start Page

	$consoleObj = new ConsoleOption($mysqli);

	$cID = $consoleObj->findConsoleIDByName("View Custom Form Submissions");
	$consoleObj->select($cID);
	$consoleInfo = $consoleObj->get_info_filtered();

	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);

	$customFormPageObj = new CustomForm($mysqli);

	
	
	// Check Login
	if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $customFormPageObj->select($_POST['cfID'])) {
		$memberInfo = $member->get_info();
	}
	else {
		exit();
	}

}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($consoleObj->findConsoleIDByName("View Custom Form Submissions"));
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$arrSubmissions = $customFormPageObj->getSubmissions();


foreach($arrSubmissions as $submissionID) {
	
	$customFormPageObj->objSubmission->select($submissionID);
	if($customFormPageObj->objSubmission->get_info("seenstatus") == 0) {
		$customFormPageObj->objSubmission->update(array("seenstatus"), array("1"));
	}
	
	$arrSubmissionDetail = $customFormPageObj->getSubmissionDetail($submissionID);
	
	echo "
	
	
		<div class='formDiv' style='margin-bottom: 30px'>
	
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Date Submitted:</td><td class='main'>".getPreciseTime($arrSubmissionDetail['submitdate'])."</td>
				</tr>
				<tr>
					<td class='formLabel'>IP Address:</td><td class='main'>".$arrSubmissionDetail['ipaddress']."</td>
				</tr>
		";
	
	
	foreach($arrSubmissionDetail['components'] as $componentID => $formValue) {
		
		$customFormPageObj->objComponent->select($componentID);
		$componentInfo = $customFormPageObj->objComponent->get_info_filtered();

		if($componentInfo['componenttype'] != "separator") {
			echo "
				<tr>
					<td class='formLabel' valign='top'>".$componentInfo['name'].":</td>";
			if(!is_array($formValue)) {
			
				echo "
					<td class='main' valign='top'>".nl2br($formValue)."</td>
				</tr>
				";
				
			}
			else {
				
				echo "
					<td class='main' valign='top'>
					";
				$counter = 1;
				foreach($formValue as $multiValue) {
	
					echo $counter.". ".nl2br($multiValue)."<br>";
					
					$counter++;
					
				}
				
				echo "</td></tr>";
				
			}
		
		}
		else {
			
			echo "
				<tr>
					<td colspan='2' class='main'><br>
						<b>".$componentInfo['name']."</b>
					
						<div class='dottedLine' style='width: 90%; padding-top: 3px; margin-bottom: 5px'></div>
					</td>
				</tr>
			
			";
			
		}
		
	}
	
	echo "
		<tr>
			<td colspan='2' align='right' class='main'><br><br>
				<a href='javascript:void(0)' onclick='deleteSubmission(".$submissionID.")'>Delete</a>
			</td>
		</tr>	
	</table></div>";
	
}


if(count($arrSubmissions) == 0) {
	
	echo "
	
		<div class='shadedBox main' style='width: 35%; margin-left: auto; margin-right: auto'>
		
			<p align='center'>
				<i>
					There are no submissions for this form!<br><br>
					<a href='".$MAIN_ROOT."members/console.php?cID=".$cID."'>Go Back</a>
				</i>
			</p>
		
		</div>
	
	";
	
}


?>
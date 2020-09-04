<?php


	$donationInfo = $campaignObj->donationObj->get_info_filtered();
	$campaignObj->select($donationInfo['donationcampaign_id']);
	$campaignInfo = $campaignObj->get_info_filtered();
	
	
	
	$breadcrumbObj->popCrumb();
	$breadcrumbObj->addCrumb($consoleTitle, MAIN_ROOT."members/console.php?cID=".$cID);
	$breadcrumbObj->addCrumb("Donation Log: ".$campaignInfo['title'], MAIN_ROOT."members/console.php?cID=".$_GET['cID']."&campaignID=".$campaignInfo['donationcampaign_id']."&p=log");
	$breadcrumbObj->addCrumb("Donation Details");
	$breadcrumbObj->setTitle("Donation Details");
	
	$breadcrumbObj->updateBreadcrumb();

	include("donationdetails_form.php");
	
	
?>


<script type='text/javascript'>
	$(document).ready(function() {
		$('#consoleBottomBackButton').attr("href", "<?php echo MAIN_ROOT."members/console.php?cID=".$_GET['cID']; ?>&campaignID=<?php echo $campaignInfo['donationcampaign_id']; ?>&p=log");
		$('#consoleTopBackButton').attr("href", "<?php echo MAIN_ROOT."members/console.php?cID=".$_GET['cID']; ?>&campaignID=<?php echo $campaignInfo['donationcampaign_id']; ?>&p=log");
	});
</script>
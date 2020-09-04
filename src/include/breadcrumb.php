<?php 

	$hooksObj->run("breadcrumb");

?>

<div class='breadCrumbTitle' id='breadCrumbTitle'><?php echo $breadcrumbObj->getTitle(); ?></div>
<div class='breadCrumb' id='breadCrumb' style='padding-top: 0px; margin-top: 0px'>
	<?php echo $breadcrumbObj->getBreadcrumb(); ?>
</div>

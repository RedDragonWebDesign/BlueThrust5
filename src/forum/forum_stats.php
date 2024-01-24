<?php

	if(!defined("MAIN_ROOT")) { exit(); }


	$totalPostsQuery = "SELECT COUNT(*) as totalPosts FROM ".$dbprefix."forum_post";
	$totalPostsResult = $mysqli->query($totalPostsQuery);
	$totalPostRow = $totalPostsResult->fetch_assoc();

	$totalPosts = number_format($totalPostRow['totalPosts'],0);


	$totalTopicQuery = "SELECT COUNT(*) as totalTopics FROM ".$dbprefix."forum_topic";
	$totalTopicResult = $mysqli->query($totalTopicQuery);
	$totalTopicRow = $totalTopicResult->fetch_assoc();

	$totalTopics = number_format($totalTopicRow['totalTopics'],0);

	// Find latest post
	$dispLatestPost = "";
	if($arrLatestPostInfo['id'] != 0) {

		$boardObj->objPost->select($arrLatestPostInfo['id']);
		$postInfo = $boardObj->objPost->get_info_filtered();
		$topicInfo = $boardObj->objPost->getTopicInfo(true);

		$postMemberObj->select($postInfo['member_id']);

		$postLink = $boardObj->objPost->getLink();

		$dispLatestPost = "<br><b>Latest Post:</b> <a href='".$postLink."'>".$topicInfo['title']."</a> by ".$postMemberObj->getMemberLink()."<br>";

	}

?>


<div class='formDiv'>
	<span class='largeFont'><b>Forum Stats:</b></span>
	
	<p style='padding-left: 10px; position: relative'>
		<b>Total Posts:</b> <?php echo $totalPosts; ?>, <b>Total Topics:</b> <?php echo $totalTopics; ?><br>
	
		<?php echo $dispLatestPost; ?>
	
		<br>
		<b><a href='<?php echo MAIN_ROOT; ?>forum/recent.php'>View Most Recent Posts</a></b> - <b><a href='<?php echo MAIN_ROOT; ?>forum/unread.php'>View Unread Posts</a></b>

	
	</p>
	
</div>
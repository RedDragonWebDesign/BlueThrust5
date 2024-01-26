<?php

if (!defined("POSTNEWS_FORM")) {
	exit();
}

$newsTextbox = $member->hasAccess($newsObj->getHTMLNewsConsole()) ? "richtextbox" : "textarea";


$i = 1;
$arrComponents = [
	"newstype" => [
		"type" => "select",
		"display_name" => "News Type",
		"options" => [1 => "Public", 2 => "Private"],
		"validate" => ["RESTRICT_TO_OPTIONS"],
		"sortorder" => $i++,
		"db_name" => "newstype",
		"attributes" => ["class" => "textBox formInput", "id" => "newsType", "onchange" => "updateTypeDesc()"],
		"html" => "<div class='tinyFont formInput' id='typeDesc' style='vertical-align: bottom; padding-left: 10px; padding-bottom: 5px'></div>"
	],
	"pintohp" => [
		"type" => "checkbox",
		"display_name" => "Pin to Homepage",
		"tooltip" => "Pinning a news post to the homepage will show the post under the Announcements section, instead of the Latest News section.",
		"db_name" => "hpsticky",
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput"],
		"options" => [1 => ""]
	],
	"subject" => [
		"type" => "text",
		"display_name" => "Subject",
		"sortorder" => $i++,
		"attributes" => ["class" => "textBox formInput", "style" => "width: 35%"],
		"db_name" => "postsubject"
	],
	"newspost" => [
		"type" => $newsTextbox,
		"display_name" => "Message",
		"sortorder" => $i++,
		"db_name" => "newspost",
		"attributes" => ["class" => "textBox formInput", "id" => "newsPost", "style" => "width: 100%", "rows" => 18],
		"validate" => ["NOT_BLANK", "formFilterNewsPost"]
	],
	"submit" => [
		"type" => "submit",
		"sortorder" => $i++,
		"attributes" => ["class" => "submitButton formSubmitButton"],
		"value" => "Post News"
	]

];



$setupFormArgs = [
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"description" => "Use the form below to post news.",
	"saveObject" => $newsObj,
	"saveMessage" => "Successfully Posted News!",
	"saveType" => "add",
	"attributes" => ["action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"],
	"saveAdditional" => ["dateposted" => time(), "member_id" => $memberInfo['member_id']]
];


echo "		
		<script type='text/javascript'>

			function updateTypeDesc() {
				$(document).ready(function() {
					$('#typeDesc').hide();
					if($('#newsType').val() == \"1\") {
						$('#typeDesc').html('<i>Share this news for the world to see!</i>');
					}
					else {
						$('#typeDesc').html('<i>Only show this post to members!</i>');
					}
					$('#typeDesc').fadeIn(250);
				
				});
			}
			
			updateTypeDesc();
		</script>
		
	";


function formFilterNewsPost() {
	global $member, $newsObj;

	if ($member->hasAccess($newsObj->getHTMLNewsConsole())) {
		$_POST['newspost'] = str_replace("<?", "", $_POST['newspost']);
		$_POST['newspost'] = str_replace("?>", "", $_POST['newspost']);
		$_POST['newspost'] = str_replace("<script", "", $_POST['newspost']);
		$_POST['newspost'] = str_replace("</script>", "", $_POST['newspost']);
	}
}

<?php

if (!defined("HPIMAGE_FORM")) {
	exit();
}

$imageOrderObj = new ImageSlider($mysqli);
$imageOptions = [];
$result = $mysqli->query("SELECT * FROM ".$dbprefix."imageslider ORDER BY ordernum DESC");
while ($row = $result->fetch_assoc()) {
	$imageOptions[$row['imageslider_id']] = $row['name'];
}

if (count($imageOptions) == 0) {
	$imageOptions['first'] = "(first image)";
}

$i=1;
$arrComponents = [
		"imageinfo" => [
			"type" => "section",
			"option" => ["section_title" => "Image Information"],
			"sortorder" => $i++
		],
		"imagename" => [
			"type" => "text",
			"tooltip" => "This will only be used to identify the image when managing home page images.",
			"attributes" => ["class" => "formInput textBox"],
			"sortorder" => $i++,
			"display_name" => "Name",
			"db_name" => "name"
		],
		"imageupload" => [
			"type" => "file",
			"display_name" => "Image",
			"options" => ["file_types" => [".gif", ".png", ".jpg", ".bmp"], "file_prefix" => "hpimage_", "save_loc" => BASE_DIRECTORY."images/homepage/", "ext_length" => 4, "append_db_value" => "images/homepage/"],
			"sortorder" => $i++,
			"attributes" => ["class" => "textBox", "style" => "width: 100%"],
			"db_name" => "imageurl"
		],
		"displayorder" => [
			"type" => "beforeafter",
			"sortorder" => $i++,
			"attributes" => ["class" => "textBox"],
			"display_name" => "Display Order",
			"options" => $imageOptions,
			"validate" => [["name" => "VALIDATE_ORDER", "orderObject" => $imageOrderObj]],
			"db_name" => "ordernum"
		],
		"displaystyle" => [
			"type" => "select",
			"sortorder" => $i++,
			"display_name" => "Display Style",
			"attributes" => ["class" => "textBox formInput"],
			"options" => ["fill" => "Fill", "stretch" => "Stretch"],
			"db_name" => "fillstretch",
			"validate" => ["RESTRICT_TO_OPTIONS"]
		],
		"messageinfosection" => [
			"type" => "section",
			"options" => ["section_title" => "Message Information", "section_description" => "Leave this section blank to just display the image."],
			"sortorder" => $i++
		],
		"autofill" => [
			"type" => "select",
			"sortorder" => $i++,
			"display_name" => "Auto-fill",
			"attributes" => ["class" => "formInput textBox", "id" => "autofill"],
			"options" => [
						"select" => "Select",
						"news" => "News Post",
						"tournament" => "Tournament",
						"event" => "Event",
						"custom" => "Custom"
					]
		],
		"autofillid" => [
			"type" => "custom",
			"sortorder" => $i++,
			"html" => "
				<label class='formLabel' style='display: inline-block'></label>
				<select id='autofillID' class='textBox formInput' disabled='disabled'><option value''>Select</option></select>
				"
		],
		"messagetitle" => [
			"type" => "text",
			"sortorder" => $i++,
			"display_name" => "Title",
			"attributes" => ["class" => "textBox formInput bigTextBox", "id" => "imageTitle"],
			"db_name" => "messagetitle"
		],
		"messagetext" => [
			"type" => "textarea",
			"sortorder" => $i++,
			"attributes" => ["class" => "textBox formInput bigTextBox", "rows" => 4, "id" => "imageMessage"],
			"db_name" => "message",
			"display_name" => "Message"

		],
		"messagelink" => [
			"type" => "text",
			"sortorder" => $i++,
			"display_name" => "Link",
			"attributes" => ["class" => "textBox formInput bigTextBox", "id" => "linkURL"],
			"db_name" => "link"
		],
		"linktarget" => [
			"type" => "select",
			"sortorder" => $i++,
			"display_name" => "Link Target",
			"attributes" => ["class" => "textBox formInput"],
			"db_name" => "linktarget",
			"options" => ["" => "Same Window", "_blank" => "New Window"]
		],
		"showwhen" => [
			"type" => "select",
			"sortorder" => $i++,
			"attributes" => ["class" => "textBox formInput"],
			"db_name" => "membersonly",
			"options" => ["Always", "Logged In", "Logged Out"],
			"display_name" => "Show When"
		],
		"submit" => [
			"type" => "submit",
			"sortorder" => $i++,
			"value" => "Add Image",
			"attributes" => ["class" => "submitButton formSubmitButton"]
		]

	];

$setupFormArgs = [
			"name" => "console-".$cID,
			"components" => $arrComponents,
			"description" => "Use the form below to add an image to the home page image slider.",
			"saveObject" => $imageSliderObj,
			"saveMessage" => "Successfully Added New Home Page Image: <b>".filterText($_POST['name'])."</b>!",
			"saveType" => "add",
			"attributes" => ["action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"],
			"beforeAfter" => true
	];



echo "
		<div id='autoFillInfo' style='display: none'></div>
		<script type='text/javascript'>
		
			$(document).ready(function() {
				
					$('#autofill').change(function() {
					
						if($('#autofill').val() != 'select' && $('#autofill').val() != 'custom') {
							$('#autofillID').removeAttr('disabled');
							
							$.post('".$MAIN_ROOT."members/include/news/include/imageslider_getattachtype.php', { attachtype: $('#autofill').val() }, function(data) {
								$('#autofillID').html(data);
							});
							
						}
						else {
							$('#autofillID').html(\"<option value=''>Select</option>\");
							$('#autofillID').attr('disabled', 'disabled');
						}
					
					});
					
					
					$('#autofillID').change(function() {
					
						if(($('#autofill').val() != 'select' && $('#autofill').val() != 'custom') && $('#autofillID').val() != '') {
						
						
							$.post('".$MAIN_ROOT."members/include/news/include/imageslider_getattachinfo.php', { attachtype: $('#autofill').val(), attachID: $('#autofillID').val() }, function(data) {
							
								$('#autoFillInfo').html(data);
							
							});
						
						
						}
					
					});
				
				});
		
		</script>
	";

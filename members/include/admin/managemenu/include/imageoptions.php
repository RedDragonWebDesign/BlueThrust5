<?php

// Image Options
$imageOptionComponents = array(
	"imagefile_image" => array(
		"type" => "file",
		"options" => array("file_types" => array(".gif", ".png", ".jpg", ".bmp"), "file_prefix" => "menuitem_", "save_loc" => "../images/menu/", "ext_length" => 4, "append_db_value" => "images/menu/"),
		"sortorder" => $i++,
		"attributes" => array("class" => "textBox", "style" => "width: 100%"),
		"display_name" => "Image"
	),
	"width_image" => array(
		"type" => "text",
		"sortorder" => $i++,
		"attributes" => array("class" => "textBox formInput", "style" => "width: 5%"),
		"display_name" => "Width",
		"tooltip" => "Leave blank if you want to use the default image width.",
		"html" => "<div class='formInput' style='vertical-align: bottom; padding-left: 5px; padding-bottom: 2px'><i>px</i></div>"
	),
	"height_image" => array(
		"type" => "text",
		"sortorder" => $i++,
		"attributes" => array("class" => "textBox formInput", "style" => "width: 5%"),
		"display_name" => "Height",
		"tooltip" => "Leave blank if you want to use the default image height.",
		"html" => "<div class='formInput' style='vertical-align: bottom; padding-left: 5px; padding-bottom: 2px'><i>px</i></div>"
	),
	"linkurl_image" => array(
		"type" => "text",
		"sortorder" => $i++,
		"attributes" => array("class" => "textBox formInput", "style" => "width: 30%"),
		"display_name" => "Link URL",
		"tooltip" => "Leave blank if you don't want your image linking to anything."
	),
	"targetwindow_image" => array(
		"type" => "select",
		"display_name" => "Target Window",
		"sortorder" => $i++,
		"attributes" => array("class" => "textBox formInput"),
		"options" => array("" => "Same Window", "_blank" => "New Window")
	),
	"textalign_image" => array(
		"type" => "select",
		"display_name" => "Image Align",
		"attributes" => array("class" => "textBox formInput"),
		"options" => $textAlignOptions,
		"sortorder" => $i++
	)
);
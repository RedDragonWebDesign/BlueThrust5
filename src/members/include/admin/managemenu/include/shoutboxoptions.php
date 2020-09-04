<?php

// Shoutbox Options
$shoutboxOptionComponents = array(
	"width_shoutbox" => array(
		"type" => "text",
		"attributes" => array("class" => "textBox formInput", "style" => "width: 5%; margin-right: 10px"),
		"html" => "<select class='formInput textBox' name='widthunit_shoutbox' id='shoutBoxWidthPercent'><option value='0'>pixels</option><option value='1'".$shoutboxWidthPercentSelected.">percent</option></select>",
		"display_name" => "Width",
		"sortorder" => $i++
	),
	"height_shoutbox" => array(
		"type" => "text",
		"attributes" => array("class" => "textBox formInput", "style" => "width: 5%; margin-right: 10px"),
		"html" => "<select class='formInput textBox' name='heightunit_shoutbox'><option value='0'>pixels</option><option value='1'".$shoutboxHeightPercentSelected.">percent</option></select>",
		"display_name" => "Height",
		"sortorder" => $i++
	),
	"textboxwidth_shoutbox" => array(
		"type" => "text",
		"attributes" => array("class" => "textBox formInput", "style" => "width: 5%; margin-right: 10px"),
		"html" => "<div class='formInput'  id='shoutBoxTextBoxWidth'>pixels</div>",
		"display_name" => "Textbox Width",
		"sortorder" => $i++
	)

);

?>
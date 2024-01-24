<?php

/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */
$prevFolder = "../../../../";
require_once("../../../../_setup.php");
require_once("../../../../classes/member.php");
require_once("../../../../classes/rank.php");
require_once("../../../../classes/consoleoption.php");
require_once("../../../../classes/menuitem.php");
require_once("../../../../classes/menucategory.php");


$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$menuItemObj = new MenuItem($mysqli);
$menuCatObj = new MenuCategory($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Menu Items");
$consoleObj->select($cID);

if ($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if ($member->hasAccess($consoleObj) && $menuItemObj->select($_POST['itemID'])) {



		$menuItemObj->move($_POST['iDir']);

		require_once("include/menuitemlist.php");
		/*
		$menuItemObj->select($_POST['itemID']);
		$menuCatObj->select($menuItemObj->get_info("menucategory_id"));

		echo "

			<script type='text/javascript'>

				$(document).ready(function() {

					$.post('".$MAIN_ROOT."themes/_refreshmenus.php', { refreshSectionID: '".$menuCatObj->get_info("section")."' }, function(data) {
                        $('#menuSection_".$menuCatObj->get_info("section")."').html(data);
					});

				});

			</script>

		";
		*/

	}


}
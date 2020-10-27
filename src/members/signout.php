<?php

/*
 * Bluethrust Clan Scripts v4
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */

$prevFolder = "../";
include("../_setup.php");

$memberObj = new Member($mysqli);

if($memberObj->select($_SESSION['btUsername']) && $memberObj->authorizeLogin($_SESSION['btPassword'])) {
	$memberObj->update(array("loggedin"), array(0));
}

if(isset($_COOKIE['btUsername']) || isset($_COOKIE['btPassword'])) {
	setcookie("btUsername", "", time()-3600, $MAIN_ROOT);
	setcookie("btPassword", "", time()-3600, $MAIN_ROOT);
}

$_SESSION['btPassword'] = "";
$_SESSION['btUsername'] = "";
$_SESSION['btRememberMe'] = "";



echo "
<script type='text/javascript'>
window.location = '".$MAIN_ROOT."';
</script>
";


?>
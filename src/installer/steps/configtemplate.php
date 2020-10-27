<?php

$filterConfigPass = str_replace('"', '\"', $_POST['dbpass']);
$filterConfigPass = str_replace('$', '\$', $filterConfigPass);

$filterConfigKey = str_replace('"', '\"', $_POST['adminkey']);
$filterConfigKey = str_replace('$', '\$', $filterConfigKey);

$configInput = "<?php

   /*
	* Bluethrust Clan Scripts v4
	* Copyright ".date("Y")."
	*
	* Author: Bluethrust Web Development
	* E-mail: support@bluethrust.com
	* Website: http://www.bluethrust.com
	*
	* License: http://www.bluethrust.com/license.php
	*
	*/
	
	\$dbhost = \"".$_POST['dbhost']."\";
	\$dbuser = \"".$_POST['dbuser']."\";
	\$dbpass = \"".$filterConfigPass."\";
	\$dbname = \"".$_POST['dbname']."\";
	
	\$dbprefix = \"".$_POST['tableprefix']."\";
	
	\$MAIN_ROOT = \"".$setMainRoot."\";
	\$BASE_DIRECTORY = \"".$setDocumentRoot."\";
	
	\$ADMIN_KEY = \"".$filterConfigKey."\"; // KEY FOR EXTRA SECURITY WHEN ADDING CONSOLE OPTION
	
	define(\"ADMIN_KEY\", \$ADMIN_KEY);

?>
";

?>
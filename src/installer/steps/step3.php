<?php

	if($_POST['step2submit']) {

		// Check Connection Again
		$mysqli = new btmysql($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpass'], $_POST['dbname']);
		$mysqli->set_tablePrefix($_POST['tableprefix']);

		if($mysqli->connect_errno !== 0) {
			$dispError = "
			&nbsp;&nbsp;<b>&middot;</b> Unable to connect to database!  Make sure you entered the correct information.<br><br>
			&nbsp;&nbsp;<b>MySQL Response:</b> ".$mysqli->connect_error."<br>";

			$countErrors++;
		}
		else {

			
			if($_POST['installType'] == 1) {
				// Fresh Install
			
				// Check Username
	
				if(trim($_POST['adminusername']) == "") {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;<b>&middot;</b> The admin username may not be blank.<br>";
				}
	
				// Check Password
	
				if($_POST['adminpassword'] != $_POST['adminpassword_repeat']) {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;<b>&middot;</b> Your passwords did not match.<br>";
				}
	
				if(strlen(trim($_POST['adminpassword'])) < 6) {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;<b>&middot;</b> The admin password must be at least 6 characters long.<br>";
				}
				
			}
			else {
				// Updating
				
				
				$member = new Member($mysqli);
				$member->select($_POST['adminusername']);
				
				
				if(!$member->authorizeLogin($_POST['adminpassword'], 1)) {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;<b>&middot;</b> The admin username/password combination was incorrect.<br>";
				}
				elseif($member->authorizeLogin($_POST['adminpassword'], 1) && $member->get_info("rank_id") != 1) {
					$countErrors++;
					$dispError .= "&nbsp;&nbsp;<b>&middot;</b> You entered incorrect admin login information.<br>";
				}
				
				
			}
	
			// Check Admin Key

			if(strlen(trim($_POST['adminkey'])) < 3) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;<b>&middot;</b> The admin key must be at least 3 characters long.<br>";
			}

			if($_POST['adminkey'] != $_POST['adminkey_repeat']) {
				$countErrors++;
				$dispError .= "&nbsp;&nbsp;<b>&middot;</b> Your admin keys did not match.<br>";
			}

			
			if($countErrors == 0) {

				echo "
					<table class='mainTable'>
						<tr>
							<td class='tdTitle'>
								Installing Bluethrust Clan Scripts v4
							</td>
						</tr>
						<tr>
							<td>
								Creating Config File...<br><br>
				";
				$url = $_SERVER['REQUEST_URI']; //returns the current URL
				$setMainRoot = str_replace("installer/index.php?step=3", "", $url);
				$setDocumentRoot = str_replace("installer/index.php", "", $_SERVER['SCRIPT_FILENAME']);
				include("steps/configtemplate.php");

				
				
				if(file_put_contents("../_config.php", $configInput)) {
					echo "Config File Created!<br><br>";
				}
				else {
					echo "Unable to populate config file!  You will have to manually create the config file.  Click <a href=''>HERE</a> to view instructions on setting it up.<br><br>";
				}


				echo "
					Preparing to create database...<br><br>
				";


				// Check if tables have already been installed.

				$result = $mysqli->query("SHOW TABLES");
				$arrTestTables = array();

				while($row = $result->fetch_array()) {
					$arrTestTables[] = $row[0];
				}

				$arrTableMatches = array();
				$countTableMatches = 0;
				foreach($arrTableNames as $tableName) {
					$tempTableName = $_POST['tableprefix'].$tableName;

					if(in_array($tempTableName, $arrTestTables)) {
						$countTableMatches++;
						$arrTableMatches[] = $tempTableName;
					}

				}


				if($_POST['installType'] == 2) {
					include("steps/backupinserts.php");	
				}
				
				$blnConvertWebsiteInfo = false;
				if($countTableMatches > 0) {
				
					// Check if using the old websiteinfo table
					$result = $mysqli->query("SELECT websiteinfo_id FROM ".$_POST['tableprefix']."websiteinfo");
					if($result->num_rows < 60) {
						$blnConvertWebsiteInfo = true;
					}
				
					include("steps/backupdb.php");

				}
				
				// Install New SQL
				
				if($_POST['installType'] == 1) {
					$fullSQL = file_get_contents("cs4.sql");			
				}
				else {
					$fullSQL = file_get_contents("cs4update.sql");
				}
				
				if($_POST['tableprefix'] != "") {
					$fullSQL = str_replace("CREATE TABLE IF NOT EXISTS `", "[SWAPCREATEWITHTABLEPREFIX]", $fullSQL);
					$fullSQL = str_replace("INSERT INTO `", "[SWAPINSERTWITHTABLEPREFIX]", $fullSQL);

					$fullSQL = str_replace("[SWAPCREATEWITHTABLEPREFIX]", "CREATE TABLE IF NOT EXISTS `".$_POST['tableprefix'], $fullSQL);
					$fullSQL = str_replace("[SWAPINSERTWITHTABLEPREFIX]", "INSERT INTO `".$_POST['tableprefix'], $fullSQL);

					$fullSQL = str_replace("ALTER TABLE `ipban`", "ALTER TABLE `".$_POST['tableprefix']."ipban`", $fullSQL);
					
				}

			
				if($_POST['installType'] == 2) {
					// $oldInsertSSQL and $alterSQL --> from backupinserts.php
				
					$fullSQL .= $alterSQL;
					$fullSQL .= $oldInsertSQL;
				}
				
				

				if($mysqli->multi_query($fullSQL)) {


					do {
						if($result = $mysqli->store_result()) {
							$result->free();
						}
					}
					while($mysqli->next_result());
									

					echo "Successfully set up database!<br><br>";

					
					if($_POST['installType'] == 1) {
						// Generate New Salt
						$randomString = substr(md5(uniqid("", true)),0,22);
						$randomNum = rand(4,10);
						if($randomNum < 10) {
							$randomNum = "0".$randomNum;
						}
	
						$strSalt = "$2a$".$randomNum."$".$randomString;
	
	
						$encryptPassword = crypt($_POST['adminpassword'], $strSalt);
	
						$mysqli->query("INSERT INTO ".$_POST['tableprefix']."members (username, password, password2, rank_id, datejoined, lastlogin) VALUES ('".$_POST['adminusername']."', '".$encryptPassword."', '".$strSalt."', '1', '".time()."', '".time()."')");
					}
					else {
					
						if($blnConvertWebsiteInfo) {
							// Convert websiteinfo table for people updating
							define("CONVERT_WEBSITEINFO", true);
							include("steps/convertwebsiteinfo.php");
						}
						
						// Updating --> Check for all console options and categories
						
						$consoleCatObj = new ConsoleCategory($mysqli);
						$consoleOptionObj = new ConsoleOption($mysqli);
						
						// Checking Console Categories First
						$arrConsoleCategoryIDs = array();
						$arrCheckConsoleCategories = array();
						$result = $mysqli->query("SELECT * FROM ".$_POST['tableprefix']."consolecategory ORDER BY ordernum DESC");
						while($row = $result->fetch_assoc()) {
							$arrCheckConsoleCategories[] = $row['name'];
							
							if(in_array($row['name'], $arrConsoleCategories)) {
								$tempCatID = array_search($row['name'], $arrConsoleCategories);
								$arrConsoleCategoryIDs[$tempCatID] = $row['consolecategory_id'];
							}
							
						}
						
						$pmCatID = "";
						
						foreach($arrConsoleCategories as $consoleCategory) {
							if(!in_array($consoleCategory, $arrCheckConsoleCategories)) {
								$consoleCatObj->selectByOrder(1);
								$newOrderNum = $consoleCatObj->makeRoom("after");
							
								$consoleCatObj->addNew(array("name", "ordernum"), array($consoleCategory, $newOrderNum));
								$tempCatID = array_search($consoleCategory, $arrConsoleCategories);
								$arrConsoleCategoryIDs[$tempCatID] = $consoleCatObj->get_info("consolecategory_id");
								$consoleCatObj->resortOrder();
								
								if($consoleCategory == "Private Messages") {
									$pmCatID = $arrConsoleCategoryIDs[$tempCatID];
								}
								
							}
														
						}
						
						
						// Checking Console Options
						$arrColumns = array("consolecategory_id", "pagetitle", "filename", "sortnum", "defaultconsole", "hide", "sep");
						foreach($arrConsoleOptionNames as $key => $consoleOptionName) {
							$checkConsole = $consoleOptionObj->findConsoleIDByName($consoleOptionName);
							
							if($checkConsole === false) {
								$tempCatID = $arrConsoleCategoryIDs[$arrConsoleOptionInfo[$key]['category']];
								$consoleOptionObj->setCategoryKeyValue($tempCatID);
								$consoleOptionObj->resortOrder();
								
								$highestSortNum = $consoleOptionObj->getHighestSortNum();
																
								$newOrderNum = $highestSortNum+1;
								
								if($arrConsoleOptionInfo[$key]['addsep'] == "1") {
									$arrValues = array($tempCatID, "-separator-", "", ($newOrderNum), "1", "", "1");
									$consoleOptionObj->addNew($arrColumns, $arrValues);
									
									$newOrderNum++;
								}
								
								$arrValues = array($tempCatID, $consoleOptionName, $arrConsoleOptionInfo[$key]['filename'], $newOrderNum, "1", $arrConsoleOptionInfo[$key]['hide'], "");
								
								$consoleOptionObj->addNew($arrColumns, $arrValues);
																								
								$consoleOptionObj->resortOrder();
							}
							elseif($consoleOptionName == "Private Messages" && $checkConsole !== false && $pmCatID != "") {
								
								$consoleOptionObj->select($checkConsole);
								$consoleOptionObj->update(array("consolecategory_id", "sortnum"), array($pmCatID, 0));
								$consoleOptionObj->resortOrder();
								
							}
							
						}						
						
						
						// Check for valid theme
						$arrValidThemes = array();
						$themeOptions .= "";
						$websiteInfoObj = new WebsiteInfo($mysqli);
						$websiteInfoObj->select(1);
						$themeName = $websiteInfoObj->get_info("theme");
						
						// Add New Websiteinfo
						$websiteInfoObj->multiUpdate(array("default_timezone", "date_format", "display_date"), array("America/New_York", "l, F j, Y", "1"));
						
						
						$verifyTheme = file_exists("../themes/".$themeName."/themeinfo.xml");
						
						$_SESSION['btUsername'] = $member->get_info("username");
						$_SESSION['btPassword'] =  $member->get_info("password");
						
						
						if(!$verifyTheme) {
							
							$arrThemes = scandir("../themes");
							$themeOptions = "";
							
							
							foreach($arrThemes as $themeName) {
							
								$themeURL = "../themes/".$themeName;
							
								if(is_dir($themeURL) && $themeName != "." && $themeName != ".." && is_readable($themeURL."/THEMENAME.txt") && file_exists("../themes/".$themeName."/themeinfo.xml")) {
									$arrValidThemes[] = $themeName;
									$dispThemeName = file_get_contents($themeURL."/THEMENAME.txt");
									$themeOptions .= "<option value='".$themeName."'".$dispSelected.">".$dispThemeName."</option>";
								}
							}
							
							if(count($arrValidThemes) == 0) {
								$themeMessage = "You don't have any supported themes installed.  Please install an updated theme and re-run the updater.";								
								
								$jqDialogButton = "
								
									'OK': function() {
										$(this).dialog('close');
									}
								
								
								";
							
							}
							else {
								$themeMessage = "Your previous theme is no longer supported.  Please choose a new theme from the dropdown list below.<br><br><select id='theme' class='textBox'>".$themeOptions."</select>";
							
								$jqDialogButton = "
								
								
									'Choose': function() {/*
										$.post('steps/updatetheme.php', { themeName: $('#theme').val(), user: '".$_POST['adminusername']."', pass: '".$_POST['adminpassword']."' }, function(data) {
											$('#updateTheme').html(data);
										});*/
										$(this).dialog('close');
									}
								
								";
							
							}
							
							echo "
							
								<div id='themeError' style='display: none'>
							
									<p class='main' align='center'>
										".$themeMessage."
									</p>
									
								</div>
								
								<div id='updateTheme'></div>
								
								<script type='text/javascript'>
								
									$(document).ready(function() {
									
										$('#themeError').dialog({
										
											title: 'Theme Error',
											show: 'scale',
											width: 400,
											modal: true,
											resizable: false,
											zIndex: 99999,
											open: function(event, ui) { $('.ui-dialog-titlebar-close', $(this).parent()).hide(); },
											buttons: {
												".$jqDialogButton."
											}
										
										
										});
									
									});
								
								</script>
							
							";
							
						}	
						
					}
					
					file_put_contents("_installrunning.txt", "done");
					
					echo "
						You may now log in to the website using the information below:<br><br>
						<b>Username:</b> ".$_POST['adminusername']."<br>
						<b>Password:</b> ".$_POST['adminpassword']."<br><br><br>

						When you first browse your website, the system will attempt to delete the installer folder. If it fails, you will be notified and will need to do it manuallly.<br><br>

						<b><u>NOTE:</u></b> If you do not delete the install folder, your website will be at risk of being hacked.
						<br><br>
						<p align='center'>
							<a href='".$setMainRoot."'>View Your Website Now!</a>
						</p>
					";


				}
				else {
					echo "Unable to create database!";
				}

				echo "</td></tr></table>";
			}


			if($countErrors > 0) {

				$_POST['step2submit'] = false;

			}



		}


	}


	if(!$_POST['step2submit']) {

		$_POST['step1submit'] = true;

		include("step2.php");

	}



?>
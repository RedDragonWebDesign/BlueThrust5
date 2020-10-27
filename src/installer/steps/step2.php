<?php
	
	$countErrors = 0;
	if($_POST['step1submit']) {
		
		$mysqli = new btmysql($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpass'], $_POST['dbname']);
		
		if($mysqli->connect_errno !== 0) {
			$dispError .= "
				&nbsp;&nbsp;<b>&middot;</b> Unable to connect to database!  Make sure you entered the correct information.<br><br>
				&nbsp;&nbsp;<b>MySQL Response:</b> ".$mysqli->connect_error."<br>";

			$countErrors++;
		}
		
		if($countErrors == 0) {

			echo "
				<div class='pageTitle'>Step 2</div>
				";
			
			if($dispError != "") {
				echo "
				<div class='errorDiv'>
				<b>Unable to continue installation because of the following error:</b><br><br>
				".$dispError."
				</div>
				";
			}
			
			$selectUpdateInstall = "";
			if($_POST['installType'] == 2) {
				$selectUpdateInstall = " selected";	
			}
			
			echo "
				<form action='index.php?step=3' method='post'>
				<table class='mainTable'>
			
					<tr>
						<td class='tdTitle' colspan='2'>Database Information - Continued</td>
					</tr>
					<tr>
						<td colspan='2'>Below is your database information.  Please confirm again and if you would like, enter a table prefix.<br><br></td>
					</tr>
					<tr>
						<td class='tdLabel'>Database Name:</td>
						<td><input type='text' class='textBox' name='dbname' value='".$_POST['dbname']."'></td>
					</tr>
					<tr>
						<td class='tdLabel'>Database Username:</td>
						<td><input type='text' class='textBox' name='dbuser' value='".$_POST['dbuser']."'></td>
					</tr>
					<tr>
						<td class='tdLabel'>Database Password:</td>
						<td><input type='password' class='textBox' name='dbpass' value='".$_POST['dbpass']."'></td>
					</tr>
					<tr>
						<td class='tdLabel'>Database Host:</td>
						<td><input type='text' class='textBox' name='dbhost' value='".$_POST['dbhost']."'></td>
					</tr>
					<tr>
						<td class='tdLabel'>Install Type:</td>
						<td><select name='installType' class='textBox'><option value='1'>Fresh Install</option><option value='2'".$selectUpdateInstall.">Update</option></select></td>
					</tr>
					<tr>
						<td class='tdLabel'>Table Prefix: (optional)</td>
						<td><input type='text' class='textBox' value='".$_POST['tableprefix']."' name='tableprefix'></td>
					</tr>
					<tr>
						<td class='tdTitle' colspan='2'><br>Administrator Information</td>
					</tr>
					<tr>
						<td colspan='2'>
							In this section you will set the Administrator account settings.  The admin password must be at least 6 characters long.  The admin key must be at least 3 characters long.<br><br><b><u>NOTE:</u></b> If you are choosing to update instead of fresh install, enter the current admin username and password.<br><br>
						</td>
					</tr>
					<tr>
						<td class='tdLabel'>Admin Username:</td>
						<td><input type='text' name='adminusername' class='textBox'></td>
					</tr>
					<tr>
						<td class='tdLabel'>Admin Password:</td>
						<td><input type='password' name='adminpassword' class='textBox'></td>
					</tr>
					<tr>
						<td class='tdLabel'>Repeat Password:</td>
						<td><input type='password' name='adminpassword_repeat' class='textBox'></td>
					</tr>
					<tr>
						<td class='tdLabel'>Set Admin Key: <a href='javascript:void(0)' onmouseover=\"showToolTip('This is a separate password used for extra security.')\" onmouseout='hideToolTip()'>(?)</a></td>
						<td><input type='password' name='adminkey' class='textBox'></td>
					</tr>
					<tr>
						<td class='tdLabel'>Repeat Admin Key:</td>
						<td><input type='password' name='adminkey_repeat' class='textBox'></td>
					</tr>
					<tr>
						<td colspan='2' align='center'><br><br>
							<input type='submit' value='Go to Step 3' style='width: 125px' name='step2submit' class='submitButton'>
						</td>
					</tr>
					
					
				</table>
				</form>
			
			";


		}
		else {
			$_POST['step1submit'] = false;
		}
		
		
		
	}
	
	if(!$_POST['step1submit']) {
		
		include("step1.php");	
	}



?>
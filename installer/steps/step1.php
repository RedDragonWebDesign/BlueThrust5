<div class='pageTitle'>Step 1</div>
<?php
	if($countErrors > 0) {
		echo "
			<div class='errorDiv'>
				<b>Unable to continue installation because of the following error:</b><br><br>
				".$dispError."
			</div>
		";
	}

?>

<div id='newThemeNeeded' class='errorDiv' style='display: none'>
	<b>NOTE:</b> A major update to themes has been made in R15.  Please download a new copy of the theme you were using here: <a href='http://bluethrust.com/themes' target='_blank'>http://bluethrust.com/themes</a>.  
</div>

<form action='index.php?step=2' method='post'>
	<table class='mainTable'>			
		<tr>
			<td class='tdTitle' colspan='2'>Database Information</td>
		</tr>
		<tr>
			<td class='tdLabel'>Database Name:</td>
			<td><input type='text' class='textBox' name='dbname'></td>
		</tr>
		<tr>
			<td class='tdLabel'>Database Username:</td>
			<td><input type='text' class='textBox' name='dbuser'></td>
		</tr>
		<tr>
			<td class='tdLabel'>Database Password:</td>
			<td><input type='password' class='textBox' name='dbpass'></td>
		</tr>
		<tr>
			<td class='tdLabel'>Database Host:</td>
			<td><input type='text' class='textBox' name='dbhost'></td>
		</tr>
		<tr>
			<td class='tdLabel'>Install Type:</td>
			<td><select name='installType' id='installType' class='textBox'><option value='1'>Fresh Install</option><option value='2'>Update</option></select></td>
		</tr>
		<tr>
			<td colspan='2' align='center'><br><br>
				<input type='submit' name='step1submit' style='width: 125px' class='submitButton' value='Go to Step 2'>
			</td>
		</tr>
	</table>
</form>

<script type='text/javascript'>
	$(document).ready(function() {
		$('#installType').change(function() {
		
			if($(this).val() == "2") {
				$('#newThemeNeeded').show();
			}
			else {
				$('#newThemeNeeded').hide();
			}
	
		});
	});
</script>
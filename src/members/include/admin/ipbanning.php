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

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}

?>


<div class='formDiv'>

	<div class='errorDiv' style='display: none'>
		<strong>Unable to add new IP ban because the following errors occurred:</strong><br><br>
		<span id='displayErrorsDiv'></span>
	</div>

	<div class='main formTable' style='margin-top: 5px'>
		<div class='dottedLine'><b>New IP Ban:</b></div>
		
		<p style='padding-left: 5px; padding-top: 5px; margin: 0px'>
			Use the form below to IP ban users from accessing your website.  Use an asterik (*) as a wildcard to ban an entire range.
		</p>
	</div>
	
	<table class='formTable'>
		<tr>
			<td class='formLabel'>IP Address:</td>
			<td class='main'><input type='text' id='ipaddress' class='textBox'></td>
		</tr>
		<tr>
			<td class='formLabel' valign='top'>Ban Period:</td>
			<td class='main' valign='top'>
				<select id='banPeriod' class='textBox'><option value='60'>1 hour</option><option value='1440'>1 day</option><option value='10080'>1 week</option><option value='43200'>1 month</option><option value='525600'>1 year</option><option value='0'>Forever</option><option value='-1'>Custom</option></select>
				<div id='enterCustomAmountDiv' style='display: none; margin-top: 5px'>
					<input type='text' id='customBanPeriod' class='textBox' style='width: 10%'> <select id='customBanPeriodUnit' class='textBox'><option value='minute'>minutes</option><option value='hour'>hours</option><option value='day'>day</option><option value='week'>weeks</option><option value='month'>months</option><option value='year'>years</option></select>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan='2' align='center'><br>
				<input type='button' id='btnIPBan' class='submitButton' value='Ban'>
			</td>
	</table>
	
	<div class='main formTable' style='margin-top: 5px'>
		<div class='dottedLine'><b>Current IP Bans:</b></div>
	</div>
	<table class='formTable' style='border-spacing: 0px; width: 80%'>
		<tr>
			<td class='formTitle' style='width: 40%; border-right: 0px'>IP Address:</td>
			<td class='formTitle' style='width: 45%; border-right: 0px'>Expires:</td>
			<td class='formTitle' style='width: 15%; border-left: 0px'></td>
		</tr>
	</table>
	
	<div class='loadingSpiral' id='loadingSpiral'>
		<p align='center'>
			<img src='<?php echo $MAIN_ROOT; ?>themes/<?php echo $THEME; ?>/images/loading-spiral.gif'><br>Loading
		</p>
	</div>
	<div id='ipBanList'>
		<?php 
			define("SHOW_BANLIST", true);
			include("ipbanning/ipbanlist.php");
		?>
	</div>
	<br>
</div>
<div id='ipBanMessage' style='display: none'></div>

<script type='text/javascript'>

	$(document).ready(function() {

		$('#banPeriod').change(function() {
			if($(this).val() == "-1") {
				$('#enterCustomAmountDiv').show();
			}
			else {
				$('#enterCustomAmountDiv').hide();
			}
		});

		$('#btnIPBan').click(function() {

			var postData;
			$('.errorDiv').hide();
			
			if($('#banPeriod').val() == "-1") {

				postData = {

					ipaddress: $("#ipaddress").val(),
					customExp: 1,
					banLength: $('#customBanPeriod').val(),
					banLengthUnit: $('#customBanPeriodUnit').val()

				};
								
			}
			else {

				postData = { ipaddress: $('#ipaddress').val(), expTime: $('#banPeriod').val() };

			}
			
			$.post('<?php echo $MAIN_ROOT; ?>members/include/admin/ipbanning/addipban.php', postData, function(data) {

				jsonData = JSON.parse(data);

				if(jsonData['result'] == "success") {
					$('#ipBanMessage').html("<p class='main' align='center'>Successfully added new IP Ban!</p>");
					$('#ipBanMessage').dialog({
						title: 'Add IP Ban',
						zIndex: 99999,
						modal: true,
						show: 'scale',
						width: 400,
						resizable: false,
						buttons: {
						
							'OK': function() {
								$(this).dialog('close');
							}
						
						}
					});


					$('#ipaddress').val("");
					$('#banPeriod').val("60");

					$('#banPeriod').change();

					reloadIPBanList();
					
				}
				else {
					var dispErrors = "<ul>";
					for(var i in jsonData['errors']) {
						dispErrors += "<li>"+jsonData['errors'][i]+"</li>";
					}
					dispErrors += "</ul>";

					$('#displayErrorsDiv').html(dispErrors);
					$('.errorDiv').fadeIn(250);
					$('html, body').animate({ scrollTop: 0 });
				}
				
			});

		});


		$("body").delegate("img[data-deleteip]", "click", function() {

			$('#loadingSpiral').show();
			$('#ipBanList').hide();
			
			$.post('<?php echo $MAIN_ROOT; ?>members/include/admin/ipbanning/deleteipban.php', { ipaddress: $(this).attr('data-deleteip') }, function(data) {

				jsonData = JSON.parse(data);
				if(jsonData['result'] == "success") {
					reloadIPBanList();
				}
				else {
					$('#ipBanMessage').html("<p class='main' align='center'>Unable to save information to database! Please contact the website administrator.</p>");
					$('#ipBanMessage').dialog({
						title: 'Delete IP Ban',
						zIndex: 99999,
						modal: true,
						show: 'scale',
						width: 400,
						resizable: false,
						buttons: {
						
							'OK': function() {
								$(this).dialog('close');
							}
						
						}
					});

				}
				
			});
			
		});
		
		function reloadIPBanList() {

			$('#loadingSpiral').show();
			$('#ipBanList').hide();
			$.post('<?php echo $MAIN_ROOT; ?>members/include/admin/ipbanning/ipbanlist.php', { }, function(data) {
				$('#ipBanList').html(data);
				$('#ipBanList').fadeIn(250);
				$('#loadingSpiral').hide();
			});

		}
		
	});

</script>
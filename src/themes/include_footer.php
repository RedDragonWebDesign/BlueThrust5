	<div id='notificationDiv'></div>
	<div id='notificationContainer'></div>
	
	<div id='toolTip'></div>
	<div id='toolTipWidth'></div>
	
	
	<?php echo $dispMembersOnlyTagger; ?>
	
	
	<div id='refreshMenusDiv' style='display: none'></div>
		<?php
		
			if(constant('LOGGED_IN')) {
				
				$memberObj = new Member($mysqli);
				$memberObj->select($_SESSION['btUsername']);
				$memberInfo = $memberObj->get_info();
				
				echo "
					<audio id='notificationSound'>
				";
				if($memberInfo['notifications'] == 0) {
					echo "
						<source src='".$MAIN_ROOT."themes/".$THEME."/notification.mp3'></source>
						<source src='".$MAIN_ROOT."themes/".$THEME."/notification.ogg'></source>
					";
				}
				
				echo "
					</audio>
					";
				
				if($memberInfo['notifications'] == 0 || $memberInfo['notifications'] == 1) {
					echo "
			
					<script type='text/javascript'>
						var intCountNotificationCheck = 0;
				
						function checkForNotification() {
				
							$(document).ready(function() {
				
								$.post('".$MAIN_ROOT."members/include/_notificationcheck.php', { }, function(data) {
				
									$('#notificationContainer').html(data);
									
								});
				
							});
				
							if(intCountNotificationCheck < 5) {
								setTimeout(\"checkForNotification()\", 120000);
							}
							
							intCountNotificationCheck++;
						}
						
						checkForNotification();
					</script>
				";
					
				}
			}
			
		?>

		
		<script type='text/javascript'>
		
			function reloadShoutbox() {
				$(document).ready(function() {

					$.post('<?php echo $MAIN_ROOT; ?>members/include/news/include/reloadshoutbox.php', { divID: "<?php echo $themeMenusObj->arrShoutBoxIDs[0]; ?>" }, function(data) {

						$('#<?php echo $themeMenusObj->arrShoutBoxIDs[0]; ?>').html(data);
	
					});
						
						
					
				});

				
				setTimeout("reloadShoutbox()", 20000);
			}


			setTimeout("reloadShoutbox()", 20000);
		
		</script>
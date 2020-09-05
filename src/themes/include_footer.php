	<div id='notificationDiv'></div>
	<div id='notificationContainer'></div>
	<div id='toolTip'></div>
	<div id='toolTipWidth'></div>
	<?php echo $dispMembersOnlyTagger; ?>
	<div id='refreshMenusDiv' style='display: none'></div>
	
	<!-- Script to fill #notificationContainer (and possibly play a sound) if there's a notification -->
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
	
	<!-- Shoutbox -->
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
	
	<?php if ( DEBUG ): ?>	
		<div id="sql-profiler" style="margin: 0 15px;">
			<p style="font-size: 14pt;">
				PHP Version: <strong><?php echo phpversion(); ?></strong>
			</p>
			
			<h1>
				5 Slowest Queries
			</h1>
			
			<?php
				$slowQueries = $SQL_PROFILER;
				usort($slowQueries, function ($b, $a) {
					return $a['duration'] <=> $b['duration'];
				});
				$slowQueries = array_slice($slowQueries, 0, 5);
			?>
			
			<table style="border: 3px solid blue; border-collapse: collapse; font-size: 11pt; table-layout: fixed; margin: 1em 0; width: 100%;">
				<thead>
					<tr>
						<th style="border: 3px solid blue; width: 4%;">
							#
						</th>
						<th style="border: 3px solid blue; width: 6%;">
							Seconds
						</th>
						<th style="border: 3px solid blue; width: 40%;">
							Query
						</th>
						<th style="border: 3px solid blue; width: 50%;">
							Stack Trace
						</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 0; foreach ( $slowQueries as $query ): ?>
						<?php $i++; ?>
						<tr>
							<td style="border: 3px solid blue;">
								<?php echo $i; ?>
							</td>
							<td style="border: 3px solid blue;">
								<?php echo $query['duration']; ?>
							</td>
							<td style="border: 3px solid blue;">
								<?php echo $query['query']; ?>
							</td>
							<td style="border: 3px solid blue;"
								><?php echo $query['stack_trace']; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			
			<h1>
				5 Most Repeated Queries
			</h1>
			
			<?php
				$repeatedQueries = $SQL_PROFILER;
				
				// delete all data except for query
				foreach ( $repeatedQueries as $key => $value ) {
					$repeatedQueries[$key] = [
						'query' => $value['query'],
						'stack_trace' => $value['stack_trace'],
						'count' => 1,
					];
				}
				
				// sort alphabetically
				function compareByName($a, $b) {
					return strcmp($a["query"], $b["query"]);
				}
				usort($repeatedQueries, 'compareByName');
				
				// then start merging duplicates
				$currentKey = 0;
				$currentValue = $repeatedQueries[0];
				for ( $i = 1; $i < count($repeatedQueries); $i++ ) {
					$value = $repeatedQueries[$i];
					if ( $value == $currentValue ) {
						$repeatedQueries[$currentKey]['count']++;
						unset($repeatedQueries[$i]);
					} else {
						$currentKey = $i;
						$currentValue = $value;
					}
				}
				
				// sort by sub-key "count"
				usort($repeatedQueries, function ($b, $a) {
					return $a['count'] <=> $b['count'];
				});
				
				$repeatedQueries = array_slice($repeatedQueries, 0, 5);
			?>
			
			<table style="border: 3px solid blue; border-collapse: collapse; font-size: 11pt; table-layout: fixed; margin: 1em 0; width: 100%;">
				<thead>
					<tr>
						<th style="border: 3px solid blue; width: 5%;">
							Count
						</th>
						<th style="border: 3px solid blue; width: 45%;">
							Query
						</th>
						<th style="border: 3px solid blue; width: 50%;">
							Stack Trace Of 1 Random Query
						</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 0; foreach ( $repeatedQueries as $query ): ?>
						<?php $i++; ?>
						<tr>
							<td style="border: 3px solid blue;">
								<?php echo $query['count']; ?>
							</td>
							<td style="border: 3px solid blue;">
								<?php echo $query['query']; ?>
							</td>
							<td style="border: 3px solid blue;">
								<?php echo $query['stack_trace']; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			
			<h1>
				Query List
			</h1>
			
			<table style="border: 3px solid blue; border-collapse: collapse; font-size: 11pt; table-layout: fixed; margin: 1em 0; width: 100%;">
				<thead>
					<tr>
						<th style="border: 3px solid blue; width: 4%;">
							#
						</th>
						<th style="border: 3px solid blue; width: 6%;">
							Seconds
						</th>
						<th style="border: 3px solid blue; width: 40%;">
							Query
						</th>
						<th style="border: 3px solid blue; width: 50%;">
							Stack Trace
						</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 0; foreach ( $SQL_PROFILER as $query ): ?>
						<?php $i++; ?>
						<tr>
							<td style="border: 3px solid blue;">
								<?php echo $i; ?>
							</td>
							<td style="border: 3px solid blue;">
								<?php echo $query['duration']; ?>
							</td>
							<td style="border: 3px solid blue;">
								<?php echo $query['query']; ?>
							</td>
							<td style="border: 3px solid blue;"
								><?php echo $query['stack_trace']; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
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

	if(!defined("SHOW_PROFILE_MAIN")) {
		exit();	
	}

	include_once($prevFolder."plugins/youtube/youtube.php");
	include_once($prevFolder."plugins/youtube/ytbuttoncss.php");
	$ytObj = new Youtube($mysqli);

	
	if($ytObj->hasYoutube($memberInfo['member_id'])) {
		
		$ytInfo = $ytObj->get_info_filtered();
		
		if(($ytInfo['showsubscribe']+$ytInfo['showvideos']) > 0) {
		
			echo "
					<div class='formTitle' style='position: relative; text-align: center; margin-top: 20px'>Youtube</div>
					
					<table class='profileTable' style='border-top-width: 0px'>
						<tr>
							<td class='main' style='padding: 25px 0px'>
							<div id='loadingSpiralYTCache' class='loadingSpiral' style='padding-top: 0px'>
								<p align='center'>
									<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Refreshing Data
								</p>
							</div>
				";
			
			if($ytInfo['showsubscribe'] == 1) {
				
				echo "

					<div id='ytInfoCard'>".$ytObj->dispSubscribeButton()."</div>
					<div style='font-style: italic; text-align: center; margin-top: 3px; margin-left: auto; margin-right: auto; margin-bottom: 25px; position: relative' class='main'>
						Last updated <span id='lastUpdateTime'>".getPreciseTime($ytInfo['lastupdate'])."</span>
					</div>

				";
				
			}
			
			if($ytInfo['showvideos'] > 0) {
				
				echo "

					<div id='ytVideosContainer' class='ytProfileVideos'>
						<div style='position: absolute'>
						";
				
				$result = $mysqli->query("SELECT * FROM ".$dbprefix."youtube_videos WHERE youtube_id = '".$ytInfo['youtube_id']."' ORDER BY youtubevideo_id LIMIT ".$ytInfo['showvideos']);
				while($row = $result->fetch_assoc()) {
					
					echo "<div class='ytVideo'><a href='http://www.youtube.com/watch?v=".$row['video_id']."' target='_blank'><img src='".$row['thumbnail']."' width='185' height='104' style='border: 0px'><p class='main' style='padding-top: 2px; margin-top: 0px'>".$row['title']."</a></p></div>";
				}
								
				
				echo "
						</div>
					</div>
					
				";
				
				
				
				echo "
					<div class='videoScroller'></div>
					
				
					<script type='text/javascript'>
						$(document).ready(function() {
							var videoHeight = 0;
							$('.ytVideo').each(function(index) {
								if($(this).height() > videoHeight) {
									videoHeight = $(this).height();
								}
							});
							
							$('#ytVideosContainer').css('height', videoHeight+'px');
							
							
							
							var maxScroll = $('#ytVideosContainer')[0].scrollWidth-$('#ytVideosContainer').width();
							if(maxScroll > 0) {
								$('.videoScroller').slider({
								
									max: maxScroll,
									slide: function(event, ui) {
										$('#ytVideosContainer').scrollLeft($(this).slider('option', 'value'));
									},
									change: function(event, ui) {
										$('#ytVideosContainer').scrollLeft($(this).slider('option', 'value'));
									}
								});
							}
							
						});
					</script>
		
					
				";
				
				
			}
			
			
			echo "
							</td>
						</tr>
					</table>		
				";
			
			
			if((time()-$ytInfo['lastupdate']) > 1800) {
				
				echo "
					
					<script type='text/javascript'>
					
						$(document).ready(function() {
						
							$('#loadingSpiralYTCache').show();
							$('#ytInfoCard').fadeOut(250);
						
							$.post('".$MAIN_ROOT."plugins/youtube/reloadcache.php', { yID: '".$ytInfo['youtube_id']."' }, function(data) {
								
								postResult = JSON.parse(data);
								
								if(postResult['result'] == 'success') {
									$('#ytInfoCard').html(postResult['html']);
									$('#lastUpdateTime').html(postResult['time']);
	
								}							
								
								$('#ytInfoCard').fadeIn(250);
								$('#loadingSpiralYTCache').hide();
								
								var bubbleRight = ($('.ytBubble').width()*-1)-20;
								$('.ytBubble').css('right', bubbleRight+'px');
								
							});
						
						});
					
					</script>
				
				";
			}
		
		}
		
		
	}
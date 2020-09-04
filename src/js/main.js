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

var intShowToolTip = 0;


function displayClock(intOffset, intHours, intMinutes, strDivID) {

	var dateObj = new Date();
	var intSeconds = dateObj.getSeconds();
	var strAMPM = "AM";
	var intSaveHours = intHours;
	
	if(intHours > 12) {
		intHours = intHours-12;
		strAMPM = "PM";
	}
	
	if(intHours == 0) {
		intHours = 12;
	}
	
	dispMinutes = intMinutes;
	if(intMinutes < 10) {
		dispMinutes = "0"+intMinutes;
	}
	
	strJQDivID = "#"+strDivID;
	
      
	var strFullTime = intHours+":"+dispMinutes+" "+strAMPM;

	if(!isNaN(intOffset) && intOffset != "") {
        currentTimestamp = dateObj.getTime()+(dateObj.getTimezoneOffset()*60*1000);

	    dateObj.setTime(currentTimestamp+(intOffset*1000));
	
        var fullDate = (dateObj.getMonth()+1)+"/"+dateObj.getDate();      

        strFullTime = fullDate+" "+strFullTime;
	}


	$(strJQDivID).html(strFullTime);
	
	
	if(intSeconds == 59) {
		intMinutes = intMinutes+1;
		if(intMinutes == 60) {
			intHours = intHours+1;
			intMinutes = 0;
			if(intHours > 24) {
				intHours = 1;
			}
		}
	}
	
	setTimeout(function(){ displayClock(intOffset,intSaveHours,intMinutes,strDivID) }, 1000);
}

/*
function displayClock(intHours, intMinutes, strDivID) {

	var dateObj = new Date();
	var intSeconds = dateObj.getSeconds();
	var strAMPM = "AM";
	var intSaveHours = intHours;
	
	if(intHours > 12) {
		intHours = intHours-12;
		strAMPM = "PM";
	}
	
	if(intHours == 0) {
		intHours = 12;
	}
	
	dispMinutes = intMinutes;
	if(intMinutes < 10) {
		dispMinutes = "0"+intMinutes;
	}
	
	strJQDivID = "#"+strDivID;
	
	var strFullTime = intHours+":"+dispMinutes+" "+strAMPM;
	$(strJQDivID).html(strFullTime);
	
	
	if(intSeconds == 59) {
		intMinutes = intMinutes+1;
		if(intMinutes == 60) {
			intHours = intHours+1;
			intMinutes = 0;
			if(intHours > 24) {
				intHours = 1;
			}
		}
	}
	
	setTimeout(function(){ displayClock(intSaveHours,intMinutes,strDivID) }, 1000);
}
*/

function displayDate(intOffset, strDivID) {
	
	var dateObj = new Date();
	var testTime = dateObj.getTime();
	
	dateObj.setTime(testTime+(intOffset*1000));
	
	var fullDate = (dateObj.getMonth()+1)+"/"+dateObj.getDate();
	
	setTimeout(function(){ displayClock(intOffset, strDivID) }, 1000);
	
}


function popupDialog(strTitle, strLink, strDivId) {
	
	$(document).ready(function() {
		
		divId = "#"+strDivId;
		$(divId).dialog({
			title: strTitle,
			modal: true,
			zIndex: 99999,
			width: 400,
			resizable: false,
			show: "scale",
			buttons: {
				"Ok": function() {
					$(this).dialog("close");
				}
			},
			beforeClose: function() {
				if(strLink != "") {
					window.location = strLink;
				}
			}
			
		});
		$('.ui-dialog :button').blur();
	});
	
}

function showToolTip(strMessage, intWidth) {
	intShowToolTip = 1;
	
	if(intWidth != null) {
		$('#toolTip').css("width", intWidth+"px");
	}
	
	$('#toolTipWidth').html(strMessage);

	if($('#toolTipWidth').width() > 300) {
		
		$('#toolTip').width(300);
		
	}
	else {
		
		$('#toolTip').width($('#toolTipWidth').width());
	}
	
	$('#toolTipWidth').html("");
	$('#toolTip').html(strMessage);

	
}



$(document).ready(function() {
	$(document).mousemove(function(e) { 
		if(intShowToolTip == 1) {
			$('#toolTip').css("left", e.pageX+12);
			$('#toolTip').css("top", e.pageY+12);
			$('#toolTip').css("z-index", "9999999999");
			$('#toolTip').show();
		}
	});	
});
	

function hideToolTip() {
	intShowToolTip = 0;
	$(document).ready(function() {
		$('#toolTip').hide();
		//alert('hi');
	});
}

function loadPage(strPageUrl, strPostVars, strDivId) {
	$(document).ready(function() {

		$.post(strPageUrl, { postVars: strPostVars }, function(data) {
			alert(data);
		});
		
		
		
	});
}


function postShoutbox(strUpdateDiv, strPostLink) {
	
	$(document).ready(function() {
		
		var strPostMessageBox = "#"+strUpdateDiv+"_message";
		var strPostMessage = $(strPostMessageBox).val();
		var jqUpdateDiv = "#"+strUpdateDiv;
		$(strPostMessageBox).val("");
		$.post(strPostLink, { message: strPostMessage, updateDiv: strUpdateDiv }, function(data) {

			$(jqUpdateDiv).html(data);
			$(jqUpdateDiv).animate({
				scrollTop:$(jqUpdateDiv)[0].scrollHeight
			}, 1000);
			
			
		});
		
	});
	
}

function deleteShoutbox(intPostID, strPostLink, strUpdateDiv) {
	
	$(document).ready(function() {
		
		var jqUpdateDiv = "#"+strUpdateDiv;
		var jqLoadingDiv = "#"+strUpdateDiv+"_loading_"+intPostID;
		var jqManageDiv = "#"+strUpdateDiv+"_manage_"+intPostID;
		$(jqManageDiv).hide();
		$(jqLoadingDiv).show();
		$.post(strPostLink, { postID: intPostID, updateDiv: strUpdateDiv }, function(data) {
			
			$(jqUpdateDiv).html(data);
						
			
		});
		
	});

}

function embedPoll(MAINROOT, embedDivID, pollID) {

	$(document).ready(function() {
		
		$.post(MAINROOT+'polls/embed.php', { pID: pollID }, function(data) {
			$('#'+embedDivID).html(data);
		});
		
	});
	
}

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

function selectAllCheckboxes(divID, checkOrUncheck) {

	if(checkOrUncheck != 1) {
		checkOrUncheck = 0;
	}

	var strJQInfo = "#"+divID+" [type='checkbox']";
	$(strJQInfo).each(function() {

		if(checkOrUncheck == 1) {
			$(this).attr("checked", true);
		}
		else {
			$(this).attr("checked", false);
		}
	
		
	});


}
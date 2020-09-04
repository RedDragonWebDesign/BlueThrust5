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

var intSelectedConsole = 1;

function moverCategory(intCategoryNum) {

	var strCategoryName = "categoryName"+intCategoryNum;
	document.getElementById(strCategoryName).className = "consoleCategory_clicked";
	
}

function moutCategory(intCategoryNum) {

	if(intSelectedConsole != intCategoryNum) {
		var strCategoryName = "categoryName"+intCategoryNum;
		document.getElementById(strCategoryName).className = "consoleCategory";
	}

}


function selectCategory(intCategoryNum) {
	
	var strOldCat = "#categoryOption"+intSelectedConsole;
	var strNewCat = "#categoryOption"+intCategoryNum;
	var intOldCat = intSelectedConsole;
	
	intSelectedConsole = intCategoryNum;
	moverCategory(intCategoryNum);
	moutCategory(intOldCat);
	
	$(document).ready(function() {
		$(strOldCat).hide();
		$(strNewCat).show();
		
		$('#myAccountPageCategories').removeClass("showConsoleCategories").addClass("hideConsoleCategories");
		$('#myAccountPageOptions').removeClass("hideConsoleOptions").addClass("showConsoleOptions");
	});
	
}

function sendPostData(arrData, strPageName) {

	
	$(document).ready(function() {
		$.post(strPageName, { postData: arrData }, function(data) {
			alert(data);
		});
	});

}

$(document).ready(function() {
	$("div[data-goback='yes']").click(function() {
		$('#myAccountPageCategories').removeClass('hideConsoleCategories').addClass('showConsoleCategories');
		$('#myAccountPageOptions').removeClass('showConsoleOptions').addClass('hideConsoleOptions');
	});
});
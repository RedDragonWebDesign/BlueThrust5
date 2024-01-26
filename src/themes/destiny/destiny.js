$(document).ready(function() {

	// Configuration

	var miniLogo = true; // Set to false if you do not want to use a mini logo



	// Only edit below if you know what you are doing!!

	var showLogo = true;

	$(window).scroll(function() {



		if ($(window).scrollTop() >= 424) {
			$('#destinyBG').css("top", $(window).scrollTop()-424);
		} else {
			$('#destinyBG').css("top", 0);
		}


		if ($(window).scrollTop() >= 171) {
			$('#topBarBGImg').css("background-position", "center -171px");
		} else {
			$('#topBarBGImg').css("background-position", "center "+(-$(window).scrollTop())+"px");
		}

		if (miniLogo && $(window).scrollTop() > 168 && showLogo) {
			showLogo = false;
			$('#logoSmall').animate({
				top: "18px"
			}, 250);
		} else if (miniLogo && $(window).scrollTop() <= 168 && !showLogo) {
			$('#logoSmall').animate({
				top: "81px"
			}, 250);
			showLogo = true;
		}

	});
});

(function($) {

	$.fn.imageslider = function(options) {
				
		var settings = $.extend( {
			'DivPrefix': '',
			'ImageWidth': [],
			'ImageHeight': [],
			'ImageWidthUnit': [],
			'ImageHeightUnit': [],
			'ImageFillStretch': [],
			'DotSpacing': '20',
			'TotalImages' : '0',
			'SwitchDelay': '5000',
			'Images': [],
			'Links': [],
			'LinkTarget': [],
			'ImageTitle': [],
			'ImageDescription': [],
			'DotImage': '',
			'DotImageHover': '',
			'ContainerWidth': '600',
			'ContainerHeight': '400',
			'ContainerWidthUnit': 'px',
			'ContainerHeightUnit': 'px'
		
		}, options);
		
		var arrDotValues = { };
		
		var arrImageScrollerIDs = new Array();
		var arrDots = new Array();
		var intCounter = parseInt(settings['TotalImages']);
		
		for(var i = 0; i<=intCounter; i++) {
			
			arrImageScrollerIDs[i] = settings['DivPrefix']+"_IS"+i;
			arrDots[i] = settings['DivPrefix']+"_dot_"+i;
			
			tempAddHover = arrDots[i]+"_hover";
			
			arrDotValues[arrDots[i]] = i;
			arrDotValues[tempAddHover] = i;
			
		}
		
		
		
		return this.each(function() {
			
			if($('#hpImageSliderWrapper').width() < settings['ContainerWidth']) {
				settings['ImageWidth'] = $('#hpImageSliderWrapper').width()-30;
			}
			
			var $this = $(this);
						
			var currentDivID, currentDotDiv, currentDotDivHover, intPrevID, prevDivID, prevDotDiv, prevDotDivHover, intIntervalID;
			var intCurrentImage = 0;
			
			var intDotWidth = 14;
			var intFullDotSpacing = intDotWidth+parseInt(settings['DotSpacing']);
			
			var intTotalDotWidth = (intDotWidth*(intCounter+1))+(parseInt(settings['DotSpacing'])*intCounter);
			
			var intFirstDotLeftVal = Math.round(parseInt(settings['ImageWidth'])/2)-Math.round(intTotalDotWidth/2);
			
			var dispImageSlider = "<div class='hp_imgScrollContainer' style='width: "+settings['ContainerWidth']+settings['ContainerWidthUnit']+"; height: "+settings['ContainerHeight']+settings['ContainerHeightUnit']+"'>";
			
			var imageDivsHTML = "";
			var showImage = "style = 'display: block'";
			
			
			for(var i = 0; i<=intCounter; i++) {
				
				if(i > 0) {
					showImage = "";
				}
				
				
				
				tempOverlayHTML = "";
				if(settings['ImageTitle'][i] != "" || settings['ImageDescription'][i] != "") {
					tempOverlayHTML = "<div class='hp_imageScrollerOverlay'><div class='hp_imageScrollerOverlayTitle'>"+settings['ImageTitle'][i]+"</div><div class='hp_imageScrollerOverlayMessage'>"+settings['ImageDescription'][i]+"</div></div>";
				}
				
				if(settings['ImageFillStretch'][i] == "stretch" && settings['Links'][i] != "") {
					imageDivsHTML += "<div id='"+settings['DivPrefix']+"_IS"+i+"' class='hp_imagescroller' "+showImage+"><a href='"+settings['Links'][i]+"' target='"+settings['LinkTarget'][i]+"'>"+tempOverlayHTML+"<img src='"+settings['Images'][i]+"' style='width: "+settings['ImageWidth'][i]+settings['ImageWidthUnit'][i]+"; height: "+settings['ImageHeight'][i]+settings['ImageHeightUnit'][i]+"'></a></div>";
				}
				else if(settings['ImageFillStretch'][i] == "stretch" && settings['Links'][i] == "") {
					imageDivsHTML += "<div id='"+settings['DivPrefix']+"_IS"+i+"' class='hp_imagescroller' "+showImage+">"+tempOverlayHTML+"<img src='"+settings['Images'][i]+"' style='width: "+settings['ImageWidth'][i]+settings['ImageWidthUnit'][i]+"; height: "+settings['ImageHeight'][i]+settings['ImageHeightUnit'][i]+"'></div>";
				}
				else if(settings['ImageFillStretch'][i] == "fill" && settings['Links'][i] != "") {
					imageDivsHTML += "<div id='"+settings['DivPrefix']+"_IS"+i+"' class='hp_imagescroller' "+showImage+"><a href='"+settings['Links'][i]+"' target='"+settings['LinkTarget'][i]+"'>"+tempOverlayHTML+"<div style=\"background: url('"+settings['Images'][i]+"'); width: "+settings['ImageWidth'][i]+settings['ImageWidthUnit'][i]+"; height: "+settings['ImageHeight'][i]+settings['ImageHeightUnit'][i]+"\"></div></a></div>";
				}
				else if(settings['ImageFillStretch'][i] == "fill" && settings['Links'][i] == "") {
					imageDivsHTML += "<div id='"+settings['DivPrefix']+"_IS"+i+"' class='hp_imagescroller' "+showImage+">"+tempOverlayHTML+"<div style=\"background: url('"+settings['Images'][i]+"'); width: "+settings['ImageWidth'][i]+settings['ImageWidthUnit'][i]+"; height: "+settings['ImageHeight'][i]+settings['ImageHeightUnit'][i]+"\"></div></div>";
				}
			
			}
			
			dispImageSlider += imageDivsHTML+"</div>";
			
			dispImageSlider += "<div class='hp_dotsContainer' style='width: "+settings['ContainerWidth']+settings['ContainerWidthUnit']+"'>";
			
			var imageDotDivsHTML = "";
			
			var hideHover = "";
			var hideNormal = "; display: none";
			var intLeft = intFirstDotLeftVal;
		
			for(i = 0; i<=intCounter; i++) {
				imageDotDivsHTML += "<div class='hp_imgScrollerDot' data-prefix = '"+settings['DivPrefix']+"' id='"+settings['DivPrefix']+"_dot_"+i+"' style='left: "+intLeft+"px;"+hideNormal+"'><img src='"+settings['DotImage']+"'></div>";
				imageDotDivsHTML += "<div class='hp_imgScrollerDot' data-prefix = '"+settings['DivPrefix']+"' id='"+settings['DivPrefix']+"_dot_"+i+"_hover' style='left: "+intLeft+"px;"+hideHover+"'><img src='"+settings['DotImageHover']+"'></div>";
			
				hideHover = "; display: none";
				hideNormal = "";
				intLeft += intFullDotSpacing;
				
			}
		
			dispImageSlider += imageDotDivsHTML+"</div>";

			
			$this.html(dispImageSlider);
			
			
			$("div[data-prefix='"+settings['DivPrefix']+"']").click(function() {
				
				intCurrentImage--;
				if(intCurrentImage < 0) {
					intCurrentImage = parseInt(settings['TotalImages']);
				}
				
				$("#"+arrImageScrollerIDs[intCurrentImage]).fadeOut(400);
				$("#"+arrDots[intCurrentImage]).fadeIn(400);
				$("#"+arrDots[intCurrentImage]+"_hover").fadeOut(400);
				
				intCurrentImage = arrDotValues[$(this).attr('id')];
				intIntervalID = window.clearInterval(intIntervalID);

				switchImage();
				
			});
			
			
			function switchImage() {
						
				currentDivID = "#"+arrImageScrollerIDs[intCurrentImage];
				currentDotDiv = "#"+arrDots[intCurrentImage];
				currentDotDivHover = "#"+arrDots[intCurrentImage]+"_hover";
				
				
				if(intCurrentImage == 0) {
					intPrevID = parseInt(settings['TotalImages']);
				}
				else {
					intPrevID = intCurrentImage-1;
				}
				
				
				prevDivID = "#"+arrImageScrollerIDs[intPrevID];
				prevDotDiv = "#"+arrDots[intPrevID];
				prevDotDivHover = "#"+arrDots[intPrevID]+"_hover";
				
				
				$(currentDivID).fadeIn(1000);
				$(prevDivID).fadeOut(400);
				
				// Fade In Current BB Hover Dot/Fade Out Current BB Normal Dot
				$(currentDotDiv).fadeOut(400);
				$(currentDotDivHover).fadeIn(400);

				// Fade In Previous BB Normal Dot/Fade Out Previous BB Hover Dot
				$(prevDotDiv).fadeIn(400);
				$(prevDotDivHover).fadeOut(400);
				
				intCurrentImage++;
				
				if(intCurrentImage > parseInt(settings['TotalImages'])) {
					intCurrentImage = 0;
				}
				
				
				intIntervalID = self.setTimeout(function() { switchImage(); }, parseInt(settings['SwitchDelay']));
			}
			
			
			switchImage();
			
			
		});
		
	};
	
}) ( jQuery );
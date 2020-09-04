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



include_once("basicorder.php");



class ImageSlider extends BasicOrder {

	public $blnLoggedIn = false;
	public $strDisplayStyle = "slider";
	public $intDisplayWidth = 600;
	public $intDisplayHeight = 400;
	public $strDisplayWidthUnit = "px";
	public $strDisplayHeightUnit = "px";
	public $arrImageIDs;
	public $strTheme;
	
	
	public function __construct($sqlConnection) {
		
		
		$this->MySQL = $sqlConnection;
		$this->strTableName = $this->MySQL->get_tablePrefix()."imageslider";
		$this->strTableKey = "imageslider_id";
		
		
	}

	
	/*
	 * - getLocalImageURL Function -
	 * 
	 * Used to determine if the image attached to the selected rank is a local image or external image.
	 * 
	 * Returns FALSE when the image is an external url.
	 * Returns the local image address when the image is on the server.
	 * 
	 */
	
	function getLocalImageURL() {
		global $MAIN_ROOT;
		$returnVal = false;
		if($this->intTableKeyValue != "") {
	
			if(strpos($this->arrObjInfo['imageurl'], "http://") === false) {
	
				$returnVal = $this->arrObjInfo['imageurl'];
	
			}
	
		}
	
		return $returnVal;
	}
	

	function getImageIDs() {
		
		$this->arrImageIDs = array();
		$filterSQL = ($this->blnLoggedIn) ? " OR membersonly = '1' " : " OR membersonly = '2' ";
		
		$result = $this->MySQL->query("SELECT imageslider_id FROM ".$this->strTableName." WHERE membersonly = '0'".$filterSQL."ORDER BY ordernum DESC");
		while($row = $result->fetch_assoc()) {
			$this->arrImageIDs[] = $row['imageslider_id'];
		}
		
		return $this->arrImageIDs;
		
	}
	
	
	function selectRandomImage() {
		
		$this->getImageIDs();
		$intRandomKey = array_rand($this->arrImageIDs);
		
		$this->select($this->arrImageIDs[$intRandomKey]);
		
	}
	
	
	function dispHomePageImage() {
		global $websiteInfo;
		$this->getImageIDs();
		
		if(count($this->arrImageIDs) == 0) {
			echo "";			
		}
		elseif(count($this->arrImageIDs) == 1 && $this->select($this->arrImageIDs[0]) && $this->arrObjInfo['fillstretch'] == "stretch") {
		
			
			$addOverlay = "";
			if($this->arrObjInfo['message'] != "" || $this->arrObjInfo['messagetitle'] != "") {
				$addOverlay = "
					<div class='hp_imageScrollerOverlay'>
						<div class='hp_imageScrollerOverlayTitle'>
							".$this->arrObjInfo['messagetitle']."
						</div>
						<div class='hp_imageScrollerOverlayMessage'>
							".$this->arrObjInfo['message']."
						</div>
					</div>
				";
			}
			
			$addLink = ($this->arrObjInfo['link'] != "") ? "<a href='".$this->arrObjInfo['link']."' target='".$this->arrObjInfo['linktarget']."'>" : "";
			$closeLinkTag = ($addLink != "") ? "</a>" : "";
			
			echo "
			
				<div class='hp_imgScrollContainer' style='width: ".$this->intDisplayWidth.$this->strDisplayWidthUnit."; height: ".$this->intDisplayHeight.$this->strDisplayHeightUnit."'>
					".$addLink.$addOverlay."<img src='".$this->arrObjInfo['imageurl']."' style='width: ".$this->intDisplayWidth.$this->strDisplayWidthUnit."; height: ".$this->intDisplayHeight.$this->strDisplayHeightUnit."'>".$closeLinkTag."
				</div>
			
			";
		
		}
		elseif(count($this->arrImageIDs) == 1 && $this->select($this->arrImageIDs[0]) && $this->arrObjInfo['fillstretch'] == "fill") {
			
			$this->select($this->arrImageIDs[0]);
			
			
			$addOverlay = "";
			if($this->arrObjInfo['message'] != "" || $this->arrObjInfo['messagetitle'] != "") {
				$addOverlay = "
				<div class='hp_imageScrollerOverlay'>
					<div class='hp_imageScrollerOverlayTitle'>
						".$this->arrObjInfo['messagetitle']."
					</div>
					<div class='hp_imageScrollerOverlayMessage'>
						".$this->arrObjInfo['message']."
					</div>
				</div>
				";
			}
			
			$addLink = ($this->arrObjInfo['link'] != "") ? "<a href='".$this->arrObjInfo['link']."' target='".$this->arrObjInfo['linktarget']."'>" : "";
			$closeLinkTag = ($addLink != "") ? "</a>" : "";
			
			
			echo "
			
				".$addLink."<div class='hp_imgScrollContainer' style=\"background: url('".$this->arrObjInfo['imageurl']."'); width: ".$this->intDisplayWidth.$this->strDisplayWidthUnit."; height: ".$this->intDisplayHeight.$this->strDisplayHeightUnit."\">".$addOverlay."</div>".$closeLinkTag."
			
			";
			
		}
		elseif($this->strDisplayStyle == "random" && count($this->arrImageIDs) > 1) {

			$this->selectRandomImage();
			
			$addOverlay = "";
			if($this->arrObjInfo['message'] != "" || $this->arrObjInfo['messagetitle'] != "") {
				$addOverlay = "
					<div class='hp_imageScrollerOverlay'>
						<div class='hp_imageScrollerOverlayTitle'>
							".$this->arrObjInfo['messagetitle']."
						</div>
						<div class='hp_imageScrollerOverlayMessage'>
							".$this->arrObjInfo['message']."
						</div>
					</div>
				";				
			}
			
			$addLink = ($this->arrObjInfo['link'] != "") ? "<a href='".$this->arrObjInfo['link']."' target='".$this->arrObjInfo['linktarget']."'>" : "";
			$closeLinkTag = ($addLink != "") ? "</a>" : "";
			
			if($this->arrObjInfo['fillstretch'] == "stretch") {
			echo "
				<div class='hp_imgScrollContainer'>
					".$addLink.$addOverlay."<img src='".$this->arrObjInfo['imageurl']."' style='width: ".$this->intDisplayWidth.$this->strDisplayWidthUnit."; height: ".$this->intDisplayHeight.$this->strDisplayHeightUnit."'>".$closeLinkTag."
				</div>
			";
			}
			else {
				
				echo "
					".$addLink."<div class='hp_imgScrollContainer' style=\"background: url('".$this->arrObjInfo['imageurl']."'); width: ".$this->intDisplayWidth.$this->strDisplayWidthUnit."; height: ".$this->intDisplayHeight.$this->strDisplayHeightUnit."\">".$addOverlay."</div>".$closeLinkTag."
				";
			}
			

		}
		elseif($this->strDisplayStyle == "slider" && count($this->arrImageIDs) > 1) {
	
			foreach($this->arrImageIDs as $imgID) {
				
				$this->select($imgID);
				$arrImages[] = $this->arrObjInfo['imageurl'];
				$arrImageLinks[] = $this->arrObjInfo['link'];
				$arrImageLinkTarget[] = $this->arrObjInfo['linktarget'];
				$arrImageWidth[] = $this->intDisplayWidth;
				$arrImageHeight[] = $this->intDisplayHeight;
				$arrImageWidthUnit[] = $this->strDisplayWidthUnit;
				$arrImageHeightUnit[] = $this->strDisplayHeightUnit;
				$arrDisplayStyle[] = $this->arrObjInfo['fillstretch'];
				$arrImageTitle[] = filterText($this->arrObjInfo['messagetitle']);
				$arrImageDescription[] = filterText(str_replace(array("\r", "\n"), "\\n", $this->arrObjInfo['message']));
			}
			
			
			$dispImages = "'".implode("','", $arrImages)."'";
			$dispImageLinks = "'".implode("','", $arrImageLinks)."'";
			$dispImageLinkTarget = "'".implode("','", $arrImageLinkTarget)."'";
			$dispImageWidth = implode(",", $arrImageWidth);
			$dispImageHeight = implode(",", $arrImageHeight);
			$dispImageWidthUnit = "'".implode("','", $arrImageWidthUnit)."'";
			$dispImageHeightUnit = "'".implode("','", $arrImageHeightUnit)."'";
			$dispDisplayStyle = "'".implode("','", $arrDisplayStyle)."'";
			$dispImageTitle = "'".implode("','", $arrImageTitle)."'";
			$dispImageDescription = "'".implode("','", $arrImageDescription)."'";

			echo "
				
				<div id='hpImageScroller'></div>
			
				<script type='text/javascript'>
					$('#hpImageScroller').imageslider({
						'DivPrefix': 'mainHPScroller',
						'Images': [".$dispImages."],
						'Links': [".$dispImageLinks."],
						'LinkTarget': [".$dispImageLinkTarget."],
						'ImageWidth': [".$dispImageWidth."],
						'ImageHeight': [".$dispImageHeight."],
						'ImageWidthUnit': [".$dispImageWidthUnit."],
						'ImageHeightUnit': [".$dispImageHeightUnit."],
						'ImageFillStretch': [".$dispDisplayStyle."],
						'ImageTitle': [".$dispImageTitle."],
						'ImageDescription': [".$dispImageDescription."],
						'DotImage': 'themes/".$this->strTheme."/images/imagescroller.png',
						'DotImageHover': 'themes/".$this->strTheme."/images/imagescroller_hover.png',
						'TotalImages': '".(count($this->arrImageIDs)-1)."',
						'SwitchDelay': '8000',
						'ContainerWidth': '".$this->intDisplayWidth."',
						'ContainerHeight': '".$this->intDisplayHeight."',
						'ContainerWidthUnit': '".$this->strDisplayWidthUnit."',
						'ContainerHeightUnit': '".$this->strDisplayHeightUnit."'
					});
				</script>
				
			";
			
			
			
			
		}
		
		
	}
	
	public function delete() {
		
		$returnVal = false;
		if($this->intTableKeyValue != "") {
		
			$info = $this->arrObjInfo;
			$returnVal = parent::delete();
			
			deleteFile(BASE_DIRECTORY.$info['imageurl']);
		}
		
	}
	
	
}

?>
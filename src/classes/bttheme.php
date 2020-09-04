<?php


	class btTheme {
		
		
		public $menusObj;
		
		protected $themeName;
		protected $themeDir;
		protected $arrHead;
		protected $arrHeadOrder;
		protected $clanName;
			
		
		public function initHead() {
			global $PAGE_NAME;
			
			$this->arrHead = array();
			$this->arrHeadOrder = array();
			
			$this->setTitle($PAGE_NAME.$this->clanName);
			$this->addHeadItem("jquery-ui-css", "<link rel='stylesheet' type='text/css' href='".MAIN_ROOT."themes/".$this->themeDir."/jqueryui/jquery-ui-1.8.17.custom.css'>");
			$this->addHeadItem("jquery", "<script type='text/javascript' src='".MAIN_ROOT."js/jquery-1.6.4.min.js'></script>");
			$this->addHeadItem("jquery-ui", "<script type='text/javascript' src='".MAIN_ROOT."js/jquery-ui-1.8.17.custom.min.js'></script>");
			$this->addHeadItem("btcs4css", "<link rel='stylesheet' type='text/css' href='".MAIN_ROOT."themes/btcs4.css.php'>");
			$this->addHeadItem("mainstyle", "<link rel='stylesheet' type='text/css' href='".MAIN_ROOT."themes/".$this->themeDir."/style.css'>");
			$this->addHeadItem("mainjs", "<script type='text/javascript' src='".MAIN_ROOT."js/main.js'></script>");
			$this->addHeadItem("imageslider", "<script type='text/javascript' src='".MAIN_ROOT."js/imageslider.js'></script>");
		}
		
		
		public function displayHead() {
			global $hooksObj, $EXTERNAL_JAVASCRIPT, $PAGE_NAME;
			
			$this->setTitle($PAGE_NAME.$this->clanName);			
			
			$hooksObj->run("head");
			
			foreach($this->arrHeadOrder as $value) {

				echo $this->arrHead[$value]."\n";
				
			}
			
			if(isset($EXTERNAL_JAVASCRIPT) && $EXTERNAL_JAVASCRIPT != "") {
				echo $EXTERNAL_JAVASCRIPT;
			}
			
		}

		public function displayCopyright() {
			
			echo "
				Powered By: <a href='http://bluethrust.com' target='_blank'>Bluethrust Clan Scripts v4</a> - <a href='http://bluethrust.com/themes' target='_blank'>".$this->themeName." Theme</a><br>
				&copy; Copyright ".date("Y")." ".$this->clanName;
			
		}
		
		public function addHeadItem($itemName, $itemValue) {
			$this->arrHead[$itemName] = $itemValue;
			
			$this->arrHeadOrder[] = $itemName;
			
		}

		public function updateHeadItem($itemName, $itemValue) {

			if(isset($this->arrHead[$itemName])) {
				
				$this->arrHead[$itemName] = $itemValue;
				
			}
			
		}
		
		public function removeHeadItem($itemName) {
			unset($this->arrHead[$itemName]);
			
			$key = array_search($itemName);
			if($key !== false) {
				unset($this->arrHeadOrder[$key]);	
			}
		}
		
		public function moveHeadItem($itemName, $newPosition) {

			if(isset($this->arrHead[$itemName])) {
				
				if(isset($this->arrHeadOrder[$newPosition])) {
					$newOrderArray = array();
					foreach($this->arrHeadOrder as $key => $value) {
						if($key == $newPosition) {
							$newOrderArray[] = $itemName;
							$newOrderArray[] = $value;
						}
						elseif($value != $itemName) {
							$newOrderArray[] = $value;
						}
					}

					$this->arrHeadOrder = $newOrderArray;
				}
				else {
					$this->arrHeadOrder[$newPosition] = $itemName;	
				}

			}
			
		}
		
		public function requiredFooterFile() {
			include(BASE_DIRECTORY."themes/include_footer.php");
		}
		
		public function setTitle($title) {
			
			if(!isset($this->arrHead['title'])) {
				$this->addHeadItem("title", "<title>".$title."</title>");
			}
			else {
				$this->arrHead['title'] = "<title>".$title."</title>";	
			}
			
		}
		
		public function setThemeName($name) {
			$this->themeName = $name;	
		}
		
		public function setThemeDir($dir) {
			$this->themeDir = $dir;	
		}
		
		public function getThemeName() {
			return $this->themeName;	
		}
		
		public function getThemeDir() {
			$this->themeDir;	
		}
		
		public function setClanName($name) {
			$this->clanName = $name;	
		}
		
		public function getClanName() {
			return $this->clanName;	
		}
		
	}


?>
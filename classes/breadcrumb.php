<?php

	class BreadCrumb {


		protected $title;
		protected $arrBreadcrumb = array();	
		protected $separator = ">";

		
		function setTitle($strTitle) {
			$this->title = $strTitle;
		}
		
		function getTitle() {
			return $this->title;	
		}
		
		function setSeparator($strSeparator) {
			$this->separator = $strSeparator;
		}
		
		
		function clearBreadcrumb() {
			$this->arrBreadcrumb = array();	
		}
		
		
		function addCrumb($crumbName, $crumbLink="") {
			$this->arrBreadcrumb[] = array("link" => $crumbLink, "value" => $crumbName);
		}

		function getBreadcrumb() {

			$breadcrumbs = array();
			foreach($this->arrBreadcrumb as $breadcrumbInfo) {
				
				if($breadcrumbInfo['link'] != "") {
					$breadcrumbs[] = "<a href='".$breadcrumbInfo['link']."'>".$breadcrumbInfo['value']."</a>";
				}
				else {
					$breadcrumbs[] = $breadcrumbInfo['value'];	
				}
				
			}
			
			return implode(" ".$this->separator." ", $breadcrumbs);
		
		}
		
		function popCrumb() {
			return array_pop($this->arrBreadcrumb);
		}
		
		function updateBreadcrumb() {
			echo "
				<script type='text/javascript'>
					$('#breadCrumbTitle').html(\"".addslashes($this->getTitle())."\");
					$('#breadCrumb').html(\"".addslashes($this->getBreadcrumb())."\");
				</script>
			";
		}
		
	}
	
?>
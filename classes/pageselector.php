<?php

	class PageSelector {
		
		
		protected $pageLink;
		protected $numOfPages;
		protected $currentPage;
		protected $amountToShow = 5; // Total page numbers shown before clipping
		
		public function setLink($link) {
			$this->pageLink = $link;			
		}
		
		
		public function setPages($pages) {
			$this->numOfPages = $pages;	
		}
		
		
		public function setCurrentPage($page) {
			$this->currentPage = $page;
		}
		
		public function setAmountToShow($amount) {
			$this->amountToShow = $amount;			
		}
		
		public function validatePageNumber($pageNum) {
			
			if(!isset($pageNum) || !is_numeric($pageNum) || $pageNum > $this->numOfPages || $pageNum < 1) {
				$pageNum = 1;	
			}
			
			return $pageNum;
		}
		
		public function getPageNumbersShown() {

			$arrReturn = array();
			
			$midAmount = floor(($this->amountToShow/2));
			
			for($i=$this->currentPage; $i<=($this->currentPage+$midAmount); $i++) {
				$arrReturn[] = $i;
			}
			
			
			for($i=($this->currentPage-$midAmount); $i<$this->currentPage; $i++) {
				$arrReturn[] = $i;	
			}			
			
			$arrReturn = array_unique($arrReturn);
			
			foreach($arrReturn as $key => $value) {
				$maxValue = max($arrReturn) <= $this->numOfPages ? max($arrReturn) : $this->numOfPages;
				$minValue = min($arrReturn) > 0 ? min($arrReturn) : 1;

				if($value < 1 && $maxValue != $this->numOfPages) {
					$arrReturn[$key] = $maxValue+1;
				
				}
				elseif($value < 1 && $maxValue == $this->numOfPages) {
					unset($arrReturn[$key]);	
				}
				
				
				if($value > $this->numOfPages && $minValue != 1) {
					$arrReturn[$key] = $minValue-1;
				}
				elseif($value > $this->numOfPages && $minValue == 1) {
					unset($arrReturn[$key]);	
				}
			}
			sort($arrReturn);

			
			return $arrReturn;
						
		}
		
		
		public function show() {
			
			if($this->numOfPages > 1) {

				$arrPages = $this->getPageNumbersShown();
				
				$dispFirstDots = ($arrPages[0] == 2) ? "" : " ... ";
				$dispLastDots = ($arrPages[count($arrPages)-1] == $this->numOfPages-1) ? "" : " ... ";
				
				$dispPrevButton = ($this->currentPage == 1) ? "" : "<span class='pageArrowButton'><a href='".$this->pageLink.($this->currentPage-1)."'>&laquo; Prev</a></span>";
				$dispNextButton = ($this->currentPage == $this->numOfPages) ? "" : "<span class='pageArrowButton'><a href='".$this->pageLink.($this->currentPage+1)."'>Next &raquo;</a></span>";
				$dispFirstPageButton = (in_array(1, $arrPages)) ? "" : "<span class='pageNum'><a href='".$this->pageLink."1'>1</a></span>".$dispFirstDots;
				$dispLastPageButton = (in_array($this->numOfPages, $arrPages)) ? "" : $dispLastDots."<span class='pageNum'><a href='".$this->pageLink.$this->numOfPages."'>".$this->numOfPages."</a></span>";
				
				echo "<div class='pageSelectorDiv'>";
				echo $dispPrevButton.$dispFirstPageButton;
				foreach($arrPages as $pageNum) {
					
					if($pageNum == $this->currentPage) {
						echo "<div class='pageNum currentPage'>".$pageNum."</div>";
					}
					else {
						echo "<div class='pageNum'><a href='".$this->pageLink.$pageNum."'>".$pageNum."</a></div>";
					}	
					
				}
				echo $dispLastPageButton.$dispNextButton;
				echo "</div>";
			}
			
		}
		
		
		
	}

?>
<?php


	class ForumCategory extends BasicOrder {
		
		
		protected $forumBoardObj;
		
		public function __construct($sqlConnection) {

			$this->MySQL = $sqlConnection;
			$this->strTableName = $this->MySQL->get_tablePrefix()."forum_category";
			$this->strTableKey = "forumcategory_id";
			$this->strAssociateTableName = $this->MySQL->get_tablePrefix()."forum_board";
			$this->strAssociateKeyName = "forumboard_id";	
			
			$this->forumBoardObj = new ForumBoard($sqlConnection);
			
		}
		
		
		/*
		public function delete() {
			
			$boards = $this->getAssociateIDs();
			foreach($boards as $boardID) {

				if($this->forumBoardObj->select($boardID)) {
					$this->forumBoardObj->delete();	
				}
				
			}
			
			parent::delete();
			
		}
		*/
		
	}



?>
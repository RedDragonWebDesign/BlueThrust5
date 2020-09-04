<?php

	class RibbonWoWMenu extends btThemeMenu {
		
		
		public $defaultShoutboxWidth = 150;
		
		public function __construct($sqlConnection) {
			
			parent::__construct("ribboncamo", $sqlConnection);	
			
		}	
		
		
		public function displayMenuCategory($loc="top") {
			// Placeholder function
			
			$menuCatInfo = $this->menuCatObj->get_info();
			
			if($loc == "top" && $this->intMenuSection != 2) {
				// Side Menus
				
				
				$menuCatCSS = ($this->intMenuSection == 0) ? "leftMenuTitle" : "rightMenuTitle";
				
				if($menuCatInfo['headertype'] == "image") {
					echo "<img src='".MAIN_ROOT.$menuCatInfo['headercode']."'>";
				}
				else {
					$menuCatInfo['headercode'] = $this->replaceKeywords($menuCatInfo['headercode']);			
					echo "<div class='menuTitle ".$menuCatCSS."'>".$menuCatInfo['headercode']."</div>";
				}
				
				echo "<div class='menuItemsDiv'>";
					
			}
			elseif($this->intMenuSection != 2) {
				
				echo "</div>";
				
			}
			
			
			
		}
		
		
		public function displayLink() {
			
			if($this->intMenuSection == 2) {

				$menuLinkInfo = $this->menuItemObj->objLink->get_info();
				$checkURL = parse_url($menuLinkInfo['link']);
				
				if(!isset($checkURL['scheme']) || $checkURL['scheme'] = "") {
					$menuLinkInfo['link'] = MAIN_ROOT.$menuLinkInfo['link'];
				}
				
				
				echo "<div class='topMenuItem'><a href='".$menuLinkInfo['link']."' target='".$menuLinkInfo['linktarget']."'>".strtoupper($this->menuItemInfo['name'])."</a></div>";
				
				
			}
			else {

				parent::displayLink();
				
			}
		}
				
		
		
		public function displayLoggedOut() {
			// Placeholder function
			
			echo "
			
			
				<form class='loginFormSection' action='".MAIN_ROOT."login.php' method='post' style='position: relative; padding: 0px; margin: 0px'>
					<p>
						Username:
						<input type='text' name='user' class='loginTextbox'>
					</p>
					<p>
						Password:
						<input type='password' name='pass' class='loginTextbox'>
					</p>
					<p>Remember Me: <input type='checkbox' name='rememberme' value='1'></p>
					<p><a href='".MAIN_ROOT."signup.php'>Sign Up</a><br><a href='".MAIN_ROOT."forgotpassword.php'>Forgot Password?</a></p>
					<br>
					<p>
						<input type='submit' name='submit' class='loginSubmitButton' value='LOG IN'>
					</p>
					<div style='clear: both'></div>
				</form>
			
			
			";
			
			
		}
		
		public function displayLoggedIn() {
			// Placeholder function
			
			echo "
			
				<div class='loggedInSection'>
					<b>Account Name:</b><br>
					<p>".$this->memberObj->getMemberLink()."</p>
					<div class='dottedLine' style='margin: 5px 0px'></div>
					<b>Rank:</b>
					<p>".$this->data['memberRank']."</p>
					<div class='dottedLine' style='margin: 5px 0px'></div>
					<b>Member Options:</b><br>
					<ul class='loggedInMenuList'>
						<li><a href='".MAIN_ROOT."members'>My Account</a></li>
						<li>".$this->data['pmLink']."</li>
						<li><a href='".MAIN_ROOT."members/signout.php'>Sign Out</a></li>
					</ul>
				</div>
			
			";
			
		}
		
		
	}

?>
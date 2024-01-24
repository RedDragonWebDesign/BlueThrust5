<?php

	class DestinyMenu extends btThemeMenu {


		public function __construct($sqlConnection) {

			parent::__construct("bluegrid", $sqlConnection);

		}


		public function displayMenuCategory($loc="top") {
			// Placeholder function

			$menuCatInfo = $this->menuCatObj->get_info();

			if ($loc == "top" && $this->intMenuSection != 2) {

				if ($menuCatInfo['headertype'] == "image") {
					echo "<img src='".MAIN_ROOT.$menuCatInfo['headercode']."'>";
				}
				else {
					$menuCatInfo['headercode'] = $this->replaceKeywords($menuCatInfo['headercode']);
					echo "<div class='menuCatTitle'>".$menuCatInfo['headercode']."</div>";
				}

				echo "<div class='menuItems'>";

			}
			elseif ($this->intMenuSection != 2) {

				echo "</div>";

			}

		}


		public function displayLink() {

			if ($this->intMenuSection == 2) {

				$menuLinkInfo = $this->menuItemObj->objLink->get_info();
				$checkURL = parse_url($menuLinkInfo['link']);

				if (!isset($checkURL['scheme']) || $checkURL['scheme'] = "") {
					$menuLinkInfo['link'] = MAIN_ROOT.$menuLinkInfo['link'];
				}

				echo "<div class='topMenuItem'><a href='".$menuLinkInfo['link']."' target='".$menuLinkInfo['linktarget']."'>".strtoupper($this->menuItemInfo['name'])."</a></div>";

			}
			else {

				parent::displayLink();

			}

		}

		public function displayImage() {

			if ($this->intMenuSection == 2) {

				$menuImageInfo = $this->menuItemObj->objImage->get_info();
				$checkURL = parse_url($menuItemInfo['imageurl']);
				if (!isset($checkURL['scheme']) || $checkURL['scheme'] = "") {
					$menuImageInfo['imageurl'] = MAIN_ROOT.$menuImageInfo['imageurl'];
				}

				$dispSetWidth = ($menuImageInfo['width'] != 0) ? "width: ".$menuImageInfo['width']."px; " : "";
				$dispSetHeight = ($menuImageInfo['height'] != 0) ? "height: ".$menuImageInfo['height']."px; " : "";

				echo "
					<div style='text-align: ".$menuImageInfo['imagealign']."; display: inline-block; margin: 0px 1.42%; padding: 5px'>
				";

				if ($menuImageInfo['link'] != "") {

					$checkURL = parse_url($menuImageInfo['link']);
					if (!isset($checkURL['scheme']) || $checkURL['scheme'] = "") {
						$menuImageInfo['link'] = MAIN_ROOT.$menuImageInfo['link'];
					}

					echo "
						<a href='".$menuImageInfo['link']."' target='".$menuImageInfo['linktarget']."'><img src='".$menuImageInfo['imageurl']."' style='".$dispSetWidth.$dispSetHeight."' title='".$menuItemInfo['name']."'></a>
					";
				}
				else {
					echo "
						<img src='".$menuImageInfo['imageurl']."' title='".$menuItemInfo['name']."' style='".$dispSetWidth.$dispSetHeight."'>
					";
				}

				echo "
					</div>
				";

			}
			else {
				parent::displayImage();
			}

		}


		public function displayLoggedOut() {
			// Placeholder function

			echo "
				<form action='".MAIN_ROOT."login.php' method='post'>
					<div class='loginDiv'>
						<div class='loginFormGroup'>
							USERNAME:<br>
							<input type='text' class='loginTextbox' name='user'>
						</div>
						
						<div class='loginFormGroup'>
							PASSWORD:<br>
							<input type='password' class='loginTextbox' name='pass'>
						</div>
						
						<div class='loginFormGroup'>
							<input type='checkbox' name='rememberme' value='1' checked>
						</div>
						
						<div class='loginFormGroup'>
							<input type='submit' name='submit' value='LOGIN' class='loginButton'>
						</div>
					</div>
				</form>
			";

		}

		public function displayLoggedIn() {
			// Placeholder function

			$rank = new Rank($this->MySQL);
			$rank->select($this->memberObj->get_info("rank_id"));

			$memberLink = $this->memberObj->getMemberLink(array("wrapper" => false));
			$dispMemberLink = "".$rank->get_info_filtered("name")." <a href='".$memberLink."'>".$this->memberObj->get_info_filtered("username")."</a>";

			echo "
				<div class='loginDiv'>
					
					<div class='loggedInAvatar'>".$this->memberObj->getProfilePic()."</div>
					<div class='loggedInInfo'>
						Logged In as <b>".$dispMemberLink."</b>
						<p align='center'>
							<a href='".MAIN_ROOT."members'>My Account</a> - ".$this->data['pmLink']." - <a href='".MAIN_ROOT."members/signout.php'>Log Out</a>
						</p>
					</div>
				
				</div>
			";
		}


	}
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

	class Form {
		
		public $formName;
		public $components;
		public $saveAdditional;
		public $objSave;
		public $attributes;
		public $saveType;	// Add or Update
		public $wrapper = array("<div class='formDiv'>", "</div>");
		public $errors = array();
		public $saveMessage;
		public $saveMessageTitle;
		public $afterSave;
		public $saveLink;
		public $prefillValues = true;
		public $blnSaveResult;
		public $beforeAfter = false;
		public $isContainer = false;
		public $embedJS;
		public $attachmentForm;
		public $attachmentObj;
		private $arrDeleteFiles = array();
		public $arrSkipPrefill = array();
		
		
		private $richtextboxJSFile;
		private $colorpickerJSFile;
		
		/*
		 * Components Array Example
		 * 
		 * $arr = array(
		 * 		'display_name' => 'Username',
		 * 		'type' => 'text', (text, textarea, select, checkbox, radio, button, submit, file, custom),
		 * 		'tooltip' => 'tool tip text',
		 * 		'value' => '' // you can also put value in the attributes array, but use here instead to pre-fill multi-select type inputs and textareas
		 * 		'attributes' => array('name' => 'component_name', 'id' => 'component_id', 'style' => 'component_style', 'class=' => 'component_class'),
		 * 		'sortorder' => 1,
		 * 		'db_name' => 'column_name',
		 * 		'html' => '' // Used only if the type is custom
		 * 		'options' => array(value => display) // Used for checkboxes, radio buttons and select boxes,
		 * 		'validate' => array("NOT_BLANK", "NUMBER_ONLY")
		 * 
		 * );
		 * 
		 * $components = $arr;
		 * 
		 */
		
		public function __construct($args=array()) {
			
			$this->buildForm($args);
			$this->richtextboxJSFile = "<script type='text/javascript' src='".MAIN_ROOT."js/tiny_mce/jquery.tinymce.js'></script>";
			$this->colorpickerJSFile = "<script type='text/javascript' src='".MAIN_ROOT."js/colorpicker/jquery.miniColors.js'></script><link rel='stylesheet' media='screen' type='text/css' href='".MAIN_ROOT."js/colorpicker/jquery.miniColors.css'>";
		}
		
		public function buildForm($args) {
			
			$this->formName = $args['name'];
			$this->components = $args['components'];
			$this->objSave = $args['saveObject'];
			$this->attributes = $args['attributes'];
			$this->saveType = $args['saveType'];
			$this->description = $args['description'];
			$this->saveMessage = $args['saveMessage'];
			$this->saveMessageTitle = $args['saveMessageTitle'];
			$this->afterSave = $args['afterSave'];
			$this->saveLink = $args['saveLink'];
			$this->saveAdditional = $args['saveAdditional'];
			$this->embedJS = $args['embedJS'];
			$this->attachmentForm = false;
			
			if(isset($args['wrapper'])) {
				$this->wrapper = $args['wrapper'];	
			}
			
			if(isset($args['beforeAfter'])) {
				$this->beforeAfter = $args['beforeAfter'];	
			}

			if(isset($args['mysql'])) {
				$this->attachmentObj = new Download($args['mysql']);
				$this->attachmentForm = true;	
			}
			
			
			if($args['prefill']) {
				$this->arrSkipPrefill = $args['skipPrefill'];
				$this->prefillDBValues();				
			}
			
		}
		
		/*
		 * - show Method -
		 * 
		 * Used to display the actual form
		 * 
		 */
		
		public function show() {
			global $MAIN_ROOT, $hooksObj;
			
			$hooksObj->run($this->formName);
			
			uasort($this->components, array("Form", "sortForm"));
			
			$countRichTextbox = 0;
			
			$blnFileUploadForm = false;
			
			$displayForm = "";
			$afterJS = $this->embedJS;
			
			foreach($this->components as $componentName => $componentInfo) {
				
				$dispAttributes = $this->convertAttributes($componentInfo['attributes']);
				
				$displayForm .= $componentInfo['before_html'];
				
				// Output Component Name
				if($componentInfo['display_name'] != "") {
					$dispToolTip = ($componentInfo['tooltip'] != "") ? " <a href='javascript:void(0)' onmouseover=\"showToolTip('".addslashes($componentInfo['tooltip'])."')\" onmouseout='hideToolTip()'>(?)</a>" : "";					
					$displayForm .= "
						<label class='formLabel' style='display: inline-block'>".$componentInfo['display_name'].":".$dispToolTip."</label>		
					";
				}
				
				// Output input
				switch($componentInfo['type']) {
					case "autocomplete":
						$afterJS .= $this->autocompleteJS($componentInfo['options']['list'], $componentInfo['options']['real_id'], $componentInfo['options']['fake_id']);
						$fakeComponentName = "fake".$componentName;
						$displayForm .= "<input type='text' name='".$fakeComponentName."' value='".filterText($_POST[$fakeComponentName])."' ".$dispAttributes." id='".$componentInfo['options']['fake_id']."'><input type='hidden' name='".$componentName."' value='".$componentInfo['value']."' id='".$componentInfo['options']['real_id']."'>";
						break;
					case "textarea":
						$displayForm .= "<textarea name='".$componentName."' ".$dispAttributes.">".$componentInfo['value']."</textarea>";
						break;
					case "richtextbox":
						$afterJS .= $this->richTextboxJS($componentInfo['attributes']['id'], $componentInfo['allowHTML']);
						$displayForm .= "
							<div class='formInput' style='width: 100%'>
								<textarea name='".$componentName."' ".$dispAttributes.">".$componentInfo['value']."</textarea>
							</div>
						";
						$countRichTextbox++;
						unset($GLOBALS['richtextEditor']);
						break;
					case "codeeditor":
						$afterJS .= $this->codeEditorJS($componentInfo['attributes']['id']);
						$displayForm .= "
							<div style='background-color: white; position: relative; margin-top: 10px'><div id='".$componentInfo['attributes']['id']."' class='codeEditor'>".$componentInfo['value']."</div></div>
							<textarea id='".$componentInfo['attributes']['id']."_code' name='".$componentName."' style='display: none'></textarea>
						";
						break;
					case "datepicker":
						
						$datePick = new DateTime();
						$datePick->setTimestamp($componentInfo['value']/1000);
						$datePick->setTimezone(new DateTimeZone("UTC"));
						
						$formatDatePick = $datePick->format("n-j-Y");
						
						$afterJS .= $this->datepickerJS($componentInfo['attributes']['id'], $componentInfo['options']);
						$displayForm .= "<input type='text' value='".$componentInfo['options']['defaultDate']."' ".$dispAttributes." readonly='readonly'><input type='hidden' id='".$componentInfo['options']['altField']."' name='".$componentName."' value='".$formatDatePick."'>";
						break;
					case "select":
						$displayForm .= "<select name='".$componentName."' ".$dispAttributes.">";
						foreach($componentInfo['options'] as $optionValue => $displayValue) {
							$dispSelected = "";
							if($optionValue == $componentInfo['value']) {
								$dispSelected = " selected";	
							}
							
							if(in_array($optionValue, $componentInfo['non_selectable_items'])) {
								$dispSelected = " disabled class='disabledSelectItem'";
							}
							
							$displayForm .= "<option value='".$optionValue."'".$dispSelected.">".$displayValue."</option>";	
						}
						$displayForm .= "</select>";
						break;
					case "checkbox": // Checkbox and radio are basically same thing, so checkbox falls through to radio section
					case "radio":
						if(is_array($componentInfo['options'])) {	
							$componentCounter = 1;					
							foreach($componentInfo['options'] as $optionValue => $displayValue) {
								$dispSelected = "";
								
								$newComponentName = $componentName;
								if(count($componentInfo['options']) > 1) {
									$newComponentName .= "_".$componentCounter;
									
									if($componentCounter > 1) {
										$displayForm .= "<label class='formLabel' style='display: inline-block'></label> ";	
									}
									
									$componentCounter++;
								}
								
								
								if($optionValue == $componentInfo['value']) {
									$dispSelected = " checked";
								}
								
								$dispLabel = ($displayValue != "") ? "<label class='formLabel formInput'>".$displayValue."</label><br>" : "";
								
								$displayForm .= "<input name='".$newComponentName."' type='".$componentInfo['type']."' value='".$optionValue."' ".$dispAttributes." ".$dispSelected."> ".$dispLabel;
							}
						}
						else {
							
							$dispChecked = "";
							if($componentInfo['checked']) {
								$dispChecked = " checked";	
							}
							
							$displayForm .= "<input name='".$componentName."' type='".$componentInfo['type']."' value='".$componentInfo['value']."' ".$dispAttributes.$dispChecked.">";	
						}
						break;
					case "file":
						$blnFileUploadForm = true;
						
						$displayForm .= "
							<div class='formInput' style='margin-bottom: 20px'>
								File:<br>
								<input type='file' name='".$componentName."_file' ".$dispAttributes.">
								<ul class='tinyFont' style='margin-top: 0px'>";
						if(is_array($componentInfo['options']['file_types'])) {
							$displayForm .= "<li>File Types: ".implode(", ", $componentInfo['options']['file_types'])."</li>";
						}
						
						if($componentInfo['options']['default_dimensions'] != "") {
							$displayForm .= "<li>Dimensions: ".$componentInfo['options']['default_dimensions']."</li>";	
						}
						
						$displayForm .= "<li><a href='javascript:void(0)' onmouseover=\"showToolTip('The file size upload limit is controlled by your PHP settings in the php.ini file.')\" onmouseout='hideToolTip()'>File Size: ".ini_get("upload_max_filesize")."B or less</a></li></ul>"; 
						
						$displayForm .= "<p><b><i>OR</i></b></p>";
						
						$displayForm .= "URL:<br><input type='text' name='".$componentName."_url' ".$dispAttributes.">";
						
						if($componentInfo['value'] != "") {
							
							$displayForm .= "<br><a href='".$MAIN_ROOT.$componentInfo['value']."' target='_blank'>View Saved File</a>";
							
						}
						
						
						$displayForm .= "</div>";
						break;
					case "section":
						
						$displayForm .= "<div ".$dispAttributes.">";
						if($componentInfo['options']['section_title'] != "") {
							$displayForm .= "<p class='dottedLine' style='margin: 0px; margin-top: 25px; padding-bottom: 2px'><b>".$componentInfo['options']['section_title']."</b></p>";
						}
						
						if($componentInfo['options']['section_description'] != "") {
							$displayForm .= "<p>".$componentInfo['options']['section_description']."</p>";
						}
						
						if($componentInfo['components'] != "" && is_array($componentInfo['components'])) {
							
							$sectionFormObj = new Form();
							$sectionFormObj->isContainer = true;
							$sectionFormObj->components = $componentInfo['components'];
							$displayForm .= $sectionFormObj->show();
							
						}
						$displayForm .= "</div>";
						
						break;
					case "beforeafter":
						$this->beforeAfter = true;
						foreach($componentInfo['options'] as $optionValue => $displayValue) {
							$dispSelected = "";	
							if($optionValue == $componentInfo['before_after_value']) {
								$dispSelected = " selected";
							}
							
							if($optionValue != $componentInfo['value']) {
								$displayOptions .= "<option value='".$optionValue."'".$dispSelected.">".$displayValue."</option>";
							}
							
						}
						
						$afterSelected = ($componentInfo['after_selected'] == "after") ? " selected" : "";
						
						$displayForm .= "<div class='formInput'>
											<select name='".$componentName."_beforeafter' ".$dispAttributes.">
												<option value='before'>Before</option>
												<option value='after'".$afterSelected.">After</option>
											</select>
											<br>
											<select name='".$componentName."' ".$dispAttributes.">
											".$displayOptions."
											</select>
										</div>
										";
						
						break;
					case "custom":
						break;
					case "colorpick":
						
						$afterJS .= $this->colorpickerJS($componentInfo['attributes']['id'], $componentInfo['allowHTML']);
						$displayForm .= "<input type='text' name='".$componentName."' value='".$componentInfo['value']."' ".$dispAttributes.">";
						
						break;
					default:
						$displayForm .= "<input type='".$componentInfo['type']."' name='".$componentName."' value='".$componentInfo['value']."' ".$dispAttributes.">";					
				}
				
				$displayForm .= $componentInfo['html'];
				
				if($componentInfo['type'] != "section") {
					$displayForm .= "<br>";
				}
				
			}
			
			$dispFormAttributes = $this->convertAttributes($this->attributes);
			if($blnFileUploadForm) { $dispFormAttributes .= "  enctype='multipart/form-data'"; }
			
			$dispErrors = "";
			if(count($this->errors) > 0) {
				$dispErrors = "<div class='errorDiv'><strong>The following errors occurred:</strong><ul>";
				foreach($this->errors as $dispError) {
					$dispErrors .= "<li>".$dispError."</li>";
				}
				$dispErrors .= "</ul></div>";
			}
			
			if(!$this->isContainer) {
				echo "<form ".$dispFormAttributes.">".$this->wrapper[0].$dispErrors.$this->description."<div class='formTable'>".$displayForm."</div>".$this->wrapper[1]."<input type='hidden' name='checkCSRF' value='".$_SESSION['csrfKey']."'></form>";
			}

			
			if($afterJS != "" && !$this->isContainer) {
				echo "
					<script type='text/javascript'>
						".$afterJS."
					</script>
				";
			}
			
		
			if($this->isContainer) {
				$js = "";
				if($afterJS != "") {
					$js = "<script type='text/javascript'>
							".$afterJS."
						</script>";
				}
				
				return $displayForm.$js;	
			}
			
			
		}
		
		public function prefillPostedValues() {
			
			$filterTypes = array("file", "beforeafter", "button");
			
			foreach($this->components as $componentName => $componentInfo) {
				if(!in_array($componentInfo['type'], $filterTypes)) {
					$this->components[$componentName]['value'] = $_POST[$componentName];
				}		
			}
			
		}
		
		public function prefillDBValues() {

			if($this->saveType == "update") {
				$info = $this->objSave->get_info_filtered();
				foreach($this->components as $key => $value) {
					if($this->components[$key]['db_name'] != "" && !in_array($this->components[$key]['db_name'], $this->arrSkipPrefill)) {
						$this->components[$key]['value'] = $info[$this->components[$key]['db_name']];
					}
				}
				
			}
			
		}
		
		
		/*
		 * - validate -  
		 * 
		 * Used to validate the data entered into the form.
		 * 
		 * Presets: NOT_BLANK, NUMBER_ONLY, RESTRICT_TO_OPTIONS
		 * 
		 * 
		 * RESTRICT_TO_OPTIONS: Used for components with options (i.e. selectboxes, multi-checkboxes and multi-radiobuttons)
		 * 
		 */
		
		
		public function validate() {
			$returnVal = false;
			foreach($this->components as $componentName => $componentInfo) {
			
				foreach($componentInfo['validate'] as $validateMethod) {
					
					$arrValidate = array();
					if(is_array($validateMethod)) {
						$arrValidate = $validateMethod;
						$validateMethod = $arrValidate['name'];
					}
					
				
					
					switch($validateMethod) {
						case "NOT_BLANK":
							if(($componentInfo['type'] == "checkbox" || $componentInfo['type'] == "radio") && count($componentInfo['options']) > 1) {
								$componentCounter = 1;
								$countBlanks = 0;
								foreach($componentInfo['options'] as $optionName => $optionValue) {
									
									$fullComponentName = $componentName."_".$componentCounter;
									if(trim($_POST[$fullComponentName]) == "") {
										$countBlanks++;							
									}
																		
									$componentCounter++;
								}

								if($countBlanks == count($componentInfo['options'])) {
									$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] : "You must select at least one value for ".$componentInfo['display_name'].".";
								}
								
							}
							elseif($componentInfo['type'] != "file" && trim($_POST[$componentName]) == "") {
								$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] : $componentInfo['display_name']." may not be blank.";
							}
							break;
						case "NUMBER_ONLY":
							if(!is_numeric($_POST[$componentName]) && $componentInfo['type'] != "datepicker") {
								$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] : $componentInfo['display_name']." may only be a numeric value.";	
							}
							elseif($componentInfo['type'] == "datepicker") {

								$checkDate = explode("-", $_POST[$componentName]);
								
							}
							break;
						case "POSITIVE_NUMBER":
							if($_POST[$componentName] < 0) {
								$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] : $componentInfo['display_name']." must be a positive number.";
							}
							break;
						case "RESTRICT_TO_OPTIONS":
							
							if(is_array($componentInfo['options'])) {
								$arrPostNames = array();
								$arrPossibleValues = array();
								$postCounter = 1;
								foreach($componentInfo['options'] as $optionValue => $displayValue) {	
									$arrPossibleValues[] = $optionValue;
									$arrPostNames[] = $componentName."_".$postCounter;
									$postCounter++;
								}
								
								if(($componentInfo['type'] == "checkbox" || $componentInfo['type'] == "radio") && count($componentInfo['options']) > 1) {
									$countErrors = 0;
									foreach($arrPostNames as $postName) {

										if(isset($_POST[$postName]) && !in_array($_POST[$postName], $arrPossibleValues)) {
											$countErrors++;
										}
										
									}
									
									if($countErrors > 0) {
										$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] : "You selected an invalid value for ".$componentInfo['display_name'].".";										
									}
									
								}
								elseif(!in_array($_POST[$componentName], $arrPossibleValues)) {
									$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] : "You selected an invalid value for ".$componentInfo['display_name'].".";									
								}
								
							}
							
							break;
						case "IS_SELECTABLE":
							
							$selectBackID = isset($arrValidate['select_back']) ? $arrValidate['selectObj']->get_info($arrValidate['select_back']) : "";
							
							if(!$arrValidate['selectObj']->select($_POST[$componentName])) {
								$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] : "You selected an invalid value for ".$componentInfo['display_name'].".";
							}
							
							$arrValidate['selectObj']->select($selectBackID);
							
							break;
						case "IS_NOT_SELECTABLE":
							$selectBackID = isset($arrValidate['select_back']) ? $arrValidate['selectObj']->get_info($arrValidate['select_back']) : "";
							
							if($arrValidate['selectObj']->select($_POST[$componentName])) {
								$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] : "The value selected for ".$componentInfo['display_name']." is already in use.";
							}
							
							$arrValidate['selectObj']->select($selectBackID);
							
							break;
						case "CHECK_LENGTH":
							
							if($arrValidate['min_length'] != "" && strlen(trim($_POST[$componentName])) < $arrValidate['min_length']) {
								$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] : "The value for ".$componentInfo['display_name']." must be at least ".$arrValidate['min_length']." characters long.";
							}
							
							if($arrValidate['max_length'] != "" && strlen(trim($_POST[$componentName])) > $arrValidate['max_length']) {
								$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] :  "The value for ".$componentInfo['display_name']." can be a max of ".$arrValidate['min_length']." characters long.";							
							}
														
							break;
						case "EQUALS_VALUE":
							
							if($arrValidate['value'] != $_POST[$componentName]) {
								$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] : "You entered an incorrect value for ".$componentInfo['display_name'].".";
							}
							
							break;
						case "NOT_EQUALS_VALUE":
							if($arrValidate['value'] == $_POST[$componentName]) {
								$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] : "You entered an incorrect value for ".$componentInfo['display_name'].".";
							}
							break;
						case "GREATER_THAN":
							if($arrValidate['value'] > strlen(trim($_POST[$componentName]))) {
								$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] : $componentInfo['display_name']." must be a value greater than ".$arrValidate['value'].".";
							}
							break;
						case "LESS_THAN":
							if($arrValidate['value'] < strlen(trim($_POST[$componentName]))) {
								$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] : $componentInfo['display_name']." must be a value less than ".$arrValidate['value'].".";
							}
							break;
						case "VALIDATE_ORDER":
							
							if($arrValidate['orderObject'] != "") {
								
								if($arrValidate['set_category'] != "") {
									$arrValidate['orderObject']->setCategoryKeyValue($arrValidate['set_category']);
								}
								
								$checkOrder = $arrValidate['orderObject']->validateOrder($_POST[$componentName], $_POST[$componentName."_beforeafter"], $arrValidate['edit'], $arrValidate['edit_ordernum']);	
								if($checkOrder === false) {
									$this->errors[] = ($arrValidate['customMessage'] != "") ? $arrValidate['customMessage'] : "You selected an invalid ".$componentInfo['display_name'].".";							
								}
								else {
									$_POST[$componentName] = $checkOrder;
									$this->components[$componentName]['resortOrderObject'] = $arrValidate['orderObject'];
									
								}

								if(isset($arrValidate['select_back'])) {
									$arrValidate['orderObject']->select($arrValidate['select_back']);
								}
								
							}
							
							break;
						default:
							if(!is_array($validateMethod)) {
								call_user_func($validateMethod);
							}
							else {
								call_user_func_array($validateMethod['function'], $validateMethod['args']);	
							}
					}
					
				}
				
				
								
				if($componentInfo['type'] == "file" && $_POST[$componentName] == "") {
					// Check Upload
					$uploadFile = "noupload";
					$outsideLink = false;
					if($_FILES[$componentName."_file"]['name'] != "") {
						$uploadFile = new BTUpload($_FILES[$componentName."_file"], $componentInfo['options']['file_prefix'], $componentInfo['options']['save_loc'], $componentInfo['options']['file_types']);
					}
					elseif($_POST[$componentName."_url"] != "") {
						$uploadFile = new BTUpload($_POST[$componentName."_url"], $componentInfo['options']['file_prefix'], $componentInfo['options']['save_loc'], $componentInfo['options']['file_types'], $componentInfo['options']['ext_length'], true);
						$outsideLink = true;
					}
					
					if($uploadFile != "noupload") {
						
						
						
						if($this->attachmentForm) {
							$this->attachmentObj->setUploadObj($uploadFile);
							$this->attachmentObj->setCategory($componentInfo['options']['download_category']);
							
							if(!$this->attachmentObj->uploadFile()) {
								$this->errors[] = "Unable to upload ".$componentInfo['display_name'].". Make sure that the file is not too big and correct extension.";	
							}
							else {
								$_POST[$componentName] = $componentInfo['options']['append_db_value'].$uploadFile->getUploadedFileName();
							}
							
							
							
						}
						else {
							if(!$uploadFile->uploadFile()) {
								$this->errors[] = "Unable to upload ".$componentInfo['display_name'].". Make sure that the file is not too big and correct extension.";	
							}
							else {
								$_POST[$componentName] = $componentInfo['options']['append_db_value'].$uploadFile->getUploadedFileName();
								// Check if updating, and delete old file
								
								if($this->saveType != "add" && $componentInfo['db_name'] != "" && $this->objSave->get_info($componentInfo['db_name']) != "") {
									$this->arrDeleteFiles[] = $this->objSave->get_info($componentInfo['db_name']);
								}
								
							}
						}
						
						
						
					}
					elseif($componentInfo['value'] != "") {
						$_POST[$componentName] = $componentInfo['value'];	
					}
					
					if(in_array("NOT_BLANK", $componentInfo['validate'])) {
						if($_POST[$componentName] == "") {
							$this->errors[] = $componentInfo['display_name']." may not be blank.";
						}
					}
					
				}
				elseif($componentInfo['type'] == "datepicker") {
					
					$formatDate = explode("-", $_POST[$componentName]);
					$datePick = new DateTime();
					$datePick->setTimezone(new DateTimeZone("UTC"));
					$datePick->setDate($formatDate[2], $formatDate[0], $formatDate[1]);
					$dateTimestamp = $datePick->format("U");
					
					$_POST[$componentName] = $dateTimestamp;	
				}				
					
				
			}
			
			
			if($_POST['checkCSRF'] != $_SESSION['csrfKey']) {
				$this->errors[] = "Invalid CSRF Token.  Possible Hacking attempt?";
			}
			
			
			if(count($this->errors) == 0) {
				$returnVal = true;	
			}

			
			return $returnVal;
		}
		
		
		
		/*
		 * - save -
		 * 
		 * Saves the form data to the database
		 * 
		 */
		
		
		public function save() {
			global $hooksObj;
			$hooksObj->run($this->formName);
			
			$this->blnSaveResult = false;
			
			$arrResortOrder = array();
			if($this->validate()) {

				$arrColumns = array();
				$arrValues = array();
				foreach($this->components as $componentName => $componentInfo) {			
					
					if(isset($componentInfo['db_name']) && $componentInfo['db_name'] != "") {
						$arrColumns[] = $componentInfo['db_name'];
						$arrValues[] = $_POST[$componentName];
					}

					if($componentInfo['type'] == "beforeafter") {
						$this->beforeAfter = true;	
					}
					
				}
				
				
				foreach($this->saveAdditional as $dbName => $dbValue) {
					$arrColumns[] = $dbName;
					$arrValues[] = $dbValue;	
				}
				
				
				if($this->objSave != "" && $this->saveType == "add") {
					$this->blnSaveResult = $this->objSave->addNew($arrColumns, $arrValues);
				}
				elseif($this->objSave != "") {
					$this->blnSaveResult = $this->objSave->update($arrColumns, $arrValues);
					
					
					if(count($this->arrDeleteFiles) > 0) {
						foreach($this->arrDeleteFiles as $file) {
							unlink(BASE_DIRECTORY.$file);
						}
					}
					
					
				}
				else {
					$this->blnSaveResult = true;
				}
	
				
				if(!$this->blnSaveResult) {
					$this->errors[] = "Unable to save information to the database.  Please contact the website administrator.";
				}
				else {

					if(is_array($this->afterSave)) {
						foreach($this->afterSave as $saveFunction) {
							if(!is_array($saveFunction)) {
								call_user_func($saveFunction);
							}
							else {
								call_user_func_array($saveFunction['function'], $saveFunction['args']);	
							}
							
						}
					}
					
					
					if($this->beforeAfter) {
						foreach($this->components as $componentName => $componentInfo) {	
							
							// Check for Display Order input types, need to resort order
							if($componentInfo['type'] == "beforeafter" && $componentInfo['resortOrderObject'] != "") {
								$componentInfo['resortOrderObject']->resortOrder();			
							}
							
						}
					}
					
				}
				
			}
			
			return $this->blnSaveResult;
			
		}
		
		static function sortForm($a, $b) {
			$returnVal = 1;
			if($a['sortorder'] == $b['sortorder']) {
				$returnVal = 0;	
			}
			elseif($a['sortorder'] < $b['sortorder']) {
				$returnVal = -1;
			}
			
			return $returnVal;
		}
		
		/*
		 * - convertAttributes Function -
		 * 
		 * Converts attributes to string format if it's an array
		 * example output: name = 'form_name' id='form_id'
		 * 
		 */
		
		public function convertAttributes($attr) {
			
			if(is_array($attr)) {
				$returnVal = "";
				foreach($attr as $attrName => $attrValue) {
					$returnVal .= $attrName."='".$attrValue."' ";
				}
				
			}
			else {
				$returnVal = $attr;
			}
			
			return $returnVal;
		}
		
		
		public function showSuccessDialog() {
			
			$popupLink = ($this->saveLink == "") ? MAIN_ROOT."members" : $this->saveLink;
			
			$dispDialogTitle = ($this->saveMessageTitle == "") ? "Confirmation" : $this->saveMessageTitle;
			
			if($this->saveMessage != "") {
				echo "
				
					<div style='display: none' id='successBox'>
						<p align='center'>
							".$this->saveMessage."
						</p>
					</div>
					
					<script type='text/javascript'>
						popupDialog('".$dispDialogTitle."', '".$popupLink."', 'successBox');
					</script>
				
				";
			}
			else {

				echo "
					<script type='text/javascript'>
						window.location = '".$popupLink."'
					</script>
				";
				
			}
			
		}
		
		
		/*
		 * - autocompleteJS -
		 */
		
		private function autocompleteJS($searchList, $idTextbox, $wordTextbox) {

			$returnVal = "
			
				$(document).ready(function() {
					
					var arr".$idTextbox." = ".$searchList.";
				
					$('#".$wordTextbox."').autocomplete({
						source: arr".$idTextbox.",
						minLength: 3,
						select: function(event, ui) {
						
							$('#".$idTextbox."').val(ui.item.id);
						
						}
						
					
					
					});
				
				});
			
			";
			
			return $returnVal;
		}
		
		/*
		 * - codeEditorJS-
		 */
		
		private function codeEditorJS($componentID) {
		
			$returnVal = "
			
			var ".$componentID." = ace.edit('".$componentID."');
			".$componentID.".getSession().setMode('ace/mode/php');
			".$componentID.".setTheme('ace/theme/eclipse');
			".$componentID.".setHighlightActiveLine(false);
			".$componentID.".setShowPrintMargin(false);
			
			
			";
			
			return $returnVal;
		}
		
		/*
		 * - richTextboxJS -
		 */
		
		private function richTextboxJS($componentID, $allowHTML=false) {
			global $MAIN_ROOT, $THEME, $hooksObj;
			
			$addHTML = ($allowHTML) ? ",code" : "";
			
			
			$GLOBALS['richtextEditor'] = "

			
				$(document).ready(function() {	
					$('#".$componentID."').tinymce({
					
							script_url: '".MAIN_ROOT."js/tiny_mce/tiny_mce.js',
							theme: 'advanced',
							plugins: 'autolink,emotions,advimagescale',
							cleanup_on_startup: true,
							advimagescale_max_width: 550,
							advimagescale_max_height: 150,
							advimagescale_loading_callback: function(imgNode) {
						        alert('resized to ' + imgNode.width + 'x' + imgNode.height);
						    },
							theme_advanced_buttons1: 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,link,unlink,image,emotions,|,quotebbcode,codebbcode".$addHTML.",',
							theme_advanced_buttons2: 'forecolorpicker,fontselect,fontsizeselect',
							theme_advanced_resizing: true,
							content_css: '".MAIN_ROOT."themes/".THEME."/btcs4.css',
							theme_advanced_statusbar_location: 'none',
							style_formats: [
								{title: 'Quote', inline : 'div', classes: 'forumQuote'}
							
							],
							setup: function(ed) {
								ed.addButton('quotebbcode', {
									
									title: 'Insert Quote',
									image: '".MAIN_ROOT."js/tiny_mce/quote.png',
									onclick: function() {
										ed.focus();
										innerText = ed.selection.getContent();
										
										ed.selection.setContent('[quote]'+innerText+'[/quote]');
									}
								});
								
								ed.addButton('codebbcode', {
									
									title: 'Insert Code',
									image: '".MAIN_ROOT."js/tiny_mce/code.png',
									onclick: function() {
										ed.focus();
										innerText = ed.selection.getContent();
										
										ed.selection.setContent('[code]'+innerText+'[/code]');
									}
								
								});
							}
							
							
						
						});
					});

			";
			$GLOBALS['rtCompID'] = $componentID;
			$hooksObj->run("form_richtexteditor");
			
			unset($GLOBALS['rtCompID']);
			
			return $GLOBALS['richtextEditor'];
		}
		
		private function datepickerJS($componentID, $componentOptions) {
			
			$returnVal = "	
				$('#".$componentID."').datepicker({
					changeMonth: ".$componentOptions['changeMonth'].",
					changeYear: ".$componentOptions['changeYear'].",
					dateFormat: '".$componentOptions['dateFormate']."',
					minDate: ".$componentOptions['minDate'].",
					maxDate: ".$componentOptions['maxDate'].",
					yearRange: '".$componentOptions['yearRange']."',
					defaultDate: '".$componentOptions['defaultDate']."',
					altField: '#".$componentOptions['altField']."',
					altFormat: 'm-d-yy'
				});
			";
			
			return $returnVal;
		}
		
		private function colorpickerJS($componentID) {
			$returnVal = "
				$('#".$componentID."').miniColors({
					change: function(hex, rgb) { }
				});
			";
			
			return $returnVal;
		}
		
		public function getRichtextboxJSFile() {
			return $this->richtextboxJSFile;	
		}
		
		public function getColorpickerJSFile() {
			return $this->colorpickerJSFile;			
		}
	}
	

?>
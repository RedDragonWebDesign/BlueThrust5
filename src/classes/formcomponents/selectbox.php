<?php

	class SelectBox extends FormComponent {

		protected $arrOptions;
		protected $nonSelectableItems;

		public function setOptions($options) {
			$this->arrOptions = $options;
		}

		public function setNonSelectableItems($items) {
			$this->nonSelectableItems = $items;
		}

		public function getHTML($componentName="", $componentValue="", $attributes=array()) {
			$displayForm = '';

			if($componentName != "") {
				$this->setComponentName($componentName);
			}

			if($componentValue != "") {
				$this->setComponentValue($componentValue);
			}

			if(count($attributes) > 0) {
				$this->setAttributes($attributes);
			}

			$form = new Form();
			$dispAttributes = $form->convertAttributes($this->arrAttributes);

			$displayForm .= "<select name='".$this->componentName."' ".$dispAttributes.">";
			foreach($this->arrOptions as $optionValue => $displayValue) {
				$dispSelected = "";
				if($optionValue == $this->componentValue) {
					$dispSelected = " selected";
				}

				if( is_array($this->nonSelectableItems) && in_array($optionValue, $this->nonSelectableItems)) {
					$dispSelected = " disabled class='disabledSelectItem'";
				}

				$displayForm .= "<option value='".$optionValue."'".$dispSelected.">".filterText($displayValue)."</option>";
			}
			$displayForm .= "</select>";

			return $displayForm;
		}

	}
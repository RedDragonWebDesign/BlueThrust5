<?php

	abstract class FormComponent {
		
		protected $componentName;
		protected $componentValue;
		protected $arrAttributes;
		
		
		public function setComponentName($name) {
			$this->componentName = $name;	
		}
		
		public function setComponentValue($value) {
			$this->componentValue = $value;	
		}
		
		public function setAttributes($attributes) {
			$this->arrAttributes = $attributes;	
		}
		
		public function display() {
			echo $this->getHTML();	
		}
		
		abstract function getHTML();
		
	}

?>
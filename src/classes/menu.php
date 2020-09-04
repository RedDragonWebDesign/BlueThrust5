<?php


class Menu {
	
	protected $xml;
	
	public function __construct($themeInfoXML) {
	
		$xml = new XMLReader();
		$xml->open($themeInfoXML);
		
	}
	
}


?>
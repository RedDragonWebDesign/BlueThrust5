<?php
/*
 * BBCodeParser
 * 
 * Author: Leo Rojas
 * E-mail: leorojas22@gmail.com
 * 
 * Simple BB Code Parser
 * 
 * 
 * Format BBCodes in this way:
 * 
 * 	array("bbOpenTag" => "[b]", "bbCloseTag" = > "[/b]", "htmlOpenTag" => "<b>", "htmlCloseTag" => "</b>", "type" => "simple")
 * 
 * 	- Populate $arrBBCodes with multiple arrays like the one above with different bb codes.
 * 	- Do btBBCodeParse->parse($string) to retrieve the output
 * 	- 2 different types, simple and complex.  If you set the type to complex, you should also set the arguments total
 * 
 * 
 * ****I saw there was a BBCode parser on php.net both with PECL and PEAR, but I wanted a standalone one for
 * in case users did not have the ability to use the packages.
 * 
 * 
 */

class btBBCode {
	
	
	protected $arrBBCodes;
	
	public function __construct($bbCodeArray=array()) {
		// Quick way to add bb codes
		$this->arrBBCodes = $bbCodeArray;
	}
	
	public function addBBCode($newBBCode) {
		
		$arrCheckKeys = array_keys($newBBCode);
		
		if(in_array("bbOpenTag", $arrCheckKeys) && in_array("bbCloseTag", $arrCheckKeys) && in_array("htmlOpenTag", $arrCheckKeys) && in_array("htmlCloseTag", $arrCheckKeys)) {
		
			$this->arrBBCodes[] = $newBBCode;
		
		}
		
	}
	
	public function parse($strText) {
		
		foreach($this->arrBBCodes as $bbCodes) {
			
			if($bbCodes['type'] == "simple") {
			
				$strText = str_replace($bbCodes['bbOpenTag'],$bbCodes['htmlOpenTag'],$strText);
				$strText = str_replace($bbCodes['bbCloseTag'],$bbCodes['htmlCloseTag'],$strText);
			}
			else {
				$strText = preg_replace(
			}
			
		}
		
	}
	
	
	
}



// Default BB Codes

$arrBold = array("bbOpenTag" => "[b]", "bbCloseTag" => "[/b]", "htmlOpenTag" => "<span style='font-weight: bold'>", "htmlCloseTag" => "</span>");
$arrItalic = array("bbOpenTag" => "[i]", "bbCloseTag" => "[/i]", "htmlOpenTag" => "<span style='font-style: italic'>", "htmlCloseTag" => "</span>");
$arrUnderline = array("bbOpenTag" => "[u]", "bbCloseTag" => "[/u]", "htmlOpenTag" => "<span style='text-decoration: underline'>", "htmlCloseTag" => "</span>");


$arrLink = array("bbOpenTag" => "[url]", "bbCloseTag" => "[/url]", "htmlOpenTag" => "<a href='", "htmlCloseTag" => "</span>");




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


class BTUpload {
	
		
	protected $arrFile; // Should be a $_FILE array
	protected $arrFileExtensions;
	protected $intExtLength;
	protected $strNewFileLoc;
	protected $strFileExt;
	protected $strUploadedFileName;
	protected $strFilePrefix;
	protected $intUploadSizeLimit;
	protected $strOutsideFileURL;
	protected $blnOutsideLink;
	public $arrErrors = array();
	
	const ONE_MEGABYTE = 1048576;
	
	function __construct($uploadfile, $prefix, $fileloc = "", $allowableExt=array(), $extlength = 4, $outsideLink = false) {
		
		if(!$outsideLink) {
			$this->arrFile = $uploadfile;
		}
		else {
			$this->strOutsideFileURL = $uploadfile;
		}
		
		
		$this->blnOutsideLink = $outsideLink;
		$this->arrFileExtensions = $allowableExt;
		$this->intExtLength = $extlength;
		$this->strNewFileLoc = $fileloc;
		$this->strFilePrefix = $prefix;
		
		$defaultUploadSize = ini_get("upload_max_filesize");
		
		$defaultUploadSize = str_replace("G", "", $defaultUploadSize);
		$defaultUploadSize = str_replace("M", "", $defaultUploadSize);
		$defaultUploadSize = str_replace("K", "", $defaultUploadSize);
		
		$this->intUploadSizeLimit = $defaultUploadSize;
		
		
		
	}
		
	
	/*
	 * - Extension Check Function - 
	 * 
	 * Returns a boolean value of true or false on whether the chosen file, $arrFile, has the correct extension.
	 * Will always return true if there are no extensions set.
	 * 
	 */
	
	
	function checkExtensions() {
		
		$returnVal = false;
		
		if($this->blnOutsideLink) {
			$strFileName = $this->strOutsideFileURL;
		}
		else {
			$strFileName = $this->arrFile['name'];
		}
		
		$checkExt = 0;

		foreach($this->arrFileExtensions as $fileExt) {
			if(strtolower(substr($strFileName,(strlen($fileExt))*-1)) == $fileExt) {
				$checkExt++;
				$this->strFileExt = $fileExt;
			}
		}
		
		if($checkExt > 0 || count($his->arrFileExtensions) == 0) {
			$returnVal = true;
		}
		
		return $returnVal;
		
	}
	
	
	
	
	/*
	 * - File Size Check Function -
	 * 
	 * Returns true if the uploaded file is equal to or under the file size limit
	 * Returns false if the uploaded file is over the file size limit
	 * 
	 */
	
	function checkFileSize() {
		$returnVal = false;
		
		
		if($this->blnOutsideLink) {
			
			$arrHeaders = get_headers($this->strOutsideFileURL, 1);
			if(isset($arrHeaders['Content-Length']) && $arrHeaders['Content-Length'] <= (self::ONE_MEGABYTE*$this->intUploadSizeLimit)) {
				$returnVal = true;
			}
			
		}
		else {
		
			if($this->arrFile['size'] <= (self::ONE_MEGABYTE*$this->intUploadSizeLimit)) {
				$returnVal = true;
			}
			
		}
		
		return $returnVal;
	}
	
	function uploadFile() {
		
		$blnUploadFile = false;

		if(!is_dir($this->strNewFileLoc)) {
			mkdir($this->strNewFileLoc);	
		}
		
		if($this->checkExtensions() && $this->checkFileSize()) {
			$this->strUploadedFileName = uniqid($this->strFilePrefix).$this->strFileExt;
			

			//print_r($this->arrFile);
			
			if(!$this->blnOutsideLink) {
				$blnUploadFile = move_uploaded_file($this->arrFile['tmp_name'], $this->strNewFileLoc.$this->strUploadedFileName);
			}
			else {

				$uploadContents = file_get_contents($this->strOutsideFileURL);
				
				$createFile = file_put_contents($this->strNewFileLoc.$this->strUploadedFileName, $uploadContents);
				
				if($createFile !== false) {
					$blnUploadFile = true;
					
				}
				
			}
			
			if(!$blnUploadFile) {
				$this->arrErrors[] = "Can't Upload";
			}
			
			
		}
		else {
			$this->arrErrors[] = "File Size and Extension";	
		}

		
		return $blnUploadFile;
	}
	
	
	
	
	// Getter and Setter Methods
	
	function setExtensions($allowableExt) {
		$this->arrFileExtensions = $allowableExt;
	}
	
	
	function getExtensions() {
		return $this->arrFileExtensions;
	}
	
	function setFilePrefix($fileprefix) {
		$this->strFilePrefix = $fileprefix;
	}
	
	function getFilePrefix() {
		return $this->strFilePrefix;
	}
	
	function setFile($uploadfile) {
		$this->arrFile = $uploadfile;
	}
	
	function getFile() {
		return $this->arrFile;
	}
	
	function setExtLength($extlength) {
		$this->intExtLength = $extlength;
	}
	
	function getExtLength() {
		return $this->intExtLengh;
	}
	
	function setFileLoc($fileloc) {
		$this->strNewFileLoc = $fileloc;
	}
	
	function getFileLoc() {
		return $this->strNewFileLoc;
	}
	
	function setUploadSizeLimit($uploadSize) {
		$this->intUploadSizeLimit = $uploadSize;
	}
	
	function getUploadSizeLimit() {
		return $this->intUploadSizeLimit;	
	}
	
	function getUploadedFileName() {
		return $this->strUploadedFileName;
	}
}

?>
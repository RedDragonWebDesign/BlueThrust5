<?php

/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */


class Download extends Basic {

	protected $objUpload;
	protected $objDownloadCategory;
	protected $strMIMEType;
	protected $arrSplitFileNames;
	protected $intFileSize;
	protected $filterExtensions = ["php", "js"];


	function __construct($sqlConnection) {

		$this->MySQL = $sqlConnection;
		$this->strTableKey = "download_id";
		$this->strTableName = $this->MySQL->get_tablePrefix()."downloads";

		$this->objDownloadCategory = new DownloadCategory($sqlConnection);

		$this->arrSplitFileNames = [];
	}

	function setCategory($intCatID) {

		return $this->objDownloadCategory->select($intCatID);
	}

	function uploadFile($uploadfile = "", $fileloc = "", $downloadCatID = "", $outsidelink = false) {

		$returnVal = false;
		if ($this->setCategory($downloadCatID)) {
			$this->intFileSize = 0;

			$allowableExt = $this->objDownloadCategory->getExtensions(false);

			if ($uploadfile != "") {
				$this->objUpload = new BTUpload($uploadfile, "", $fileloc, $allowableExt, 4, $outsidelink);
			}

			if ($this->objUpload->uploadFile() && $this->splitFile()) {
				$returnVal = true;
			}
		}

		return $returnVal;
	}

	/** Split File for Downloads */
	public function splitFile() {
		global $websiteInfo;

		$returnVal = false;
		$countErrors = 0;
		$fullFileName = $this->objUpload->getFileLoc().$this->objUpload->getUploadedFileName();

		if ($websiteInfo['split_downloads']) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$this->strMIMEType = finfo_file($finfo, $fullFileName);

			$this->arrSplitFileNames = [];
			$handle = fopen($fullFileName, 'rb');
			if ($handle) {
				$file_size = filesize($fullFileName);
				$this->intFileSize = $file_size;
				$parts_size = floor($file_size/2);
				$modulus=$file_size % 2;
				for ($i=0; $i<2; $i++) {
					if ($modulus!=0 && $i==1) {
						$parts[$i] = fread($handle, $parts_size+$modulus);
					} else {
						$parts[$i] = fread($handle, $parts_size);
					}

					if ($parts[$i] === false) {
						$countErrors++;
					}
				}

				if (fclose($handle) && $countErrors == 0) {
					for ($i=0; $i<2; $i++) {
						$filePrefix[$i] = uniqid(time());
						$this->arrSplitFileNames[] = "split_".$filePrefix[$i];
						$tempFileName = $this->objUpload->getFileLoc()."split_".$filePrefix[$i];
						$handle = fopen($tempFileName, 'wb');

						if (!$handle || fwrite($handle, $parts[$i]) === false) {
							$countErrors++;
						}
					}

					if (fclose($handle) && $countErrors == 0 && unlink($fullFileName)) {
						$returnVal = true;
					}
				}
			}
		} else {
			// Do not split downloads
			$newName = $this->objUpload->getUploadedFileName().".download";
			if ($this->renameFile($newName)) {
				$this->intFileSize = filesize($this->objUpload->getFileLoc().$newName);
				$this->arrSplitFileNames[0] = $newName;
				$this->arrSplitFileNames[1] = "";
				$returnVal = true;
			}
		}

		return $returnVal;
	}


	public function getSplitNames() {

		return $this->arrSplitFileNames;
	}


	public function getMIMEType() {
		return $this->strMIMEType;
	}

	public function getFileSize() {
		return $this->intFileSize;
	}

	public function setUploadObj($uploadObj) {
		$this->objUpload = $uploadObj;
	}


	public function delete() {

		$returnVal = false;
		if ($this->intTableKeyValue != "") {
			$info = $this->arrObjInfo;
			$returnVal = parent::delete();

			deleteFile(BASE_DIRECTORY.$info['splitfile1']);
			deleteFile(BASE_DIRECTORY.$info['splitfile2']);
		}

		return $returnVal;
	}

	private function renameFile($newName) {

		$fullFileName = $this->objUpload->getFileLoc().$this->objUpload->getUploadedFileName();
		$newFileName = $this->objUpload->getFileLoc().$newName;

		return rename($fullFileName, $newFileName);
	}

}

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
class btHooks {

	protected $data = [];


	function addHook($hookName, $function, $args = "") {

		$this->data[$hookName][] = ["function" => $function, "args" => $args];
	}

	function removeHook($hookName, $function) {
		foreach ($this->data[$hookName] as $key => $hookInfo) {
			if ($hookInfo['function'] == $function) {
				unset($this->data[$hookName][$key]);
			}
		}
	}

	function run($hookName) {
		if ( ! isset($this->data[$hookName]) ) {
			return;
		}

		foreach ($this->data[$hookName] as $hookInfo) {
			if (function_exists($hookInfo['function'])) {
				if ($hookInfo['args'] == "") {
					call_user_func($hookInfo['function']);
				} else {
					call_user_func_array($hookInfo['function'], $hookInfo['args']);
				}
			}
		}
	}


}

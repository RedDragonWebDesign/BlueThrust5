import { browser } from '@wdio/globals'

/**
* main page object containing all methods, selectors and functionality
* that is shared across all page objects
*/
export default class Page {
	/**
	* Opens a sub page of the page
	* @param path path of the sub page (e.g. /path/to/page.html)
	*/
	open (path = '') {
		return browser.url(`http://localhost/Code/BlueThrust5%20R17/BlueThrust5/src/${path}`)
	}
}

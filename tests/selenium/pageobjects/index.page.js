import { $ } from '@wdio/globals'
import Page from './page.js';

/**
 * sub page containing specific selectors and methods for a specific page
 */
class IndexPage extends Page {
	/**
	 * define selectors using getter methods
	 */
	get clocks () {
		return $('.clocksDiv');
	}

	get newsTicker () {
		return $('#hpNewsTicker');
	}

	get membersOnline () {
		return $('b=Members Online:');
	}

	get websiteStatistics () {
		return $('td=Website Statistics');
	}

	/**
	 * a method to encapsule automation code to interact with the page
	 * e.g. to login using username and password
	 */

	/**
	 * overwrite specific options to adapt it to page object
	 */
	open () {
		return super.open();
	}
}

export default new IndexPage();

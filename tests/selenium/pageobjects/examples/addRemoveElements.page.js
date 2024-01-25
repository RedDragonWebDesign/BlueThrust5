import { $ } from '@wdio/globals'
import Page from './page.js';

/**
 * sub page containing specific selectors and methods for a specific page
 */
class AddRemoveElementsPage extends Page {
    /**
     * define selectors using getter methods
     */
    get addElementButton () {
        return $('[onclick="addElement()"]');
    }

    get deleteButton () {
        return $('[onclick="deleteElement()"]');
    }

    /**
     * a method to encapsule automation code to interact with the page
     * e.g. to login using username and password
     */
    async addElement () {
        await this.addElementButton.click();
    }

    async delete () {
        await this.deleteButton.click();
    }

    /**
     * overwrite specific options to adapt it to page object
     */
    open () {
        return super.open('add_remove_elements/');
    }
}

export default new AddRemoveElementsPage();

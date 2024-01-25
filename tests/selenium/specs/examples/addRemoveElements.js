import { expect } from '@wdio/globals'
import AddRemoveElementsPage from '../../pageobjects/examples/AddRemoveElements.page.js'
import SecurePage from '../../pageobjects/examples/secure.page.js'

describe('My AddRemoveElements application', () => {
    it('should add an element', async () => {
        await AddRemoveElementsPage.open();

        await expect(AddRemoveElementsPage.deleteButton).not.toBeExisting();
        await AddRemoveElementsPage.addElement();
        await expect(AddRemoveElementsPage.deleteButton).toBeExisting();
        await AddRemoveElementsPage.delete();
        await expect(AddRemoveElementsPage.deleteButton).not.toBeExisting();
    })
})


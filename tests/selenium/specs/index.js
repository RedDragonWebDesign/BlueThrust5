import { expect } from '@wdio/globals'
import IndexPage from '../pageobjects/index.page.js'

describe('index.php', () => {
    it('should have all its elements', async () => {
        await IndexPage.open();

        await expect(IndexPage.clocks).toBeExisting();
        await expect(IndexPage.newsTicker).toBeExisting();
        await expect(IndexPage.membersOnline).toBeExisting();
        await expect(IndexPage.websiteStatistics).toBeExisting();
    })
})


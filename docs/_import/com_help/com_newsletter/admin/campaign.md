<!--
status: imported
source: core/components/com_newsletter/admin/help/en-GB/campaign.phtml
imported: 2026-09-09
-->
# Campaigns

Campaigns enable the admin user to create and maintain a unique 32-character random secret associated with a specific email or newsletter campaign.

**Though the administrative interface for campaigns is located with Hubzero Newsletters, the two features, Campaigns and Newsletters, are not presently related.**

## Purpose

Campaigns were developed to provide a random secret that could be hashed together with unique per-user secrets and a unique Hub secret to provide a unique code. This code can be used to form a URL that will be emailed to the user to provide them with secure access to Hub features.

Campaigns are designed to expire after a period of time that defaults to 90 days. A campaign can be edited to change the expiration date. After a campaign's expiration date has passed, it can no longer be used.

## Viewing Campaigns

To view existing Campaigns, navigate to Components -> Newsletter, then select the Campaigns tab.

In the Campaigns display, you may search for a specific Campaign name, or sort Campaigns by name or date.

## Creating Campaigns

To create a Campaign, click the "+" sign in the Campaigns display. The Campaign Editor will be shown. Type the name and description of the campaign and select an expiration date. The default expiration date is 90 days from the creation date.

Then, click the check mark (Save) or star (Save and Exit). Or, click the X to cancel.

The Campaign will be saved, along with a unique secret, your user information, and the creation date. Note that the secret is not shown.

## Editing Campaigns

To edit a Campaign, click an existing Campaign's name in the Campaigns display. Alternately, select an existing Campaign's checkbox and click the "Edit" icon. The Campaign Editor will be shown.

You may edit the name, description, and expiration date of the campaign. You may also reset the Campaign's secret, by clicking the checkbox labeled "Reset Campaign Secret". Use caution when resetting the secret. This action will invalidate any URLs prepared with the Campaign! To save your edits, click the check mark (Save) or star (Save and Exit). To cancel, click the X. The Campaign will be saved. Regardless of whether it has been changed, the secret is not shown.

## Deleting Campaigns

To delete a Campaign, click the checkbox next to a Campaign's name in the Campaigns display. You may select multiple campaigns to delete.

Next, click the garbage can icon in the Campaigns display. You will be prompted to proceed with, or cancel, deletion.

If you proceed with deletion, the Campaign will be permanently deleted. This will invalidate the existing campaign, so delete only with caution!

<!--
status: imported
source: core/components/com_redirect/admin/help/en-GB/link.phtml
imported: 2026-09-09
-->
# Redirect Manager: Edit Link

This screen is accessed from the back-end administrator panel. It is used to add or edit redirects.

## How to access

Navigate to the the Redirect Manager. To add a new Redirect, click on the *New* icon in the toolbar. To edit an existing Redirect, click on the Redirect's *Expired URL* field or check the Redirect's checkbox and press the *Edit* icon in the toolbar.

## Description

This is where you can add a new redirect or edit an existing one. The URL you want to redirect from must not be a working one on your website which actually loads a web page. It can be the URL to a web page which you have disabled in the back-end administrator interface. The Source URL you specify when you create the redirect should be the full URL as you would type it in your web browser. The Destination URL you specify when you create a redirect must be the full URL as well.

<a id="Details_and_Options"></a>

## Details and Options

## Edit/New Link

- **Source URL.** The URL to be redirected.
- **Destination URL.** The URL to redirect to.
- **Comment.** A comment that is only viewable in the administrator back-end. It is primarily intended for administrator reference only.
- **ID**. This is a unique identification number for this item assigned automatically by the CMS. It is used to identify the item internally, and you cannot change this number. When creating a new item, this field displays 0 until you save the new entry, at which point a new ID is assigned to it.

## Options

- **State**: State of the item. Possible values are:
  - *Published*: The item is published. This is the only state that will allow regular website users to view this item.
  - *Unpublished*: The item is unpublished.
  - *Archived*: The item has been archived.
  - *Trashed*: The item has been sent to the Trash.

## Details

- **Created Date.** The date the Redirect was created on.
- **Last Updated Date.** Shows the last date the item was modified.
- **Hits.** The number of times an item has been viewed.

<a id="Toolbar"></a>

## Toolbar

At the top right you will see the toolbar:

The functions are:

- **Save**. Saves the redirect and stays in the current screen.
- **Save & Close**. Saves the redirect and closes the current screen.
- **Save & New**. Saves the redirect and keeps the editing screen open and ready to create another redirect.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.
- **Help**. Opens this help screen.

<a id="Quick_Tips"></a>

## Quick Tips

- When entering URLs into the *Expired/Source URL* and *New/Destination URL* fields, enter the complete URL as you would type it into your web browser to view the web page.
- When entering URLs into the *Source URL* and *Destination URL* fields, enter the complete URL as you would type it into your web browser to view it. The *Source URL* should be a URL that does not resolve to any valid web page on your website. You can specify a source URL for a web page that you have put into a disabled state in the administrator back-end. Ensure the *State* option is set to **Enabled**.

<a id="Related_information"></a>

## Related information

- To manage redirects: Redirect Manager

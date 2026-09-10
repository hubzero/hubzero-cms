<!--
status: imported
source: core/components/com_installer/admin/help/en-GB/customexts.phtml
imported: 2026-09-09
-->
# Extension Manager: Manage

## Overview

This screen is accessed from the back-end administrator panel. It is used to enable/disable/uninstall extensions that are installed in your installation. Some examples of extensions are plug-ins, components, and site templates.

## How to Access

Select **Extensions → Extension Manager** from the drop-down menu of the ***Administrator Panel***. Then select the **Manage** menu item in the ***Extension manager*** screen that appears.

## Description

This screen allows you to Enable/Disable extensions. To Enable/Disable an extension is the same as to Publish/Unpublish it. In addition you can uninstall extensions from this screen as well.

## Column Headers

Click on the column heading to sort the list by that column's value. The list will be sorted in order by that column and a sort icon will show next to the column name, as shown below. Click a second time to reverse the sort to high-to-low. The sort icon will change to high-to-low, as shown below.

- **Checkbox**. Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **Name.** The name of the extension.
- **Location.** Whether the extension is a public front-end or an administrator back-end extension. 'Site' means it is a public front-end extension. 'Administrator' means it is an administrator back-end extension.
- **Status.** (Enable/Disable) The published status of the extension.
- **Extension Type.** The extension type. Some examples of extension types are module, plug-in, template, or language.
- **Version.** The version number of the Extension.
- **Date.** The date this extension was released.
- **Author.** The author of this extension.
- **Folder.** If the extension is a plug-in, the subdirectory of your installation's /plugins directory where the extension is located.
- **ID**. This is a unique identification number for this item assigned automatically by the CMS. It is used to identify the item internally, and you cannot change this number. When creating a new item, this field displays 0 until you save the new entry, at which point a new ID is assigned to it.
- **Display #.** (Use Global/Hide/Show) Whether to hide or show the Display # control that allows the user to select the number of items to show in the list. An example is shown below. If there are more items than this number, you can use the page navigation buttons (Start, Prev, Next, End, and page numbers) to navigate between pages. Note that if you have a large number of items, it may be helpful to use the Filter options, located above the column headings, to limit which items display.

## List Filters

The list of extensions which appears on this screen may be very large. You can use one of the available filters or any combination of them to limit the number of extensions displayed to just the extensions which match your filter parameters.

- **Filter by Partial Title or ID.** In the upper left is a filter field and two buttons, as shown below.
  - To filter by partial title, enter part of the title and click Search.
  - To filter by ID number, enter "id:xx", where "xx" is the ID number (for example, "id:9").

- Click Clear to clear the Filter field and restore the list to its unfiltered state.

**Filter by Extension Location, Status, Type and Folder**

- **Select Location.** Select an extension location from the drop-down list box of available clients. There are two locations - Site and Administrator. Site locations are extensions which are designed for use in areas outside of the administrator interface. Administrator extensions are for the back-end administrative functions of your installation. Usually you would only directly interact with these extensions through the administrator web interface.
- **Select status.** Select the extension's status. This is published, unpublished or protected (a default extension that may not be uninstalled or unpublished)
- **Select type.** Select the extension type from the drop-down list box of available types.
- **Select folder.** Select a plug-in folder from the drop-down list box of available folders. There is a separate folder for each type of plug-in defined in your installation.

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Enable**. Makes the selected extension(s) available for use on your website.
- **Disable**. Makes the selected extension(s) unavailable for use on your website.
- **Refresh cache**. Refresh the information displayed for the selected extension(s).
- **Uninstall**. Uninstall the selected extension(s).
- **Options**. Opens the Options window where settings such as default parameters or permissions can be edited.
- **Help**. Opens this help screen.

## Links to Other Screens

At the top left, you will see the following five links:

- **Install.** Links to the Install Screen.
- **Update.** Links to the Update Screen.
- **Manage.** Links to the **Manage Screen**.
- **Discover.** Links to the Discover Screen.
- **Database.** Links to the Database Screen.
- **Warnings.** Links to the Warnings Screen.
- **Install Languages.** Links to the Languages Screen.

## Quick Tips

- Throughout the administrator interface you will see the terms "Publish" and "Unpublish". *Publish* means the same thing as *Enable* or *Activate*. *Unpublish* means the same thing as *Disable* or *Deactivate*. You may see the terms used interchangeably in extension screens and help documentation you find online.
- Some information such as *Date* or *Author* may not be displayed for an extension. This is not a problem. It merely means the information was not specified by the extension developer in the extension's installation package.

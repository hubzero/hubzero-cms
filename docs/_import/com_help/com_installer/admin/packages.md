<!--
status: imported
source: core/components/com_installer/admin/help/en-GB/packages.phtml
imported: 2026-09-09
-->
# Extension Manager: Update

This screen is accessed from the back-end administrator panel. It is used to update extensions that are installed in your installation.

## How to Access

- Select **Extensions → Extension Manager** from the drop-down menu of the ***Administrator Panel***. Then select the **Update** menu item in the ***Extension manager*** screen that appears, or
- Press the extension Update button in the Control Panel

## Description

This screen allows you to update installed extensions. You will only be able to update extensions which support this feature. For extensions which support this feature, you will be able to perform an in-place update of the extension without having to upload and install the updated extension files like you did with previous versions of the CMS. The update feature utilizes standard HTTP connection mechanisms to download the extension update files from a remote update server.

## Column Headers

- **Checkbox**. Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **Name.** The name of the extension.
- **Install Type.** The type of installation that will be performed by the update. Usually this will be type ***Update*** which will perform an in-place update of the extension
- **Type.** The extension type. Examples of extension types are module, plug-in, template, component, or language.
- **Version.** The version number of the available update.
- **Folder.** If the extension is a plug-in, the subdirectory of your installation's /plugins directory where the extension is located. By default has the following subdirectories in the plugins directory which each represent the different types of plug-ins that are defined: authentication, content, editors, editors-xtd, extension, search, system, user.
- **Client.** Specifies if this is a site or administrator extension.
- **URL Details.** The URL for the extension update XML file which contains the information needed by your installation to perform the update.

## List Filters

- **Page Controls.** When the number of items is more than one page, you will see a page control bar as shown below.
  - **Display #:** Select the number of items to show on one page.
  - **Start:** Click to go to the first page.
  - **Prev:** Click to go to the previous page.
  - **Page numbers:** Click to go to the desired page.
  - **Next:** Click to go to the next page.
  - **End:** Click to go to the last page.

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Update**. Updates the selected extension(s).
- **Find Updates**. Scans your installation and locates extensions with an available update.
- **Purge Cache**. Clears cached information about available extension updates. This will update the list of available extension updates shown on this screen.
- **Options**. Opens the Options window where settings such as default parameters or permissions can be edited.
- **Help**. Opens this help screen.

## Links to Other Screens

At the top left, you will see the following five links:

- **Install.** Links to the Install Screen.
- **Update.** Links to the **Update Screen**.
- **Manage.** Links to the Manage Screen.
- **Discover.** Links to the Discover Screen.
- **Database.** Links to the Database Screen.
- **Warnings.** Links to the Warnings Screen.
- **Install Languages.** Links to the Languages Screen.

## Quick Tips

- Only extensions which support the update system will be listed in this screen. If you use extensions which do not support the new update system or you are not sure, consult the extension developer's website.
- It is critical to keep your extensions up-to-date. Failure to do so may expose a vulnerability in your installation which can be exploited by hackers.
- It is recommended to backup your installation files and database before attempting to update extensions or the installation itself. This will ensure that you can restore your installation to its previous state if the update fails or causes unexpected results.

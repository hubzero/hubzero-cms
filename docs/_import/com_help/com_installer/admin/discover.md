<!--
status: imported
source: core/components/com_installer/admin/help/en-GB/discover.phtml
imported: 2026-09-09
-->
# Extension Manager: Discover

This screen is accessed from the back-end administrator panel. It is used to discover and install extensions into your installation. Some examples of extensions are plug-ins, components, and site templates.

## How to Access

- Select **Extensions → Extension Manager** from the drop-down menu of the ***Administrator Panel***. Then select the **Discover** menu item in the ***Extension manager*** screen that appears.

## Description

This screen allows you to discover extensions that have not gone through the normal installation process. This provides a method to install large extensions which are not possible to install using the normal process. For example, some extensions are too large in file size to upload using the web interface due to limitations of the web hosting environment. Using this feature you can upload extension files directly to your web server using some other means such as FTP or SFTP and place those extension files into the appropriate directory. You can then use the discover feature to find the newly uploaded extension and activate it in your installation. Using the discover operation you can also discover and install multiple extensions at the same time.

## Column Headers

- **Checkbox.** Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items.
- **Name.** The name of the extension.
- **Extension Type.** The extension type - module, plug-in, template, language.
- **Version.** The version number of the Extension.
- **Date.** The date this extension was released.
- **Folder.** If the extension is a plug-in, the subdirectory of your installation's /plugins directory where the extension is located.
- **Client.** Specifies if this is a site or administrator extension.
- **Author.** The author of this extension.
- **ID.** The ID number. This is a unique identification number for this item assigned automatically by the CMS. It is used to identify the item internally, for example in internal links. You cannot change this number.

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Install**. Installs the selected extension.
- **Discover**. Searches the installation directories for uninstalled extensions. Any uninstalled extensions found will be displayed in the extension listing. Caches the search results so they continue to be displayed on subsequent visits to this screen.
- **Purge Cache**. Clears cached information about uninstalled extensions. This will update the list of uninstalled extensions shown on this screen.
- **Options**. Opens the Options window where settings such as default parameters or permissions can be edited.
- **Help**. Opens this help screen.

## Links to Other Screens

At the top left, you will see the following five links:

- **Install.** Links to the Install Screen.
- **Update.** Links to the Update Screen.
- **Manage.** Links to the Manage Screen.
- **Discover.** Links to the **Discover Screen**.
- **Database.** Links to the Database Screen.
- **Warnings.** Links to the Warnings Screen.
- **Install Languages.** Links to the Languages Screen.

## Outline

An extension can be installed with the discover function by doing the following steps:

1. Upload the uncompressed extension files to the appropriate directory in your installation. For example, if you wanted to upload a site template extension to your installation you would create a directory for the template extension in the /templates sub-directory of your installation. Then you would upload the template extension files into that directory you created.
2. After you have uploaded the extension files, go back to the Extension Manager Discover screen and click the **Discover** item in the toolbar. This will initiate the search function which will search your extension directories looking for uninstalled extensions.
3. If the extension you uploaded is compatible with Hubzero 2.0 and is not already installed, it will appear in the listing of discovered extensions in this screen.
4. Click the check-box to the left of the discovered extension and then click the **Install** item in the toolbar. This will install the extension into your installation.

## Quick Tips

- If you have many extensions you want to install you can upload them all into the appropriate directories of your installation and then use this screen to discover all of them in one operation. You can then install all of the extensions in one step by selecting all of them and pressing the **Install** item in the toolbar.

## Related Information

- If you install a Component Extension, it will be listed as a new Menu Item in the *Components* menu.
- You can assign a Menu Item to an installed Component Extension in the Menu Item Manager by clicking the *New* toolbar button. The new Component will show in the Internal Link list of Menu Item Types.
- If you install a Module Extension, it will be added to the list of the Modules in the Module Manager, where you can enable/disable it. You can also customize it's parameters in the Module Edit screen.
- An installed Plugin Extension will be added to the list of the Plugin Manager, where you can enable/disable it. You can also customize its parameters in the Plugin Edit screen.
- An installed Template Extension will be added to the Site or Administrator list of the Template Manager where you can assign it to all of the pages or to the selected ones. You can also customize its parameters, edit the HTML or CSS source, or preview the available Module positions.
- An installed Language Extension will be added to the Site or Administrator list of the Language Manager, depending on the client attribute of the Extension. This screen let's you assign it as the default language, if desired.

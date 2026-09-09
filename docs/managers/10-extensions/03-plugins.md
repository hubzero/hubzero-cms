<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/extensions/plugins
source-id: 3408
modified: 2009-10-01
imported: 2026-09-09
-->
# Plugins Manager

## Overview

The Plugin Manager allows you to enable and disablePlugins and to edit a Plugin's Details and Parameters.

The Plugin Manager can be accessed by selecting **Extensions** > **Plugin Manager** from the drop-down menu on the Back-end of your HUBzero installation.

- **Plug-in Name:** The name of the plug-in. The name explains where the plug-in is located and what the plug-in is for.
  - For example: **Dashboard-Collections** means that the plug-in is on the user’s Dashboard with a module for Collections.

- **Status:** If the plug-in is viewable on the Hub or not.
  - Enabled: The plug-in is available on the frontend and on the backend of the Hub.
  - Disabled: The plug-in has been removed from the frontend of the Hub but is still accessible on the backend.
- **Ordering:** Where the plug-in is on the master list of plug-ins. This list can be seen when plug-ins are being enabled the ordering determines how the plug-in is displayed in the drop-down.
- **Type:** The location of the plug-in.
  - This is the first part of the plug-in’s title.
- **Element:** What the plug-in is implementing.
  - This is the second part of the plug-in’s title.
- **Access:** Who is able to view or use the plug-in.
  - Public: All visitors or users on the Hub are able to view and use the plug-in.
  - Registered: Only registered users are able to view and use the plug-in.
  - Special: Only users given special permissions can access and use the plug-in.
- **ID:** The identification number given to the plugin after it was created on the Hub. This allows for easier search results.

## Column Headers

- **#:** An indexing number automatically assigned by Joomla! for ease of reference.
- **Checkbox:** Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **Plugin Name:** The Name of the Plugin. Click on the Name to open the Plugin for editing.
- **Enabled:** A green tick or a red X showing whether the use of the component is enabled/disabled. Click the icon to toggle the item between enabled and disabled.
- **Order:** The order to display items. If the list is sorted by this column, you can change the order by clicking the arrows or by entering the sequential order and clicking **Save Order**. Note that the display order on a page is set in the Parameters - Advanced section for each Menu Item. If that order is set to use something other than **Order**(for example, **Title - Alphabetical**), then the order value in this screen will be ignored. If the Menu Item Order parameter is set to use **Order**, then the items will display on the page based on the order in this screen.
- **Access Level:** Who has access to this item. You can change an item's Access Level by clicking on the icon in the column. Current options are:
  - Public: Everyone has access
  - Registered: Only registered users have access
  - Special: Only users with author status or higher have access
- **Type:** The Type of the Plugin. Possible types are: authentication, content, editors, editors-xtd, search, system, user, and xmlrpc. These are also the names of the subfolders where the Plugin files are located. For example, Plugins with a Type of **authentication** are located in the folder **plugins/authentication**.
- **File:** The name of the Plugin files. Each Plugin has two files, a **.php** file and a **.xml** file. So, for example, the Authentication - Facebook plugin has two files: **facebook.php** and **facebook.xml**.
- **ID:** The ID number. This is a unique identification number for this item assigned automatically by Joomla!. It is used to identify the item internally, for example in internal links. You can not change this number.
- **Display #:** The number of items to display on one page. If there are more items than this number, you can use the page navigation buttons (Start, Prev, Next, End, and page numbers) to navigate between pages. Note that if you have a large number of items, it may be helpful to use the Filter options, located above the column headings, to limit which items display (*where applicable*).

## Toolbars

- **Enable:** To enable one or more items, select them using the Checkbox and press this button. You may also toggle between Enabled and Disabled by clicking on the icon in the **Enabled** column.
- **Disable:** To disable one or more items, select them using the Checkbox and press this button. You may also toggle between Enabled and Disabled by clicking on the icon in the **Enabled** column.
- **Edit:** Select one item and click on this button to open it in edit mode. If you have more than one item selected (where applicable), the first item will be opened. You can also open an item for editing by clicking on its Title or Name.
- **Help:** Opens this Help Screen.
- **Check-in**
- **Options**

## List Filters

You can filter the list of items by typing in part of the Title or the ID number. Or you can select a combination of Category and Published State.

- **Filter:** If you have a large number of items on the list, you can use this filter to find the desired item(s) quickly. Enter either part of the title or an ID number and press **Go** to display the matching items. You can enter in whole words or part of a word. For example, **ooml** will match all titles with the word **Joomla!** in them.
- **Filter by Type and State:** The selections may be combined. Only items matching both selections will display in the list.
- **Select Type:** Select a Type from the drop-down list box to select only Plugins of this Type.
- **Select State:** Select a state (Enabled or Disabled) from the drop-down list box to select only Plugins with this state.
- **Select Access**

## Edit Plugins

You can edit details and parameters for Plugins. Some Plugins have several parameters, while others don't have any.

- **Details:** The Details section is the same for all Plugins, as follows:
- **Name:** The Name of the Plugin.
- **Enabled:** Whether or not this Plugin is enabled.
- **Type:** The Type of the Plugin. This value cannot be changed.
- **Plugin File:** The name of the Plugin file. Each Plugin has two files with this name. One has the file extension **.php** and the other has the file extension **.xml**.
- **Access Level:** Who has access to this item. Enter the desired level using the drop-down list box. Current options are:
  - Public: Everyone has access
  - Registered: Only registered users have access
  - Special: Only users with author status or higher have access
- **Order:** The order this item will display in the Manager screen. Use the drop-down list box to change the Order. You can select **First** or **Last** to make this the first or last item. Or you can select an item from the list. In this case, the current item will be listed just *after* the item you select. Note that the Order can also be changed in the Manager screen.
- **Description:** The description of what this Plugin does. This cannot be changed.
- **Parameters:** This will vary between plugins.

## Reordering

1. Log in to the backend of the Hub and access the **Extensions** tab
2. Click on the **Plugin-in Manager** navigate to the **Plug-in Manager: Plug-ins** page
3. Locate the column labeled **Ordering** and then click on the title of the column
4. Arrows will appear on the left side of the numbering
5. To begin reordering click the arrows up or down to reorder the plug-ins
6. Save the changes by clicking the **Save** icon

## Enabling a Plug-in

1. Log in to the backend of the Hub and access the **Extensions** tab and then click on **Plug-in Manager**
2. Locate the plug-in that needs enabling and select the box beside the plug-in’s name
3. Click **Enable** to enable the plug-in
4. The plug-in will now be useable on the frontend of the Hub

## Editing a Plug-in

1. Log in to the backend of the Hub and access the **Extensions** tab and then click on **Plug-in Manager**
2. Locate the plug-in that needs editing and select the box beside the plug-in’s name
3. Click **Edit** to begin editing the plug-in
4. Click **Save & Close** to save the newly edited content

## Disabling a Plug-in

1. Log in to the backend of the Hub and access the **Extensions** tab and then click on **Plug-in Manager**
2. Locate the plug-in that needs disabling and select the box beside the plug-in’s name
3. Click **Disable** to disable the plug-in
4. The plug-in will now be removed from the frontend of the Hub, but still accessible from the backend

<!--
status: imported
source: core/components/com_menus/admin/help/en-GB/menus.phtml
imported: 2026-09-09
-->
# Menu Manager

Provides an overview of the menus available on a site. This includes the details of each individual menu's number of items published, unpublished and names of linked modules.

## How to Access

To access this screen you can either:

- Click the **Menu Manager** button in the Control Panel, or
- Select **Menus → Menu Manager** from the drop-down menus.

## Description

Menus allow a user to navigate through the site. A menu is an object which contains one or more menu items. Each menu item points to a logical page on the site. A menu module is required to place the menu on the page. One menu can have more than one module. For example, one module might show only the first level menu items and a second module might show the level 2 menu items.

The process for adding a menu to the site is normally as follows:

1. Create a new menu (using this screen).
2. Create one or more new menu items on the menu. Each menu item will have a specific menu item type.
3. Create one or more menu modules to display the menu on the site. When you create the modules, you will select which menu items (pages) the modules will show on.

## Column Headers

- **Checkbox**. Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **Title**. The name of the menu.
- **# Published**. Number of published menu items in this menu.
- **# Unpublished**. Number of unpublished menu items in this menu.
- **# Trashed**. Number of trashed menu items in this menu.
- **Modules Linked to the Menu**. Lists any menu modules associated with the menu. The column shows the module's name, access level, and position on template.

- **ID**. This is a unique identification number for this item assigned automatically by the CMS. It is used to identify the item internally, and you cannot change this number. When creating a new item, this field displays 0 until you save the new entry, at which point a new ID is assigned to it.

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

- **New**. Opens the editing screen to create a new menu.
- **Edit**. Opens the editing screen for the selected menu. If more than one menu is selected (where applicable), only the first menu will be opened. The editing screen can also be opened by clicking on the Title or Name of the menu.
- **Delete**. Deletes the selected menus. Works with one or multiple menus selected. Deleting a menu also deletes all the menu items it contains and any associated menu modules. On clicking Delete you will be asked to confirm that you want to delete the selected menus. Clicking OK button will delete the menus. Click Cancel to abort the deletion.
- **Rebuild**. Reconstructs and refreshes the menu table. Normally, you do *not* need to rebuild this table. This function is provided in case the data in the table becomes corrupted.
- **Options**. Opens the Options window where settings such as default parameters or permissions can be edited. See Menus Configuration.
- **Help**. Opens this help screen.

## Options

## Buttons Common to All Tabs

- **Save**. Saves the banner options and stays in the current screen.
- **Save & Close**. Saves the banner options and closes the current screen.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.

## Page Display Options

- **Browser Page Title.** Optional text for the "Browser Page Title" Element. If blank - a default value is used.
- **Show Page Heading.** (Show/Hide) Show or hide the Browser page title (above) in the heading of the page.
- **Page Heading.** Optional alternative text for the page heading.
- **Page Class.** Add an optional page class to elements in the page. It allows CSS styling specific to menu items pages.

## Permissions Tab

This screen allows you to set the component permissions. This is important to consider if you have sites with many different user categories all of whom need to have different accessibilities to the component. The screenshot below describes what you should see and the text below that describes what each permission level gives the user access to:

You work on one Group at a time by opening the slider for that group. You change the permissions in the Select New Settings drop-down list boxes. The options for each value are Inherited, Allowed, or Denied. The Calculated Setting column shows you the setting in effect. It is either Not Allowed (the default), Allowed, or Denied. Note that the Calculated Setting column is not updated until you press the Save button in the toolbar. To check that the settings are what you want, press the Save button and check the Calculated Settings column.

The default values used here are the ones set in the Global Configuration Permissions Tab

- - **Configure**  
    Open the menus component option screens (the modal window these options are in)
  - **Access Administration Interface**  
    Open the menus component manger screens
  - **Create**  
    Create new menus in the component
  - **Delete**  
    Delete existing menus in the component
  - **Edit**  
    Edit existing menus in the component
  - **Edit State**  
    Change an menus state (Publish, Unpublish, Archive, and Trash) in the component.

There are two very important points to understand from this screen. The first is to see how the permissions can be inherited from the parent Group. The second is to see how you can control the default permissions by Group and by Action. This provides a lot of flexibility. For example, if you wanted Shop Suppliers to be able to have the ability to create an article about their product, you could just change their Create value to "Allowed". If you wanted to not allow members of Administrator group to delete objects or change their state, you would change their permissions in these columns to Inherited (or Denied). It is also important to understand that the ability to have child groups is completely optional. It allows you to save some time when setting up new groups. However, if you like, you can set up all groups to have Public as the parent and not inherit any permissions from a parent group.

## Quick Tips

- It is expedient to give a descriptive title for new menus because, later, you will see it in the *Backend Menus* menu. It is a good idea to fill in the *Description* field with information about the menu. If you enter a short title in the *Module title* field, you can identify the menu's module using that title in the Module Manager.
- Though you can create a copy of a selected menu by clicking the *Copy* toolbar button, you can make another instance in the Module Manager as well.
- When you create a new menu, use only English alphanumeric characters without space in the Unique Name field. It is a good idea using only a-z, 0-9 and underscore (_) characters. Please read the tooltips as well.
- If you don't enter a *Module title*, no module will be created and the menu cannot be displayed in the front end. However you can use the Module Manager later to create a new mod_mainmenu module, and assign it to the menu.
- If you delete an existing menu, do not forget that all the menu items of the respective menu will be also deleted.
- The Main Menu has your default menu item, so it **should not be deleted**. The default menu item is your home page, and your site will not function if it is deleted. If you change the default menu item, make sure that you don't delete that menu item either! The menu with the default menu item is marked with an asterisk (\*) in the *Menus* menu.

## Related Information

- To add new Menus: Menu Manager New/Edit
- To set action permissions for menus: Menus Configuration
- To add or edit Menu Items: Menu Item Manager
- To add or edit Menu Modules: Module Manager

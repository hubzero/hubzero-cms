<!--
status: imported
source: core/components/com_modules/admin/help/en-GB/modules.phtml
imported: 2026-09-09
-->
# Module Manager

## How to Access

- Select **Extensions → Module Manager** from the drop-down menu on the back-end of your installation.
- Click on **Module Manager** in the Control Panel of the administrator interface.

## Description

The Module Manager is where you add and edit Modules. Modules are used to display content and/or media around the main content.

**Module Facts:**

1. All hubs websites require at least 1 Menu Module
2. All Other Module Types are Optional. (Examples: News, Banner, Latest News, Polls)
3. Every Menu is accompanied by a menu module. (Example mod_mainmenu)
4. Multiple occurrences of similar module types.
5. Some Modules are linked to components. For example, each Menu Module is related to one Menu component. To define a Menu, you need to create the Menu and Menu Items using the Menus screens and then create the Module for the Menu using this screen. Other Modules, such as Custom HTML and Breadcrumbs, do not depend on any other content. See Module Manager - New/Edit for information about the different Module Types.

The installation is accompanied with 20 module types.

## Column Headers

Click on the column heading to sort the list by that column's value.

- **Checkbox**. Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **Module Name.** The name of the Module. You can click on the name to open the Module for editing.

- **State**: State of the item. Possible values are:
  - *Published*: The item is published. This is the only state that will allow regular website users to view this item.
  - *Unpublished*: The item is unpublished.
  - *Archived*: The item has been archived.
  - *Trashed*: The item has been sent to the Trash.

- **Position.** The position on the page where this module is displayed. Positions are locations on the page where modules can be placed (for example, "left" or "right"). Positions are defined in the Template in use for the page. Positions can also be used to insert a Module inside an Article using the syntax "{loadposition xxx}", where "xxx" is a unique position for the module.
- **Order.** The order to display modules within a Position. If the list is sorted by this column, you can change the display order of modules within a Position by selecting a Position in the "Select Position" filter and then clicking the arrows or by entering the sequential order and clicking 'Save Order'.

- **Type.** The system name of the Module. Many Extensions contribute additional Modules. See Module Manager New/Edit for information about each of the standard Modules.

- **Pages.** The Menu Items where this Module will be displayed. Options are "All" for all Menu Items, "None" for no Menu Items, and "Varies" for selected Menu Items. A Module will only display on Menu Items where it is selected.

- **Access Level**. Who has access to this item. Current options are:
  - Public: Everyone has access
  - Registered: Only registered users have access
  - Special: Only users with author status or higher have access

- You can change an item's Access Level by clicking on the icon in the column.

- **Language**. Item language.

- **ID**. The ID number. This is a unique identification number for this item assigned automatically by the CMS. It is used to identify the item internally, for example in internal links. You can not change this number.

## Toolbar

At the top right you will see the toolbar:

The functions are:

- **New**. Opens the editing screen to create a new module.
- **Edit**. Opens the editing screen for the selected module. If more than one module is selected (where applicable), only the first module will be opened. The editing screen can also be opened by clicking on the Title or Name of the module.
- **Duplicate**. Makes a copy of the selected module. The copy is created immediately and is given the same name as the original but prefixed with "Copy of" and/or suffixed with a number (eg. "(2)") so that it can be distinguished from the original and any other copies.
- **Publish**. Makes the selected modules available to visitors to your website.
- **Unpublish**. Makes the selected modules unavailable to visitors to your website.
- **Check In**. Checks-in the selected modules. Works with one or multiple modules selected.
- **Trash**. Changes the status of the selected modules to indicate that they are trashed. Trashed modules can still be recovered by selecting "Trashed" in the Select Status filter and changing the status of the modules to Published or Unpublished as preferred. To permanently delete trashed modules, select "Trashed" in the Select Status filter, select the modules to be permanently deleted, then click the Empty Trash toolbar icon.
- **Options**. Opens the Options window where settings such as default parameters or permissions can be edited.
- **Help**. Opens this help screen.

## List Filters

## Filter by Partial Title

- **Filter by Partial Title or ID.** In the upper left is a filter field and two buttons.
  - To filter by partial title, enter part of the title and click Search.
  - To filter by ID number, enter "id:xx", where "xx" is the ID number (for example, "id:9").

- Click Clear to clear the Filter field and restore the list to its unfiltered state.

## Site and Administrator Links

At the top right, the first drop-down box will look like:

- **Site.** Opens the Site tab. This is the default tab and allows you to manage the Modules for the front end of the web site.
- **Administrator.** This tab allows you to manage the Modules for the back end administration of the web site. If you do not need to change the administrator menus, no modifications are required here.

## Filter by State, Position, Type, Access and Language

In the upper right area, above the column headings, are drop-down list boxes. The selections may be combined. Only items matching both selections will display in the list.

- **Filter by Published Status.** Lets you show only items with the selected published status.
  - *- Select Status -:* Shows items that are Published and Unpublished. Does *not* show items that are Trashed or Archived.
  - *Published:* Shows only items that are Published.
  - *Unpublished:* Shows only items that are Unpublished.
  - *Archived:* Shows only items that are Archived.
  - *Trashed:* Shows only items that are Trashed. *Important Note:* To permanently delete items:
    1. Change the status of the items to Trashed.
    2. Change the Status filter to Trashed. At this point the trashed items will show and an icon called "Empty trash" will show in the toolbar.
    3. Select the desired trashed items and click on "Empty Trash" in the toolbar. The items will be permanently deleted.
  - *All:* Shows all items regardless of published status.

- **Select Position.** Select a Position from the drop-down list box of available Positions.

- **Select Type.** Select the Module Type from the drop-down list box of available Module Types. Additional ones may be available if you have installed any Extensions. See Module Manager - New/Edit for information about the available Module Types.

- **Filter by Viewing Access Level.** Lets you show only items that have a specified viewing access level. The list box will show the access levels defined for your site, similar to the example below.
  - *- Select Access -:* Show items with any viewing access level.
  - *&amp;lt;your access level>:* Show items only with this viewing access level.

- **Filter by Language.** Lets you show only items that have a specific language assigned. The list box will show the languages defined for your site, similar to the example below.
  - *- Select Language - or All:* Show items for any language.
  - *&amp;lt;your language>:* Show items only for this language.

## Quick Tips

- You can change the order of Modules within a Module Position as follows:
  - Select the desired Position using the Position Filter. This will limit the list to Modules that are assigned to this Position.
  - Change the order using the Up or Down arrows or by typing the order number and pressing the Save Order icon. The Modules will display in the new order within the Position.
- You can open a Module for editing by clicking on the Module Name. This opens the Module Manager - New/Edit screen.

## Related Information

- To create or edit Modules: Module Manager - New/Edit

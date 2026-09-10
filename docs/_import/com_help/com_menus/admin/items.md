<!--
status: imported
source: core/components/com_menus/admin/help/en-GB/items.phtml
imported: 2026-09-09
-->
# Menu Manager: Menu ITems

## How to access

Select **Menus → [name of the menu]** from the drop-down menu on the back-end of your installation. For example, if a Menu is called "Main Menu", select **Menus → Main Menu**. Or navigate to the Menu Manager and click on the icon in the Menu Items column.

## Description

The Menu Item Manager lists the menu items contained in a menu created using the Menu Manager.

## Column Headers

- **Checkbox**. Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.

- **Title**. The name of the item. For a Menu Item, the Title will display in the Menu. For an Article or Category, the Title may optionally be displayed on the web page. This entry is required. You can open the item for editing by clicking on the Title.

- **Published**. Whether the item has been published or not. You can change the Published state by clicking on the icon in this column.

- **Ordering**. The order in which to display items. You can sort the list by order number by clicking on the Ordering label in the column heading. If the list is sorted by this column, you can change the order by clicking the arrows or by entering the sequential order and clicking the 'Save Order' icon. This is shown in the screen below. Note that the ordering is only defined within a single category. For this reason, it is easier to re-order the list if you use the Category filter to select one category. Note that the display order on a page can often be overridden the 'Advanced Options' section of each Menu Item.

- **Access.** The viewing level access for this item.

- **Menu Item Type.** The Menu Item Type selected when this menu item was created. This can be one of the core menu item types or a menu item type provided by an installed extension.

- **Home**. The yellow star icon designates which menu item is the current Home Page. Clicking on an empty Star icon will designate that menu item as the new Home Page.

- **Language**. Item language.

- **ID**. This is a unique identification number for this item assigned automatically by the CMS. It is used to identify the item internally, and you cannot change this number. When creating a new item, this field displays 0 until you save the new entry, at which point a new ID is assigned to it.

## Toolbar

At the top right you will see the toolbar. The functions are:

- **New**. Opens the editing screen to create a new menu item.
- **Edit**. Opens the editing screen for the selected menu item. If more than one menu item is selected (where applicable), only the first menu item will be opened. The editing screen can also be opened by clicking on the Title or Name of the menu item.
- **Publish**. Makes the selected menu items available to visitors to your website.
- **Unpublish**. Makes the selected menu items unavailable to visitors to your website.
- **Check In**. Checks-in the selected menu items. Works with one or multiple menu items selected.
- **Trash**. Changes the status of the selected menu items to indicate that they are trashed. Trashed menu items can still be recovered by selecting "Trashed" in the Select Status filter and changing the status of the menu items to Published or Unpublished as preferred. To permanently delete trashed menu items, select "Trashed" in the Select Status filter, select the menu items to be permanently deleted, then click the Empty Trash toolbar icon.
- **Home**. Makes the page associated with the currently selected menu item the home page of your site. That is, it becomes the default page on your website.
- **Rebuild**. Reconstructs and refreshes the relevant table. Normally, you do *not* need to rebuild this table. This function is provided in case the data in the table becomes corrupted.
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

## List Filters

- **Filter by Partial Title or ID.** In the upper left is a filter field and two buttons, as shown below.
  - To filter by partial title, enter part of the title and click Search.
  - To filter by ID number, enter "id:xx", where "xx" is the ID number (for example, "id:9").

- Click Clear to clear the Filter field and restore the list to its unfiltered state.

- **Page Controls.** When the number of items is more than one page, you will see a page control bar as shown below.
  - **Display #:** Select the number of items to show on one page.
  - **Start:** Click to go to the first page.
  - **Prev:** Click to go to the previous page.
  - **Page numbers:** Click to go to the desired page.
  - **Next:** Click to go to the next page.
  - **End:** Click to go to the last page.

- **Menu.** Allows you to choose a different menu item to display the items within.

- **Filter by Max Levels (Category Level).** Lets you show only items whose category is at or above the specified level in the category hierarchy.
  - *- Select Max Levels -:* Show all items regardless of level of their assigned category.
  - *1:* Only show items whose category is at the top level in the category hierarchy (in other words, with categories whose parent category is "- No Parent -".)
  - *2-10:* Only show items whose category is in the top 2-10 levels in the category hierarchy.

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

- **Filter by Viewing Access Level.** Lets you show only items that have a specified viewing access level. The list box will show the access levels defined for your site, similar to the example below.
  - *- Select Access -:* Show items with any viewing access level.
  - *&amp;lt;your access level>:* Show items only with this viewing access level.

- **Filter by Language.** Lets you show only items that have a specific language assigned. The list box will show the languages defined for your site, similar to the example below.
  - *- Select Language - or All:* Show items for any language.
  - *&amp;lt;your language>:* Show items only for this language.

## Quick Tips

- Select an item and click on the *Home* button to set your Home page.
- Set different filter options to only show some of the menu items.

## Related information

- Menu Manager

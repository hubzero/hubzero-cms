<!--
status: imported
source: core/components/com_categories/admin/help/en-GB/categories.phtml
imported: 2026-09-09
-->
# Category Manager

Select **Content → Category Manager** from the drop-down menu on the back-end of your installation.

<a id="description"></a>

## Description

The Category Manager is where you can edit existing Categories and create new ones. Articles are organized into Sections and Categories. Categories are the second level of organization underneath Sections. Every Section contains one or more Categories. The special Section 'Uncategorized' has a special Category also called 'Uncategorized'. These are built in.

<a id="column-headers"></a>

## Column Headers

Click on the column heading to sort the list by that column's value.

- **Checkbox**  
  Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **Title**  
  The name of the item. For a Menu Item, the Title will display in the Menu. For an Article or Category, the Title may optionally be displayed on the web page. This entry is required. You can open the item for editing by clicking on the Title.
- **Status**  
  (Published/Unpublished/Archived/Trashed) The published status of the item.
- **Ordering**  
  The order in which to display items. You can sort the list by order number by clicking on the Ordering label in the column heading. If the list is sorted by this column, you can change the order by clicking the arrows or by entering the sequential order and clicking the 'Save Order' icon. Note that the ordering is only defined within a single category. For this reason, it is easier to re-order the list if you use the Category filter to select one category.
- **Access**  
  The viewing level access for this item.
- **Language**  
  Item language.
- **ID**  
  This is a unique identification number for this item assigned automatically by the CMS. It is used to identify the item internally, and you cannot change this number. When creating a new item, this field displays 0 until you save the new entry, at which point a new ID is assigned to it.

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar. The functions are:

- **New**  
  Opens the editing screen to create a new content category.
- **Edit**  
  Opens the editing screen for the selected content category. If more than one content category is selected (where applicable), only the first content category will be opened. The editing screen can also be opened by clicking on the Title or Name of the content category.
- **Publish**  
  Makes the selected content categories available to visitors to your website.
- **Unpublish**  
  Makes the selected content categories unavailable to visitors to your website.
- **Archive**  
  Changes the status of the selected content categories to indicate that they are archived. Archived content categories can be moved back to the published or unpublished state by selecting "Archived" in the Select Status filter and changing the status of the content categories to Published or Unpublished as preferred.
- **Check In**  
  Checks-in the selected content categories. Works with one or multiple content categories selected.
- **Trash**  
  Changes the status of the selected content categories to indicate that they are trashed. Trashed content categories can still be recovered by selecting "Trashed" in the Select Status filter and changing the status of the content categories to Published or Unpublished as preferred. To permanently delete trashed content categories, select "Trashed" in the Select Status filter, select the content categories to be permanently deleted, then click the Empty Trash toolbar icon.
- **Rebuild**  
  Reconstructs and refreshes the content category table. Normally, you do *not* need to rebuild this table. This function is provided in case the data in the table becomes corrupted.
- **Options**  
  Opens the Options window where settings such as default parameters or permissions can be edited.
- **Help**  
  Opens this help screen.

<a id="toolbar-links"></a>

## Toolbar Links

At the top left, above the Filter, you will see the following links:

- **Articles**  
  Click this link to go to the [Content Manager: Articles](https://help.hubzero.org/index.php?option=com_help&view=help&keyref=Help25:Content_Article_Manager) screen.
- **Categories**  
  This link takes you to the screen you are currently on.
- **Featured Articles**  
  Click this link to go to the [Content Manager: Featured Articles](https://help.hubzero.org/index.php?option=com_help&view=help&keyref=Help25:Content_Featured_Articles) screen.

<a id="toolbar-links"></a>

## List Filters

- **Filter by Partial Title**  
  You can filter the list of items either by entering in part of the title or the ID number. Or you can select a combination of Section, Category, Author, and Published State.
  
  - **Filter by Partial Title or ID.** In the upper left is a filter field and two buttons.
    - To filter by partial title, enter part of the title and click Search.
    - To filter by ID number, enter "id:xx", where "xx" is the ID number (for example, "id:9").
  
  Click Clear to clear the Filter field and restore the list to its unfiltered state.
- **Filter by Max Levels, Status, Access, Language**  
  In the upper right area, above the column headings, are 4 drop-down list boxes. The selections may be combined. Only items matching all selections will display in the list.
- **Filter by Max Levels (Category Level)**  
  Lets you show only items whose category is at or above the specified level in the category hierarchy.
  
  - *- Select Max Levels -:* Show all items regardless of level of their assigned category.
  - *1:* Only show items whose category is at the top level in the category hierarchy (in other words, with categories whose parent category is "- No Parent -".)
  - *2-10:* Only show items whose category is in the top 2-10 levels in the category hierarchy.
- **Filter by Published Status**  
  Lets you show only items with the selected published status.
  
  - *- Select Status -:* Shows items that are Published and Unpublished. Does *not* show items that are Trashed or Archived.
  - *Published:* Shows only items that are Published.
  - *Unpublished:* Shows only items that are Unpublished.
  - *Archived:* Shows only items that are Archived.
  - *Trashed:* Shows only items that are Trashed. *Important Note:* To permanently delete items:
    1. Change the status of the items to Trashed.
    2. Change the Status filter to Trashed. At this point the trashed items will show and an icon called "Empty trash" will show in the toolbar.
    3. Select the desired trashed items and click on "Empty Trash" in the toolbar. The items will be permanently deleted.
  - *All:* Shows all items regardless of published status.
- **Filter by Viewing Access Level**  
  Lets you show only items that have a specified viewing access level. The list box will show the access levels defined for your site, similar to the example below.
  
  - *- Select Access -:* Show items with any viewing access level.
  - *&amp;lt;your access level>:* Show items only with this viewing access level.
- **Filter by Language**  
  Lets you show only items that have a specific language assigned. The list box will show the languages defined for your site, similar to the example below.
  
  - *- Select Language - or All:* Show items for any language.
  - *&amp;lt;your language>:* Show items only for this language.

<a id="batch-process"></a>

## Batch Process

This section allows you to change settings for a group of selected items. You can change one value or all three values at one time. Note that if you copy items to a new category, changes you have selected fro access level and language will be applied to the copies, not the original. To batch process a group of items:

1. Select one or more items on the list by checking the desired check boxes.
2. Set one or more of the following values:
   - To change the access levels, select the desired new access level from the Set Access Level list box.
   - To change the Language, select the desired language from the Set Language list box.
   - To change the Category, select a category. To leave the category unchanged, use the default value of "Select".
     - To copy the items to a different category, select the desired category from the category list box and check the Copy option. In this case, the original items are unchanged and the copies are assigned to the new category and, if selected, the new access level and language.
     - To move the items to a different category, select the desired category from the category list box and check the Move option. In this case, the original items will be moved to a new category and, if selected, be assigned the new access level and language.
3. When all of the settings are entered, click on Process to perform the changes. A message "Batch process completed successfully." will show.

Note that nothing will happen if you (a) don't have any items selected or (b) have not selected an access level, language, or category.

If you wish to clear your entered selections, click on the Clear button. This will return all of the Batch controls to their default values. Note that this does *not* uncheck the check boxes for the items.

<a id="quick-tips"></a>

## Quick Tips

- Click on the Title of a Category to edit it.
- Click on the green check mark or the red X in the Published column to toggle between Published and Unpublished.
- Click on the Column Headers to sort the Categories by that column. Click a second time to sort descending (Z to A).

<a id="related-info"></a>

## Related Information

- To add or edit Categories: [Category Manager - New/Edit](https://help.hubzero.org/index.php?option=com_help&view=help&keyref=Help25:Components_Content_Categories_Edit)
- To create a page showing Articles for a Category in a Blog layout: [Menu Item Manager - New/Edit - Category Blog Layout](https://help.hubzero.org/index.php?option=com_help&view=help&keyref=Screen.menus.edit.15#Category_Blog_Layout)
- To create a page showing Articles for a Category in a List layout: [Menu Item Manager - New/Edit - Category List Layout](https://help.hubzero.org/index.php?option=com_help&view=help&keyref=Screen.menus.edit.15#Category_List_Layout)

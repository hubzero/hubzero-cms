<!--
status: imported
source: core/components/com_content/admin/help/en-GB/articles.phtml
imported: 2026-09-09
-->
# Article Manager: Articles

- [Overview](#overview)
- [Column Headers](#column-headers)
- [Toolbar](#toolbar)
- [Sub-menu Links](#sub-menu)
- [List Filters](#list-filters)

<a id="overview"></a>

## Description

The Article Manager is used to add and edit articles. See [Toolbar](#toolbar) below for a detailed list of all functions.

<a id="column-headers"></a>

## Column Headers

Click on the column heading to sort the list by that column's value. The list will be sorted in order by that column and a sort icon will show next to the column name, as shown below.

Click a second time to reverse the sort to high-to-low. The sort icon will change to high-to-low.

- **Checkbox**. Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **Title**. The name of the item. For a Menu Item, the Title will display in the Menu. For an Article or Category, the Title may optionally be displayed on the web page. This entry is required. You can open the item for editing by clicking on the Title.
- **Status.** (Published/Unpublished/Archived/Trashed) The published status of the item.
- **Featured.** Whether or not the Article will show on the Featured Articles Page. You can change an Article's published state by clicking on the icon in the column.
- **Category.** The Category this item belongs to.
- **Ordering**. The order in which to display items. You can sort the list by order number by clicking on the Ordering label in the column heading. If the list is sorted by this column, you can change the order by clicking the arrows or by entering the sequential order and clicking the 'Save Order' icon. Note that the ordering is only defined within a single category. For this reason, it is easier to re-order the list if you use the Category filter to select one category.
- **Access.** The viewing level access for this item.
- **Created by.** Name of the User who created this item.
- **Date.** The date this Article was created. This date is added automatically by the CMS, but you may change it in the Publishing Options - Article section of the Content Article Manager Edit.
- **Hits.** The number of times an item has been viewed.
- **Language**. Item language.
- **ID**. This is a unique identification number for this item assigned automatically by the CMS. It is used to identify the item internally, and you cannot change this number. When creating a new item, this field displays 0 until you save the new entry, at which point a new ID is assigned to it.

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar. The functions are:

- **New**. Opens the editing screen to create a new article.
- **Edit**. Opens the editing screen for the selected article. If more than one article is selected (where applicable), only the first article will be opened. The editing screen can also be opened by clicking on the Title or Name of the article.
- **Publish**. Makes the selected articles available to visitors to your website.
- **Unpublish**. Makes the selected articles unavailable to visitors to your website.
- **Featured.** Marks selected articles as featured. Works with one or multiple articles selected.
- **Archive**. Changes the status of the selected articles to indicate that they are archived. Archived articles can be moved back to the published or unpublished state by selecting "Archived" in the Select Status filter and changing the status of the articles to Published or Unpublished as preferred.
- **Check In**. Checks-in the selected articles. Works with one or multiple articles selected.
- **Trash**. Changes the status of the selected articles to indicate that they are trashed. Trashed articles can still be recovered by selecting "Trashed" in the Select Status filter and changing the status of the articles to Published or Unpublished as preferred. To permanently delete trashed articles, select "Trashed" in the Select Status filter, select the articles to be permanently deleted, then click the Empty Trash toolbar icon.
- **Options**. Opens the Options window where settings such as default parameters or permissions can be edited. See Article Manager Options for more information.
- **Help**. Opens this help screen.

<a id="toolbar-links"></a>

## Toolbar Links

At the top left, above the Filter, you will see the following links:

- **Articles.** This link takes you to the screen you are currently on.
- **Categories.** Click this link to go to the Content Manager: Categories screen.

<a id="list-filters"></a>

## List Filters

Above the column headers are a series of controls that let you limit what items show in the manager screen. More than one filter may be entered. In this case, only items that meet all of the filter conditions will show on the list.

- **Filter by Partial Title or ID.** In the upper left is a filter field and two buttons, as shown below.
  - To filter by partial title, enter part of the title and click Search.
  - To filter by ID number, enter "id:xx", where "xx" is the ID number (for example, "id:9").

- Click Clear to clear the Filter field and restore the list to its unfiltered state.

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
- **Filter by Category.** Lets you show only items assigned to a specific category. The list box will show the categories defined for your site, similar to the example below.
  - *- Select Category -:* Show items assigned to any category.
  - *&amp;lt;your category>:* Show items assigned only to this category.
- **Filter by Max Levels (Category Level).** Lets you show only items whose category is at or above the specified level in the category hierarchy.
  - *- Select Max Levels -:* Show all items regardless of level of their assigned category.
  - *1:* Only show items whose category is at the top level in the category hierarchy (in other words, with categories whose parent category is "- No Parent -".)
  - *2-10:* Only show items whose category is in the top 2-10 levels in the category hierarchy.
- **Filter by Viewing Access Level.** Lets you show only items that have a specified viewing access level. The list box will show the access levels defined for your site, similar to the example below.
  - *- Select Access -:* Show items with any viewing access level.
  - *&amp;lt;your access level>:* Show items only with this viewing access level.
- **Filter by Author.** Lets you show only items that have the specified author. The list box will show the authors for your site, similar to the example below.
  - *- Select Author -:* Show all items regardless of their author.
  - *&amp;lt;your author>:* Only show items with the specified author.
- **Filter by Language.** Lets you show only items that have a specific language assigned. The list box will show the languages defined for your site, similar to the example below.
  - *- Select Language - or All:* Show items for any language.
  - *&amp;lt;your language>:* Show items only for this language.
- **Page Controls.** When the number of items is more than one page, you will see a page control bar.
  - **Display #:** Select the number of items to show on one page.
  - **Start:** Click to go to the first page.
  - **Prev:** Click to go to the previous page.
  - **Page numbers:** Click to go to the desired page.
  - **Next:** Click to go to the next page.
  - **End:** Click to go to the last page.

<a id="options"></a>

## Batch Process

This section allows you to change settings for a group of selected items.

You can change one value or all three values at one time. Note that if you copy items to a new category, changes you have selected fro access level and language will be applied to the copies, not the original.

To batch process a group of items:

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

<a id="options"></a>

## Options

Clicking on the Options icon in the toolbar opens a modal window where you can set component level options for articles. To see the help screen for Options, click Article Manager Options

<a id="tips"></a>

## Quick Tips

- If Jhe CMS is installed without sample data, one article category called "Uncategorised" is created automatically. If you want to use other categories for articles, you should create them before trying to add articles.
- To see trashed and archived articles, set the Status filter to All.
- To change the ordering of articles within a category, click on the Ordering column heading to sort by this column. Also, it is easier to see the ordering if you filter on the desired category.

<a id="related"></a>

## Related Information

- To add or edit Articles: Article Manager: New/Edit
- To manage Categories: Category Manager

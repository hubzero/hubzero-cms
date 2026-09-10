<!--
status: imported
source: core/components/com_content/admin/help/en-GB/featured.phtml
imported: 2026-09-09
-->
# Article Manager: Featured Articles

- [Overview](#overview)

<a id="overview"></a>

## Description

The Featured Articles is the place where you control which Articles are displayed on the Front Page and in what order they are displayed. The Front Page is often the Home page of a web site, but it can be any page in the site. The Front Page is created using a Menu Item with the Front Page layout.

## Column Headers

Click on the column heading to sort the list by that column's value.

- **Checkbox**. Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **Title**. The name of the item. For a Menu Item, the Title will display in the Menu. For an Article, Section, or Category, the Title may optionally be displayed on the web page. This entry is required. You can open the item for editing by clicking on the Title.
- **Published.** Whether or not this item is published. Select *Yes* or *No* from the radio button group to set the Published state for this item.
- **Category**. The Category this item belongs to. Clicking on the Category title opens the Category for editing. See Category Manager - Edit.
- **Order**. The order in which to display the Articles on the Front Page. If the list is sorted by this column, you can change the order by clicking the arrows or by entering the sequential order and clicking 'Save Order'. Note that the display order on the Front Page is set in the Parameters - Advanced section for the Front Page Blog Menu Item. To sort Front Page Articles using this Order value, set the Category Order to "No. Order by Primary Order Only" and set the Primary Order to "Default". If these Parameters are set to other values (for example, "Oldest First" or "Title (Alphabetical)"), then the Articles will be sorted that way and this column will be ignored.
- **Access Level**. Who has access to this item. Current options are:
  - Public: Everyone has access
  - Registered: Only registered users have access
  - Special: Only users with author status or higher have access

- Enter the desired level using the drop-down list box.

- **Created by**. Name of the User who created this item. This will default to the currently logged-in user. If you want to change this to a different user, click the Select User button to select a different user.
- **Date**. The date this Article was created. This date is added automatically by the CMS, but you may change it in the Parameters - Article section of the Article Manager - New/Edit.
- **Hits.** The number of hits for an Article. A hit is the number of times a page has been viewed. Hits can be reset to 0 in the Article Manager - New/Edit screen.
- **Language.** Select the language for this item. If you are not using the multi-language feature, keep the default of 'All'.
- **ID**. The ID number. This is a unique identification number for this item assigned automatically by the CMS. It is used to identify the item internally, for example in internal links. You can not change this number.

## Toolbar

At the top right you will see the toolbar. The functions are:

- **New**. Opens the editing screen to create a new article.
- **Edit**. Opens the editing screen for the selected article. If more than one article is selected (where applicable), only the first article will be opened. The editing screen can also be opened by clicking on the Title or Name of the article.
- **Publish**. Makes the selected articles available to visitors to your website.
- **Unpublish**. Makes the selected articles unavailable to visitors to your website.
- **Archive**. Changes the status of the selected articles to indicate that they are archived. Archived articles can be moved back to the published or unpublished state by selecting "Archived" in the Select Status filter and changing the status of the articles to Published or Unpublished as preferred.
- **Check In**. Checks-in the selected articles. Works with one or multiple articles selected.
- **Remove.**. Removes one or more articles from featured status. Featured articles are shown in featured views. Removed articles may still be available on other pages.
- **Trash**. Changes the status of the selected articles to indicate that they are trashed. Trashed articles can still be recovered by selecting "Trashed" in the Select Status filter and changing the status of the articles to Published or Unpublished as preferred. To permanently delete trashed articles, select "Trashed" in the Select Status filter, select the articles to be permanently deleted, then click the Empty Trash toolbar icon.
- **Options**. Opens the Options window where settings such as default parameters or permissions can be edited.
- **Help**. Opens this help screen.

## Toolbar Links

At the top left, above the Filter, you will see the following two links:

- **Articles.** Click this link to go to the Content Manager: Articles screen.
- **Categories.** Click this link to go to the Content Manager: Categories screen.
- **Featured Articles.** This link takes you to the screen you are currently on.

## List Filters

**Filter by Partial Title**

You can filter the list of items either by entering in part of the title or the ID number. You can also select a combination of State, Access, and Language.

- **Filter by Partial Title or ID.** In the upper left is a filter field and two buttons, as shown below.
  - To filter by partial title, enter part of the title and click Search.
  - To filter by ID number, enter "id:xx", where "xx" is the ID number (for example, "id:9").

- Click Clear to clear the Filter field and restore the list to its unfiltered state.

**Filter by Status, Access, Language**

In the area in the upper right, above the column headings, there are 3 drop-down list boxes as shown below:

The selections may be combined. Only items matching all selections will display in the list.

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

- Only the Articles that have already been added to the Front Page will display on this screen. Articles can be added to the Front Page in the Article Manager or when an Article is added or edited in Article Manager - New/Edit.
- You can control the number of columns, number of articles, and other features of the Front Page layout in the Menu Item Manager - New/Edit - Front Page Blog Layout.

## Related Information

- To change the way Articles are displayed on the Front Page Layout: Menu Item Manager - New/Edit - Front Page Blog Layout
- To work with Articles: Article Manager
- To work with Categories: Category Manager
- To work with Sections: Section Manager
- To work with Users: User Manager

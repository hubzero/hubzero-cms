<!--
status: imported
source: core/components/com_languages/admin/help/en-GB/languages.phtml
imported: 2026-09-09
-->
# Language Manager: Content Languages

- [How to Access](#How_to_Access)
- [Description](#Description)
- [Column Headers](#Column_Headers)
- [List Filters](#List_Filters)
- [Options](#Options)
- [Permissions Tab](#Permissions_Tab)
- [Toolbar](#Toolbar)
- [Toolbar Links](#Toolbar_Links)
- [Quick Tips](#Quick_Tips)
- [Related Information](#Related_Information)

<a id="How_to_Access"></a>

## How to Access

Select **Extensions → Language Manager** from the drop-down menu on the back-end of your installation **or** click the icon **Language** in the control center then click the **Content** tab.

<a id="Description"></a>

## Description

In the Language Manager: Content Languages tab you can set the Native Title, Language Code, SEF Prefix, and Image Prefixes of the installed or to be installed languages.

These are used when you set your site as multilanguage.

<a id="Column_Headers"></a>

## Column Headers

- **#**. An indexing number automatically assigned for ease of reference.
- **Checkbox**. Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **Title.** The names of the installed Languages on this web site.
- **Native Title.** Language title IN the native language.
- **Language Code.** The language tag - example: en-GB for English (UK). This should be the exact prefix used for the language installed or to be installed.
- **URL Language Code.** The language tag that will displayed in the URL - example: en for English.
- **Image Prefix.** Name of the image file for this language when using the "Use Image Flags" Language Switcher basic option. Example: If 'en' is chosen, then the image shall be en.gif. Images and CSS for this module are in media/mod_languages/

- **Status.** (Published/Unpublished/Archived/Trashed) The published status of the item.

- **Ordering.** Select the ordering of the module as compared to other modules in the same position. See **Position**.

- **Access.** The viewing level access for this item.
- **Home.** Is the language the language of the Home Page.

- **ID**. This is a unique identification number for this item assigned automatically. It is used to identify the item internally, and you cannot change this number. When creating a new item, this field displays 0 until you save the new entry, at which point a new ID is assigned to it.

<a id="List_Filters"></a>

## List Filters

## Filter by Partial Title

You can filter the list of items by typing part of the Language's name or the ID number of the language.

## Filter by Published State and Access

In the upper right area, above the column headings, there are two drop-down list boxes as shown below.

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

## Number of Items to Display

Below the list you'll find:

- **Page Controls.** When the number of items is more than one page, you will see a page control bar as shown below.
  - **Display #:** Select the number of items to show on one page.
  - **Start:** Click to go to the first page.
  - **Prev:** Click to go to the previous page.
  - **Page numbers:** Click to go to the desired page.
  - **Next:** Click to go to the next page.
  - **End:** Click to go to the last page.

<a id="Options"></a>

## Options

Click the Options button to open the **Language Manager Options** window which lets you configure this component.

## Buttons Common to All Tabs

At the top right of the Options modal window you will see the toolbar.

- **Save**. Saves the language options and stays in the current screen.
- **Save & Close**. Saves the language options and closes the current screen.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.

<a id="Permissions_Tab"></a>

## Permissions Tab

This screen allows you to set the component permissions in the CMS. This is important to consider if you have sites with many different user categories all of whom need to have different accessibilities to the component. The screenshot below describes what you should see and the text below that describes what each permission level gives the user access to.

You work on one Group at a time by opening the slider for that group. You change the permissions in the Select New Settings drop-down list boxes. The options for each value are Inherited, Allowed, or Denied. The Calculated Setting column shows you the setting in effect. It is either Not Allowed (the default), Allowed, or Denied. Note that the Calculated Setting column is not updated until you press the Save button in the toolbar. To check that the settings are what you want, press the Save button and check the Calculated Settings column.

The default values used here are the ones set in the Global Configuration Permissions Tab

- - **Configure**  
    Open the language manager option screens (the modal window these options are in)
  - **Access Administration Interface**  
    Open the language manager manger screens
  - **Create**  
    Create new languages and string overrides in the component
  - **Delete**  
    Delete existing languages and string overrides in the component
  - **Edit**  
    Edit existing languages and string overrides in the component
  - **Edit State**  
    Change an languages and string overrides state (Publish, Unpublish, Archive, and Trash) in the component.

There are two very important points to understand from this screen. The first is to see how the permissions can be inherited from the parent Group. The second is to see how you can control the default permissions by Group and by Action. This provides a lot of flexibility. For example, if you wanted Shop Suppliers to be able to have the ability to create an article about their product, you could just change their Create value to "Allowed". If you wanted to not allow members of Administrator group to delete objects or change their state, you would change their permissions in these columns to Inherited (or Denied). It is also important to understand that the ability to have child groups is completely optional. It allows you to save some time when setting up new groups. However, if you like, you can set up all groups to have Public as the parent and not inherit any permissions from a parent group.

<a id="Toolbar"></a>

## Toolbar

At the top right you will see the toolbar. The functions are:

- **New**. Opens the editing screen to create a new content language.
- **Edit**. Opens the editing screen for the selected content language. If more than one content language is selected (where applicable), only the first content language will be opened. The editing screen can also be opened by clicking on the Title or Name of the content language.
- **Publish**. Makes the selected content languages available to visitors to your website.
- **Unpublish**. Makes the selected content languages unavailable to visitors to your website.
- **Trash**. Changes the status of the selected content languages to indicate that they are trashed. Trashed content languages can still be recovered by selecting "Trashed" in the Select Status filter and changing the status of the content languages to Published or Unpublished as preferred. To permanently delete trashed content languages, select "Trashed" in the Select Status filter, select the content languages to be permanently deleted, then click the Empty Trash toolbar icon.
- **Install Language.** Redirects to the Extension Manager Language Installer
- **Options**. Opens the Options window where settings such as default parameters or permissions can be edited.
- **Help**. Opens this help screen.

<a id="Toolbar_Links"></a>

## Toolbar Links

At the top left, above the columns, you will see four links as shown below:

- **Installed - Site.** Shows the Languages available for the front-end of the web site. See Installed Languages Help Screen
- **Installed - Administrator.** Shows the Languages available for the back-end of the web site. See Installed Languages Help Screen
- **Content.** Shows the Content Languages available for the web site. See **Extensions Language Manager Content**.
- **Overrides.** Allows you to override any language strings in any language file through the backend. See Language Overrides Help Screen

<a id="Quick_Tips"></a>

## Quick Tips

- Users can use any Language from the list of installed Languages, either by having it assigned in the User Manager or by filling out a Menu Item Manager - New/Edit - User Form Layout at the Front end. This will cause the system prompts to be generated in this Language just for this User. For example, if a User chooses Spanish as their language, then the Search Module will show with prompts in Spanish.
- This User's choice is not affected by the Default Language set for the Front-end.

- - Changing a User's Language or the Default Language does not affect the web site's Articles and other content.

- **Important**: Do not delete the default language files (for example, with FTP). This will create errors on both the Front-end and Back-end.
- Additional Languages can be added using the Extension Manager - Install Screen.
- If desired, you can show the Front-end site in one Language and show the Back-end administration pages in a different Language. Also, individual articles can be configured to use a different language in the Advanced Parameter pane when editing the Article.

<a id="Related_Information"></a>

## Related Information

- To install more Languages: Extension Manager - Install Screen
- To uninstall a Language: Extension Manager - Manage
- To change the Language for a User: User Manager - New/Edit
- To set the Language of an Article: Article Manager - New/Edit - Parameters - Advanced

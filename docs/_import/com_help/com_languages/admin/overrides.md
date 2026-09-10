<!--
status: imported
source: core/components/com_languages/admin/help/en-GB/overrides.phtml
imported: 2026-09-09
-->
# Language Manager: Language Overrides

- [Overview](#overview)
- [Column Headers](#column-headers)
- [Toolbar](#toolbar)
- [List Filters](#list-filters)

<a id="overview"></a>

## Description

Here you can override any language string in your website in any installed language.

## Column Headers

- **Checkbox**. Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **Constant.** The language string constant being overridden.
- **Text.** The text that is replacing the language constant.
- **Language Tag.** The language tag of the text.

- **Location.** Whether the item is a public front-end or an administrator back-end item. 'Site' means it is a public front-end item. 'Administrator' means it is an administrator back-end item.

- **#**. An indexing number automatically assigned by the CMS for ease of reference.

## List Filters

## Filter by Partial Title

You can filter the list of items by typing part of the constant's name or the ID number of the constant.

## Filter by Language

In the upper right area, above the column headings, there is a drop-down list boxes.

You can filter by the Language of the Strings and by site or administrator access

## Number of Items to Display

Below the list you'll find:

- **Page Controls.** When the number of items is more than one page, you will see a page control bar as shown below.
  - **Display #:** Select the number of items to show on one page.
  - **Start:** Click to go to the first page.
  - **Prev:** Click to go to the previous page.
  - **Page numbers:** Click to go to the desired page.
  - **Next:** Click to go to the next page.
  - **End:** Click to go to the last page.

## Options

Click the Options button to open the **Language Manager Options** window which lets you configure this component.

## Buttons Common to All Tabs

At the top right of the Options modal window you will see the toolbar.

- **Save**. Saves the language options and stays in the current screen.
- **Save & Close**. Saves the language options and closes the current screen.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.

## Permissions Tab

This screen allows you to set the component permissions. This is important to consider if you have sites with many different user categories all of whom need to have different accessibilities to the component. The screenshot below describes what you should see and the text below that describes what each permission level gives the user access to:

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

## Toolbar

At the top right you will see the toolbar. The functions are:

- **New**. Opens the editing screen to create a new language override.
- **Edit**. Opens the editing screen for the selected language override. If more than one language override is selected (where applicable), only the first language override will be opened. The editing screen can also be opened by clicking on the Title or Name of the language override.
- **Delete**. Deletes the selected language overrides. Works with one or multiple language overrides selected.
- **Options**. Opens the Options window where settings such as default parameters or permissions can be edited.
- **Help**. Opens this help screen.

## Toolbar Links

At the top left, above the columns, you will see four links as shown below:

- **Installed - Site.** Shows the Languages available for the front-end of the web site. See Installed Languages Help Screen
- **Installed - Administrator.** Shows the Languages available for the back-end of the web site. See Installed Languages Help Screen
- **Content.** Shows the Content Languages available for the web site. See Extensions Language Manager Content.
- **Overrides.** Allows you to override any language strings in any language file through the backend. See **Language Overrides Help Screen**

## See Also

Layout Overrides

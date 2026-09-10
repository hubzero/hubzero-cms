<!--
status: imported
source: core/components/com_templates/admin/help/en-GB/styles.phtml
imported: 2026-09-09
-->
# Template Manager: Styles

<a id="Description"></a>

## Description

The **Template Manager: Styles** screen allows you to manage template styles which you can then apply to your installation. You can specify the default style to be applied to web pages in the public and administrator interfaces. You can also configure different styles for the various menu item web pages on your site. Styles allow to you have multiple different configurations of a template saved and then apply them on a site-wide or per-page basis.

<a id="Column_Headers"></a>

## Column Headers

Click on the column heading to sort the list by that column's value.

- **Checkbox**  
  Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **Style**  
  The style name. The default style that is installed with a new template will have '-Default' at the end of its name.
- **Location**  
  Whether the style is a public front-end or an administrator back-end style. 'Site' means it is a public front-end style. 'Administrator' means it is an administrator back-end style.
- **Template**  
  The name of the template which the style was derived from.
- **Default**  
  Whether or not the style is the default applied to pages which do not have a specific style configured. A gold star means the style is the default. There can be only one default style set for locations.
- **Assigned**  
  Whether or not the template is configured on a web page. A green circle with a white check mark in it means the style is being used on at least one web page of your site.
- **ID**  
  This is a unique identification number for this item assigned automatically. It is used to identify the item internally, and you cannot change this number. When creating a new item, this field displays 0 until you save the new entry, at which point a new ID is assigned to it.

<a id="List_Filters"></a>

## List Filters

## Filter by Partial Title

You can filter the list of items by typing part of the styles's name or the ID number of the style.

## Filter by Location and Template

In the upper right area, above the column headings, there are two drop-down list boxes. The selections may be combined. Only items matching all selections will be displayed in the list.

- **Select Location.** The location the template is used in - Site or Administrator.
- **Select Template.** The template which the style is derived from.

## Number of Items to Display

- **Page Controls.** When the number of items is more than one page, you will see a page control bar as shown below.
  - **Display #:** Select the number of items to show on one page.
  - **Start:** Click to go to the first page.
  - **Prev:** Click to go to the previous page.
  - **Page numbers:** Click to go to the desired page.
  - **Next:** Click to go to the next page.
  - **End:** Click to go to the last page.

<a id="Toolbar"></a>

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Make Default**. Makes the selected template style the default.
- **Edit**. Opens the editing screen for the selected contact. If more than one contact is selected (where applicable), only the first contact will be opened. The editing screen can also be opened by clicking on the Title or Name of the contact.
- **Duplicate**. Makes a copy of the selected template style. The copy is created immediately and is given the same name as the original but prefixed with "Copy of" and/or suffixed with a number (eg. "(2)") so that it can be distinguished from the original and any other copies.
- **Delete**. Deletes the selected template styles. Works with one or multiple template styles selected.
- **Options**. Opens the Options window where settings such as default parameters or permissions can be edited.
- **Help**. Opens this help screen.

<a id="Options"></a>

## Options

Click the Options button to open the **Template Manager Options** window which lets you configure this component.

## Buttons Common to All Tabs

At the top right of the Options modal window you will see the toolbar.

- **Save**. Saves the template options and stays in the current screen.
- **Save & Close**. Saves the template options and closes the current screen.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.

## Templates Tab

- **Preview Module Positions.** Enables the preview of the module positions in the template by appending tp=1 to the web address. Also enables the Preview button in the list of templates.

## Permissions Tab

This screen allows you to set the component permissions. This is important to consider if you have sites with many different user categories all of whom need to have different accessibilities to the component. The screenshot below describes what you should see and the text below that describes what each permission level gives the user access to.

You work on one Group at a time by opening the slider for that group. You change the permissions in the Select New Settings drop-down list boxes. The options for each value are Inherited, Allowed, or Denied. The Calculated Setting column shows you the setting in effect. It is either Not Allowed (the default), Allowed, or Denied.

Note that the Calculated Setting column is not updated until you press the Save button in the toolbar. To check that the settings are what you want, press the Save button and check the Calculated Settings column.

The default values used here are the ones set in the [Global Configuration Permissions Tab](https://help.hubzero.org/index.php?option=com_help&component=com_config&page=global_config)

- **Configure**  
  Open the template manager option screens (the modal window these options are in)
- **Access Administration Interface**  
  Open the template manager manger screens
- **Create**  
  Create new styles in the component
- **Delete**  
  Delete existing styles in the component
- **Edit**  
  Edit existing templates and styles in the component
- **Edit State**  
  Change an templates and styles state (Publish, Unpublish, Archive, and Trash) in the component.

There are two very important points to understand from this screen. The first is to see how the permissions can be inherited from the parent Group. The second is to see how you can control the default permissions by Group and by Action.

This provides a lot of flexibility. For example, if you wanted Shop Suppliers to be able to have the ability to create an article about their product, you could just change their Create value to "Allowed". If you wanted to not allow members of Administrator group to delete objects or change their state, you would change their permissions in these columns to Inherited (or Denied).

It is also important to understand that the ability to have child groups is completely optional. It allows you to save some time when setting up new groups. However, if you like, you can set up all groups to have Public as the parent and not inherit any permissions from a parent group.

<a id="Toolbar_Links"></a>

## Toolbar Links

At the top left, above the Filter, you will see the following two links:

- **Styles.** This link takes you to the screen you are currently on.
- **Templates.** Click this link to go to the [Template Manager: Templates](https://help.hubzero.org/index.php?option=com_help&component=com_templates&page=templates) screen.

<a id="Quick_Tips"></a>

## Quick Tips

If you want to use the same Style for all of the pages on your site, you just assign one style as the Default. You can also assign different Styles to different pages.

<a id="Related_Information"></a>

## Related Information

To edit installed templates: [Template Manager: Templates](https://help.hubzero.org/index.php?option=com_help&component=com_templates&page=templates)

<!--
status: imported
source: core/components/com_templates/admin/help/en-GB/templates.phtml
imported: 2026-09-09
-->
# Template Manager: Templates

This screen is accessed from the back-end administrator panel. It is used to preview and templates in your website.

<a id="Description"></a>

## Description

The **Template Manager: Templates** screen allows you to preview and edit templates which are installed in your installation.

<a id="Column_Headers"></a>

## Column Headers

Click on the column heading to sort the list by that column's value.

- **Preview.** A image preview of the template (not this will only appear if supplied with the template).
- **Template.** The name of the template.
- **Location.** Whether the style is a public front-end or an administrator back-end style. 'Site' means it is a public front-end style. 'Administrator' means it is an administrator back-end style.
- **Version.** The version number of the item.
- **Date.** The date the item was created by the developer.
- **Author.** The developer of the template.

<a id="List_Filters"></a>

## List Filters

### Filter by Partial Title

- **Filter by Partial Title or ID.** In the upper left is a filter field and two buttons, as shown below.
  - To filter by partial title, enter part of the title and click Search.
  - To filter by ID number, enter "id:xx", where "xx" is the ID number (for example, "id:9").

- Click Clear to clear the Filter field and restore the list to its unfiltered state.

### Filter by Location

In the upper right area, above the column headings, there is a drop-down list box as shown below:

Only items matching the selection will be displayed in the list.

- **Select Location.** Select an extension location from the drop-down list box of available clients. There are two locations - Site and Administrator.

### Number of Items to Display

Below the list you'll find:

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

This screen allows you to set the component permissions in the CMS. This is important to consider if you have sites with many different user categories all of whom need to have different accessibilities to the component. The screenshot below describes what you should see and the text below that describes what each permission level gives the user access to:

You work on one Group at a time by opening the slider for that group. You change the permissions in the Select New Settings drop-down list boxes. The options for each value are Inherited, Allowed, or Denied. The Calculated Setting column shows you the setting in effect. It is either Not Allowed (the default), Allowed, or Denied. Note that the Calculated Setting column is not updated until you press the Save button in the toolbar. To check that the settings are what you want, press the Save button and check the Calculated Settings column.

The default values used here are the ones set in the Global Configuration Permissions Tab

- - **Configure**  
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

There are two very important points to understand from this screen. The first is to see how the permissions can be inherited from the parent Group. The second is to see how you can control the default permissions by Group and by Action. This provides a lot of flexibility. For example, if you wanted Shop Suppliers to be able to have the ability to create an article about their product, you could just change their Create value to "Allowed". If you wanted to not allow members of Administrator group to delete objects or change their state, you would change their permissions in these columns to Inherited (or Denied). It is also important to understand that the ability to have child groups is completely optional. It allows you to save some time when setting up new groups. However, if you like, you can set up all groups to have Public as the parent and not inherit any permissions from a parent group.

<a id="Toolbar_Links"></a>

## Toolbar Links

At the top left, above the Filter, you will see the following two links:

- **Styles.** Click this link to go to the Template Manager: Styles screen.
- **Templates.** This link takes you to the screen you are currently on.

<a id="Related_Information"></a>

## Related Information

To edit templates styles: Template Manager: Styles

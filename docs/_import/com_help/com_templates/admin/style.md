<!--
status: imported
source: core/components/com_templates/admin/help/en-GB/style.phtml
imported: 2026-09-09
-->
# Template Manager: Edit Style

<a id="Description"></a>

## Description

This is where you edit a template's styles. When a template is first installed, a default style is created for it. The default style for the template will have the same name as the template with a *- Default* suffix. To make a different variation of the default template style, check the default style's checkbox and press the *Duplicate* icon in the toolbar. Then edit the duplicate.

<a id="Details_and_Options"></a>

## Details and Options

## Details

- **Style Name.** The name of the style. This is the name that will display in the Style column of the *Template Manager: Styles* screen.
- **Template.** The name of the template the style is derived from.
- **Default.** Whether or not the style is the default for the location.
- **ID**. This is a unique identification number for this item assigned automatically. It is used to identify the item internally, and you cannot change this number. When creating a new item, this field displays 0 until you save the new entry, at which point a new ID is assigned to it.
- **Template description.** The description of the template the style is derived from.

## Menu assignment

This section contains all the menu items configured in your website. To apply the current style to a menu item's corresponding web page, check the box next to the menu item. You can press the *Toggle Selection* button to invert the menu item selections.

> **Note:** If a checkbox is grayed out and cannot be checked then it could be because the menu item is in use by another user. You can see if this is the case by going to the [menu manager screen for the menu](https://help.hubzero.org/index.php?option=com_help&view=help&keyref=Help25:Menus_Menu_Item_Manager) concerned. If there is a padlock symbol next to the menu item then it is currently in use by another user.

## Advanced Options

This section **may not be present for all templates** and is dependent on the actual template being used. If a template from which a style is derived from has configurable options they will be present here. It is these additional configurable options which allow you to have multiple different styles of templates with variations of these options. The options available will vary based on what options the template developer made available.

<a id="Toolbar"></a>

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Save**. Saves the template style and stays in the current screen.
- **Save & Close**. Saves the template style and closes the current screen.
- **Save as Copy**. Saves your changes to a copy of the current template style. Does not affect the current template style. This toolbar icon is not shown if you are creating a new template style.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.
- **Help**. Opens this help screen.

<a id="Related_Information"></a>

## Related Information

To install templates: [Extension Manager: Install](https://help.hubzero.org/index.php?option=com_help&view=help&keyref=Help25:Extensions_Extension_Manager_Install)

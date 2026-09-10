<!--
status: imported
source: core/components/com_members/admin/help/en-GB/quotaclasses.phtml
imported: 2026-09-09
-->
# Members: Quota Classes

- [Overview](#overview)
- [Column Headers](#column-headers)
- [Toolbar](#toolbar)
- [Sub-menu Links](#sub-menu)
- [Possible Values](#possible-values)

<a id="overview"></a>

## Overview

File system quota classes can be managed here. Changing these numbers will adjust all members currently assign that class.

<a id="column-headers"></a>

## Column Headers

- **Checkbox**  
  Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **ID**  
  This is a unique identification number for this item assigned automatically by the system. It is used to identify the item internally and cannot be changed. When creating a new item, this field is 0 until the entry is saved, at which point a new ID is assigned to it.
- **Alias**  
  The short name given to an individual class for easy identification.
- **Soft Blocks Limit**  
  Block level warning limit.
- **Hard Blocks Limit**  
  Block level upper limit.
- **Soft Files Limit**  
  File count warning limit.
- **Hard Files Limit**  
  File count upper limit.

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar.

The functions are:

- **New**  
  Opens the editing screen to create a new quota class.
- **Edit**  
  Opens the editing screen for the selected entry. If more than one entry is selected (where applicable), only the first entry will be opened. The editing screen can also be opened by clicking on the **Alias** of the entry.
- **Delete**  
  Permanently deletes selected items.
- **Help**  
  Opens this help screen.

<a id="sub-menu"></a>

## Sub-menu Links

At the top left, you will see sub-menu options. The links are:

- **Import**

<a id="possible-values"></a>

## Possible Values

Of the 4 columns available, the soft and hard block limits are the most important and should be considered as described below. Typically the hard limit will be larger than the soft limit. The limit block size is 1024.

- **Soft Blocks Limit**  
  This limit is the point at which a user will be warned that their quota has been exceeded. At that point, they will have seven days to rectify the issue. After seven days, if they have not reduced their storage below the soft limit, they will no longer be able to write to the file system.
  
  Hard Blocks Limit
  
  At no point can the user exceed this amount of storage on the file system. Irrelevant of the seven day warning period mentioned above, this limit cannot be exceeded.

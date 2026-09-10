<!--
status: imported
source: core/components/com_wishlist/admin/help/en-GB/list.phtml
imported: 2026-09-09
-->
# Wish Lists: Edit List

- [Overview](#overview)
- [Toolbar](#toolbar)
- [Details](#details)
- [Parameters](#parameters)

<a id="overview"></a>

## Overview

A wish list.

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar.

The functions are:

- **Save**  
  Save changes and return to the edit form.
- **Cancel**  
  Discard any changes made and return to the listing of entries.
- **Help**  
  Opens this help screen.

<a id="details"></a>

## Details

Every entry consists of a **title**, **category**, and **reference ID**.

- **Category**  
  The category or "type" the list is. This value will typically correspond to another component that may have a wish list plugin.
- **Reference ID**  
  The unique ID of the object the wish list is attached to. This is used in conjuntion with the **category** field. For example: A list with Category of "group" and Reference ID of "1234" will be associated to group #1234.
- **Title**  
  The title of the post. A required field.
- **Description**  
  A short description of the wish list.

<a id="parameters"></a>

## Parameters

The following options control various aspects of the published state (when and if an entry is available to users).

- **State**  
  The published status of the entry.
- **Public**  
  Controls access level. If checked, all users can access the wish list. If not checked, the list is private.

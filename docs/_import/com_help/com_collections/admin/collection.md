<!--
status: imported
source: core/components/com_collections/admin/help/en-GB/collection.phtml
imported: 2026-09-09
-->
# Collections Manager: Edit Collection

- [Overview](#overview)
- [Toolbar](#toolbar)
- [Details](#details)
- [Publishing](#publishing)

<a id="overview"></a>

## Overview

This is the edit/creation form for a collection.

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar.

The functions are:

- **Save**  
  Save changes and return to the edit form.
- **Save & Close**  
  Save changes and return to the listing of entries.
- **Cancel**  
  Discard any changes made and return to the listing of entries.
- **Help**  
  Opens this help screen.

<a id="details"></a>

## Details

Every entry consists of a **owner type**, **owner ID**, **title**, and **alias**.

- **Owner type**  
  (group/member) The type of owner.
- **Owner ID**  
  The ID of the owner. This is used in conjunction with *owner type*.
- **Title**  
  The title of the entry. A required field.
- **Alias**  
  This is an optional identifier used priamrily for URLs. When an alias isn't provided, one is generated from the provided title. The text is made lowercase, all punctuation is stripped, and spaces are turned into dashes.
- **Description**  
  A brief description for the collection.

<a id="publishing"></a>

## Publishing

The following options control various aspects of the published state (when and if an entry is available to users).

- **State**  
  The published status of the entry. Unpublished and trashed jobs will never be run.
- **Access level**  
  Which user 'access levels' have access to this item. You can change an item's Access Level by clicking on its name to open it up for editing. The default user 'access levels' which come preconfigured are:
  
  - Public: Everyone has access including website visitors who have not logged in
  - Registered: Only users with registered status or higher will have access
  - Special: Only users with author status or higher have access

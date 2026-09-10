<!--
status: imported
source: core/components/com_kb/admin/help/en-GB/category.phtml
imported: 2026-09-09
-->
# Knowledge Base: Category

- [Overview](#overview)
- [Toolbar](#toolbar)
- [Details](#details)
- [Publishing](#publishing)

<a id="overview"></a>

## Overview

A knowledge base category.

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

Every entry consists of a **title**, **alias**, and **description**.

- **Parent Category**  
  The parent category.
- **Title**  
  The title of the post. A required field.
- **Alias**  
  This is an optional identifier used priamrily for URLs. When an alias isn't provided, one is generated from the provided title. The text is made lowercase, all punctuation is stripped, and spaces are turned into dashes.
- **Description**  
  The primary body of the post. A required field.

<a id="publishing"></a>

## Publishing

The following options control various aspects of the published state (when and if an entry is available to users).

- **Publish**  
  The published status of the entry. Unpublished and trashed jobs will never be run.
- **Access level**  
  Which user 'access levels' have access to this item. You can change an item's Access Level by clicking on its name to open it up for editing. The default user 'access levels' which come preconfigured are:
  
  - Public: Everyone has access including website visitors who have not logged in
  - Registered: Only users with registered status or higher will have access
  - Special: Only users with author status or higher have access

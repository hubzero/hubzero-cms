<!--
status: imported
source: core/components/com_kb/admin/help/en-GB/article.phtml
imported: 2026-09-09
-->
# Knowledge Base: Article

- [Overview](#overview)
- [Toolbar](#toolbar)
- [Details](#details)
- [State](#state)
- [Parameters](#parameters)

<a id="overview"></a>

## Overview

A knowledge base article.

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

Every entry consists of a **title**, **alias**, and **content**.

- **Category**  
  The category the article resides in.
- **Sub-category**  
  The sub-category the article resides in.
- **Title**  
  The title of the post. A required field.
- **Alias**  
  This is an optional identifier used priamrily for URLs. When an alias isn't provided, one is generated from the provided title. The text is made lowercase, all punctuation is stripped, and spaces are turned into dashes.
- **Body**  
  The primary body of the post. A required field.
- **Tags**  
  This is an optional, comma-separated list of keywords or phrases that describe the content of the entry.

<a id="state"></a>

## State

The following options control various aspects of the published state (when and if an entry is available to users).

- **State**  
  The published status of the entry. Unpublished and trashed jobs will never be run.
- **Access level**  
  Which user 'access levels' have access to this item. You can change an item's Access Level by clicking on its name to open it up for editing. The default user 'access levels' which come preconfigured are:
  
  - Public: Everyone has access including website visitors who have not logged in
  - Registered: Only users with registered status or higher will have access
  - Special: Only users with author status or higher have access

<a id="Parameters"></a>

## Parameters

The following options control various aspects of the article's display and functionality.

- **Authors**  
  Show or hide the author of the article.
- **Comments**  
  Allow or disallow comments on an article. If allowed, a comment thread and post form will be displayed below the article.
- **Close Comments**  
  If comments are enabled: The time period in which to disable the post form and disallow any new comments.
- **Feeds**  
  Enable or disable a comment RSS feed.
- **Feed Entries**  
  The amount of text to be displayed within the feed.
- **Date**  
  (Hide/Created/Modified/Published) How (And what) date to display on the article.

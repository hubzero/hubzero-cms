<!--
status: imported
source: core/components/com_activity/admin/help/en-GB/entry.phtml
imported: 2026-09-09
-->
# Blog Entry

- [Overview](#overview)
- [Toolbar](#toolbar)
- [Details](#details)
- [Publishing](#publishing)

<a id="overview"></a>

## Overview

The edit/creation form for a blog entry.

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

Every entry consists of a **title**, **alias**, and **content**.

- **Scope**  
  (Site/Member/Group) The type of blog the entry resides in.
- **Group**  
  The group associated with the blog this entry resides in. \*This only applies if the **scope** is set to "group".
- **Title**  
  The title of the post. A required field.
- **Alias**  
  This is an optional identifier used priamrily for URLs. When an alias isn't provided, one is generated from the provided title. The text is made lowercase, all punctuation is stripped, and spaces are turned into dashes.
- **Content**  
  The primary body of the post. A required field.
- **Tags**  
  This is an optional, comma-separated list of keywords or phrases that describe the content of the entry.

<a id="publishing"></a>

## Publishing

The following options control various aspects of the published state (when and if an entry is available to users).

- **State**  
  The published status of the entry. Unpublished and trashed jobs will never be run.
- **Publish up**  
  A timestamp (YYYY-MM-DD hh:mm:ss) for when an entry should **start** being available. This allows for an entry to be set up and published in advance of when one wishes the entry to be read. When used with "Publish down", this gives the option of creating a window of time one wants the entry to available. For instance, an entry can be scheduled to be only available between March and September.
- **Publish down**  
  A timestamp (YYYY-MM-DD hh:mm:ss) for when an entry should **stop** being available. This allows for an entry to stop being available after a given date/time. When used with "Publish upn", this gives the option of creating a window of time one wants an entry to be available. For instance, an entry can be scheduled to be only available between March and September.
- **Allow comments**  
  Allow other users to post comments on this entry?

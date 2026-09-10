<!--
status: imported
source: core/components/com_wiki/admin/help/en-GB/page.phtml
imported: 2026-09-09
-->
# Wiki Manager: Edit Page

- [Overview](#overview)
- [Toolbar](#toolbar)
- [Details](#details)
- [Parameters](#parameters)

<a id="description"></a>

## Description

This is where you can add a new Page or edit an existing Page.

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar. The functions are:

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

- **Title**  
  The title of the page. When a page isn't provided, one is generated from the provided pagename. Spaces are injected between camel-cased words.
- **Pagename**  
  This is the primary identifier for a page and is also used for URLs. Pagenames are camel-cased (each new word starts with a capital letter) and may contain only numers, letters, and colons. Anything preceding a colon will be the namespace for the page.
- **Scope**  
  This consists of the path leading up to the current page, including parent pages. For example, if a page of "ChildPage" has a parent of "ParentPage", the scope will be "ParentPage"
- **Group**  
  The group associated with the wiki this entry resides in.
- **Authors**  
  A comma-separated list of usernames. Each user represented will have full edit capabilities for a page is the **mode** is set to "Knol".
- **Tags**  
  This is an optional, comma-separated list of keywords or phrases that describe the content of the entry.

<a id="parameters"></a>

## Parameters

- **Mode**  
  Who has access to edit the page and how the page is rendered:
  
  - Wiki: All logged-in users have access to edit.
  - Knol: Only the defined lsit of **authors** have edit access. The list of authors will also be presented on the page.
  - Static: Does not apply the standard wiki page tabs and rendering. This is for pages that have their carefully designated layouts and need full use of the available component output area.
  
  Enter the desired level using the drop-down list box.
- **State**  
  The permissions status of the page. Locked pages cannot be edited by users. Trashed pages will not show up in the public wiki.
- **Access**  
  Who has access to view the page:
  
  - Public: Everyone has access
  - Registered: Only registered users have access
  - Special: Only users with author status or higher have access
  
  Enter the desired level using the drop-down list box.

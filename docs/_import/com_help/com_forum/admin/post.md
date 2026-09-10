<!--
status: imported
source: core/components/com_forum/admin/help/en-GB/post.phtml
imported: 2026-09-09
-->
# Forum Manager: Edit Post

- [Overview](#overview)
- [Toolbar](#toolbar)
- [Details](#details)
- [Publishing](#publishing)

<a id="description"></a>

## Description

This is where you can add a new Section or edit an existing Section.

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar.

The functions are:

- **Save & Close**  
  Save changes and return to the listing of entries.
- **Cancel**  
  Discard any changes made and return to the listing of entries.
- **Help**  
  Opens this help screen.

<a id="details"></a>

## Details

- **Scope**  
  (Site/Course/Group) The type of forum the entry resides in.
- **Scope ID**  
  The unique identifier associated with the Scope type.
- **Section/Category**  
  The section/category this entry can be found under. *This applies to thread-starter posts only (all threads must have an initial post).*
- **Title**  
  The Title for this item. This may or may not display on the page, depending on if the post is the thread starter or not. When not, the title is automatically generated from the first few characters of the comments.
- **Comments**  
  The content of the post.

<a id="details"></a>

## Attachment

- **File**  
  A file associated with this particular post.
- **Description**  
  A description for the associated file. If none is provided, the file's name will be used.

<a id="publishing"></a>

## Publishing

- **Post anonymously**  
  When checked, user names will not be displayed or linked back to the user's profile.
- **Make discussion sticky**  
  *This applies to thread-starter posts only.* Sticky threads will appear at the top of the list of threads, regardless of the sorting or age. This is a way of ensuring users see certain threads deemed important.
- **State**  
  The published status of the entry. Unpublished and trashed entries will not be displayed.
- **Access Level**  
  Who has access to this item. Current options are:
  
  - Public: Everyone has access
  - Registered: Only registered users have access
  - Special: Only users with author status or higher have access
  
  Enter the desired level using the drop-down list box.
- **Permissions**  
  The permissions section for the entry will be at the bottom of the screen. The options allowed are:
  
  - **Create**  
    Create new items in the section
  - **Delete**  
    Delete existing items in the section
  - **Edit**  
    Edit existing items in the section
  - **Edit State**  
    Change an items state (Publish, Unpublish, Archive, and Trash) in the section.
  - **Edit Own**  
    Edit existing items in the section that the logged in user has created.
  
  There are two very important points to understand from this screen. The first is to see how the permissions can be inherited from the parent Group. The second is to see how you can control the default permissions by Group and by Action.
  
  This provides a lot of flexibility. For example, if you wanted Shop Suppliers to be able to have the ability to create an item in the category, you could just change their Create value to "Allowed". If you wanted to not allow members of Administrator group to delete objects or change their state, you would change their permissions in these columns to Inherited (or Denied).
  
  It is also important to understand that the ability to have child groups is completely optional. It allows you to save some time when setting up new groups. However, if you like, you can set up all groups to have Public as the parent and not inherit any permissions from a parent group.
  
  Please note the inherited values will come from the Permissions set in the [Global Configuration Permissions Tab](https://help.hubzero.org/index.php?option=com_help&component=com_config&page=global_config)

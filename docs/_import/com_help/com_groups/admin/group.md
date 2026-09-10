<!--
status: imported
source: core/components/com_groups/admin/help/en-GB/group.phtml
imported: 2026-09-09
-->
# Groups Manager: Edit Group

<a id="description"></a>

## Description

This is where you can add a new Group or edit an existing Group.

<a id="details"></a>

## Details

- **Type**  
  The Title for this item. This may or may not display on the page, depending on the parameter values you choose.
- **CN**  
  The internal name of the item. The CN should consist of letters, numbers, underscores, and hyphens (-). No blank spaces allowed.
- **Title**  
  The Title for this item. This may or may not display on the page, depending on the parameter values you choose.
- **Group Logo**  
  The name of the file to serve as the group's logo.
- **Public Text**  
  This is a description, message, or piece of content about the group to be displayed publicly.
- **Private Text**  
  This is a welcome message, description, or piece of content to be displayed to *members* immediately upon entering the group. It is visible only to group members.

<a id="membership"></a>

## Membership

The options in this section control how and when users can join or request membership to the group.

- **Control membership within the group?**  
  When checked, group managers may control membership within the group via the site interface.
- **Join Policy**  
  - *Open:* Anyone can join the group.
  - *Restricted:* Users must apply for membership in the group.
  - *Invite only:* Only users that have received an invitation may join the group.
  - *Closed:* No further membership requests or additions will be allowed.
- **Credentials**  
  This applies only to the **Restricted** join policy. It is a message and/or list of credentials to presented to a user when requesting membership to the group.

<a id="access"></a>

## Access

This section allows for specifying the visiblity of the group as a whole and its individual plugins.

- **Discoverability**  
  Discoverability controls how users can find or discover the group on the site.
  
  - *Visible:* The group displays in the groups browse listing and search results.
  - *Hidden:* The group does **not** display in the groups browse listing or search results.
- **Plugin Access**  
  This field lists all the plugins and their access settings for a specific group. Accepted values are:
  
  - *anyone:* Any site visitor, logged in or not, can view/access.
  - *registered:* Only registered site users can view/access.
  - *members:* Only group members have access.
  - *nobody:* Disabled/off. The plugin is not displayed.
- **Show System Users**  
  - *Global:* Value is inherited from the component settings.
  - *No:* Do not show system users
  - *Yes:* Do show system users.

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Save & Close**  
  Saves the content category and closes the current screen.
- **Cancel/Close**  
  Closes the current screen and returns to the previous screen without saving any modifications you may have made.
- **Help**  
  Opens this help screen.

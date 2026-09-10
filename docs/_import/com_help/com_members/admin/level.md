<!--
status: imported
source: core/components/com_members/admin/help/en-GB/level.phtml
imported: 2026-09-09
-->
# User Manager: Edit Viewing Access Level

## How to access

To edit an existing access level, navigate to the Viewing Access Levels tab of the User Manager (**Users → User Manager → Viewing Access Levels**). Then

1. click on the desired Level Name or
2. click on the check box for the access level and click on the Edit button.

To create a new access level,

1. select **Users → Access Levels → Add New Access Level** from the menu or
2. click on the New button in the User Manager: Viewing Access Levels screen.

## Description

Access levels control which users can view which objects on your site. Objects include menu items, modules, categories, and component items (articles, contacts, and so on). Each object in the site is assigned to one access level. User groups are also assigned to each access level.

If a user is a member of a group that in turn has permission for an access level, then that user can view all objects assigned to that access level. It is important to understand that user groups can be arranged in a parent-child hierarchy. If so, then a child group has access to all access levels that the parent group has access to. So you don't need to assign a child group access to levels that its parent group already has access to.

## Access Levels Details

- **Level Title**. The name of this access level.
- **User Groups Having Viewing Access**. All user groups defined for the site will display, with a check box for each. Check the boxes for all groups that will have access to this level. Remember that you don't have to check child groups if a parent group has access. The child will inherit the access from the parent.

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Save**. Saves the access level and stays in the current screen.
- **Save & Close**. Saves the access level and closes the current screen.
- **Save & New**. Saves the access level and keeps the editing screen open and ready to create another access level.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.
- **Help**. Opens this help screen.

## Quick tips

- If you add a new group, you may need to edit any access levels that this group should have access to.

## Related information

- User Manager: Viewing Access Levels
- User Manager: User Groups
- ACL Tutorial

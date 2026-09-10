<!--
status: imported
source: core/components/com_members/admin/help/en-GB/group.phtml
imported: 2026-09-09
-->
# User Manager: Edit Group

## How to access

To edit an existing user group, navigate to the User Groups tab of the User Manager (**Users → User Manager → User Groups**). Then

1. click on the name of the group or
2. click on the check box for the group and click on the Edit button.

To create a new user group,

1. select **Users → Groups → Add New Group** from the menu or
2. click on the New button in the User Manager: User Groups screen.

## Description

User groups play a central role in what a user can do and see on the site. Creating user groups is normally the first step in setting up the security system for your site.

User Groups control what actions a user can take on the site. Actions include things like creating a new article, changing options for a component, or logging in. The site administrator assigns permissions for various actions to each group. Permissions for actions can be assigned at different levels in the component hierarchy (Global Configuration, component options, categories, and articles). If a user does not have permission for a given action, the user cannot perform that action.

User groups also control which objects a user can view on the site. Objects include categories, articles, modules, menu items, and others. When you create an access level, one or more user groups are assigned to it. Then, when you create an object (such as a menu item or module), the object is assigned an access level. If a user is a member of a group that is assigned to an access level, this user can view any object assigned to that access level. If not, then that user cannot view that object.

User groups can be arranged in a hierarchy. If so, then all child groups inherit the action permissions and access levels of a parent group. If used wisely, this feature can save a lot of time setting up your security system, since it means that you don't have to enter duplicate setup information.

In this screen, you can create a new user group or edit an existing user group.

## User Group Details

- **Group Title**. The name of the group.
- **Group Parent**. The parent group of this group. Note that each group inherits the permissions from its parent group. If you do not want this group to inherit any permissions, make its parent group "Public". See ACL Tutorial for more information.

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Save**. Saves the user group and stays in the current screen.
- **Save & Close**. Saves the user group and closes the current screen.
- **Save & New**. Saves the user group and keeps the editing screen open and ready to create another user group.
- **Save as Copy**. Saves your changes to a copy of the current user group. Does not affect the current user group. This toolbar icon is not shown if you are creating a new user group.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.
- **Help**. Opens this help screen.

## Quick tips

- If a new group will have similar permissions to an existing group, you can save work by making the new group a child of the existing group. That way, you only need to change the permissions that are different for the new group.

## Related information

- User Manager: Users Groups
- ACL Tutorial

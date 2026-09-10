<!--
status: imported
source: core/components/com_members/admin/help/en-GB/levels.phtml
imported: 2026-09-09
-->
# User Manager: Viewing Access Levels

User controls for creating, viewing and editing users.

## How to access

To access this screen you can either:

- Select **User Access Levels** by clicking on the User Manager icon in the Site Control Panel and then select **Viewing Access Levels** in the sub menu, or
- Click on **User → Access Levels** from the drop-down menus.

## Description

In this screen you have the ability to look at a list of your users and sort them in different ways. You can also edit and create users, groups and access levels.

## Column Headers

In the table containing the users from your site, you will see different columns. Here you can read what they mean and what is displayed in that column.

- **Checkbox**. Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **Level Name**. The name of the access level.

- **Ordering**. The order in which to display items. You can sort the list by order number by clicking on the Ordering label in the column heading. If the list is sorted by this column, you can change the order by clicking the arrows or by entering the sequential order and clicking the 'Save Order' icon. This is shown in the screen below. Note that the ordering is only defined within a single category. For this reason, it is easier to re-order the list if you use the Category filter to select one category.

- **ID**. This is a unique identification number for this item assigned automatically by the CMS. It is used to identify the item internally, and you cannot change this number. When creating a new item, this field displays 0 until you save the new entry, at which point a new ID is assigned to it.

- **Display #**. This list box shows at the bottom of the screen, below the list of items. You can select the number of items to display on one page. If there are more items than this number, you can use the page navigation buttons (Start, Prev, Next, End, and page numbers) to navigate between pages. Note that if you have a large number of items, it may be helpful to use the Filter options, located above the column headings, to limit which items display.

## Toolbar

At the top right you will see the toolbar. The functions are:

- **New**. Opens the editing screen to create a new access level.
- **Edit**. Opens the editing screen for the selected access level. If more than one access level is selected (where applicable), only the first access level will be opened. The editing screen can also be opened by clicking on the Title or Name of the access level.
- **Delete**. Deletes the selected access levels. Works with one or multiple access levels selected. Note that you cannot delete an access level that is currently being used. If you try to delete an access level that is assigned, a message showing where it is assigned will be displayed.
- **Options**. Opens the Options window where settings such as default parameters or permissions can be edited.
- **Help**. Opens this help screen.

## Options

Click the Options button to open the **User Manager Options** window which lets you configure this component.

## Buttons Common to All Tabs

- **Save**. Saves the user options and stays in the current screen.
- **Save & Close**. Saves the user options and closes the current screen.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.

## Component

- **Allow User Registration.** (Yes/No) If set Yes, users can register from the front end of the site using the Create an Account link provided on the Login module. If set to No, the "Create and Account" link will not show.
- **New User Registration Group.** The group that users are assigned to by default when they register on the site. Defaults to Registered.
- **Guest User Group.** The group that guests are assigned to. (Guests are visitors to the site who are not logged in.) This is set to Public by default. If you change this to a different group, it is possible to create content on the site that is visible to guests but not visible to logged in users. See Allowing Guest-Only Access to Menu Items and Modules.
- **Send Password.** If set to yes the user will be emailed their password on the registration email. Note there are potential security issues if this is set to yes.
- **New User Account Activation.**
  - **None.** User account will be active immediately with no action required.
  - **Self.** User will receive an email with an activation link. The account will be activated when the user clicks the activation link.
  - **Admin.** User will receive an email with an activation link. When the user clicks this link, the Site Admin will be notified via email and the Site Admin needs to activate the user's account.
- **Notification Mail to Administrators.** (Yes/No) Send email notification to administrators with **User Account Activation**.
- **Captcha.** Use Captcha for User Account Registration and Username or User Password reminders.
- **Frontend User Parameters.** (Show/Hide) If set to Show, users will be able to modify their language, editor, and help site preferences from the front end of the site. If set to Hide, the user will not be able to change these settings.
- **Frontend Language.** (Show/Hide) Show or hide the option for users to set their default site language.
- **Change Login Name.** (Yes/No) Allow user to change Login Name.
- **Maximum Reset Count.** (0-20) Maximum number of reset password attempts per **Time in Hours**. Zero means no limit on reset password attempts.
- **Time in Hours.** (1-24) Time period in hours for the **Maximum Reset Count**.

## Mass Mail

- **Subject Prefix**. Enter optional text to be inserted automatically before the subject of the mass email.
- **Mailbody Suffix**. Enter optional text to be inserted automatically after the body of the email (for example, a signature).

## Permissions

This screen allows you to set the component permissions. This is important to consider if you have sites with many different user categories all of whom need to have different accessibilities to the component. The screenshot below describes what you should see and the text below that describes what each permission level gives the user access to:

You work on one Group at a time by opening the slider for that group. You change the permissions in the Select New Settings drop-down list boxes. The options for each value are Inherited, Allowed, or Denied. The Calculated Setting column shows you the setting in effect. It is either Not Allowed (the default), Allowed, or Denied. Note that the Calculated Setting column is not updated until you press the Save button in the toolbar. To check that the settings are what you want, press the Save button and check the Calculated Settings column.

The default values used here are the ones set in the Global Configuration Permissions Tab

- - **Configure**  
    Open the users component option screens (the modal window these options are in)
  - **Access Administration Interface**  
    Open the users component manger screens
  - **Create**  
    Create new users in the component
  - **Delete**  
    Delete existing users in the component
  - **Edit**  
    Edit existing users in the component
  - **Edit State**  
    Change an users state (Publish, Unpublish, Archive, and Trash) in the component.

There are two very important points to understand from this screen. The first is to see how the permissions can be inherited from the parent Group. The second is to see how you can control the default permissions by Group and by Action. This provides a lot of flexibility. For example, if you wanted Shop Suppliers to be able to have the ability to create an article about their product, you could just change their Create value to "Allowed". If you wanted to not allow members of Administrator group to delete objects or change their state, you would change their permissions in these columns to Inherited (or Denied). It is also important to understand that the ability to have child groups is completely optional. It allows you to save some time when setting up new groups. However, if you like, you can set up all groups to have Public as the parent and not inherit any permissions from a parent group.

## List Filters

**Filter by Partial Access Level Name**

- **Search Viewing Access Levels**. In the upper left is a Search field and two buttons, as shown below.

Enter part of the level's name and press the Search to find matching names. Press Reset to clear the search field and restore the list of levels.

## Quick Tips

- Click on the name of an access level to edit it.
- Click on the Column Headers to sort the users by that column, ascending or descending.

## Related information

- Add/Edit Viewing Access Level
- Users Configuration
- User Manager: Users
- User Manager: User Groups
- ACL Tutorial

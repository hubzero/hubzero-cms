<!--
status: imported
source: core/components/com_members/admin/help/en-GB/mail.phtml
imported: 2026-09-09
-->
# Mass Mail

Send an email to registered users.

## How to access

Select **Users → Mass Mail Users** from the drop-down menu on the Back-end of your installation.

## Description

The Mass Mail screen allows Users who are members of the "Super Administrator" group to send an email message to registered users for the site. Users can be selected based on groups.

## Details and Message

- **Mail to Child Groups.** Whether or not to send the Email to members of all child groups of the selected group. For example, if this box is checked and the "Public" group is selected, the email would be sent to all users, since all groups are child groups of "Public".
- **Send in HTML mode.** Whether or not to send the Email with headers that identify it as an HTML document. Email clients that support this will render any HTML codes.
- **Send to disabled users.** Whether or not to send the Email to users who have had their accounts disabled.
- **Group.** Select the groups you want to send the Email to.
- **Subject** Enter the Subject of the Email. Try to make it as descriptive as possible. Any text entered in the *Subject Prefix* parameter in Options ([User Options → Mass Mail](https://help.hubzero.org/proxy/index.php?option=com_help&view=help&keyref=Help25:Users_Mass_Mail_Users#Mass_Mail)) will be placed in front of the subject you enter here.
- **Message.** Enter the body of the Email. Any text entered in the *Mailbody Suffix* parameter in Options ([User Options → Mass Mail](https://help.hubzero.org/proxy/index.php?option=com_help&view=help&keyref=Help25:Users_Mass_Mail_Users#Mass_Mail)) will be added to the text you enter here.

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

This screen allows you to set the component permissions in the CMS. This is important to consider if you have sites with many different user categories all of whom need to have different accessibilities to the component. The screenshot below describes what you should see and the text below that describes what each permission level gives the user access to:

You work on one Group at a time by opening the slider for that group. You change the permissions in the Select New Settings drop-down list boxes. The options for each value are Inherited, Allowed, or Denied. The Calculated Setting column shows you the setting in effect. It is either Not Allowed (the default), Allowed, or Denied. Note that the Calculated Setting column is not updated until you press the Save button in the toolbar. To check that the settings are what you want, press the Save button and check the Calculated Settings column.

The default values used here are the ones set in the Global Configuration Permissions Tab

- **Configure**  
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

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Send Mail**. Send the email and return to the main Mass Mail screen
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.
- **Options**. Opens the Options window where settings such as default parameters or permissions can be edited.
- **Help**. Opens this help screen.

## Related Information

- Users Configuration
- Front End User Registration
- Write Private Message

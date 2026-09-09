<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/users/members
source-id: 3355
modified: 2014-11-06
imported: 2026-09-09
-->
# Members

## Members Breakdown

A breakdown of all the tabs on the /administrator interface inside of User: Members.

- Members: Manage all member accounts
- Notes: Manage user notes and note categories
- Access: Manage access levels
- Points: Manage user points
- Passwords: Set and manage password rules
- Quotas: Manage user quotas for tool usage
- Registration: Manage registration process
- Import: Import a CSV file of new users
- Export: Download CSV of all users
- Plugins: Manage all member related plugins

New features added to the sub toolbar on the /administrator interface inside of User: Members.

- De-identification of Members

## Members: Editing an Account

> **Note:** Editing of users is done through the HUBzero Members Manager component, **not** the User manager. Any changes made via the Members Manager will also be reflected in the User tables. Making changes with the User Manager can lead to data becoming out of sync.

1. First login to /administrator
2. Once logged in, hover over **Users** and select **Members** from the drop-down
3. You should now be presented with a list of all the members on your site
   1. There are a variety of methods to find the specific person you wish to edit: Searching or Manually scrolling through the list to find the use
4. Once found, click the person's name to open up their profile
   1. Account: General details about the users account and assigned access groups
   2. Profile: Profile details
   3. Password: Where an administrator can manually reset a user's password and check on how many days are left before current password expires
      1. > **Warning:** If you have an automatic password filler turned on in your browser, check this section if you are editing a user's account before saving for you might reset their password with one of your saved passwords
   4. Groups: List of groups that the user is involved in either as a member or manager
   5. Hosts: List of hosts that the user has access
   6. Messaging: The user's messaging settings
5. Once you feel ready to save your changes, scroll back to the top of the page and click **Save** (the icon that looks like a floppy disk) in the upper right portion of the page

## Members: Manually Confirming a User

Users by default have the option to confirm their accounts unless an administrator changes this to administrator approval only. An administrator will need to follow these steps to confirm any new users:

1. Navigate to **/administrator** and hover over the **Users** tab
2. Click **Members** from the drop-down
3. Search for the user you wish to confirm
4. Check the box beside their account and click the **Confirm** button
5. The user's account will be confirmed

## Members: Clearing Existing Terms of Use Agreements

When terms of use are changed on the hub, it may be necessary to require users to re-accept the new terms. To reset all existing agreements, and force re-acceptance, follow these steps:

1. Go to /administrator interface
2. Navigate to the Users tab and select Members from the drop-down
3. Click the **Reset terms of use agreements for all users** button
4. Navigate to the frontend of the Hub and login to double check that the Terms & Conditions acceptance pop-up appears

## Members: Blocking a User's Account

Sometimes spammers can attack your Hub and create multiple accounts. In order to prevent the spammers from access these accounts again, you can block their accounts. This also is a way to
"unpublish" accounts of users who don't wish to have accounts on the Hub anymore versus deleting their account. This way, if they ever need access again, you only have to unblock them. Follow these steps to block a user's account:

1. Navigate to **/administrator** and hover over the **Users** tab
2. Click **Members** from the drop-down
3. Search for the user you wish to confirm
4. Check the box beside their account and click the **Block** button
5. The user's account will be blocked

## User Manager: Customize Profile

Allows an administrator to define profile/registration fields. To edit the profile schema, navigate to the Members Manager (**Users → Members**). Then click the button in the toolbar labeled **Profile**. In this screen, you have the ability to define user profile fields, if said fields are required or optional, what values are allowable, and what fields are dependent upon a value from another field being chosen.

- **Text**  
  A single-line textbox.
- **Paragraph**  
  A multi-line textbox (a.k.a. textarea).
- **Checkboxes**  
  A list of options where the user may select one or more.
- **Multiple Choice**  
  A list of options where the user can only choose one of the available options.
- **Date/Time**  
  A textbox specifically for entering timestamps.
- **Dropdown**  
  A dropdown (select box) of options where the user can only choose one of the available options.
- **Country**  
  A dropdown (select box) of countries. *The list of countries is automatically populated.*
- **Website**  
  A textbox specifically for entering URLs.
- **Hidden**  
  A hidden input.
- **ORCID**  
  A textbox for entering entering ORCIDs.
- 

**Toolbar Breakdown:**

- **Save**. Saves the user and stays in the current screen.
- **Save & Close**. Saves the user and closes the current screen.
- **Save & New**. Saves the user and keeps the editing screen open and ready to create another user.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.
- **Help**. Opens this help screen

**How to Build with the Profile Manager:**

1. Navigate to **/administrator** and hover over the **Users** tab and click on **Members** from the drop-down
2. Click the **Profile** button
3. Manage the current Profile breakdown with the following actions:
   1. **Add a new field**:
      1. **Select the field type** then fill in the label information while under the **Edit field** section
      2. Determine the Access level and the information will automatically save
   2. **Duplicate a field**:
      1. Hover over the field you want to duplicate and then click the **Duplicate field** button
   3. **Remove a field**:
      1. Hover over the field you want to remove and then click the **Remove field** button
   4. **Move fields**:
      1. Select the field you want to move and drag it up or down depending on your preference
4. Click **Save & Close** to save your changes

## Notes

Notes are a function where users can create and store notes on the Hub. On the /administrator interface, an administrator can manage the notes created by users.

- User Notes: Where an administrator can view notes make by other users and edit, approve, publish, unpublish, along with other actions to curate the notes
- Note Categories: Where an administrator can create new note categories and batch process selected categories

## Access

An area where access groups can be managed. Access groups are the various permission levels assigned to different groups of users based on their roles in the Hub. Generic default levels are: Public, Manager, Administrator, Registered, Author, Editor, and Publisher. These levels are broken up by the levels of responsibility and what the access the group has to different areas of the Hub. An administrator can see the number of users in the group in the **Users in Group** column.

An administrator can add new access groups by clicking the New button and following these steps:

1. Navigate to **/administrator** and hover over the **Users** tab and click on **Members** from the drop-down
2. Inside of **Members**, select the **Access** tab and click the **New** button
3. Add a **Group Title** and then select the **Group Parent**
4. Click **Save & Close**

An administrator can also can manage the viewing levels available on the Hub by following these steps:

1. Navigate to **/administrator** and hover over the **Users** tab and click on **Members** from the drop-down
2. Inside of **Members**, select the **Viewing Level** tab and click the **New** button
3. Add a **Level Title** and select the **Access Groups** that have this viewing access
4. Click **Save & Close**

## Points

Points are the Hub’s currency system. Hub administrators can provide royalties to active and helpful users who interact with a Hub’s community.

- Summary: Check out top users and statistics of how points are earned
- Look up User Balance: Look up a users Point total by searching with their user I.D.
- Configuration: Configure the amount of points you want to reward users when completing specific tasks which are assigned in the Alias. **NOTE:** There are only seven point alias' you can use, see Point Configuration listed below.
  - **Description**: Add a short description describing why these points were being rewarded
  - **Alias**: The alias of the rule affliated to the awarded points
  - **Points**: Define the amount of points
- Batch Transaction: Deposit or Withdraw points from user accounts through batch transactions

---

**Configure Points**

1. To set up Points on your Hub, follow these steps:
2. Log into the **/administrator** interface
3. Hover over the **Users** tab and select **Members**
4. Select the **Points** tab and then the **Configuration** tab
5. Fill out the form to add points in seven specific areas of the Hub:
   1. Question posted: Award points when a user posts a question
      1. Points: Determine the amount of points you want to award a user when they complete this task
      2. Alias: *ask*
      3. Description: Question posted
   2. Answer posted: Award points when a user posts an answer
      1. Points: Determine the amount of points you want to award a user when they complete this task
      2. Alias: *answer*
      3. Description: Answer posted
   3. Question rating posted: Award points when a user posts a rating to a question
      1. Points: Determine the amount of points you want to award a user when they complete this task
      2. Alias: *questionvote*
      3. Description: Question rating posted
   4. Answer rating posted: Award points when a user posts a rating to an answer
      1. Points: Determine the amount of points you want to award a user when they complete this task
      2. Alias: *answervote*
      3. Description: Answer rating posted
   5. Answer accepted as best: Award points when a user who asked a question in Answers accepts another user’s best answer (the user who submitted the answer earns the points)
      1. Points: Determine the amount of points you want to award a user when they complete this task
      2. Alias: *accepted*
      3. Description: Answer accepted as best
   6. Abuse report granted: Award points when a user reports abuse and an administrator grants the abuse report
      1. Points: Determine the amount of points you want to award a user when they complete this task
      2. Alias: *abusereport*
      3. Description: Abuse report granted
   7. Review rating posted: Award points when a user rates a post
      1. Points: Determine the amount of points you want to award a user when they complete this task
      2. Alias: *reviewvote*
      3. Description: Review rating posted
6. After filling out the form, click **Save & Close** to save the new point configuration

![points_config](../../media/members-points-config.png)

## Passwords

Password Rules and Password Blacklist are two sections that are already set up at default. The Password Rules comply with our latest security scan and are suggested to be turned enabled at all times so that all users meet the same rules when creating their accounts. As an administrator, you can blacklist passwords if you do not want users creating simple or Hub related passwords.

Blacklisting a Password:

1. Navigate to **/administrator** and hover over the **Users** tab and click on **Members** from the drop-down
2. Inside of **Members**, select the **Passwords** tab, then select **Password Blacklist**
3. Click the **New** button
4. Add the password in the **Word** text box
5. Click **Save & Close**

## Quotas

Managing user quotas is simplified on the backend into three main areas. As an administrator, you can increase or decrease a user's quota, create quota classes, and import quotas, by following these steps:

Increasing/Decreasing a User's Quota:

1. Navigate to **/administrator** and hover over the **Users** tab and click on **Members** from the drop-down
2. Inside of **Members**, select the **Quotas** tab and select the **Member disk quotas** tab
3. Search for the user and then click on their name
4. Increase or decrease the user's quota by changing their quota class or by changing their limits
5. Click **Save & Close**

Creating a Quota Class:

1. Navigate to **/administrator** and hover over the **Users** tab and click on **Members** from the drop-down
2. Inside of **Members**, select the **Quotas** tab and select the **Quota classes** tab
3. Click the **New** button
4. Fill in the **Class Options** form
   1. Alias: Class alias
   2. Soft blocks limit: Soft limit for quota blocks
   3. Hard blocks limit: Hard limit for quota blocks
   4. Soft files limit: Soft limit for quota files
   5. Hard files limit: Hard limit for quota files
   6. User Access Groups: Select what access groups are automatically associated with this quota class
5. Click **Save & Close**

Importing Quotas:

1. Navigate to **/administrator** and hover over the **Users** tab and click on **Members** from the drop-down
2. Inside of **Members**, select the **Quotas** tab and select the **Import Quotas** tab
3. Import existing filesystem quotas by copy and pasting a **quota.conf** file in the text box
4. Check the box next to **Overwrite matching existing entries**
5. Click **Import**

## Registration

An administrator can configure a user's registration fields from the /administrator interface on the Hub. Read more about this on [Member Registration article](../02-registration.md).

## Import

An administrator can manually import users if they are wanting to complete this process for a large research group needing access to the Hub immediately or for a classroom of students where the instructor already knows the students' information. To import users and set up import hooks, follow these steps on the [Member Import Article](../03-memberimport.md).

## Export

Exporting the current list of Hub users can be done by visiting the **Export** tab inside of **Members**. From that tab there will be a button that states **Download CSV of all users**. By clicking this button, you can download a list of all members and archive the file for your own use as an administrator or for safe keeping.

## Plugins

Plugins are used to turn on various parts of the Members interface which includes their profile and dashboard. An administrator can manage these plugins by unpublishing and publishing various plugins to limit or expand a users Member area on the frontend of the Hub. Specifically in the Member Dashboard Configuration plugin, an administrator can manually change rearrange the member dashboard modules to fit the message of the Hub.

Publishing/Unpublishing Plugins:

1. Navigate to **/administrator** and hover over the **Users** tab and click on **Members** from the drop-down
2. Inside of **Members**, select the **Plugins**
3. Check the box next to the plugin and click the **Publish** or **Unpublish** button
4. The plugin will change stats under the **Published** column

Managing Member Dashboard Configuration:

1. Navigate to **/administrator** and hover over the **Users** tab and click on **Members** from the drop-down
2. Inside of **Members**, select the **Plugins** tab
3. Locate the **Members-Dashboard** plugin and click **Manage** under the Manage column
4. Manage the modules inside of the **Members-Dashboard** plugin
   1. Drag and drop to move modules
   2. Drag the lower-right corner to expand or contract the size of the module
   3. Add more modules by clicking the **Add Modules** button and selecting a module
   4. **Push Modules to Users** by clicking this button and filling out the form, then clicking **Push Module**
5. All changes will be automatically saved for every new user
   1. Users who have already adjusted their Dashboards will not be affected by these changes

## Toolbar Features

## De-identification of Members

Release: 2.2.26

Purpose: Mechanism to remove Personally Identifiable Information (PII) from the CMS databases. Some database rows are simply deleted, and in other cases fields are deleted or replaced with anonymized values that are unrelated to the original PII (e.g., username), in which case no record or mapping is kept of the original value. Accounts will not be entirely deleted, but some data will be kept for statistics.

1. Navigate to the top toolbar in **/administrator** and hover over the **Users** tab and click on **Members** from the drop-down
2. Make sure under the **Options** modal window, under the **Permissions** tab, the "De-identify" option is set to 'allowed'. Save and Close the Options window.
3. Inside of **Members**, on the **Members** table, in the 1st column, select different members to de-identify
4. Navigate to the sub toolbar on the top right and click the 'eye-closed' icon button (next to the trash icon button), which will de-identify those specific members
5. Changes in the database will be reflected in the displayed **Members** information
6. The previously selected members will have had their information deleted from the following tables:
   - User Profiles
   - Support Tickets
   - Session and Session Geo
   - Profile Completion Awards
   - All related to Newsletter Mailing
   - Messages
   - All related to Media Tracking
   - All related to Jobs
   - Feedback
   - Event Registration
   - Blog Entries and Comments
   - All related to Cart
   - All related to Authentication
   - All related to Groups and Profiles
   - Users Quotas Log
   - All related to Attachments
   - Users Password and Password History
   - Points Subscription
7. Also, the previously selected members will have had their home directory deleted from our servers
8. Lastly, the previously selected members have had account information updated with generic info.
9. A "success" alert message should be displayed for the previously selected members.

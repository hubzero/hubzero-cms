<!--
status: imported
source: https://help.hubzero.org/documentation/240/users/groups/groupcustom
source-id: 3306
modified: 2011-11-04
imported: 2026-09-09
-->
# Customization

## Customizing a Group's Look

![groupmanagersettings](../media/groupcustom-groupmanagersettings.png)

![addingalogo](../media/groupcustom-addingalogo.png)

1. On your main group, click on **Group Manager** and click on **Edit Group Settings**
2. Use the box on the right side of the page to upload a photo. Choose that photo from the drop-down box in the **Group Logo** section
3. Specify access to individual tabs within the group in the **Group Access** section by selecting the desired option to the right of item in the list
4. Click **Save Group** to save your changes
5. To customize all the group pages at once, click on **Manage Group Pages** in the **Group** **Custom Content** section. This will take you different interface away from the group customization area, please save all other changes before making this customization

## Customizing a Group's Calendar

Users can subscribe to a group calendar two separate ways:

1. **[Download:](https://qa210.aws.hubzero.org/help/groups/calendar/subscriptions#download)** They can download a group calendar of events and import that into their calendar off the HUB.
2. **[Subscribe:](https://qa210.aws.hubzero.org/help/groups/calendar/subscriptions#subscribe)** They use a calendar application such as iCal or Outlook to subscribe to the group calendar.

<a id="download"></a>

### Downloading the Group Calendar

To download a group calendar, navigate to a group's calendar tab. If you scroll down, under the calendar of events you should see a box titled "Subscribe", which looks like the image below

![Subscribe](https://qa210.aws.hubzero.org/core/plugins/groups/calendar/help/en-GB/subscribe.png)

Here you can pick which of the group calendar's you want to download. After you have made your choices simply click the *Download* button. A iCalendar file (.ics) will be downloaded by your browser, which you can then import into any calendar application with iCalendar support.

[Here is a list of applications with support for iCalendar files (.ics) →](http://en.wikipedia.org/wiki/List_of_applications_with_iCalendar_support)

<a id="subscribe"></a>

### Subscribing to the Group Calendar

Subscribing to a group calendar has 1 major benefit over downloading a group calendar; changes made to the group events on the HUB are reflected in the subscribers calendar.

To subscribe to group calendar follow the same steps described above to download a calendar but instead click the *Subscribe* button. This will open the default calendar application on your calendar with a dialog box asking if you would like to subscribe to this calendar. If the groups calendar access setting is restricted to Registered HUB Users or Group Members, you will also be prompted for you HUB login and password.

<a id="faqs"></a>

### Subscription FAQs

- [Can I subscribe with Google Calendar?](https://qa210.aws.hubzero.org/help/groups/calendar/subscriptions#faqs-google-calendar)

<a id="faqs-google-calendar"></a>

\1. Can I subscribe with Google Calendar?

Currently Google Calendar doesn't support private or authenticated calendar subscriptions, which is good for the security of your data, but bad if you like having all you calendars in one place. Only if a group's calendar access setting is set to *Any HUB Visitor* will users be able to subscribe with their Google Calendar. This setting can be changed at anytime by any group manager, in the group customize interface.

An alternative for Google Calendar users is that they download and import a group calendar following the steps described above.

## Customizing Group Pages

**Managing Group Pages:**

1. Log in to the frontend of the Hub and access a Group that allows users access to managing pages in the Group
2. Hover over the down-arrow, next to the **Group Manager** button
3. Select from the drop-down the **Manage Group Pages** button
4. From there you can manage all of the pages inside of the Group

**Creating a Group Page:**

1. Log in to the frontend of the Hub and access a Group that allows users access to managing pages in the Group
2. Hover over the down-arrow next to the Group Manager button
3. Select from the drop-down the Manage Group Pages button
4. On the Manage Pages tab, click on the New Page button
5. Fill in the title into the title field and alias into the alias field for the new page
   1. Note: Page alias' can only contain alphanumeric characters and underscores. Spaces will be removed.
6. Then, fill in the content of the new page in the content text box
7. Select the publishing settings status from the drop-down:
   1. Published- the page is available on the Hub’s frontend
   2. Unpublished- the page is unavailable to the Hub’s frontend but can be accessed still by the creator and on the backend of the Hub
8. Select the privacy settings from the drop-down:
   1. Inherits overview tab’s privacy setting- The previous setting of the overview tab also is enabled for this page
   2. Private Page- Accessible to only members of the Hub
9. Underneath the Settings, select a category from the Category drop-down, and the settings you want for Comments from the drop down
10. Click Save Page to save the new page and the newly added content

**Embedding PHP or Javascript Code in a Group Page (advanced feature):**

It is possible to insert PHP or Javascript Code into a Group page to allow more flexibility when constructing a page. This is a feature meant for developers to provide advanced capabilities the page. Due to security considerations, a page approver must be assigned on the administrator Group interface under "Options". This is a textbox that is expecting a comma separated list of usernames who will be messaged when a page containing PHP or Javascript code is submitted. Until the page is approved, the previous version will be displayed or a message indicating the page needs to be approved, if there exists no previous version.

**Editing a Group Page:**

1. Log in to the frontend of the Hub and access a Group that allows users access to managing pages in the Group
2. Hover over the group management button
3. Select from the drop-down the **Manage Group Pages** button
4. Inside of Manage Group Pages, select the **Manage Pages** tab and then locate the page that needs editing
5. Click on the arrow next to the page’s **Manage Page** button
6. From the drop-down select **Edit Page** to begin editing the page’s content
7. Edit the content inside of the group page and then click **Save Page** to save the newly edited content

**Deleting a Group Page:**

1. Log in to the frontend of the Hub and access a Group that allows users access to managing pages in the Group
2. Hover over the down-arrow next to the **Group Manager** button
3. Select from the drop-down the **Manage Group Pages** button
4. Inside of Manage Group Pages, select the **Manage Pages** tab and then locate the page that needs deleting
5. Click on the arrow next to the page’s **Manage Page** button
6. From the drop-down select **Delete Page** and the page will automatically be removed from the group and the Hub

**Creating a New Page Category:**

1. Log in to the frontend of the Hub and access a Group that allows users access to managing pages in the Group
2. Hover over the down-arrow next to the **Group Manager** button
3. Select from the drop-down the **Manage Group Pages** button
4. Navigate to the **Manage Page Categories** tab
5. Click on the **New Page Category** button and then fill in the title and select the color for the new category
6. Click **Save Category** to create a new category

**Editing a Page Category:**

1. Log in to the frontend of the Hub and access a Group that allows users access to managing pages in the Group
2. Hover over the down-arrow, next to the **Group Manager** button
3. Select from the drop-down the **Manage Group Pages** button
4. Navigate to the **Manage Page Categories** tab
5. Locate the category that needs editing and by the **Manage Page Category** button click the arrow
6. From the drop-down select **Edit Page Category** and then edit the content inside of the page category
7. Click **Save Category** once finished to save the newly edited content

**Deleting a Page Category:**

1. Log in to the frontend of the Hub and access a Group that allows users access to managing pages in the Group
2. Hover over the down-arrow, next to the **Group Manager** button
3. Select from the drop-down the **Manage Group Pages** button
4. Navigate to the **Manage Page Categories** tab
5. Locate the category that needs to be deleted and by the **Manage Page Category** button click the arrow
6. From the drop-down select **Delete Page Category** and the page category will be automatically removed from the group and from the Hub

## Group Forum: Digest Emails

Group members have the ability to receive emails from the group forum. The group member can be emailed every time another member creates a new post in a forum. Each group member can manage their own settings so they receive new posts in the group.

1. Navigate to the main Group page then click on the **Forum** tab
2. Inside of the Group Forum, locate *Email Settings* and click on **Change your settings**
3. In the pop-up determine the preferred email setting by clicking the check box next to *Email me about new posts in this group*
4. Then, select the radio button next to the preferred emailing time frame of receiving group forum emails **Individually as new posts are made** or **As part of a (Daily, Weekly, Monthly) digest email**
5. Click **Save** to save the email setting changes

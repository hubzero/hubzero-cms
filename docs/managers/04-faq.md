<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/fqas
source-id: 3345
modified: 2014-11-07
imported: 2026-09-09
-->
# Frequently Asked Questions

## Article Pages: How do you set parameters for articles and other content items?

Many Article parameters, such as **Show Title**, **Show Author**, and so on, can be set form the backend of the Hub. This can be done by logging in to the backend, navigating to the **Article Manager: Articles** page **(Content-> Article Manager)**, and then selecting the article you would like to adjust the parameter for. By clicking the box beside the title of the article, and then clicking the **Edit** button to open up the article for editing.

Typically, parameters at the individual article and menu item levels can be set to a specific value or to a value of **Use Global**. If the individual article's parameter is set, then that value controls the setting. If this is set to **Use Global** then the menu item parameter is checked.

## Courses: How can I view the outline a student would see in a course?

If you wish to view and check any prerequisites that have been set as an instructor you have to enroll in the course as a student. Currently the instructor’s view of the course outline and the outline students see are not the same. An instructor or a manager of a course will be able to view all of the content and see that it is published or unpublished. For students, the outline they view depends on the prerequisites for the parts of the course and what is available to them during the course.

The best way for an administrator of a course to view what a student can see, would be to create a testing account and enroll as a student in the course.

## Groups: How do you send a group message?

Group messaging allows you to send out a message as an email to all the group members. A Group Manager is the only one with the permissions to send out messages from the group to all group members. To send out group messages follow these steps:

1. Navigate to **https://yourhub.org/groups** and log into the Hub
2. Navigate to the group page and click on the **Members** tab
3. Click on the email icon to send a message to all of the group members
   1. To email only a specific group member, select the same icon next to the individual’s name.
4. Compose the message by:
   1. Selecting whom the message is for from the drop-down.
   2. Fill in the subject title.
   3. Write up the message.
5. Click the **Send** button to send the message to the group

## Projects: How do you connect Google Drive in a Project?

> **Note:** Managers and Collaborators of a Project can connect their Google Drives, but the creator of the Project must connect their Google Drive account prior to others adding on their accounts to the project.

1. Navigate to **https://yourhub.org/projects** and log into the Hub
2. Access the project that you wish to add your Google Drive to
3. Inside of the project access the **Files** tab and locate the **Connect** button
4. Click the **Connect** button and then click the second **Connect** button
5. Then choose the account or login to your Google account
6. You will be relocated to a new page where you will accept the project files to Google Drive for various access levels. Click **Accept** to confirm the last step
7. Then you will be relocated back to the project confirming your Google Drive connection to the project

## Support: How do I add other attachment file extensions for tickets?

1. Navigate to **https://yourhub.org/administrator** and login to the as an administrative user
2. Click on the **Components** tab and then click on **Support**
3. On the **Support: Tickets** page, click on the **Options** button
4. Inside of **Support Configuration** click on the **Files** tab
5. Under **Extensions**, add the new file extension that can be uploaded to a ticket
6. Click **Save & Close** to save the changes and to exit out of the **Support Configuration** pop-up

## Wishlist: How do I change the status of a wish?

The status of a wish serves to indicate to the general site users whether the wish was accepted/rejected for implementation and when to expect the proposed feature online. Once a wish is submitted, an administrator of the Hub can change the status of the wish. To change the status simply open up the wish and then click **Change status** which is located under the wish. From the drop-down box select the new status of the wish:

- **Pending**: The wish is pending a response.
- **Accepted**: The wish had been accepted and is currently being worked on.
- **Rejected**: The wish has been rejected due to other contributing factors that disallow the ability to create the wish. Example: Practicality or User use case
- **Granted**: The wish has come true and is now available to the Hub.

Once the status has been selected, click **Change Status** to save the new status and inform the user who submitted the wish what stage their wish is in right now.

## How do I redirect a URL to point to another existing Hub component?

**Scenario:** A professor has a group called "mainclass" and wants sutdents to use another group called "spring2016class"

1. Navigate to **/administrator** and hover over the **Menu** tab and select **Menu Manager** from the drop-down
2. Locate the **Default** menu and click on the title to enter the menu interface
3. Locate the **Parent** group, in this case, locate the **Groups** menu
   1. If you can not find the parent "Groups", it might have not been created. Follow these additional steps to create the new group:
      1. Click the **New** button
      2. Click **Select** and from the list select **External URL** from the **System Links** section
      3. Fill in the form:
         1. **Menu Title**: Groups
         2. **Link**: groups
      4. Click Save & Close to save the new menu item
4. Create the redirect portion, repeat steps 1-9 *except* fill out the **Menu Title** as **Mainclass** and the link as **spring2016class**
   1. This will redirect users from **/groups/mainclass** to **/groups/spring2016class**
5. Nest the Mainclass under Groups to create the redirect
   1. This is done in the **Menu Item Details** under **Parent**
   2. Set the Parent for Mainclass as **Groups**
   3. Click **Save & Close**

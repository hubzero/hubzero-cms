<!--
status: imported
source: https://help.hubzero.org/documentation/240/users/projects/projectfiles
source-id: 3315
modified: 2013-07-10
imported: 2026-09-09
-->
# Project Files

## File Quota Usage

Every Project that is created is given a specific amount of GB for files and databases. To check on the usage of quotas follow these steps:

1. Navigate to your Project and click on Files
2. At the bottom of the Files page, click on the bar labeled Disk Usage
3. You will be taken to a page that breaks down the amount of space you have left and the space that has been used by uploaded files

Now, if you are apart of a larger project and would like to request a quota increase for your project, then follow these steps:

1. Navigate to the frontend of the Hub and in the URL address bar of your browser type in (http://yourhub.org/support/tickets)
2. Inside the Support system, click on New Ticket located in the upper right of the screen
3. In the ticket fill out this information:
   1. Name:
   2. Project Name:
   3. Grant Title:
   4. Grant PI:
   5. Grant Agency:
   6. Grant Budget:
   7. Short Explanation about your Project:
4. Once you have filled out this information, click Submit, then a Support Team member will be in touch

---

## Project File Connectors

Hubzero allows for files to be shared either on a repository owned by the Hub or connect to other file management platforms. Project File Connectors allow for these third party file management systems to enable files uploaded on the Hub yet stored in their system. Follow these instructions to set-up and provide Project File Connectors to your users:

1. Navigate to the third party file management system of your choosing, such as **Google Drive** or **Dropbox**
2. Set-up either a new project or application and fill in information about the Hub in order to obtain an **API key** and **API secret** upon saving
3. Make sure you set the callback address to your Hub and indicate the appropriate file system
   1. **Example:** When setting up a Google Drive API connector to redirect to YourHub, the following callback should be used - *https://yourhub.org/developer/callback/googledriveAuthorize*
4. Once the **API key** and **API secret** have been created successfully, navigate back to the **/administrator** interface and **login**
5. Hover over **Extensions** and select from the dropdown **Plug-in Manager**
6. Search or locate the appropriate plugin connected to the file system
   1. **Example:** If the file system chosen is Google Drive, one would select the **Filesystem - Google Drive** plugin
7. Click on the title of the plugin and change the status to **Enabled**
8. Insert the **API Key** in the **Client ID** and the **API Secret** in the **Client Secret**
9. Fill in an further information as necessary then click **Save & Close**
10. While still at the **Plug-in Manager**, search for and click the **Projects - Files** plugin.
11. Scroll down until you see an option for **Default Action**, and set it to **Connections (view available connections)**.
12. Navigate to a **Project** you would like to setup a new file system
    1. **Note:** You must be a manager of the Project in order for you to set-up the File System
13. Click on **Files** and then click on **New Connection** and choose the file system from the dropdown
14. Choose to bypass the secondary step of connecting to the same API

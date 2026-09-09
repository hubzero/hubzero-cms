<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/components/projects/projectfileconnect
source-id: 3386
modified: 2017-11-03
imported: 2026-09-09
-->
# Project File Connectors

## Project File Connectors

HUBzero allows for files to be shared either on a repository owned by the Hub or connect to other file management platforms. Project File Connectors allow for these third-party file management systems to enable files uploaded on the Hub yet stored in their system. Follow these instructions to set-up and provide Project File Connectors to your users:

1. Navigate to the third party file management system of your choosing, such as **Google Drive** or **Dropbox**
2. Set-up either a new project or application and fill in information about the Hub in order to obtain an **API key** and **API secret** upon saving
3. Make sure you set the callback address to your Hub and indicate the appropriate file system
   1. **Example:** When setting up a Google Drive API connector to redirect to YourHub, the following callback should be used - *https://yourhub.org/*developer/callback/googledriveAuthorize
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
14. Choose to bypass the secondary step of connecting to the same API.....

## Setting up an External Application (Dropbox, Google Drive, AWS S3)

For using external services via OAuth2 the Hub needs a client identifier and secret for use with that service. The methods of obtaining these values vary from service to service and are subject to change at the provider's discretion. Below are the instructions for setting up a developer application on some services (adapting the instructions with some searching should yield similar results for other services or services that have changed their process in the future).

### [Creating an application on Dropbox]

1. Navigate to https://www.dropbox.com/developers
2. Sign in using the Dropbox credentials that you would like your Hub (the server itself) to authenticate against, this is usually an account owned by the group or organization.
3. On the left menu, click "My Apps".
4. On the top right, click "Create App".
5. Select the access level you would like to grant your Hub to your users Dropbox accounts.
6. Give the application a name (this name will be displayed to users when they authorize access to their Dropbox account).
7. Read and agree (or disagree) to Dropbox's terms.
8. Click "Enable additional users".
9. In the redirect URIs section, add the URI of "https://&amp;lt;yourhub.org>/developer/callback/dropboxAuthorize".
10. At this point you should save the "App key" and show and save your "App secret", you will need to input these on cdmHUB in a later step.
11. You can now click on the branding tab and brand the application to your Hub's needs (adding icons, links to the website, etc.). These are all used when a user is asked to enter their Dropbox credentials on behalf of your Hub.

**Dropbox currently limits development accounts to 500 users per id/key, this can be raised by contacting Dropbox support and applying for a production key once that limit has been reached.**

**[Creating an application on Dropbox]**

1. Log in to your Google account and go to the APIs & services
2. Navigate to "Credentials" using the left-hand menu
3. On the "Credentials" page, click "Create credentials" and choose "OAuth client ID"
4. Create "New Credentials"
5. On the "Create client id" page, select "Web application". In the new fields that display, set the following parameters:
   1. Field: Description
   2. Name: The name of your web app
   3. Authorized JavaScript origins:
      1. https://yourhub.org
   4. Authorized redirect URIs:
      1. https://yourhub.org/projects/auth
      2. https://yourhub.org/developer/callback/googledriveAuthorize
   5. Web App Credentials: Configuration
6. Click "Create" to proceed
   1. Your "Client Id" and "Client Secret" will be displayed
7. Save your Client Id and Client Secret to enter into the Connection settings into "Filesystems - Google Drive" plugin
   1. Additional documentation can be found here:
      1. https://cloud.google.com/docs/authentication/api-keys

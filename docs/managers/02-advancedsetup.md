<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/advancedsetup
source-id: 3338
modified: 2015-09-21
imported: 2026-09-09
-->
# Getting Started: Advanced Setup

## Your Hub's Getting Started Page

Your hub should have a getting started page at &amp;lt;your hub url>/gettingstarted. Most of the items addressed on this page are discussed in further detail below.

## Editing "About You", "How to Contact", & "Terms of Use" Pages

1. First login to the administrative interface of your hub
2. Once logged in, find **Content** in the main menu bar located toward the top of the page
3. Click on the **Article Manager** link
4. You should now be presented with a list of all the content articles. There are a few methods to find the specific entry you want to edit. You can filter by state, category, author, etc. or search for the title of the article page. A third option would be to look through the list manually, if you really want
5. Please find the **About Us** page as we will use that as an example. Once found, click the article title to edit it
6. You now should be presented with a page where you can edit the text, title and other page parameters similar to the image below
7. When your edits are complete, click the **Save & Close** toolbar button to save your changes and return you to the Article Manager screen
8. Alternatively, you may click the **Save** toolbar button to save your article but leave it open for editing

## Facebook Authentication

## Step 1: Setting Up Facebook

1. Navigate to (https://developers.facebook.com)
2. Begin by logging into a Facebook account or creating a new Facebook account for your hub
3. After successfully logging into Facebook, click the **My Apps** tab, then in the drop-down select **Register as a developer**
4. Read through the Facebook policies and accept the policies by changing the answer from **No** to **Yes** with the switch button, and then click **Register**
5. Select the **My Apps** tab again and from the drop-down click **Add a New App**
6. Select the platform of the new app by clicking the **WWW** or "Website" icon
7. In the upper right corner of the page, select the **Skip and Create App ID** button
8. Fill in the **Display Name** in the name field and select the category of the application depending on your field
9. Fill in the rest of the **New App ID** information or leave it blank depending on your preference, then click **Create App ID**
10. On the application dashboard, copy the App ID and App Secret. In order to copy the App Secret you will have to click **Show** in order to access the full secret code

## Step 2: Enabling Facebook

1. Navigate to your hub, and access the /administrator interface
2. Click the **Extensions** tab and then from the drop-down select **Plug-in Manager**
3. Locate the **Authentication-Facebook** plug-in and open the plug-in by clicking the plug-in name
4. Inside the plug-in, change the status of the plug-in from **Disabled** to **Enabled**
5. Insert the App ID and Consumer Secret into the corresponding fields and click **Save & Close**
6. The Facebook Authentication will be available for users to use in order to login into the hub

## LinkedIn Authentication

## Step 1: Setting Up LinkedIn

1. Navigate to (https://www.linkedin.com/secure/developer)
2. Begin by logging into a LinkedIn account or creating a new LinkedIn account for your hub
3. After successfully logging into LinkedIn, under the list of applications click **Add New Application**
4. Fill out the form to register a new application
   - Under *Default Application Permissions*, check "**r_basicprofile**" and "**r_emailaddress**".
   - Under *OAuth 2.0*, add your hub's URL (e.g., https://yourhub.org) as an **Authorized Redirect URLs**.
5. Click **Add Application** once you have finished filling out all the required fields in the application form
6. Once you receive a message stating, **Your application was successfully created**, copy the API key and the Secret key

## Step 2: Enabling LinkedIn

1. Navigate to the /administrative interface of your hub
2. Under the **Extensions** tab select from the drop-down **Plug-in Manager**
3. Inside the Plug-in Manager, locate the **Authentication-LinkedIn** plug-in
4. Select the title of the plug-in, then inside the plug-in fill in the API key and the Secret key
5. Enable the plug-in by selecting from the Status drop-down, then click **Save & Close** to save the changes that were made
6. The LinkedIn plug-in will be successfully enabled and available for users to use as another option to log in to your hub

## Google Authentication

## Step 1: Enable the Google+ API

1. Navigate to the Google Developers Console (https://console.developers.google.com/) and log into a Google Account or create a new Google account for your hub
2. Select **Credentials** and click **Create Credentials** and then click **OAuth Client ID**
3. Add the following URLs as redirects
   1. https://yourhub.org/index.php?option=com_users&task=user.login&authenticator=google
   2. https://yourhub.org/index.php?option=com_users&task=user.link&authenticator=google
4. Save and collect your **Client ID** and **Client secret**

## Step 2: Enable the Authentication-Google Plug-in

1. Once you have collected your **Client ID** and **Client secret**, log into the /administrator interface for your hub
2. Select the **Extensions** tab and then from the drop down select the **Plug-in Manager** button
3. Locate the **Authentication-Google** plug-in and select the plug-in's title
4. Inside the plug-in, locate the **Status** section and select **Enable** from the drop-down
5. Fill in the **Client ID** and **Client Secret** into the appropriate fields
6. Save all the changes by clicking **Save & Close**
7. Your users will be able to log into your instance with Google authentication

## ORCID Authentication

## Step 1: Set Up ORCID Authentication on ORCID

1. Navigate and sign-in to ORCID
2. Once signed-in, navigate to <https://orcid.org/developer-tools>
3. Complete the developer form:
   1. Name of application
   2. Website URL
   3. Description
   4. Redirect URLs
      1. **https://yourhub.org/index.php?option=com_users&task=user.link&authenticator=orcid**
      2. **https://yourhub.org/index.php?option=com_users&task=user.login&authenticator=orcid**
4. Click the “Save” icon on the right side
5. Copy Client ID and Client secret

## Step 2: Enabling ORCID

1. Navigate back to your hub and log into the /administrative interface
2. Click on the **Extensions** tab and the and select from the drop-down the **Module Manager**
3. On the **Plugin Manager** page and locate **ORCID** from the new module list
4. Fill in the **Client ID** and **Client Secret** into the appropriate fields
5. Save all the changes by clicking **Save & Close**

## CI Logon

## Step 1: Set Up CI Logon

1. Navigate to cilogon.org/oauth2/register and begin filling out the form:
   1. Client Name: Name of the Hub
   2. Contact Email: Main email to receive information; best if we use our mailman set-up for the support contact list (**support@hubname.org**)
   3. Callback URLs:
      1. **https://hubname.org/index.php?option=com_users&task=user.login&authenticator=cilogon**
      2. **https://hubname.org/users?authenticator=cilogon**
      3. **https://hubname.org/administrator/index.php?option=com_login&task=login&authenticator=cilogon**
      4. **https://hubname.org/index.php?option=com_users&task=user.link&authenticator=cilogon**
   4. Client type: Confidential
   5. Scopes: Select all in the list
   6. Refresh Token: No
2. Click “**Register Client**”
3. You will receive on the next page the client ID and client secret; save both

## Step 2: Enabling CI Logon

1. Navigate to hubname.org/administrator and sign in as an administrator
2. Once signed in, navigate to “**Extensions**” and then “**Plugin Manager**”
3. Search and click on the “**CI Logon Authentication**” plugin
4. Add in the client ID and client secret and set-up the plugin to use the site login and optionally also allow administrator login
5. Click “**Save & Close**”
6. CI Logon will email the contact once they have confirmed the request and you can now use CILogon to sign into the hub

## Google Analytics

## Step 1: Set Up Google Analytics on Google

1. Navigate to (google.com/analytics) and select **Access Google Analytics**
2. You will be required to login to a Google account.
   1. We suggest creating a Google Account for your hub
3. Click **Sign Up** and begin filling in the required fields in the analytics form
4. Select **Get tracking ID,** and then read and accept the terms of Google Anayltics by clicking **I accept**
5. You will receive a tracking ID. This ID will be used on the hub in order to finish the set up process

## Step 2: Enabling Google Analytics

1. Navigate back to your hub and log into the /administrative interface
2. Click on the **Extensions** tab and the and select from the drop-down the **Module Manager**
3. On the **Module Manager** page click the New button and locate **Google Analytics** from the new module list
4. Open the new module by clicking on it's title then fill in the tracking ID, title, and the position of the module to be in the footer (typically, this module should be in a position present on every page--for HUBzero-built templates, that tends to be position **footer**)
5. Click **Save & Close** and Google Analytics will be installed in your hub

## Google Drive

## Step 1: Configuring Google Drive in Projects on a hub

1. Navigate to console.developers.google.com and log in with your hub Gmail account
2. Click **Create Project** to create a new project for your hub
   1. Enter the Project Name. We suggest using your hub name
   2. Enter the Project ID. The Project ID is a globally unique identifier that cannot be renamed. Use the ID given, or specify a new one in the field
   3. Click **Create**
3. Once created, click the link to your project to open it
4. From the Dashboard, click “Enable an API” to select services for the project. From this list, find and turn on Drive API and Drive SDK services by clicking **Off** next to each to switch them to **On**. Be sure to accept Terms of Service if asked
5. Next, navigate to the left hand menu to find APIs & auth. Click **Consent Screen** to set up the branding information. Fill in all required fields
   1. Choose the hub email account from the Choose your email dropdown
   2. In Product name, type ***Project Files to Google Drive Connector***
   3. Click **Save**
6. Next, click **Credentials** under APIs & auth
7. Click **Create new Client ID**
8. On the Create Client ID screen, choose **Web Application** under Application Type
9. In the Authorize Javascript orgins field, type your hub URL (i.e. **https://yourhub.org**)
10. In the Authorized Redirect URIS field, type your hub URL and add /projects/auth (i.e. https://www.yourhub.org/projects/auth)
11. Click **Create Client ID**
12. Under Client ID for web application, note the Client ID and Client Secret, as these will be needed
13. Under Public API Access, click Create New Key
14. Choose **Browser key** from the Create a new key pop-up
15. In the HTTP Referrers field, type in your hub name followed by/\*
    - Example: yourhub.org/\*
16. Click Create. Note the API Key: After this step, you will navigate back to the hub to configure

## Step 2: Turning on Google Drive in Projects

1. Navigate the /administrator interface for your hub and log in
2. Navigate to the **Extensions** menu, and click **Plug-in Manager**
3. In the search, type **Projects**, and click **Search**
4. In the results, find the Projects – Files plugin. Click the title to edit
5. Scroll down until you see the Google Connection Enabled option
6. Choose Y**es** from the drop-down
7. Fill in the Google Client ID, Client Secret, and API Key fields with the information from your account
8. Choose when to auto-sync
9. If you would like to only make Google Drive available to certain projects, type the alias of the project into the Connected Projects
10. Click **Save & Close**

## CKEditor

CKEditor is a HTML text editor designed to simplify web content creation. It’s a WYSIWYG editor that brings common word processing features directly into the web pages being built. After a hub has been updated to R1.3.0 various settings need to be adjusted on the administrator backend of the hub to allow CKEditor for all users.

## Step 1: Global Configuration

1. Navigate to the /administrator interface for your hub, and log in
2. Select the **Site** tab from the top menu
3. Click on the **Global Configuration** from the drop-down to open up the global configuration settings
4. Under the **Site** tab navigate to **Site Settings** and then locate **Default Editor**
5. Select **Editor-CKEditor** from the drop-down
6. Click **Save & Close** to save the newly changed settings

­­

## Step 2: HTML Format Handler Plug-in

1. Navigate to the /administrator interface for your hub, and log in
2. Select the **Extensions** tab from the top menu
3. Click on the **Plug-in Manager** from the drop-down
4. Enter **Content – HTML Format Handler** into the search box and click **Search**
5. Click on the **Content – HTML Format Handler** title to open up the plug-in
6. In **Details** change the status to **Enabled** and then click **Save & Close**

## ReCAPTCHA

## Step 1: Set Up ReCAPTCHA on Google

1. Navigate to (https://www.google.com/recaptcha/intro/index.html) and select **Get reCAPTCHA**
2. You will be required to log in to a Google account, at this point consider creating a new account for your hub or use a previously created account
3. Fill in the **Label** field and **Domain** field with the appropriate information
4. Copy the **Site key** and **Secret key**

## Step 2: Enabling ReCAPTCHA

1. Navigate to the /administrator interface for your hub, and log in
2. Select the **Extensions** tab and from the drop-down select **Plug-in Manager**
3. Locate the **CAPTCHA-ReCAPTCHA** plug-in and click on the title
4. Change the status of the plug-in from **Disabled** to **Enabled**, then fill in the **Public key** and the **Secret key**
5. Save the changes by clicking **Save & Close**, then ReCAPTCHA will be activated on your hub

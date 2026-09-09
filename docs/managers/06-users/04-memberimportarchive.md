<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/users/memberimportarchive
source-id: 3359
modified: 2019-09-25
imported: 2026-09-09
-->
# Member Import (archive)

## Overview

The members bulk importer/updater is designed to allow a site administrator to bulk import or update member data. Due to its ability to mass upload/modify accounts, it is an administrator only feature.

**Warning!**

The improper use of this feature can have damaging consequences. Use with caution.

The member should only be used in special circumstances. Minimum requirements for the CSV (first name, last name, username, password, etc.) and any additional registration fields (member demographics) must be gathered by and administrator for every new account to be imported. In most cases it is more time effective and less error prone for a new members to enter this information in the registration form instead of passing the information through the administrator.

**Please note, if you are using Member Import on a HIPAA hub, please complete the extra steps on delivering passwords to members.**

## Registration Fields

The member importer follows the parameters set in the registration component. If a registration field is required during the initial registration form (organization, for example), the field must be included in the uploaded CSV file in addition to the required fields for the CSV. Not all registration fields are available via the member importer, those should be collected from the new member on the next login, if desired.

## CSV Preparation

1. Log into the administrator of the hub, i.e. https://hubname.org/adminsitrator
2. Navigate from the main menu to **Users** -> **Members**, from the page menu under the title **Members**, click on the **Im****port** tab (last one on the right).
3. Find the control button at the top, right of the page. Click on the left most button that looks like a grid to download the example CSV.
4. Save the file with a different filename that represents the group of new members you will be importing.
   1. Note: Do not save it in Excel format, the import only accepts CSV files.
5. Edit the CSV. Remove the word **Example** from all fields and enter the required fields and those required as set by registration. See the example below that was importer successfully previously.

| **uidNumber** | **name** | **username** | **email** | **registerDate** | **homeDirectory** | **userPassword** | **orgtype** | **organization** |
|---|---|---|---|---|---|---|---|---|
|  | Hub Man | hubman | [hubman@hubzero.org](mailto:hubman@hubzero.org) |  | /home/hubzero/hubman | N23L023B2N3T | uknown | unknown |

| **countryresident** | **countryorigin** | **gender** | **url** | **reason** | **mailPreferenceOption** | **usageAgreement** | **modifiedDate** | **emailConfirmed** | **regIP** | **regHost** |
|---|---|---|---|---|---|---|---|---|---|---|
| US | US |  |  |  | 1 | 1 |  | 1 |  |  |

| **nativeTribe** | **phone** | **givenName** | **middleName** | **surname** | **picture** | **vip** | **public** |
|---|---|---|---|---|---|---|---|
|  |  | Hub |  | Man |  | 0 | 0 |

| **note** | **locked** | **orcid** | **interests** | **race** | **disability** |
|---|---|---|---|---|---|
|  |  |  |  |  |  |

1. CSV field descriptions.
   - uidNumber: Typically leave blank when creating new accounts.
   - name: Full name is required. First name and Last is usually enough.
   - username: Required. If not provided and admin must choose an somethign appropriate.
   - email: Required. Please only use valid email addresses. Emails will be send to the member.
   - registerDate: Leave Blank
   - homeDirectory: Required. Administrator must know the member home directory path to be created. For example, **/home/example/hubman** (/home/hubname/username).
   - userPassword: Required. Must be randomly generated and will be provided to the new member in plain text. The new member will be asked to reset the password when first logging on.
   - orgtype: Optional. Depends on the set registration parameters.
   - organization: Optional. Depends on the set registration parameters.
   - countryresident: Optional. Depends on the set registration parameters.
   - countryorigin: Optional. Depends on the set registration parameters.
   - gender: Optional. Depends on the set registration parameters.
   - url: Optional. Depends on the set registration parameters.
   - reason: Optional. Depends on the set registration parameters.
   - mailPreferenceOption: Required. 1=agree to send newsletters, etc., 0=disagree to send newsletters,etc.
   - usageAgreement: Required. 1= agreed. 0=disagree, must agree when logging on for the first time.
   - modifiedDate: Leave blank.
   - emailConfirmed: Required. 1=member account is emailed confirmed, 0=new member must confirm (click on a link and login) the account via email.
   - regIP: Leave blank.
   - regHost: Leave blank.
   - nativeTribe: Leave blank.
   - phone: Optional. Depends on the set registration parameters.
   - givenName: Required. Usually the First name of the member.
   - middleName: Optional.
   - surname: Required. Usually the Last name of the member.
   - picture: Optional.
   - vip: Optional.
   - public: Required. 1=public, 0=private.
   - note: Optional.
   - locked: Optional.
   - orcid: Optional.
   - interests: Optional.
   - race: Optional.
   - disability: Optional.

## Creating a new import and uploading a completed CSV

1. Login to the administrative side of the Hub
2. Click the **Members** tab and from the drop-down click on **Import**
3. From the control button on the top right of the page, click on the button with the plus icon
4. In the new page that opens, enter a name for the import and a short description
5. Below the description, choose the desired parameters
   1. Approved: Typically set to yes
   2. Email new member: If yes, an email will be sent to the email entered into the CSV providing the member with their login and password in plain text
6. In the top right column, click **Upload** to upload a CSV file. From the control buttons at the top right, click **Save**
7. Select your CSV
8. Select the desired mode:
   1. Update: when creating new members
   2. Patch: for correcting fields of existing members (be sure the CSV is configured appropriately)
9. From the control buttons at the top right, click **Save & Close**

## Running (and a test run) the import

Run a test. It is always a good idea to do a test run before actually importing or updating many members.

1. Select the import you wish to test run by checking the box next to the CSV title
2. Click on the **Test Run** button
3. You will be directed to a page with a count down. You may click on the button in the middle of the page to run the import immediately or wait for the downtime to complete
4. After the import run has been completed, you will see the results displayed below the progress bar
5. Click on each item will display details for each record

## Running the import (after a test run)

1. After the test run, check a few records to make sure that everything looks OK
2. Click on **Run** button, that is just under the expired countdown
3. The real import run will complete in a few moments and display results below the progress bar
4. The import is now complete

## HIPAA Hubs: Delivering Passwords

Before getting started, you will need the each imported member's phone number and email address. Once you have obtained this information, follow these steps:

1. Notify all users via email that they will be contacted by a site administrator to deliver their password​
   1. ​As a safety, the email notice should specify to call you if they are not called on the schedule time. This is to catch mismatched email addresses and phone numbers.
2. Once you have schedule a time to call the user, edit their account so that the password is already expired, which forces the user to change their password as soon as they login for the first time. To do this follow these steps:
   1. Navigate to **/administrator** interface
   2. Click on the **Users** tab and from the drop-down select **Members**
   3. Check the user's account and click on the **Edit** button
   4. Select the Password tab and change the **Valid for (days)** section to "0"
      1. **Note:** Before clicking **Save & Close**, make sure that you have not automatically filled the **New Password** section with a saved password from your browser. If you do automatically fill in this section and click **Save & Close**, you will overwrite the current password with one of your saved passwords.
   5. Click **Save & Close**
3. Call each user via phone to give them their passwords
   1. Verify that they recieved the email notice about the creation of their accounts
   2. Express to the user that this temporary password will expire in two days and that they need to login to the account and change their password as soon as possible
4. Store the copy of user accounts in an encrypted file or safe location that meets HIPAA regulations
   1. **Note:** Do not share this import file through email or any service that could cause a leak of sensitive information

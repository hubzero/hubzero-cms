<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/users/memberimport
source-id: 3358
modified: 2015-10-27
imported: 2026-09-09
-->
# Member Import

## Overview

The members bulk importer/ updater is designed to allow a site administrator to bulk import or update member data. Due to its ability to mass upload/ modify accounts, it is an administrator only feature.

**Warning!**

The improper use of this feature can have damaging consequences. Use with caution.

The importer should only be used in special circumstances. Minimum requirements for the CSV (first name, last name, username, password, etc.) and any additional registration fields (member demographics) must be gathered by an administrator for every account to be affected. In most cases, it is more time effective and less error prone for a new members to enter this information in the registration form instead of passing the information through the administrator.

**Please note, if you are using Member Import on a HIPAA hub, please complete the extra steps on delivering passwords to members.**

## Registration Fields

The member importer follows the parameters set in the registration component. If a registration field is required during the initial registration form (organization, for example), the field must be included in the uploaded CSV file in addition to the required fields for the CSV. Not all registration fields are available via the member importer, those should be collected from the new member on the next login, if desired.

## CSV Preparation

1. Log into hub's administrator portal, e.g. https://hubname.org/administrator
2. Navigate from the main menu to **Users** -> **Members**
3. From the page menu under the title **Members**, click on the **Im****port** tab
4. Click on the button that looks like a grid, located on the top right of the page, to download the example CSV
5. Save the file with a different name that represents the group of members to be imported
   1. Note: Do not save it in Excel format, the import only accepts CSV files.
6. Edit the CSV
   1. Fill required fields and any supplementary fields

## CSV Field Descriptions

- id: User's ID (leave blank when creating new accounts)
- name: User's full name e.g. First Last
- middleName: User's middle name
- surname: User's surname
- username: User's hub username
- email: User's email (only use valid email addresses as emails will be sent to the user)
- password: User's temporary password
- block: User's blocked status (1 for blocked, 0 for not blocked)
- approved: User's account approval status (1 for approved, 0 for not approved)
- sendEmail: User's preference for receiving email from the hub (1 to accept, 0 to decline)
- activation: User's email confirmation status (1 for confirmed, 0 for unconfirmed)
- usageAgreement: User's acceptance of hub's terms of service (1 for accepted, 0 for declined)
- homeDirectory: User's hub home directory e.g. /home/hub/user
- organization: User's organization (depends on hub's registration configuration)
- orgtype: User's organization type (depends on hub's registration configuration)
- phone: User's phone number (depends on hub's registration configuration)
- groups: A comma separated list of the aliases for the groups that the user is a member of
- projects: A comma separated list of the aliases for the projects that the user is a member of

## Creating a new import and uploading a completed CSV

1. Login to the hub's administrator portal
2. Click the **Members** tab and from the drop-down
3. Click on the **Import** tab
4. Click on the button with the plus icon located on the top right of the page
5. In the new page that opens, enter a name for the import and a short description
6. Click the file selection button in the **Upload** fields to upload a CSV file
   1. Select the desired CSV
7. Save the import using one of the save buttons in the top right

## Testing an import

It is always a good idea to do a test run before actually importing or updating members' accounts.

1. Select the import you wish to test run by checking the box for the import row
2. Click on the **Test Run** button
3. After the test has completed, you will see the results displayed below the progress bar
4. Click on each item will display details for each record

## Running an import (after a test run)

1. Click on the **Run for Real** button (under the countdown)

<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/users/supergroups
source-id: 3365
modified: 2016-07-12
imported: 2026-09-09
-->
# Super Groups

## Overview

Super Groups are advanced Hub Groups, that have their own webspace within the Hub to showcase their Group. Super Groups have a lot of extra functionality built in to allow them to customize their group.

Super Groups have the ability to include PHP and javascript code into Group pages and modules. Pages or modules that contain PHP or Javascript code will then need to be approved by a Group page approver. Notifications are sent to approvers when a page needs to be approved. Another notification will be sent to the Group managers when the page has been approved.

Super Group templates must follow HUBzero's [Super Group Template rules](../../developers/13-supergroups/README.md).

## Creating a Super Group

1. Log in to the backend of the Hub and hover over the **Users** tab and select **Groups**
2. Click **New** to create a group
3. Select the group type from the drop-down to be **Super**
4. Fill in the CN or group alias, the title and logo of the group, along with the public and private group descriptions
5. In **Membership Control**, check the box if the membership is to be controlled. The membership **Join Policy** determines access level for the group
   1. **Public:** Any user on the Hub can join the group.
   2. **Restricted:** Qualifications are given that predetermines who can join. These restrictions have to be added into the credentials.
   3. **Invite Only:** A group manager has to send an invite any new group members to the group.
   4. **Closed:** No one can join the group unless they are added from the back-end of the Hub.
6. Determine the access level of the group from hidden or visible. A hidden group is not listed on the front-end of the Hub, and any content is unavailable to any user that is not a member of the group. A visible group can be found on the Hub through regular searches
7. Once all the group’s content has been filled in, then save the new group by clicking **Save & Close**. The group will be automatically approved and visible on the Hub
8. The following will be automatically created when making the super group:
   1. Base template with custom error handling & login
   2. Separate database for super group related components, data, etc
   3. If Gitlab integration is enabled, all needed connections will be made and super group folder will be controlled via Git.

## Frontend Module Management in Super Groups

## Accessing the Module Manager:

1. Log in to the Hub and access the **Super Group** home page
2. Click on the **Manage Group Pages** link in the group manager toolbar, to get to the page manager
3. Select the **Manage Modules** tab to view the module manager
4. Search for modules by using the search drop-down that searches for a module based on location and title

## Adding a Module:

1. Log in to the Hub and access the **Super Group** home page
2. Click on the **Manage Group Pages** link in the group manager toolbar, to get to the page manager
3. Select the **Manage Modules** tab to view the module manager
4. Click the **New Module** button in the toolbar
5. Fill in all required fields and click **Save Page**

## Publishing a Module:

1. Log in to the Hub and access the **Super Group** home page
2. Click on the **Manage Group Pages** link in the group manager toolbar, to get to the page manager
3. Select the **Manage Modules** tab to view the module manager
4. Publish the module by clicking on the **Unpublished** icon. The icon will become a **Published** icon
5. The module will now be available on the front interface of the Hub

## Unpublishing a Module:

1. Log in to the Hub and access the **Super Group** home page
2. Click on the **Manage Group Pages** link in the group manager toolbar, to get to the page manager
3. Select the **Manage Modules** tab to view the module manager
4. Unpublish the module by clicking on the **Published** icon. The icon will become an **Unpublished** icon
5. The module will now be removed from the front interface of the Hub

## Editing a Module:

1. Log in to the Hub and access the **Super Group** home page
2. Click on the **Manage Group Pages** link in the group manager toolbar, to get to the page manager
3. Select the **Manage Modules** tab to view the module manager
4. Click on either the **Manage Module** button or hover over the arrow next to the button to see more options, including **Edit Module**
5. One of the new features with the module manager is the page content WYSIWYG editor. It now uses CKEditor and HTML to manage your page content
6. Modify the module with new changes, and then click the **Save Page**

## **Reordering a Module:**

1. Log in to the Hub and access the **Super Group** home page
2. Click on the **Manage Group Pages** link in the group manager toolbar, to get to the page manager
3. Select the **Manage Modules** tab to view the module manager
4. Click on either the **Manage Module** button or hover over the arrow next to the button to see more options, including **Edit Module**
5. One of the new features with the module manager is the page content WYSIWYG editor. It now uses CKEditor and HTML to manage your page content
6. Modify the ordering of the module by using the drop-down, and then click the **Save Page**

## Super Groups: Resource Template Inheritance

Originally when viewing a Resource from a Super Group, the Resource is seen with the master site template rather than the Super Group's custom template. Resource Template Inheritance allows users, when viewing a Resource from a Super Group, the Resource to inherit the Super Group's template to maintain consistent look and feel of the Super Group. '

![resource_supergroups](../media/supergroups-resource-supergroups.png)

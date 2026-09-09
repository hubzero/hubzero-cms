<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/supergroups_gitlab/setup
source-id: 3529
modified: 2014-09-10
imported: 2026-09-09
-->
# Setup

## Hub Setup

Each HUB can choose to integrate with Gitlab or not. If your HUB chooses to integrate then there are few steps to get setup and running.

1. Gitlab integration must be enabled in the Groups config, under the "super" tab. The Gitlab API URL must also be supplied along with the API key of an admin account on Gitlab (found under the "account" tab in profile section in Gitlab). This allows the HUB to do the initial group/project/repository creation when the super group is created.
2. In order for the HUB (www-data user) to make the first commit to the project, including the basic super group template and folder structure, the www-data user must have an SSH Key on the HUB machine. That SSH key must also be added to an admin account on Gitlab. This first commit actually creates the GIT repository in Gitlab.
3. The last step for HUB setup is to SSH as the www-data user to the Gitlab machine from the HUB machine.. This will approve the RSA fingerprint of the Gitlab machine for the www-data user and add the machine to the known_hosts file. If this step is omitted the hubs attempt to make the inital commit will be denied.

## Group Setup

When a super group is created on the HUB, most of the initial setup for that super group is done automatically. If you are working on a super group that was created prior to May 2014, then this setup will need to be done manually for that super group to work with Gitlab. Please enter a ticket through the support system detailing the the super group and that you would like to have your group integrated into Gitlab.

## Developer Setup

**Access**

Part of the developer setup is getting permission to access Gitlab. This must be done manually by the HUBzero development team. Please submit a ticket indicating the super group and any users (name, preferred username, & email address) that will need to have access in Gitlab for the project.

**Login & Password Change**

Once you have submitted a ticket for access to Gitlab, you will recieve an email within 48 hours with all the details you need to login to your account. The email will contain a temporary password that you will be forced to update upon login. After you login and change your password you can move on to the next step, uploading an SSH key.

**SSH Keys**

In order to make commits and push to Gitlab, you need to add an SSH key to your account. To add an SSH key, login to Gitlab, go to your profile, then SSH Keys. Click the "Add SSH Key" button, enter any title you want, paste your public SSH key in the box, and click "Add Key". If you are unsure of how to create an SSH key there is a link at the top of the "Add an SSH Key" page that links to a help page with detailed instructions.

> **Note:** You can add multiple SSH keys if you plan to make commits from multiple machines.

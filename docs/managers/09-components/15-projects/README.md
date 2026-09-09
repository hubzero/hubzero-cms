<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/components/projects
source-id: 3385
modified: 2016-07-12
imported: 2026-09-09
-->
# Projects

## Enabling Plug-in to Projects

1. Login to the backend of the Hub and click on the **Extensions** tab
2. Click on the **Plug-in Manager** under the **Extensions** tab
3. Search **Projects- Databases** in the search field to locate the Data Store plug-in
4. After locating the correct plug-in, check the box beside the **Plug-in Name**
5. Click the **Enabled** button to allow the plug-in to be used on the frontend of the Hub

> **Warning:** If the Menu Item for Databases does not display, the MySQL Configuration may not be correct for DataViewer.

## Restricting Plug-in to Specific Projects

1. Login to the backend of the Hub and click on the **Extensions** tab
2. Click on the **Plug-in Manager** under the **Extensions** tab
3. Search **Projects- Databases** in the search field to locate the Data Store plug-in
4. After locating the correct plug-in, click on the title of the plug-in under the **Plug-in Name** column
5. Inside the plug-in details, type in the names of the Projects in the **Restricted to projects** field
6. Save the changes by click the **Save & Close** button

## Uploading Large Files Using SFTP Clients

Create a Local Password

1. If you use a non-Hub related account, then you must first create a local password
2. Login to the HUB
3. On the **Dashboard**, click **Account** on the left menu
4. Under **Local Password**, type in the password in the **New Password** and **Confirm password** text boxes
5. Click **Save**

Setup SFTP Client

1. Download an SFTP client like Cyberduck or Filezila
2. Add a new site by either clicking the **Plus sign icon (+)** or **File** **-> Site Manager ->**
   **New Site**
3. Provide the following information
   1. Host: hubname.org
   2. Protocol: Change to **SFTP**
   3. User: Provide the same username used to login to the Hub
4. Click **Connect**
5. When prompted for password, provide the local password that was created above
6. Once connected, open up the **Data** file and then the **Projects** file and double click on the project that you would like to upload the files to
7. Drag and drop the files into this folder and then check inside of the site and confirm that these files are uploaded to the right project

## Enable Grant Collection Information

If you are working on a Hub that will be creating projects based on grants, then you can enable a new step to be added in the Project creation to collect grant information. By collecting grant information for grant funded projects, project quotas can be increase based on the project budget and this information can help support annual reviews of the Hub. To enable this feature, follow these steps:

1. Navigate to the backend of the Hub
2. Hover over **Components** and from the drop-down click **Projects**
3. Click the **Options** button in the upper right corner of the screen
4. Inside of Options, click the **Setup** tab
5. Find the *Collect grant info at Setup* and change the drop-down to **Yes**
6. Click **Save & Close**

<!--
status: imported
source: https://help.hubzero.org/documentation/240/users/projects/databases
source-id: 3318
modified: 2014-11-19
imported: 2026-09-09
-->
# Databases

## DataStore Lite

Creating a searchable hub database from a spreadsheet with data is made possible by DataStore Lite within Projects.
**Note:** You must be logged in on the hub and inside a project in order to use this feature.

> **Tip:** A .csv (comma separated values) file containing the data is required to create the database. A spreadsheet program such as Excel can be used as long as the file is saved in the .csv format. The first row should contain the labels for each data field/column. Each row below represents a unique data entry. If the database is to contain links to files stored in the project, provide a column with the case sensitive name and extension of each file (e.g. “projectstep1.png”).

## Creating a Database

**Upload the File to the Project**

1. Navigate to **https://yourhub.org/projects**
2. Locate the project where the database is to be added in the *My Projects* section, or use the **Start a project** button to create a new project (for help with creating a project, go to: [https://hubzero.org/documentation/1.3.0/users/projects](README.md))
3. Click **Files** underneath *Assets* on the left project menu
4. Click **Upload**
5. Drag and drop the .csv file as well as any files that are to be linked in the database into the **click or drop file** textbox. Alternately, click the **Click or drop file** box and select the .csv file as well as any additional files to add (ctrl + click to select multiple) and click **Open**
6. Click "Upload now!" and the file(s) will be uploaded

## **Create the Database - Step 1: Select the File**

1. Click **Databases** underneath *Assets* on the left project menu
2. Click **Create a database** in the upper right
3. Select the .csv file that contains the data for the Database from the list
4. Click **Next**

## **Create the Database – Step 2: Verify Data**

1. Click the pencil **Edit** icon underneath a column heading to make changes to it
2. On the **General** tab of the *Column Properties* window, several values can be set:
   
   **Label (Required)** ­– This is the title that is shown for the column.
   
   **Description** – A column description can be provided here. The user will see this description when clicking on the column title.
   
   **Width** – This column accepts a numeric pixel value. With this set, the column will remain fixed at the specified width.
   
   **Units** *–* This column accepts a unit of measure (e.g. inches, meters, liters) and will show underneath the column heading.
3. On the **Column Type** tab of the *Column Properties* window, select the appropriate column type from the drop down list:
   
   **Text [small]** – This column type should be used to display a title or short description of the entry.
   
   **Text [large]** – This column type should be used for a long description or text that is more than one sentence.
   
   On either text Column Type, check “**Limit text to a single line**” to prevent the provided text from wrapping. If the length of the text exceeds the width of the column, it will truncate what displays and the user can click on the text to view the entire content of that particular field.
   
   **Image** – This column type will display a preview of the image directly in the data field. This can be either a URL to an image on the web, or the name of an image contained in the Project files.
   
   **Link** - This column type will provide a hyperlink to the file specified. It can either be a full URL link to the page, or the name of a file contained in Project files.
   
   Check **Repository Files?** and choose the Repository Path from the drop down if the images or files are contained within the Project.
4. On the **Other** tab of the *Column Properties* window the content alignment, text color, and background color can be configured
5. Click **Update Column** to save all changes made
6. When you have verified all the columns look as intended, click **Next**

## **Create the Database - Step 3: Title & Description, Finish**

1. Type a title and description to the Database in the text boxes provided
2. Click **Finish** to finalize all changes and make the database available

## Updating a Database

## **Change the Database File**

1. Navigate to **https://yourhub.org/projects**
2. Click on the Project that contains the Database that needs updating
3. Click on **Databases** underneath *Assets* on the left project menu
4. Click the name of the .csv file being used by the database in the **Files** column to download the file
5. Open the file on your local machine using a spreadsheet application or text editor
6. Make the changes to the file that are required and save, ensuring that the file name remains the same name as the original source file
7. Navigate to **Files** underneath *Assets* on the left project menu
8. On the Files page within the project, click **Upload**
9. Drag and drop the updated .csv file into the **click or drop file** box and click **Upload now!** Alternately, click the **Click or drop file** box, select the file, click **Open** and then click **Upload now!**
10. The .csv file contained on the project files has been updated, but additional steps will need to be taken in order to update the database, as discussed in the next step

**Update the Database**

1. Within the project, click on **Databases** underneath *Assets* on the left project menu
2. Click **Update Database** next to the database that requires updates since changes have been made to its original .csv file
3. Verify the columns shown are correct, especially if changes were made to the data and/or additional columns were added
4. Also click **Edit** below the column label to change its column type as well as add optional information to the column (such as a column description)
5. Click **Next** when finished
6. Provide a title and description (perhaps indicate an update was made) and click **Finish**

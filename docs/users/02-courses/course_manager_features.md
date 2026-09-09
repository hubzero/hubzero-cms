<!--
status: imported
source: https://help.hubzero.org/documentation/240/users/courses/course_manager_features
source-id: 3290
modified: 2014-11-17
imported: 2026-09-09
-->
# Course Manager Features

## Course Administrator Features

Though the platform has been designed to be simple and self-serve, there are also plenty of options to keep you busy. Follow the tutorials below to help clear up those options, or dive deeper into the intricacies of the HUBzero courses platform!

> **Note:** You must be logged in and must have special privileges (role of Instructor, Manager, etc.) in order to perform/access the features below.

## Editing the Course Overview Page Content

1. Go to the course's overview (**https://yourhub.org/courses/{course alias}**)
2. You may edit any of the following from the course overview page:
   - **Course Title & Short description**: allows the change of the title of the course, short description, and addition of tags. To edit, click the edit button, make your changes, and click "Save".
   - **Course Overview Long description**: allows the editing of the long description of the course displayed in the Overview tab. To edit, click the "Edit" button, make your changes, and click "Save".
   - **Offerings**: an offering is a collection of materials (lectures, quizzes, etc.) that represents a version or edition of a course. If no offerings exist for the course, you will see a "Create an offering" button. If offerings exist, you will see a "New" button under the Offerings tab to create an offering. To create the first or an additional offering, click the corresponding button and provide at least the title for your new offering and click "Save".
   - **Course Overview Pages (tabs)**: you may add more pages (tabs) for additional content via the "Add page" button or edit existing added pages via the "Edit" button found under each added tab. Just click the corresponding button, add content or make your changes to an existing added page, and click "Save".
   - **Instructors or Managers**: allows management of special user permissions in the course. Clicking "Manage" will enable you to:
     1. Add users and assign them a privileged role in the course. Default roles are Instructor and Manager. Additional roles may be available depending on what the hub administrators have made available.
     2. To add a user, enter their username or hub ID in the field (the field supports multiple entries separated by a comma), and select the role you would like to assign. Click **Add** and if successful, a **Changes saved** message will appear and the added user(s) will be listed below with the assigned role.
     3. Change assigned role by selecting a different role from the drop-down next to the user’s name. A **Changes saved** message will appear upon successful role switch.
     4. Remove special privileges by clicking the box next to their name and clicking the **Remove** button and that will remove them from that position.

## Building the Course Outline

Course managers and instructors are able to build and customize their course outline. All the materials that need to be completed by students will be stored for student access throughout the course.

**Creating the Outline**

1. Navigate to the course's overview (**https://yourhub.org/courses/coursealias**) and click **Go to Course** to access the course
2. On the course’s dashboard, click **Outline**, and then click **Build outline** to begin editing
3. An outline is organized into units that are broken down into categories such as **Lectures**, **Homework,** **Exams**, and **References**. To add a new unit:
   1. Click **Add a New Unit**
      1. To edit the unit, hover over and click **New Unit**
      2. Click **Save** after any changes to the unit
4. When the unit is created, the hub’s default categories (Lectures, Homework, ect.) appear. To edit a category:
   1. Hover over the category title, and click **Edit**
   2. Click **Save** after any changes to the category
5. Add all documents and materials needed for the course by creating asset groups under each category. To add a new asset group:
   1. Click **Add new** in the categories where the material will be held
      1. **Note:** See instructions below for detailed instructions on adding material to an asset group.
6. Click **Done** to navigate back to the outline

**Adding Material to the Outline**

1. To add documents such as a lecture, homework, or exam, you can upload the document by dragging a file onto the page or adding the file by four other options:
   1. **Attach a link:** Insert links to webpages by typing them into the box with commas to separate out multiple links.
   2. **Embed a Video:** Embed a Youtube or Kaltura videos by pasting the video embed code.
   3. **Include a Wiki Page:** Create a wiki page by adding a new wiki page along with any references that will be attached to the wiki page.
   4. **Browse for Files:** Browse the files on the computer and add them to the category directly. These documents will be downloadable for users to access. This upload option will be used when creating a gradable document using a PDF.
2. Once an attachment has been added the system will check to make sure that the files are not corrupted, and then you can edit the title and availability of the course material
3. All four options allow multiple files to be uploaded at once

## Adding Graded Documents

1. To add quizzes, exams or other gradable documents, upload the file as a PDF
   1. **Note:** Only PDFs can become gradable documents.
2. You will be asked, **What do you want to do with these files?**
3. Select **Create a quiz/exam**
4. Fill in the title of the document
5. At each question, click and drag your cursor to select the entire question and all the answers. This will create a yellow box around the area of the question
6. Click beside each possible answer to create a radio button. These radio button will be what the student can select when answering the question
7. Finally, click the correct radio button that is next to the correct answer. Repeat this process next to all of the questions. When you are done creating the answer bubbles, click **Save and Close**
8. Check the box on the right to publish the homework/quiz/exam, and then a pop-up will appear with deployment settings
   1. **Note:** All deployment dates must now be set on the administrative end.
9. Select the time limit by clicking the arrows up or down in the **Time limit** text field
10. Select the number of attempts allowed by clicking the arrows up or down in the **Attempts** text field
11. Select the settings of what a user can see after they take the homework/quiz/exam:
    1. Select from the radio buttons the setting for what users will be able to see when the homework/quiz/exam session is open and when it is closed:
       1. **Only confirmation that their submission was accepted**: Users will not see their scores until after the session is closed.
       2. **Their score**: Users will only be able to see their score but not the answers.
       3. **A complete comparison of their answers to the correct answers**: Users be able to view their score and the answers to the homework/quiz/exam.
12. Click **Create deployment** to save the settings for the gradable document
13. You can always edit each homework, quiz, or exam again after submitting it by clicking the small pencil icon next to the document’s name

## Adding Prerequisites

Course items can now have prerequisites. These items must be completed prior to accessing the item for which the prerequisite applies. Prerequisites can be enabled for units and assets. These items can have other items of like kind as their prerequisite (i.e. assets can require assets and units can require units).

**Adding Prerequisites to a Unit**

1. Navigate to a course overview
2. Click on **Go to Course** to enter the course
3. Select the **Outline** tab and then click on the **Build Course** button
4. To add a prerequisite to a unit, click on the title of the Unit
5. Under **Prerequisites** choose the unit from the drop-down
   1. The prerequisite that you chose will determine the order of the course, such as making Unit 1 a prerequisite for Unit 2 forces students to complete Unit 1 before moving on to Unit 2.
6. The prerequisite will be automatically saved

**Adding Prerequisites to an Asset**

1. Navigate to a course overview
2. Click on **Go to Course** to enter the course
3. Select the **Outline** tab and then click on the **Build Course** button
4. To add a prerequisite to an asset, hover over the asset and click the **Edit** or pencil icon
5. In the **Edit Asset** pop-up, locate the **Prerequisites** section and choose the prerequisite for this asset from the drop-down
   1. The prerequisite that you chose will determine the order of the course, such as making Asset 1 a prerequisite for Asset 2 forces students to complete Asset 1 before moving on to Asset 2.
   2. Only published assets can be selected as a prerequisite.
6. The prerequisite will be automatically saved

## Deleted Assets

**Deleted Assets** is a button available on the course-building page, where you can restore deleted content. If you are in the process of building a course you can remove content in the course outline but clicking the **Delete** button or trashcan icon beside the title of the file. If something is accidentally removed from the outline, you can retrieve it but accessing the **Deleted Assets**:

1. Navigate to the course that is currently being built
2. Go into the course and from the Overview page, click on the **Outline** tab
3. Click on **Build outline** to access the content of the course
4. Inside of **Build Outline**, click on the **Deleted Assets** button to access all deleted material
5. To restore a deleted item, just click the **Restore** button next to the file that you wish to add back to the course outline
6. Any changes will be automatically saved

## Adding an Image to Pages using CKEditor

1. Navigate to a course and then click on the **Pages** tab inside the course
2. Select the **Add Page** button to create a new page or edit a previous page but clicking the “Edit” or pencil icon
3. Under the **Content** field, click or drop an image to upload the picture
4. The uploaded file will appear on the right side of the **Upload** field
5. Copy the URL that appears under the image
6. Place your cursor in the section of the page where the new image is to be placed
7. Click the **Image** button, and a pop-up of **Image Properties** will appear
8. Under the **Image Info** place the copied URL in the URL field
9. Fill in the settings if you wish to alter the dimensions of the image
10. Click **OK** to save the image properties
11. The image will be automatically placed into the content of the page
12. Click **Save** to save the new image and preview the image in the course’s page

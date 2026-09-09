<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/menus
source-id: 3366
modified: 2014-10-10
imported: 2026-09-09
-->
# Menus

## Overview

A Hub menu is a website navigation menu where users go to navigate around the Hub. The Main Menu is the first navigation path that users interact with from your landing page and usually lives in your template header. Additional menus can be added to content pages on the Hub in order to link like pages together for easier navigation. The general rule of thumb is that if a content page is not linked to a menu, the likelihood of the page being discovered by users is diminished.

## Organizing Your Articles Using Menus

If you have a set of articles that you would like to group together, you can use menus to organize those articles in an easy-to-navigate fashion.

An example of this can be found on the default hub under the About section (see also below).

1. To begin, find the URLs of the articles that you're interested in grouping together
   1. **Note:** If you can't find, or don't know the URL, it comes from the article alias, the section, and the category (see below and view these documentation pages: [Categories](08-content/articlemanager.md), [URLs](08-content/urls.md)).
2. With your article pages created, you'll need to make the menu structure
3. Navigate to **Menus -> Menu Manager** on the *Joomla!* backend and select **New**
4. Give your new menu a unique name and title, then click **Save**
5. Now you will see your new menu in the list
6. Click the icon indicated below to start adding menu items to your new menu
   - **Note:** It is important to realize, for clarity's sake, that there is a difference between **Menu** and **Menu Item**.
     1. **Menu Item:** represents an instance, or individual menu link
     2. **Menu:** represents the entity as a whole
7. Now click the **New** button to add new menu items to your menu
8. After clicking **New**, scroll down and select **Link**
9. Next, fill in the necessary information:
   - This is where you'll need to remember the URLs for the articles that you've created (you'll add the URL to the **Link** field)
   - After creating this menu item, you'll be able to access this article page via the URL for the article, or the Alias of the menu item
     - **For example:** according the screenshot below, both `http://yourhub.org/example/examplearticle1` & `http://yourhub.org/article1` will work when trying to access this page via the browser (See [URL Redirects](08-content/urls.md) for more details). For the navigational menus to display properly, though, you'll want to use the menu item alias when trying to access your article page.
10. Click **Save** in the upper right-hand corner
11. If you switch over to the front-end and navigate to your article page (via the menu item URL), you should see a page similar to this one:
12. At this point, simply repeat the previous steps to create menu items for all of the articles that you want to use together
13. The final step in this process is to assign the menu items that you've created to display on the article pages
14. To do this, navigate to **Extensions -> Module Manager** on the backend *Joomla!* interface
15. Next, select **New**
16. Scroll down and select **Menu** from the list (either click on the link, or select the radio button then scroll back up and click **Next**)

Now fill in the correct information...make sure to:​​

- Give it a **Title**
- Be sure **Enabled** is set to **Yes**
- Give it the position **Left**
- Set it to display on the correct pages
  - Under menu assignment, **control click** the menus items that we made in the preceding steps. This is what tells *Joomla!* which pages to display our new menu on.
- Under **Module Parameters**, **Menu Name** should be the title of the menu that we created earlier (**example** in this case)
- **Menu Style** should be **List** ​

17. When all of this has been done, click **Save**

## Redirecting a URL to Another Existing Hub Component

Redirecting a URL to Another Existing Hub Component Previously created groups and other components can have URL redirects added to other components instead of transferring all the files and data that live in the previous area to a new section. For example, a professor can redirect a group called **Mainclass** to another group called **Springclass**. In order to set up a redirect, follow these steps:

1. Navigate to **/administrator**, locate the tab **Menus** and select **Menu Manager** from the drop-down
2. Locate the **Default** menu group
3. Click on the **Default** group to enter its menu interface
4. Try and locate the **Parent** group
5. In this case we want groups, however, this is not listed, so let’s create it by clicking the **New** in the top right corner
6. Create the “groups” part of the URL **/groups/****mainclass**
7. Click the **Select** button next to the field *Menu Item Type Select External URL*
8. To create the groups part of the URL, fill in the form as follows:
   1. *Menu Item Type: External URL Menu Title: Groups Link: groups*
9. Click **Save & Close** from the top right corner
10. To create the redirect portion, follow step 5 again except fill out the Menu Title as mainclass and the link as spring2016class
11. This will redirect users from */groups/**mainclass* *to /groups/springdefaultclass*
12. Nest mainclass under groups to create the end result from the redirect
13. This is done in the **Menu Item Details** under **Parent**
14. Set the **Parent** to **Groups**
15. Click **Save & Close**

## Creating a New Menu

1. Navigate to **/administrator**
2. Hover over **Menus** -> **Menu Manager** and click on **Add New Menu**
3. Fill out the new menu information
4. Click **Save & Close**
5. From **Menu Manager: Menus**, locate your new menu and click on **Add a module for this menu type** under *Modules linked to the menu*
6. Create the new module for your menu by filling in the module configuration information
   1. **Note:** Most menu modules are left aligned
7. Click **Save & Close**
8. Create new menu items, either linked to articles you have created or linked to various components, by navigating to **Menus** and clicking on your new menu
9. Click on the **Menu Items** tab and click the **New** button to create menu items
   1. **Note:** Once you have linked menu items to your new menu, navigate back to "**Extensions**" -> **Module Manage**r -> Click on your new menu module -> Confirm the **Menu Assignment** for your module is affiliated with the new menu items you created

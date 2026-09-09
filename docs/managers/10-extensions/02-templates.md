<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/extensions/templates
source-id: 3407
modified: 2009-10-01
imported: 2026-09-09
-->
# Templates Manager

## Overview

The Template Manager is where you assign a default Template to your web site. You can also edit and preview Templates here.

The visual layout of both the Front-end and Back-end of your site is controlled by the Template. Templates are extensions that contain layout and style information that tells the CMS exactly how to draw each page of your site.

When you first install a hub, one Back-end Template and one or more Front-end templates are included. Other Templates can be installed from third-party developers as Extensions.

If you want to use the same Template for all of the pages on your site, you just assign one Template as the Default Template. You can also assign different Templates to different pages.

Select **Extensions** → **Template Manager** from the drop-down menu in the back-end of your installation.

## Column Headers

- **#:** An indexing number automatically assigned by Joomla! for ease of reference.
- **Template Name:** The name given to each Template by the Template author. Click the Name to open the Template for editing. If you hover the mouse over the Template Name, a small preview for the Template displays in a pop-up window. The Template Name normally corresponds to the sub-directory name that contains the Template in the `<path-to-app>/templates/` directory. For example, the files for the **kimera** Template are in the directory `<path-to-app>/templates/kimera`
- **Default:** Indicator of Default Template.
- **Assigned:** Shows whether this Template has been assigned to any specific menu items. To assign a template to menu items, open the Template for editing.
- **Version:** The version number of the Extension.
- **Date:** The date this extension was released.
- **Author:** The author of this extension.
- **Display #:** The number of items to display on one page. If there are more items than this number, you can use the page navigation buttons (Start, Prev, Next, End, and page numbers) to navigate between pages. Note that if you have a large number of items, it may be helpful to use the Filter options, located above the column headings, to limit which items display (*where applicable*).
- **Location**

## Toolbars

- **Default:** Select the Template that you want to be the default Template. Then click this button. The default star symbol will show in the Default column, indicating that this is now the default Template.
- **Edit:** Select one item and click on this button to open it in edit mode. If you have more than one item selected (where applicable), the first item will be opened. You can also open an item for editing by clicking on its Title or Name. See the section below called **Changing Text and Color** for information on the edit screen.
- **Help:** Opens this Help Screen.
- **Duplicate**
- **Delete**

## Changing Text and Color

(**Note:** You have to be logged into your hub in order to complete the following tasks)

1. Click the **Extensions** tab and then select the **Template Manager** button located in the drop-down
2. On the **Template Manager: Styles** page, check the box of the **kameleon (admin)** template and then click the **Edit** button
3. Change the Header to **Light** or **Dark** from the drop-down
4. Change the **Theme** or main color of the backend by either choosing a color from the drop-down or by selecting **Custom (color specified below)** and fill in a color code in the **Custom color box**
5. Save the changes by clicking **Save & Close** and a pop-up will appear saying **Style successfully saved**

## Kimera: Adding a Background Image

1. Navigate to the **/administrator** interface and login
2. Hover over **Content** and select **Media Manager** from the drop-down
3. Upload an image to the **Media Manger**
4. Navigate over to **Extensions** and select **Template Manager** from the drop-down
5. Select **Kimera(site)**
6. Select **Advanced options** and add in the path to the image
7. Click **Save & Close**
8. The new image will appear on the frontend of the Hub

<!--
status: imported
source: core/components/com_categories/admin/help/en-GB/category.phtml
imported: 2026-09-09
-->
# Category Manager: Edit Category

<a id="description"></a>

## Description

This is where you can add a new Category or edit an existing Category. Categories allow you to display related Articles together on a page and to filter Articles in the Article Manager. All Articles are assigned either to a Category that you create or to the special Category called 'Uncategorized'.

<a id="column-headers"></a>

## Column Headers

## Details

- **Title**  
  The Title for this item. This may or may not display on the page, depending on the parameter values you choose.
- **Alias**  
  The internal name of the item. Normally, you can leave this blank and the CMS will fill in a default value. The default value is the Title or Name in lower case and with dashes instead of spaces. You may enter the Alias manually. The Alias should consist of lowercase letters and hyphens (-). No blank spaces or underscores are allowed. The Alias will be used in the URL when SEF is activated. Note: If the title consists of non-Latin characters, the Alias will default to the current date and time, for example "2009-02-11-17-54-38".
- **Parent**  
  Allows a parent category for the category being edited/created to be chosen.
- **Published**  
  Whether or not this item is published. Select *Yes* or *No* from the radio button group to set the Published state for this item.
- **Access Level**  
  Who has access to this item. Current options are:
  
  - Public: Everyone has access
  - Registered: Only registered users have access
  - Special: Only users with author status or higher have access
  
  Enter the desired level using the drop-down list box.
- **Category Permissions**  
  The permissions section for the category will be at the bottom of the screen. The options allowed are:
  
  - **Create**  
    Create new items in the category
  - **Delete**  
    Delete existing items in the category
  - **Edit**  
    Edit existing items in the category
  - **Edit State**  
    Change an items state (Publish, Unpublish, Archive, and Trash) in the category.
  - **Edit Own**  
    Edit existing items in the category that the logged in user has created.
  
  There are two very important points to understand from this screen. The first is to see how the permissions can be inherited from the parent Group. The second is to see how you can control the default permissions by Group and by Action.
  
  This provides a lot of flexibility. For example, if you wanted Shop Suppliers to be able to have the ability to create an item in the category, you could just change their Create value to "Allowed". If you wanted to not allow members of Administrator group to delete objects or change their state, you would change their permissions in these columns to Inherited (or Denied).
  
  It is also important to understand that the ability to have child groups is completely optional. It allows you to save some time when setting up new groups. However, if you like, you can set up all groups to have Public as the parent and not inherit any permissions from a parent group.
  
  Please note the inherited values will come from the Permissions set in the Global Configuration Permissions Tab
- **Description**  
  The description for the item. Section and Category descriptions for Articles may be shown on web pages, depending on the parameter settings. These descriptions are entered using the same editor that is used for Articles. Note that Section and Category descriptions may not be edited from the front end.
- **Language**  
  Select the language for this item. If you are not using the multi-language feature, keep the default of 'All'.
- **ID**  
  The unique ID number automatically assigned to this item by the CMS. This number cannot be changed.

## Category Description

This is where you enter the description of the category. The editor you use depends on the settings for your site and your user. The core install includes multiple editor options: CKEditor (the default), TinyMCE, Code Mirror, and None. Additional editors can be installed as extension plugins if desired (see the Extensions direction category of editors for a list). A number of the editors such as CKEditor and TinyMCE are WYSIWYG (what you see is what you get) editors that allows users a familiar word-processing interface to use when editing Articles and other content. These can be configured from the [Plugin Manager](https://help.hubzero.org/index.php?option=com_help&component=com_plugins&page=plugins).

### Editor Buttons

Five buttons are located just below the edit window, as shown below:

- **Article**  
  This button opens a modal window that allows you to easily create a link to any article on the current site. The link is created using the article's title as the link text. The modal window is the same as for selecting an article for a Single Article Menu Item. To create a link to the desired article:
  
  - Place the cursor at the point in the article where you want the linked article title to be inserted.
  - Click on the Article button to open the modal window.
  - Click on the title to select the desired article in the modal window. You can use the filters and search to help find the desired article.
  - A link with the article's title will be inserted at the current cursor location.
  - If needed, you can edit the link text.
- **Image**  
  This button provides an easy way to insert an image into an Article. Images may be inserted from the 'images' folder and may also be uploaded. When you click the Image button, a window pops up, as shown below:
  
  - **Directory.** The current directory on the host server. This is the 'images' directory under your home directory. Use the drop-down list box to select a subdirectory.
  - **Up.** Navigate to the parent directory. Note that the top directory for this function is 'images/stories'. You can not navigate to a higher directory.
  - **Insert.** Insert the selected image. The insert point will be the current cursor position. You will see the image display inside the edit window.
  - **Cancel**. Cancel the operation and close the popup window. You can cancel also in clicking the X at right top corner.
  - **Thumbnail Browse Area.** Click on an image thumbnail to select the image. Click on a folder icon to navigate to that subdirectory.
  - **Image URL.** Click on one of the image thumbnails and the URL for the image will be entered for you.
  - **Align.** Select the desired alignment (left or right) from the drop-down list box.
  - **Image Description.** Enter a description for the image.
  - **Image Title.** Enter a Title for this image. This displays when a User hovers the mouse on the image.
  - **Caption.** If checked, image title will display as a caption below the image.
  - **Choose Files.** Click this button to browse to an image file to upload from your local computer. A file dialog will open allowing you to select a file.
  - **Start Upload.** Once you have selected a file, press this button to upload the file to your 'images' folder. The thumbnail for the new image will now show in the thumbnail area.
- **Pagebreak**  
  This button allows you to insert a pagebreak inside an Article. A pagebreak allows for page navigation when the article is displayed on a layout. This is useful for long articles. When this button is pressed, a popup window is displayed as shown below:
  
  - **Page Title**. Enter the title to display for the new page (for example, 'Page 2').
  - **Table of Contents Alias**. Optional field to display in the table of contents for this page. In a multi-page article, the CMS displays a 'table of contents' for the page that allows the user to select any page. If this field is blank, the Page Title will be used. If you want a different title in the table of contents, enter it here.
  - **Insert Page Break.** Click this button to insert the pagebreak with the entered fields. The Pagebreak will display as a gray dashed line across the Article. Note that a pagebreak cannot be edited. If you need to change a field in the pagebreak, click on the Article just past the pagebreak, press Backspace until the pagebreak is deleted, then insert a new pagebreak with the desired information.
  - **Read more...** This button inserts a 'Read more...' break in the Article. This shows as a red dotted line across the Article. If an Article has a 'Read more...' break, only the text before the break, called the Into Text, will initially display, along with a 'Read more...' link. If the User clicks this link, either the entire Article or just the part after the 'Read more...' link is displayed. This depends on the setting of the 'Intro Text' parameters for the Article and in the Global Configuration. The 'Read more...' break allows you to save space on pages by just showing the Intro Text. Note that the 'Read more...' break only shows in the Front Page, Section, and Category Blog layouts. If you want to insert breaks for an Article shown in an Article Layout, use the Page Break button.

## Publishing Options

**Note: This slider can be hidden by users with Admin Permission for the article manager.**

This section allows you to enter parameters for this Article. These entries are optional. The CMS automatically creates default entries for these values.

- **Created by**  
  Name of the User who created this item. This will default to the currently logged-in user. If you want to change this to a different user, click the Select User button to select a different user.
- **Created By Alias**  
  This optional field allows you to enter in an alias for this Author for this Article. This allows you to display a different Author name for this Article.
- **Created Date**  
  This field defaults to the current time when the Article was created. You can enter in a different date and time or click on the calendar icon to find the desired date.
- **Start Publishing**  
  Date and time to start publishing. Use this field if you want to enter content ahead of time and then have it published automatically at a future time.
- **Finish Publishing**  
  Date and time to finish publishing. Use this field if you want to have content automatically changed to Unpublished state at a future time (for example, when it is no longer applicable).
- **>Modified By**  
  Displays who last edited the article. Note this field is non-editable.
- **Modified Date**  
  Date and time the article was last modified on. Note this field is non-editable.
- **Revision**  
  How many times the article has been edited. Note this field is non-editable.
- **Hits**  
  The number of times an item has been viewed.

## Basic Options

- **Alternative Layout**  
  If you have defined one or more alternative layouts for the Single Article menu item, you can select the layout to use for this article. See Layout Overrides for more information about alternative layouts.
- **Image**  
  Click the Select button to choose an image that will be displayed with the category. This will open a modal window that allows you to select an image from your images folder. See [Editor Buttons](#Editor_Buttons) for more information on working with images. After you have selected an image, you can hover the mouse on the "Preview" text to see a preview of the image.
- **Note**  
  Item note. This is normally for the site administrator's use (for example, to document information about this item) and does not show in the front end of the site.

## Metadata Options

This section allows you to enter Metadata Information for this Article. Metadata is information about the Article that is not displayed but is available to Search Engines and other systems to classify the Article. This gives you more control over how the content will be analyzed by these programs. All of these entries are optional. Metadata is shown inside HTML meta elements. The entry screen is shown below:

- **Meta Description**  
  An optional paragraph to be used as the description of the page in the HTML output. This will generally display in the results of search engines. If entered, this creates an HTML meta element with a name attribute of "description" and a content attribute equal to the entered text.
- **Meta Keywords**  
  Optional entry for keywords. Must be entered separated by commas (for example, "cats, dogs, pets") and may be entered in upper or lower case. (For example, "CATS" will match "cats" or "Cats"). Keywords can be used in several ways:
  
  1. To help Search Engines and other systems classify the content of the Article.
  2. In combination with Banner tags, to display specific Banners based on the Article content. For example, say you have one Banner with an ad for dog products and another Banner for cat products. You can have your dog Banner display when a User is viewing a dog-related Article and your cat Banner display for a cat-related Article. To do this, you would:
     1. Add the keywords 'dog' and 'cat' to the appropriate Articles.
     2. Add the Tags 'dog' and 'cat' to the appropriate Banners in the Banner Manager New/Edit screen.
     3. Set the Banner module Parameter 'Search By Tags' to 'Yes in the Banner Module Edit screen.
  3. For articles only, in combination with the Related Articles module, to display Articles that share at least one keyword in common. For example, if the current Article displayed has the keywords "cats, dogs, monkeys", any other Articles with at least one of these keywords will show in the Related Articles module.
- **Author**  
  Optional entry for an Author name within the metadata. If entered, this creates an HTML meta element with the name attribute of "author" and the content attribute as entered here.
- **Robots**  
  The instructions for web "robots" that browse to this page.
  
  - *Use Global:* Use the value set in the Component→Options for this component.
  - *Index, Follow:* Index this page and follow the links on this page.
  - *No index, Follow:* Do not index this page, but still follow the links on the page. For example, you might do this for a site map page where you want the links to be indexed but you don't want this page to show in search engines.
  - *Index, No follow:* Index this page, but do not follow any links on the page. For example, you might want to do this for an events calendar, where you want the page to show in search engines but you do not want to index each event.
  - *No index, no follow:* Do not index this page or follow any links on the page.

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Save**  
  Saves the content category and stays in the current screen.
- **Save & Close**  
  Saves the content category and closes the current screen.
- **Save & New**  
  Saves the content category and keeps the editing screen open and ready to create another content category.
- **Cancel/Close**  
  Closes the current screen and returns to the previous screen without saving any modifications you may have made.
- **Help**  
  Opens this help screen.

<a id="related-info"></a>

## Related Information

- To work with existing Categories: Category Manager
- To work with Articles: Article Manager
- To create a page showing Articles for a Category in a Blog layout: Menu Item Manager - New/Edit - Category Blog Layout
- To create a page showing Articles for a Category in a List layout: Menu Item Manager - New/Edit - Category List Layout

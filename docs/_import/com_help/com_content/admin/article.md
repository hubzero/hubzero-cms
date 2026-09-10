<!--
status: imported
source: core/components/com_content/admin/help/en-GB/article.phtml
imported: 2026-09-09
-->
# Article Manager: Edit Article

- [Overview](#overview)
- [Toolbar](#toolbar)
- [Details](#details)
- [Publishing](#publishing)

<a id="overview"></a>

## Description

This is the back-end screen where you can add and edit Articles. The same screen is used for adding a new Article and editing an existing Article. You can also select the Section and Category for an Article and indicate whether or not it is Published and if it is selected to appear on the Front Page.

The Article's content is edited using the default editor selected in the User Manager - New/Edit. The default editor is called CKEditor.

A number of Parameters can be set for the Article. Metadata can also be entered.

<a id="column-headers"></a>

## Column Headers

## Edit Article

Enter the heading information for the Article.

- **Title.** The Title for this item. This may or may not display on the page, depending on the parameter values you choose.
- **Alias**. The internal name of the item, also used in the URL when SEF is activated. Normally, you can leave this blank and the CMS will fill in a default value. The default value is the Title or Name in lower case and with dashes instead of spaces. You may enter the Alias manually. The Alias should consist of lowercase letters and hyphens (-). No blank spaces or underscores are allowed. Non-Latin characters can be allowed in the alias if you set the Unicode Aliases option to Yes in Global Configuration. If this option is set to No and the title includes non-Latin characters, the Alias will default to the current date and time (for example "2012-12-31-17-54-38").
- **Category.** Select the Category for this Article from the drop-down list box.
- **Status.** The published status of this item.
  - *Published:* Item is visible in the front end of the site.
  - *Unpublished:* Item is will not be visible to guests in the front end of the site. It may be visible to logged in users who have edit state permission for the item.
  - *Archived:* Item will no longer show on blog or list menu items.
  - *Trashed:* Item is deleted from the site but still in the database. It can be permanently deleted from the database with the Empty Trash function in Article Manager.
- **Access.** Select the viewing access level for this item from the list box. The access levels that display will depend on the what has been set up for this site in Users→Access Levels. Note that access levels are separate from ACL permissions. Access levels control what a user can see. ACL permissions control what actions a user can perform.
- **Permissions.** Click the Set Permissions button to navigate to the bottom of the screen, where you can set ACL permissions for this article. See [Article Permissions](#Article_Permissions).
- **Featured.** Yes/No. Select Yes if item will be shown in the Featured menu item. Select No otherwise.
- **Language.** Select the language for this item. If you are not using the multi-language feature, keep the default of 'All'.
- **ID**. This is a unique identification number for this item assigned automatically by the CMS. It is used to identify the item internally, and you cannot change this number. When creating a new item, this field displays 0 until you save the new entry, at which point a new ID is assigned to it.

## Article Text

This is where you enter the contents of the article. The editor you use depends on the settings for your site and your user. Hubzero core includes four editor options: CKEditor, TinyMCE (the default), Code Mirror, and None. Additional editors can be installed as extension plugins if desired (see the Extensions direction category of editors for a list).

### TinyMCE Editor

TinyMCE is the default editor for both front-end and back-end users. TinyMCE is a WYSIWYG (what you see is what you get) editor that allows users a familiar word-processing interface to use when editing Articles and other content. TinyMCE can be configured with three different sets of toolbar buttons: advanced, simple, and extended. This is set as an option in the Plugin Manager for the 'Editor - TinyMCE' plugin.

**Advanced Toolbar**

The advanced toolbar provides a three-row toolbar. This is the default setting. The 3-row toolbar below provides many standard editing commands, as follows:

- **Top Row.**
  - Buttons in the upper left allow you to make text bold, italic, underlined, or strikethrough. Next to that are buttons for align left, right, center, and full.
  - Styles. Caption and System Pagebreak styles can be set. Highlight the desired text and select the style. This will allow this text to be formatted based on CSS rules.
  - Format. Select pre-defined formats for Paragraph, Address, Heading1, and so on.
- **Second Row.**
  - Unordered List, Ordered list, Outdent (move left) and Indent (indent right).
  - Undo (Ctrl+Z) and Re-do (Ctrl+Y).
  - Insert/Edit Link. To insert or edit a link, select the linked text and press this button. A popup dialog displays that lets you enter details about the link.
  - Unlink. To remove a link, highlight the linked text and press this button.
  - Insert/Edit Anchor. An anchor is a bookmark inside an article that lets you link directly to that point in the article. To insert an anchor, move the cursor to the desired location within the article and click this button. A window will display. Enter the name of the Anchor and press Insert. A small anchor icon will show in the location of the anchor. You can edit the name of the anchor by clicking on it and pressing this button. You can delete the anchor just by selecting it and pressing the Delete key.
  - Insert/Edit Image. To insert and image, place the cursor in the desired location and press this button. A popup dialog displays that lets you enter in the Image URL and other information about how the image will display.
  - Cleanup Messy Code. This button allows you to clean up HTML code, perhaps from HTML text that you copied in from another source.
  - About TinyMCE editor. Shows the TinyMCE version.
  - Edit HTML Source. A popup displays showing the HTML source code, allowing you to edit the HTML source code.
- **Third Row.**
  - Insert Horizontal Ruler.
  - Remove Formatting.
  - Toggle Guidelines/Invisible elements.
  - Subscript, Superscript, Insert Custom Character

**Simple Toolbar**

The simple toolbar provides one row of buttons as shown below.

- **First Row.**
  - Buttons allow you to make text bold, italic, underlined, or strikethrough.
  - Undo (Ctrl+Z) and Re-do (Ctrl+Y).
  - Cleanup Messy Code. This button allows you to clean up HTML code, perhaps from HTML text that you copied in from another source.
  - Unordered list, Ordered list.

The extended toolbar provides the most extensive editing options.

This option provides all of the same buttons as documented in the Advanced Toolbar above. In addition, the following options are available:

- **First Row.**
  - Font Family. Select the desired font.
  - Font Size. Select the desired font size.
- **Second Row.**
  - Find and Find/Replace.
  - Insert Date or Time.
  - Select Text Color or Background Color.
  - Toggle Full Screen Mode.
- **Third Row.**
  - Insert New Table, Table Row Properties, Table Cell Properties, Insert Row Before, Insert Row After, Delete Row, Insert Column Before, Insert Column After, Delete Column, Split Merged Table Cells, Merge Table Cells.
  - Insert Emotions.
  - Insert Embedded Media. To insert embedded media (such as Flash), place the cursor at the desired location and press this button. A popup dialog will display that allows you to enter the Type, File or URL, and other information about the media.
  - Insert horizontal line.
  - Direction Left to Right and Direction Right to Left. These buttons allow you to enter or change the text direction, for example for languages that read right to left.
- **Fourth Row.**
  - Cut, Copy, Paste, Paste as Plain Text, Paste from Word. Often when copying and pasting text from other sources, such as PDF files, Word documents, or web pages, the selected text contains formatting information that is not needed or wanted. Using the Paste as Plain Text will strip out all formatting from the text. Paste as Word tries to preserve some of the formatting while stripping out unnecessary formatting.
  - Select All.
  - Insert New Layer, Move Forward, Move Backware, Toggle Absolute Position. For working with layered items.
  - Edit CSS Style. A popup dialog box displays that allows you to enter CSS style information for the selected text.
  - Citation, Abbreviation, Acronym, Insertion, Deletion, Insert/Edit Attributes.
  - Show/Hide Visual Control Characters (like paragraph endings).
  - Show/Hide Block Elements.
  - Insert Non-Breaking Space Character.
  - Block Quote.
  - Insert Predefined Template Content.

### Code Mirror Editor

The CodeMirror editor is designed to make it easy to enter HTML code in an article or description. CodeMirror supports syntax highlighting and auto-completion, as shown in this screenshot.

CodeMirror offers some of the same advantages of using No Editor, but makes it somewhat easier to work with raw HTML code.

### No Editor

If 'No editor' is selected for a User, then a simple text editor displays. This allows you to enter in raw, unformatted HTML. You can use the toolbar 'Preview' button to preview how the HTML will display.

Note that the 'No Editor' option can be useful if you are entering in 'boilerplate' or custom HTML, for example to create a PayPal link. TinyMCE automatically re-formats and strips some HTML when a file is saved. This can cause complex HTML to not work correctly.

If this happens, you can temporarily change the editor to 'No Editor' and create the desired content. Note that if you wish to edit this content in the future, you should be careful to change your editor to 'No Editor'. Otherwise, if you open and save the content with TinyMCE, you may lose your custom HTML.

### Editor Buttons

Five buttons are located just below the edit window, as shown below:

**Article.** This button opens a modal window that allows you to easily create a link to any article on the current site. The link is created using the article's title as the link text. The modal window is the same as for selecting an article for a Single Article Menu Item. To create a link to the desired article:

- Place the cursor at the point in the article where you want the linked article title to be inserted.
- Click on the Article button to open the modal window.
- Click on the title to select the desired article in the modal window. You can use the filters and search to help find the desired article.
- A link with the article's title will be inserted at the current cursor location.
- If needed, you can edit the link text.

**Image.** This button provides an easy way to insert an image into an Article. Images may be inserted from the 'images' folder and may also be uploaded. When you click the Image button, a window pops up, as shown below:

- **Directory.** The current directory on the host server. This is the 'images' directory under your hubzero home directory. Use the drop-down list box to select a subdirectory.
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

**Pagebreak.** This button allows you to insert a pagebreak inside an Article. A pagebreak allows for page navigation when the article is displayed on a layout. This is useful for long articles. When this button is pressed, a popup window is displayed as shown below:

- **Page Title**. Enter the title to display for the new page (for example, 'Page 2').
- **Table of Contents Alias**. Optional field to display in the table of contents for this page. In a multi-page article, the CMS displays a 'table of contents' for the page that allows the user to select any page. If this field is blank, the Page Title will be used. If you want a different title in the table of contents, enter it here.
- **Insert Page Break.** Click this button to insert the pagebreak with the entered fields. The Pagebreak will display as a gray dashed line across the Article. Note that a pagebreak cannot be edited. If you need to change a field in the pagebreak, click on the Article just past the pagebreak, press Backspace until the pagebreak is deleted, then insert a new pagebreak with the desired information.
- **Read more...** This button inserts a 'Read more...' break in the Article. This shows as a red dotted line across the Article. If an Article has a 'Read more...' break, only the text before the break, called the Into Text, will initially display, along with a 'Read more...' link. If the User clicks this link, either the entire Article or just the part after the 'Read more...' link is displayed. This depends on the setting of the 'Intro Text' parameters for the Article and in the Global Configuration. The 'Read more...' break allows you to save space on pages by just showing the Intro Text. Note that the 'Read more...' break only shows in the Front Page, Section, and Category Blog layouts. If you want to insert breaks for an Article shown in an Article Layout, use the Page Break button.

**Toggle Editor.** If you are using the TinyMCE editor, a Toggle Editor button will show. This button allows you to toggle between the TinyMCE editor and No Editor.

## Publishing Options

**Note: This slider can be hidden by users with Admin Permission for the article manager.**

This section allows you to enter parameters for this Article, as shown below:

These entries are optional. The CMS automatically creates default entries for these values.

- **Created by**. Name of the User who created this item. This will default to the currently logged-in user. If you want to change this to a different user, click the Select User button to select a different user.
- **Created By Alias.** This optional field allows you to enter in an alias for this Author for this Article. This allows you to display a different Author name for this Article.
- **Created Date.** This field defaults to the current time when the Article was created. You can enter in a different date and time or click on the calendar icon to find the desired date.
- **Start Publishing.** Date and time to start publishing. Use this field if you want to enter content ahead of time and then have it published automatically at a future time.
- **Finish Publishing.** Date and time to finish publishing. Use this field if you want to have content automatically changed to Unpublished state at a future time (for example, when it is no longer applicable).
- **Modified By.** Displays who last edited the article. Note this field is non-editable.
- **Modified Date.** Date and time the article was last modified on. Note this field is non-editable.
- **Revision.** How many times the article has been edited. Note this field is non-editable.
- **Hits.** The number of times an item has been viewed.

## Article Options

**Note: This slider can be hidden by users with Admin Permission for the article manager.**

This is a set of options you can use to control how this article will show in the Featured or Category blog layout. The CMS allows you to set these options at the following levels:

1. In Article Manager→Options
2. When you set up a Category Blog or Featured Articles menu item.
3. When you create the article here.

When you create the blog menu item, you can set each of these options as follows:

- *Use Global:* Uses the setting from Article Manager→Options
- *Yes/No or Hide/Show:* Use the setting in that menu item.
- *Use Article Settings:* Use the setting set here for the specific article. This allows you to show different articles with different options in the same blog page. For example, one article might show the author and a different article might not show the author.

The following Article Options can be set:

- **Show Title.** (Use Global/Hide/Show). Whether or not to show the Article's Title.
- **Linked Titles.** (Use Global/No/Yes). If the Article's Title is shown, whether to show it as a link to the article.
- **Show Intro Text.** (Hide/Show) Hide or Show an Article's Intro Text when the 'Read more...' link is selected. Intro Text is the part of the Article before a 'Read more...' break. If this parameter is 'Show', when the User selects the 'Read more...' link, the entire article will display, including the Intro Text. If this parameter is 'Hide', when the User selects the 'Read more...' link, only the part of the Article after the 'Read more...' link will display.
- **Show Category.** (Use Global/Hide/Show). Whether or not to show the Article's Category.
- **Link Category.** (Use Global/No/Yes). If the Article's Category is shown, whether to show it as a link to a Category layout (list or blog) for that Category.
- **Show Parent.** (Use Global/Hide/Show). Whether or not to show the Article's Parent Category.
- **Link Parent.** (Use Global/No/Yes). If the Article's Parent Category is shown, whether to show it as a link to a Category layout (list or blog) for that Category.
- **Show Author.** (Use Global/Hide/Show) Whether to show the author of the Article.
- **Link Author.** (Use Global/No/Yes). If the Article's author is shown, whether to show it as a link to a Contact layout for that author. Note that the author must be set up as a Contact in Contact Manager: Edit.
- **Show Create Date.** (Use Global/Hide/Show). Whether or not to show the Article's create date.
- **Show Modify Date.** (Use Global/Hide/Show). Whether or not to show the Article's modify date.
- **Show Publish Date.** (Use Global/Hide/Show). Whether or not to show the Article's start publishing date.
- **Show Navigation.** (Use Global/Hide/Show). Whether or not to show a navigation link (for example, Next or Previous article) when you drill down to the article.
- **Show Icons.** (Use Global/Hide/Show). If set to Show, Print and Email will use icons instead of text.
- **Show Print Icon.** (Use Global/Hide/Show). Show or Hide the Print Article button.
- **Show Email Icon.** (Use Global/Hide/Show). Show or Hide the Email Article button.
- **Show Voting.** (Use Global/Hide/Show). Whether or not to show the a voting icon for the article.
- **Show Hits.** (Use Global/Hide/Show). Show or Hide the number of times the article has been hit (displayed by a user).
- **Show Unauthorised Links.** (Use Global/No/Yes). If Yes, the Intro Text for restricted articles will show. Clicking on the "Read More" link will require users to log in to view the full article content.
- **Positioning of the Links.** (Use Global/Above/Below). If Above, links are shown above the content. Otherwise they are shown below the content.
- **Read More Text.** Optional field that allows you to customize the text that shows for a "read more" link. The default wording for English is "Read more".
- **Alternative Layout.** If you have defined one or more alternative layouts for the Single Article menu item, you can select the layout to use for this article. See Layout Overrides for more information about alternative layouts.
- **Key Reference.** This is the reference that is used to pull the correct help information either from help files saved locally or on an online site like this page.

## Configure Edit Screen

**Note: This slider by default is only visible to those with Admin permissions for the Article Manager permissions.**

On some installations you may need to save the Content Options in order for these options to work.

- **Show Publishing Options.** (Use Global/No/Yes). If No, the Publishing Options slider in this screen (**Article Manager: Add/Edit Screen**) will not show. This means that back-end users will not be able to edit the fields Created by, Created by alias, Created Date, Start Publishing, or Finish Publishing. These fields will always be set to their default values.
- **Show Article Options.** (Use Global/No/Yes). If No, the Article Options slider in this screen (**Article Manager: Add/Edit Screen**) will not show. This means that back-end users will not be able to edit the fields in this slider. These fields will always be set to their default values.
- **Administrator Images and Links.** (Use Global/No/Yes). If Yes, the Images and Links slider will show in this screen (**Article Manager: Add/Edit Screen**).
- **Frontend Images and Links.** (Use Global/No/Yes). If Yes, the Images and Links fields will show in the Front End article editor screen. These fields allow users to optionally enter two images and three links in an easy-to-use form in the front end. When used with a single-article override, this can allow the site administrator to create a simple form for users to create standard article layouts.

## Images and Links

**Note: This slider can be hidden by a user with Admin permissions for the article manager.** This section lets you display images and links in your articles using standardized layouts.

- **Intro Image.** Click the Select button to select an image to be displayed in a fixed location in the Intro Text of an article. This will open a modal window that allows you to select an image from your images folder. See [Editor Buttons](#Editor_Buttons) for more information on working with images. After you have selected an image, you can hover the mouse on the "Preview" text to see a preview of the image.
- **Image Float.** (Use Global/Right/Left/None). Set the float attribute for this image.
- **Alt Text.** Set the alt attribute for the this image.
- **Caption.** Create a caption for the this image.

- **Full Article Image.** Click the Select button to select an image to be displayed in a fixed location in the Intro Text of an article. This will open a modal window that allows you to select an image from your images folder. See [Editor Buttons](#Editor_Buttons) for more information on working with images. After you have selected an image, you can hover the mouse on the "Preview" text to see a preview of the image.
- **Image Float.** (Use Global/Right/Left/None). Set the float attribute for this image.
- **Alt Text.** Enter optional alt attribute for the this image.
- **Caption.** Enter an optional caption for the this image.

- **Link A.** The URL for the first link to be displayed in a fixed location in the article text. This must be a full URL, not relative. For example, it normally would start with "http://".
- **Link A Text.** The text used for Link A. If blank, the URL will be displayed.
- **URL Target Window.** Sets the default value for the target for the first Link in the article. Choices are:
  - *Open in parent window:* Opens the in the main browser window, replacing the current article.
  - *Open in new window:* Opens the link in a new browser window.
  - *Open in pop up:* Opens the link in a pop-up browser window (without full navigation controls).
  - *Modal:* Opens the link in a modal pop-up window.

- **Link B.** The URL for the second link to be displayed in a fixed location in the article text. This must be a full URL, not relative. For example, For example, it normally would start with "http://".
- **Link B Text.** The text used for Link B. If blank, the URL will be displayed.
- **URL Target Window.** The target for the URL. Same options as Link A.

- **Link C** The URL for the third link to be displayed in a fixed location in the article text. This must be a full URL, not relative. For example, For example, it normally would start with "http://".
- **Link C Text** The text used for Link C. If blank, the URL will be displayed.
- **URL Target Window.** The target for the URL. Same options as Link A.

## Metadata Options

This section allows you to enter Metadata Information for this Article. Metadata is information about the Article that is not displayed but is available to Search Engines and other systems to classify the Article. This gives you more control over how the content will be analyzed by these programs. All of these entries are optional. Metadata is shown inside HTML meta elements. The entry screen is shown below:

- **Meta Description.** An optional paragraph to be used as the description of the page in the HTML output. This will generally display in the results of search engines. If entered, this creates an HTML meta element with a name attribute of "description" and a content attribute equal to the entered text.
- **Meta Keywords.** Optional entry for keywords. Must be entered separated by commas (for example, "cats, dogs, pets") and may be entered in upper or lower case. (For example, "CATS" will match "cats" or "Cats"). Keywords can be used in several ways:

1. To help Search Engines and other systems classify the content of the Article.
2. For articles only, in combination with the Related Articles module, to display Articles that share at least one keyword in common. For example, if the current Article displayed has the keywords "cats, dogs, monkeys", any other Articles with at least one of these keywords will show in the Related Articles module.

- **Robots.** The instructions for web "robots" that browse to this page.
  - *Use Global:* Use the value set in the Component→Options for this component.
  - *Index, Follow:* Index this page and follow the links on this page.
  - *No index, Follow:* Do not index this page, but still follow the links on the page. For example, you might do this for a site map page where you want the links to be indexed but you don't want this page to show in search engines.
  - *Index, No follow:* Index this page, but do not follow any links on the page. For example, you might want to do this for an events calendar, where you want the page to show in search engines but you do not want to index each event.
  - *No index, no follow:* Do not index this page or follow any links on the page.
- **Author.** Optional entry for an Author name within the metadata. If entered, this creates an HTML meta element with the name attribute of "author" and the content attribute as entered here.
- **Content Rights.** Optional entry that shows what rights others have to use the content in the article. If entered, this creates an HTML meta element with the name attribute of "rights" and the content attribute as entered here.
- **External Reference.** An optional reference used to link to external data sources. If entered, this creates an HTML meta element with a name attribute of "xreference" and a content attribute equal to the entered text.

## Article Permissions

The CMS lets you set permissions for articles at four levels, as follows.

1. Globally, using Global Configuration.
2. For all articles, using Article Manager→Options.
3. For all articles in a category, using Category Manager.
4. For an individual article, using this screen.

This is where you can enter permissions for this one article. To change the permissions for this article, do the following.

1. Select the Group by clicking its title.
2. Find the desired Action. Possible Actions are:
   - *Delete.* Users can delete this article.
   - *Edit.* Users can edit this article.
   - *Edit State.* User can change the published state and related information for this article.
3. Select the desired permission for the action you wish to change. Possible settings are:
   - *Inherited.* Inherited for users in this Group from the Global Configuration, Article Manager Options, or Category permissions.
   - *Allowed.* Allowed for users in this Group. Note that, if this action is Denied at one of the higher levels, the Allowed permission here will not take effect. A Denied setting cannot be overridden.
   - *Denied.* Denied for users in this Group.
4. Click Save. When the screen refreshes, the Calculated Setting column will show the effective permission for this Group and Action.

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Save**. Saves the article and stays in the current screen.
- **Save & Close**. Saves the article and closes the current screen.
- **Save & New**. Saves the article and keeps the editing screen open and ready to create another article.
- **Save as Copy**. Saves your changes to a copy of the current article. Does not affect the current article. This toolbar icon is not shown if you are creating a new article.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.
- **Help**. Opens this help screen.

<a id="tips"></a>

## Quick Tips

- The hierarchy of display parameters is as follows:
  1. *Parameters - Advanced* for the specific Article. A setting other than 'Use Global' here always controls the setting.
  2. *Parameters - Component* for the Menu Item. If the Parameters - Advanced above is 'Use Global' and this setting is not 'Use Global', then this controls the setting.
  3. *Global Configuration* settings in the Article Manager/Parameters section. Settings here only apply if both of the above are set to 'Use Global'.

- Example: The 'Title Linkable' setting in the Article's 'Parameters - Advanced' section is set to 'Use Global'. The Menu Item is an Article Layout, and 'Title Linkable' in the 'Parameters - Component' is 'No'. The Global Configuration 'Title Linkable' is set to 'Yes'. The result will be 'Yes', since the Menu Item overrides the Global Configuration.

- You can add images using either the TinyMCE Insert/Edit Image icon or the Image button below the edit area. For adding new images in an Article, it is easier to use the Image button (below the edit area). This is because it lets you browse to the image file and also lets you upload images. However, for editing an existing image, you need to use the TinyMCE icon. The Image button only supports adding new images.
- 'Read more...' breaks allow you to save space on the Front Page or on any blog layout page by showing just the first portion of an Article. 'Pagebreaks' allow you to provide multi-page navigation for long Articles. You can use both on one Article, if desired. For example, you could put a 'Read more...' break after the first paragraph of a multi-page article, and have Pagebreaks after each page. No page navigation would display on the Front Page until the User selects the 'Read more...' link. At that time, the Article's table of contents would display showing links to every page.
- You can insert a Module inside an Article by typing "{loadposition xxx}", where "xxx" is the position entered for the desired Module. Note that the position name must not conflict with a position used by your hub template. It can be any name (e.g., "mymoduleposition1") as long as it matches the position name typed in for the Module. The Menu Assignment for the Module must include the Menu Item where the Article is displayed, and the Plugin called "Content - Load Module" must be enabled (which it is by default). This feature allows you, for example, to insert a Custom HTML Module anywhere in an Article. See Module Manager - New/Edit for information about adding modules.

<a id="related"></a>

## Related Information

- To find and edit existing Articles: Article Manager
- To set options for the TinyMCE editor: TinyMCE Editor Plugin
- To set options for the CodeMirror editor: CodeMirror Editor Plugin

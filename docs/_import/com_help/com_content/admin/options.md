<!--
status: imported
source: core/components/com_content/admin/help/en-GB/options.phtml
imported: 2026-09-09
-->
# Article Manager: Options

- [Overview](#overview)
- [1 How To Access](#How_To_Access)
- [2 Description](#Description)
- [3 Screenshot](#Screenshot)
- [4 Toolbar](#Toolbar)
- [5 Form Fields](#Form_Fields)
  - [5.1 Articles Options](#Articles_Options)
  - [5.2 Editing Layout Options](#Editing_Layout_Options)
  - [5.3 Category Options](#Category_Options)
  - [5.4 Categories Options](#Categories_Options)
  - [5.5 Blog / Featured Layout Options](#Blog_.2F_Featured_Layout_Options)
  - [5.6 List Layout Options](#List_Layout_Options)
  - [5.7 Shared Options](#Shared_Options)
  - [5.8 Integration Options](#Integration_Options)
  - [5.9 Permissions](#Permissions)
- [6 Quick Tips](#Quick_Tips)
- [7 Related Information](#Related_Information)

<a id="overview"></a>

## Description

Global default settings for menu types which display articles in different layouts and formats.

## How To Access

To access this screen:

- Navigate to the Article Manager screen, then
- Click the **Options** toolbar button. This opens the Article Manager Options screen in a modal window.

## Description

This screen is where you can set global defaults for menu items that display articles. These default values will be used when you select "Use Global" for an option in an Articles menu item. For example, if you normally want to show the Create Date for an article in your Articles menu items, then set that option to "Show" here and it will be the default value. You do *not* need to set any of these options. Your site will work with the default settings.

Article options are divided into nine groups as follows. Each controls the appearance of the listed type of layout.

- **Articles:** Articles in Single Article or blog layouts.
- **Editing Layout:** Front-end article add/edit layout.
- **Category:** Single Category layouts -- for example, when you click on a category to view the articles it contains.
- **Categories:** Articles Categories layout.
- **Blog/Featured Layouts:** Featured and Category Blog Layout.
- **List Layouts:** Category List Layout.
- **Shared Options:** Shared options for list, blog, and featured layouts.
- **Integration:** Feed Link and Read More options.
- **Permissions:** ALC permissions for actions on articles.

## Toolbar

At the top right of the Options modal window you will see the toolbar.

The functions are:

- **Save.** Saves any changed options and keeps the window open. Use this, for example, to see the effect of Permissions changes you have made.
- **Save & Close.** Saves any changed options and closes the modal window.
- **Cancel.** Closes the modal window without saving any changes.

## Form Fields

## Articles Options

Article Options control how an article will show in a Single Article menu item or, in some cases, in a Blog menu item.

- **Choose a layout.** If you have defined any alternative layouts for articles, you may choose one of them from the list to be the default value for Single Article menu items.
- **Show Title.** (Hide/Show). Hide or Show the article title.
- **Linked Titles.** (Use Global/No/Yes). If the Article's Title is shown, whether to show it as a link to the article.
- **Show Intro Text.** (Hide/Show) Hide or Show an Article's Intro Text when the 'Read more...' link is selected. Intro Text is the part of the Article before a 'Read more...' break. If this parameter is 'Show', when the User selects the 'Read more...' link, the entire article will display, including the Intro Text. If this parameter is 'Hide', when the User selects the 'Read more...' link, only the part of the Article after the 'Read more...' link will display.
- **Show Category.** (Use Global/Hide/Show). Whether or not to show the Article's Category.
- **Link Category.** (No/Yes). If the category title is shown, whether or not to show it as a link to a Single Category menu item for the category. Note that you can set this to be either a blog or list layout with the "Choose a layout" option in the Category Options.
- **Show Parent.** (Hide/Show). Hide or Show the title of the parent category of the article's category. This is provided for backward compatibility to the role of Sections.
- **Link Parent.** (No/Yes). If the parent category is shown, whether or not to show it as a link to a Single Category menu item for the category. Note that you can set this to be either a blog or list layout with the "Choose a layout" option in the Category Options.
- **Show Author.** (Use Global/Hide/Show) Whether to show the author of the Article.
- **Link Author.** (No/Yes). If the author is shown, whether or not to show it as a link to the user. Note that this will only show as a link if there is a Contact associated with this user. Also, a link will not show if there is an Author Alias value for the article.
- **Show Create Date.** (Hide/Show). Hide or show the article's create date.
- **Show Modify Date.** (Hide/Show). Hide or show the date the article was last modified.
- **Show Publish Date.** (Hide/Show). Hide or show the article's published date.
- **Show Navigation.** (Hide/Show). Hide or Show a navigation link (e.g., Next, Previous) between articles.
- **Show Voting.** (Hide/Show). Hide or Show a voting button for the article. This allows users to vote for an article.
- **Show "Read More".** (Hide/Show) Whether or not to show the "Read More..." link to link from the part of the article before the "Read More..." break to the rest of the Article.
- **Show Title with Read More.** (Hide/Show) Whether or not to show the article title as part of the Read More link. If set to Show, the Read More link will be in the format "Read More: &amp;lt;article title>". If set to Hide, the Read More link will be "Read more...".
- **Read More Limit.** If the title is included with the Read More text, the maximum number of characters from the title to include. This can prevent the Read More text to become excessively long if the article has a very long title.
- **Show Icons.** (Hide/Show). Hide or Show icons for the print and email buttons. If Show, and if the print or email icons are set to show, these will show as icons instead of text links.
- **Show Print Icon.** (Hide/Show). Hide or Show a button or link to allow printing the article.
- **Show Email Icon.** (Hide/Show). Hide or Show a button or link to allow emailing the article.
- **Show Hits.** (Use Global/Hide/Show). Show or Hide the number of times the article has been hit (displayed by a user).
- **Show Unauthorised Links.** (Hide/Show). If set to Show, the Intro Text for restricted articles will show. Clicking on the "Read More" link will require users to log in to view the full article content. If set to Hide, articles that the user is not authorised to view (based on the viewing access level for the article) will not show.
- **Positioning of the Links.** (Above/Below). If there are links associated with this article, whether to show them above or below the article.

## Editing Layout Options

- **Show Publishing Options.** (No/Yes). If No, the Publishing Options slider in the Article Manager: Add/Edit Screen will not show. This means that back-end users will not be able to edit the fields Created by, Created by alias, Created Date, Start Publishing, or Finish Publishing. These fields will always be set to their default values.
- **Show Article Options.** (No/Yes). If No, the Article Options slider in the Article Manager: Add/Edit Screen will not show. This means that back-end users will not be able to edit the fields in this slider. These fields will always be set to their default values.
- **Frontend Images and Links.** (No/Yes). If Yes, the Images and Links fields will show in the Front End article editor screen. These fields allow users to optionally enter two images and three links in an easy-to-use form in the front end. When used with a single-article override, this can allow the site administrator to create a simple form for users to create standard article layouts.
- **Administrator Images and Links.** (No/Yes). If Yes, the Images and Links slider will show in the Article Manager: Add/Edit Screen.
- **URL A Target Window.** Sets the default value for the target for the first Link in the article. Choices are:
  - *Open in parent window:* Opens the in the main browser window, replacing the current article.
  - *Open in new window:* Opens the link in a new browser window.
  - *Open in pop up:* Opens the link in a pop-up browser window (without full navigation controls).
  - *Modal:* Opens the link in a modal pop-up window.
- **URL B Target Window.** Sets the default value for the target for the second Link in the article. Same options as URL A.
- **URL C Target Window.** Sets the default value for the target for the third Link in the article. Same options as URL A.
- **Intro Image Float.** (Right/Left/None). Sets the float attribute for an Intro Image selected in the Into Image field.
- **Full Text Image Float.** (Right/Left/None). Sets the float attribute for an Intro Image selected in the Into Image field.

## Category Options

Category Options control how articles will show when you drill down to a Category to view its articles.

- **Choose a layout.** (Blog/List/user defined). This lets you select the default layout to show when you click on a Category link. If you create an alternative layout for a category layout, you may select that as the default.
- **Category Title.** (Hide/Show) Hide or Show the title of the category.
- **Category Description.** (Hide/Show) Hide or Show the description for the category.
- **Category Image.** (Hide/Show) Hide or Show the category image.
- **Subcategory Levels.** (None/All/1-5). Categories can be created in a hierarchy. This lets you control how many levels of subcategories to show when showing a category view.
- **Empty Categories.** (Hide/Show) Hide or Show categories that don't contain any articles or subcategories.
- **No Articles Message.** (Hide/Show) If Show, and if Empty Categories is Show, a message "There are no articles in this category" will show if a category contains no articles.
- **Subcategories Descriptions.** (Hide/Show) Hide or Show the descriptions for subcategories that are shown.
- **# Articles in Category.** (Hide/Show) Hide or Show a count of the total number of articles in each category.

## Categories Options

Categories Options control the display of the List All Categories menu item.

- **Top Level Category Description.** (Hide/Show). Hide or Show the description of the top-level category.
- **Subcategory Levels.** (All/1-5). How many levels in the hierarchy to show.
- **Empty Categories.** (Hide/Show). Hide or Show categories that contain no articles and no subcategories.
- **Subcategories Descriptions.** (Hide/Show). Hide or Show the description of each subcategory.
- **# Articles in Category.** (Hide/Show). Hide or Show a count of the total number of articles in each category.

## Blog / Featured Layout Options

These options control the layout of the Category Blog and Featured Articles layouts.

- **# Leading Articles.** Number of Articles to show using the full width of the main display area. "0" means that no Articles will show when using the full width. If an Article has a "Read more..." break, only the part of the text before the break (the Intro text) will display.
- **# Intro Articles.** Determines the number of Articles to display after the leading Article. These Articles will display in the number of columns set in the Columns parameter below. If an Article has a "Read more..." break, only the text before the break (Intro text) will display, followed by a "Read more..." link. The order order in which to display the articles is determined by the Category Order and Article Order parameters below.
- **# Columns.** The number of columns to use in the Intro Text area. This is normally between 1 and 3 (depending on the template you are using). If 1 is used, the Into Text Articles will display using the full width of the display area, just like the Leading Articles.
- **# Links.** The number of Links to display in the 'Links' area of the page. These links allow a User to link to additional Articles, if there are more Articles than can fit on the first page of the Blog Layout.
- **Multi Column Order.** In multi-column blog layouts, whether to order articles Down the columns or Across the columns.
  - *Down:* Order articles going down the first column and then over to the next column, for example:

- - | article 1 | 1 (continued) |
    |---|---|
    | article 2 | article 4 |
    | article 3 | article 5 |

- - *Across:* Order articles going across the columns and then back to the first column, for example:

- - | article 1 | 1 (continued) |
    |---|---|
    | article 2 | article 3 |
    | article 4 | article 5 |

- **Include Subcategories.** (None/All/1-5). If None, only articles from the current category will show. If 1-5, all articles from the current category and subcategories up to and including that level will show. If All, all articles from the current category and all subcategories will show.

## List Layout Options

These options control the appearance of the Category List layout.

- **Display Select.** (Hide/Show) Whether to hide or show the Display # control that allows the user to select the number of items to show in the list. If there are more items than this number, you can use the page navigation buttons (Start, Prev, Next, End, and page numbers) to navigate between pages. Note that if you have a large number of items, it may be helpful to use the Filter options, located above the column headings, to limit which items display.
- **Filter Field.** The Filter Field creates a text field where a user can enter a field to be used to filter the articles shown in the list.

- The possible options for this (in the back-end menu item edit) are shown below.
  
  - *Hide:* Don't show a filter field.
  - *Title:* Filter on article title.
  - *Author:* Filter on the author's name.
  - *Hits:* Filter on the number of article hits.

- **Table Headings.** (Hide/Show) Table Headings show a heading above the article list. If set to *Show*, this heading will show about the list. Otherwise the list will show with no headings.
- **Show Date.** This option allows you to show a date in the list. The options are as follows.

- - *Hide:* Don't show any date.
  - *Created:* Show the created date.
  - *Modified:* Show the date of the last modification.
  - *Created:* Show the start publishing date.

- **Date Format.** Optional format string to control the format of the date (if shown). If left blank, the date will use the DATE_FORMAT_LC1 format from the language file (for example, "D M Y" for "31 December 2012" or "m-d-y" for "12-31-12"). See [PHP Date Documentation](http://www.php.net/manual/en/function.date.php) for more information.
- **Show Hits in List.** (Hide/Show) Whether to hide or show the number of hits for this item.
- **Show Author in List.** (Hide/Show) Whether to hide or show the name of the author.

## Shared Options

These options are shared by a number of the articles menu item types.

- **Category Order.** Order of Categories in this Layout. The following options are available.
  - *No Order:* Articles are ordered only by the Article Order, without regard to Category.
  - *Title Alphabetical:* Categories are displayed in alphabetical order (A to Z)
  - *Title Reverse Alphabetical:* Categories are displayed in reverse alphabetical order (Z to A)
  - *Category Manager Order:* Categories are ordered according to the Order column entered in the Category Manager.
- **Article Order.** Order of articles in this Layout. The following options are available.
  - *Most recent first:* Articles are displayed starting with the most recent and ending with the oldest.
  - *Oldest first:* Articles are displayed starting with the oldest and ending with the most recent.
  - *Title Alphabetical:* Articles are displayed by Title in alphabetical order (A to Z)
  - *Title Reverse Alphabetical:* Articles are displayed by Title in reverse alphabetical order (Z to A)
  - *Author Alphabetical:* Articles are displayed by Author in alphabetical order (A to Z)
  - *Author Reverse Alphabetical:* Articles are displayed by Author in reverse alphabetical order (Z to A)
  - *Most Hits:* Articles are displayed by the number of hits, starting with the one with the most hits and ending with the one with the least hits
  - *Least Hits:* Articles are displayed by the number of hits, starting with the one with the least hits and ending with the one with the most hits
  - *Ordering:* Articles are ordered according to the Order column entered in the Article Manager.
- **Date for Ordering.** The date used when articles are sorted by date. The following options are available.
  - *Created:* Use the article created date.
  - *Modified:* Use the article modified date.
  - *Published:* Use the article start publishing date.
- **Pagination.** Hide or Show Pagination support. Pagination provides page links at the bottom of the page that allow the User to navigate to additional pages. These are needed if the Articles will not fit on one page. The following options are available.
  - *Auto:* Pagination links shown if needed.
  - *Show:* Pagination links shown if needed.
  - *Hide:* Pagination links not shown. Note: In this case, Users will not be able to navigate to additional pages.
- **Pagination Results.** Hide or Show the current page number and total pages (e.g., "Page 1 of 2") at the bottom of each page.

## Integration Options

These options control the display of news feeds.

- **Show Feed Link.** (Hide/Show) Whether to Hide or Show a link to a news feed (RSS Feed). If set to Show, a Feed Link will show up as a feed icon in the address bar of most modern browsers).
- **For each feed item show.** (Intro Text/Full Text) If Intro Text, only the article's intro text will show in the feed. Otherwise, the entire text of the article will show.
- **Show "Read More".** (Hide/Show) Hide or Show a "Read more" link in the newsfeed.

## Permissions

This section lets you set up the default ACL permissions for all articles in all categories. These permissions may be overridden for specific categories and for specific articles.

To change the permissions, do the following.

1. Select the Group by clicking its title.
2. Find the desired Action. Possible Actions are:
   - *Configure.* Users can access this screen (**Article Manager→Options**).
   - *Access Administrative Interface.* Users can access the Article Manager.
   - *Create.* Users can create new articles.
   - *Delete.* Users can delete articles.
   - *Edit.* Users can edit the contents of articles.
   - *Edit State.* User can change the published state and related information for articles.
   - *Edit Own.* User can edit the content or published state for articles that this user created.
3. Select the desired permission for the action you wish to change. Possible settings are:
   - *Inherited.* Inherited for users in this Group from Global Configuration.
   - *Allowed.* Allowed for users in this Group. Note that, if this action is Denied at one of the higher levels, the Allowed permission here will not take effect. A Denied setting cannot be overridden.
   - *Denied.* Denied for users in this Group.
4. Click Save. When the screen refreshes, the Calculated Setting column will show the effective permission for this Group and Action.

## Quick Tips

- If you are a beginning user, you can just keep the default values here until you learn more about using global options.
- If you are an advanced user, you can save time by creating good default values here. When you set up menu items and create articles, you will be able to accept the default values for most options.
- All values set here can be overridden at the menu item, category, or article level.

## Related Information

- To create and edit articles and article categories: Article Manager
- For more information about the ACL system: ACL Tutorial

<!--
status: imported
source: core/components/com_modules/admin/help/en-GB/module.phtml
imported: 2026-09-09
-->
# Module Manager: Edit

## How to Access

Go to the Module Manager by:

- Clicking on the Module Manager icon in the Control Panel
- Top drop down menu, **Extensions** → **Module Manager**

After that, click on the '**New'** button in the toolbar to create a new Module Item, or select a Module by double clicking the Module's **Title** or check the 'check box' and click on the 'Edit' button.

## Description

The Extensions Module Manager Edit (New) allows editing an existing Module or creating a new Module by Module type.

## Screenshot

Module 'Edit' or create 'New' Module screens are dependent on Module Type. For the right column 'Basic' or 'Advanced' Options, please see:

- A **[Specific Module Type](#Related_Information)** Help screen, links with descriptions are listed below.

## Details

The screenshot above shows the editing view of a 'Archived Article' Module. For **[Specific Module Type](#Related_Information)** Help screen, links with descriptions are listed below.

## Details of Module

- **Title.** The Title for this item. This may or may not display on the page, depending on the parameter values you choose.

- **Show Title.** (Use Show or Hide). Whether or not to show the Module's Title.

- **Category.** Select the Category for this Article from the drop-down list box.

- **Position.** Click button to open a modal pop up window to select Module position by available positions in a Template(s) installed.

- - **Filter**. Filter positions by title of position.
  - **Select Status**. Filter positions by status, enabled or disabled.
  - **Type**. Filter positions by type, User(User created) or Template.
  - **Template**. Filter positions by title of template installed.
  - **Click to sort**. Click column heading, **Title** or **Templates** to sort by the column.

- **Status.** The published status of this item.
  - *Published:* Item is visible in the front end of the site.
  - *Unpublished:* Item is will not be visible to guests in the front end of the site. It may be visible to logged in users who have edit state permission for the item.
  - *Archived:* Item will no longer show on blog or list menu items.
  - *Trashed:* Item is deleted from the site but still in the database. It can be permanently deleted from the database with the Empty Trash function in Article Manager.

- **Access.** Select the viewing access level for this item from the list box. The access levels that display will depend on the what has been set up for this site in Users→Access Levels. Note that access levels are separate from ACL permissions. Access levels control what a user can see. ACL permissions control what actions a user can perform.

- **Ordering.** Select the ordering of the module as compared to other modules in the same position. See **Position**.

- **Start Publishing.** Date and time to start publishing. Use this field if you want to enter content ahead of time and then have it published automatically at a future time.

- **Finish Publishing.** Date and time to finish publishing. Use this field if you want to have content automatically changed to Unpublished state at a future time (for example, when it is no longer applicable).

- **Language.** Select the language for this item. If you are not using the multi-language feature, keep the default of 'All'.

- **Note**. Item note. This is normally for the site administrator's use (for example, to document information about this item) and does not show in the front end of the site.

- **ID**. This is a unique identification number for this item assigned automatically by the CMS. It is used to identify the item internally, and you cannot change this number. When creating a new item, this field displays 0 until you save the new entry, at which point a new ID is assigned to it.

- **Module Description.** A summary of the the Module type with a description.

## Menu assignment

This section contains all the menu items configured in your website. To apply the current style to a menu item's corresponding web page, check the box next to the menu item. You can press the *Toggle Selection* button to invert the menu item selections.

> **Note:** If a checkbox is grayed out and cannot be checked then it could be because the menu item is in use by another user. You can see if this is the case by going to the menu manager screen for the menu concerned. If there is a padlock symbol next to the menu item then it is currently in use by another user.

## Additional Options

Additional Options, such as **Basic** and **Advanced** can be found on the [specific Module Type](#Related_Information) Help screen. Please note, installed Extensions may contain more parameters in Additional Options. Please refer to the specific Extension Module's information provided by the Extension developer.

## Create New

When creating a new Module, you will be presented with a modal pop up window. Choose the module type by clicking on the module name to be taken to the 'edit' details screen.

## Toolbar

## Edit Module

For existing Modules, edit functions. At the top right you will see the toolbar. The functions are:

- **Save**. Saves the module and stays in the current screen.
- **Save & Close**. Saves the module and closes the current screen.
- **Save & New**. Saves the module and keeps the editing screen open and ready to create another module.
- **Save as Copy**. Saves your changes to a copy of the current module. Does not affect the current module. This toolbar icon is not shown if you are creating a new module.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.
- **Help**. Opens this help screen.

## New Module

For creating a New Module, new functions. At the top right you will see the toolbar. The functions are:

- **Save**. Saves the module and stays in the current screen.
- **Save & Close**. Saves the module and closes the current screen.
- **Save & New**. Saves the module and keeps the editing screen open and ready to create another module.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.
- **Help**. Opens this help screen.

## Related Information

| Related Help Screens | Description |
|---|---|
| Extensions Module Manager Articles Archive | This module shows a list of the calendar months containing archived articles. After you have changed the status of an article to archived, this list will be automatically generated. |
| Extensions Module Manager Articles Categories | This module displays a list of articles from one or more categories. |
| Extensions Module Manager Articles Category | This module displays a list of articles from one or more categories. |
| Extensions Module Manager Articles Newsflash | This Module shows one or more Articles from one Category each time the page is refreshed. One random Article or a list of Articles can be displayed. |
| Extensions Module Manager Articles Related | This Module shows a list of Articles that are related to the current Article being viewed by the user (for example, a Article Layout or a Blog or List layout where the user has clicked on an Article link). Articles are considered to be related to each other if they share at least one Keyword in the Article's Metadata Information. Article Keywords are entered in the Metadata Information section of the Article Manager - New/Edit screen. |
| Extensions Module Manager Banners | This Module allows you to show active Banners from the Banner Component created in the Banner Manager screen. |
| Extensions Module Manager Breadcrumbs | This Module shows a set of navigation links that illustrates where you are inside the web site and allows you to navigate back. |
| Extensions Module Manager Custom HTML | This allows you to create a Module that contains any valid HTML code. There are many cases where you might want to put free-form HTML inside a web page. For example, you might want to create an HTML Image Map or you might want to copy HTML code from PayPal, Amazon, or some other site.The Custom HTML Module allows you to create a self-contained HTML unit and then put it in any valid location on a page. |
| Extensions Module Manager Feed Display | This Module shows an RSS News Feed from a website. This Module is not related to the News Feeds Component or the News Feeds Layouts in the Newsfeed Manager screen but is an alternative that allows a feed to display in a Module position. |
| Extensions Module Manager Footer | This Module displays the web site copyright and license information. |
| Extensions Module Manager Language Switcher | This module allows you to switch between available Content languages. Selecting a language will take you to the home page for that language. The plugin 'System - Language Filter' has to be enabled for this to work. Please see [this tutorial](https://help.hubzero.org/proxy/index.php?option=com_help&view=help&keyref=Language_Switcher_Tutorial) on how to configure your site to work in a multilingual mode. |
| Extensions Module Manager Latest News | This Module shows a list of the most recently published Articles. |
| Extensions Module Manager Latest Users | The Latest Users module will display the latest users which who last registered on the website. |
| Extensions Module Manager Login | This Module displays a username and password Login form. It also displays a link to retrieve a forgotten password. If User registration is enabled in the User Settings of the Global Configuration screen, then the link "Create an Account" will be shown to invite Users to self-register. |
| Extensions Module Manager Menu | This Module allows you to place your Menus on the page. Every hub has at least one Menu that is created in the Menu Manager screen. The Menu Module allows you place all or part of the selected Menu at the desired position and on the desired web pages. |
| Extensions Module Manager Most Read | This Module displays a list of Articles with the highest hit counts. |
| Extensions Module Manager Random Image | This Module displays a random image from a directory. |
| Extensions Module Manager Search | This Module displays a Search entry field where the user can type in a phrase and press Enter to search the web site. |
| Extensions Module Manager Smart Search | This Module displays a Smart Search entry field where the user can type in a phrase and press Enter to search the web site. |
| Extensions Module Manager Statistics | The Statistics Module shows information about your server installation together with statistics on the Web site users, number of Articles in your database and the number of Web links you provide. |
| Extensions Module Manager Syndication Feeds | This Module creates a RSS Feed link for the page. This allows a User to create a newsfeed for the current page. |
| Extensions Module Manager Weblinks | The Weblinks module will display weblinks from within the Weblinks component. |
| Extensions Module Manager Who Online | The "Who's Online" module displays information about users that are visiting your site at a particular moment. |
| Extensions Module Manager Wrapper | The wrapper module allows you to display an external website in a module. The functionality is the same to that of the 'iFrame Wrapper' you can add as a menu item. If the page to which the wrapper is linked is too big, bars will be shown below and to the right of the wrapper, allowing you to "navigate" the page. |

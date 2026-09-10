<!--
status: imported
source: core/components/com_config/admin/help/en-GB/global_config.phtml
imported: 2026-09-09
-->
# Global Configuration

Controls all critical global settings of a the hub, they include the following: Site, System, Server, Permissions, and Text filters.

<a id="overview"></a>

## Description

The Global Configuration screen allows you to configure the hub with your personal settings. Settings made in this screen apply to the whole site.

- [Details on Site](#Site) configuration
- [Details on System](#System) configuration
- [Details on Server](#Server) configuration
- [Details on Permissions](#Permissions) configuration
- [Details on Text filters](#Text_Filters) configuration

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar.

The functions are:

- **Save**  
  Saves the global configuration settings and stays in the current screen.
- **Save & Close**  
  Saves the global configuration settings and closes the current screen.
- **Cancel/Close**  
  Closes the current screen and returns to the previous screen without saving any modifications you may have made.
- **Help**  
  Opens this help screen.

<a id="Site"></a>

## Site

<a id="Site_Settings"></a>

## Site Settings

- **Site Name**  
  The name of the site.
- **Site Offline**  
  This setting shows when the site is offline. Only Administrators will be able to see the site when *Site Offline* is set to Yes. The default setting is **No**.
- **Offline Message**  
  The message that will be displayed on the site when the site is offline. If custom message is selected then the message in the text area beneath is used.
- **Offline Image**  
  The image that will be displayed on the site when the site is offline.
- **Default Editor**  
  The default editor to use when creating articles.
- **Default Captcha**  
  The default Captcha to use when called by extensions. Remember to correctly configure the associated plugin when selecting an option here. By default, this is set to **None**.
- **Default Access Level**  
  The default access level to the site. The options here are from the Users Access Levels.
- **Default List Limit**  
  The length of lists in the Control Panel for all Users. By default, this is set to **20**.
- **Default Feed Limit**  
  The number of content items to be shown in the feed(s). By default, this is set to **10**.
- **Feed Email**  
  Newsfeeds include the authors e-mail address. Select **Author Email** to use each author's email address. Select **Site Email** to include the 'Mail From' email address for each article.

<a id="Metadata_Settings"></a>

## Metadata Settings

- **Site Meta Description**  
  This is the description of the site which is indexed by search engine spiders.
- **Site Meta Keywords**  
  These keywords describe the site and are the basis for improving the ability of search engine spiders ability to index the site.
- **Content Rights**  
  Describe what rights others have to use this content.
- **Show Title Meta Tag**  
  It shows the Meta information of each article. This Meta information is used by search engine spiders when indexing the site. Each article can have its own Meta Data information (set under the **Metadata Information** pane when creating or editing an article).
- **Show Author Meta Tag**  
  It shows the Author Meta information for articles and is used by search engine spiders when indexing the site.

<a id="SEO_Settings"></a>

## SEO Settings

SEO stands for *Search Engine Optimization*.

- **Search Engine Friendly URLs**. When set to *Yes*, URLs are rewritten to be more friendly for search engine spiders. For example, the URL: *www.example.com/index.php?option=com_content&view=etc...*, would turn into: *www.example.com/alias*. Most of the items created have an Alias box where a search engine friendly URL can be inserted. The default setting is **No**.
- **Use Apache *mod_rewrite***. When set to *Yes*, the CMS will use the *mod_rewrite* settings of Apache when creating search engine friendly URLs. Please note: it is advised that you do not modify any **.htaccess** file without an understanding of how it works. You must use the **.htaccess** file provided with the CMS in order to use this setting. To use this file, rename the **htaccess.txt** file (found in the root directory) to **.htaccess**. By default, this setting is set to **No**.
- **Adds Suffix to URL**. When set to *Yes*, the CMS will add **.html** to the end of the URLs. The default setting is **No**.
- **Unicode aliases**. If Yes, non-Latin characters are allowed in the alias (and URL). If No, a title that includes non-Latin characters will produce a default alias value of the current time and date (for example, "2012-12-31-17-54"). The default setting is **No**.
- **Add Site Name To Page Titles**. Appends the site name to all page titles. (This is what you see in your browser tab.)

## Cookie Settings

- **Cookie Domain**. Domain to use when setting session cookies. precede with '.' to make valid for all subdomains. Example: .MyDomain.org
- **Cookie Path**. Path for which the cookie is valid.

<a id="System"></a>

## System

## System Settings

- **Path to Log folder**. The path where the logs should be stored. The installer should automatically fill in this folder.
- **Help Server**. The place the CMS looks for help information when you click the **Help** button (visible in many screens and options of the administration panel). By default, it uses the CMS' local files.

## Hub Secret

The Hub Secret is unique to each Hub and is used to help validate user access to Campaigns. Campaigns are managed in the Newsletter component.

- **Reset Hub Secret**  
  This control is used to reset the Hub Secret. Resetting the Hub secret will invalidate any existing Campaigns, making users no longer able to access content with links from those Campaigns.

## Debug Settings

For this debug settings to work the associated plugin must be activated (this is enabled by default). You can also customize further what will show in the debug modes in this plugin.

- **Debug System**. This will turn on the debugging system of the CMS. When set to **Yes**, this tool will provide diagnostic information, language translations, and SQL errors. If any such issues or errors occur, they will be displayed at the bottom of each page, in both the front-end and back-end.
- **Debug Language**. This will turn on the debugging indicators (\*...\*) or (?...?) for the Language files. Debug Language will work without the Debug System tool set to on. But it will not provide additional detailed references which would help in correcting any errors.

## Cache Settings

- **Cache**. This chooses whether the site caches or not. Default setting is **ON - Progressive Caching**
- **Cache Handler**. This setting sets how the cache operates. There is only one caching mechanism which is file-based.
- **Cache Time**. This setting sets the maximum length of time (in minutes) for a cache file to be stored before it is refreshed. The default setting is **15** minutes.

## Session Settings

- **Session Lifetime**. This setting sets how long a session should last and how long a user can remain signed in for (before logging them off for being inactive). The default setting is **15** minutes.
- **Session Handler**. This setting sets how the session should be handled once a user connects and logs into the site. The default setting is set to **Database**.

<a id="Server"></a>

## Server

## Server Settings

- **Path to Temp-folder**. The path where files are temporarily stored. This is filled in by default when the CMS is installed.
- **GZIP Page Compression**. Compressing pages typically increases your site's speed. The default setting is **No**.
- **Error Reporting**. This sets the appropriate level of reporting. The default setting is **System Default**.
- **Force SSL*. This setting forces the site access for selected areas under SSL (https).*** *Note*: you must have set already the server to use SSL. Options are:
  - **None**. SSL is not activated
  - **Administrator Only**. SSL is only valid for the backend.
  - **Entire Site**. SSL is valid for the whole site (front- & backend).

## Locale Settings

- **Server Timezone**. This tool sets the current date and time. The set time should be where the site's server is located. The default setting is **(UTC 00:00) Western Europe Time, London, Lisbon, Casablanca**.

## FTP Settings

FTP stands for File Transfer Protocol. Most of these settings are set during the initial installation.

- **Enable FTP**. This setting tells the CMS to use it's built-in FTP function instead of the normal upload process used by PHP.
- **FTP Host**. The host server's URL connecting the FTP.
- **FTP Port**. The port where the FTP is accessed. The default setting is **21**.
- **FTP Username**. The username that the CMS will use when accessing the FTP server. Security recommendation: create another FTP user account to access a folder where files will be uploaded to.
- **FTP Password**. The password that the CMS will use when accessing the FTP server. Security recommendation: create another FTP user account to access the folder where files will be uploaded to.
- **FTP Root**. The root directory where files should be uploaded to.

## Database Settings

These settings are set during the initial setup. It is advised to leave these settings the way they are, unless you have a good understanding of how databases work.

- **Database Type**. The type of databased to be used. The default setting is **mysql**, but this can be changed during the initial setup.
- **Host**. The hostname where the database is located. It is typically set to **localhost** for most servers. It is possible for the hostname to be located on a different server all together.
- **Database Username**. The username to access the database.
- **Database Name**. The name of the database.
- **Database Prefix**. The prefix used before the actual table's name. This allows you to have multiple installations in the same database. The default setting is **jos\_**, but this can be changed during initial setup.

## Mail Settings

The mail settings are set during the initial setup. These settings can be changed whenever needed.

- **Mailer**. This setting sets which mailer to use to deliver emails from the site. The default setting is **PHP Mail Function**. This can be changed during the initial setup.
- **From E-mail**. The email address used to send site email.
- **From Name**. The name of the site will use when sending site emails. By default, the CMS uses the site name during the initial setup.
- **Sendmail Path**. The path where the Sendmail program is located. This is typically filled in during the initial setup. This path is only used if **Mailer** is set to **Sendmail**.
- **SMTP Authentication**. If the SMTP server requires authentication to send mail, set this to **Yes**. Otherwise leave it at **No**. This is only used if **Mailer** is set to **Sendmail**.
- **SMTP Security**. Select the security model your SMTP server uses - Default is None. Options are SSL and TTL.
- **SMTP Port**. Most unsecure servers use port 25 and most secure servers use port 465.
- **SMTP Username**. The username to use for access to the SMTP host. This is only used if **Mailer** is set to **Sendmail**.
- **SMTP Password**. The password to use for access to the SMTP host. This is only used if **Mailer** is set to **Sendmail**.
- **SMTP Host**. The SMTP address to use when sending mail. This is only used if **Mailer** is set to **Sendmail**.

<a id="Permissions"></a>

## Permissions

The permissions for each action are inherited from the level above in the permission hierarchy and from a group's parent group. Let's see how this works. The top level for this is the entire site. This is set up in the Site->Global Configuration->Permissions, as shown below.

You work on one Group at a time by opening the slider for that group. You change the permissions in the Select New Settings drop-down list boxes.

The options for each value are Inherited, Allowed, or Denied. The Calculated Setting column shows you the setting in effect. It is either Not Allowed (the default), Allowed, or Denied.

Note that the Calculated Setting column is not updated until you press the Save button in the toolbar. To check that the settings are what you want, press the Save button and check the Calculated Settings column.

## Setting Groups

The first thing to notice are the nine Actions: Site Login, Admin Login, Super Admin, Access Component, Create, Delete, Edit, Edit State. and Edit Own. These are the actions that a user can perform on an object. The specific meaning of each action depends on the context. For the Global Configuration screen, they are defined as follows:

- **Site Login**  
  Login to the front end of the site
- **Admin Login**  
  Login to the back end of the site
- **Super Admin**  
  Grants the user "super user" status. Users with this permission can do anything on the site. Only users with this permission can change Global Configuration settings (this screen). These permissions cannot be restricted. It is important to understand that, if a user is a member of a Super Admin group, any other permissions assigned to this user are irrelevant. The user can do anything action on the site. However, Access Levels can still be assigned to control what this group sees on the site. (Obviously, a Super Admin user can change Access Levels if they want to, so Access Levels do not totally restrict what a Super Admin user can see.)
- **Access Component**  
  Open the component manger screens (User Manager, Menu Manager, Article Manager, and so on)
- **Create**  
  Create new objects (for example, users, menu items, articles, weblinks, and so on)
- **Delete**  
  Delete existing objects
- **Edit**  
  Edit existing objects
- **Edit State**  
  Change object state (Publish, Unpublish, Archive, and Trash)
- **Edit Own**  
  Edit existing objects that the logged in user has created

There are two very important points to understand from this screen. The first is to see how the permissions can be inherited from the parent Group. The second is to see how you can control the default permissions by Group and by Action.

This provides a lot of flexibility. For example, if you wanted Shop Suppliers to be able to have the ability to login to the back end, you could just change their Admin Login value to "Allowed". If you wanted to not allow members of Administrator group to delete objects or change their state, you would change their permissions in these columns to Inherited (or Denied).

It is also important to understand that the ability to have child groups is completely optional. It allows you to save some time when setting up new groups. However, if you like, you can set up all groups to have Public as the parent and not inherit any permissions from a parent group.

<a id="Text_Filters"></a>

## Text Filters

Web sites can be attacked by users entering in special HTML code. Filtering is a way to protect your web site. Filtering options give you more control over the HTML that your content providers are allowed to submit. You can be as strict or as liberal as you desire, depending on your site's needs.

It is important to understand that filtering occurs at the time an article is saved, *after* it has been written or edited. Depending on your editor and filter settings, it is possible for a user to add HTML to an article during the edit session only to have that HTML removed from the article when it is saved. This can sometimes cause confusion or frustration. If you have filtering set up on your site, make sure your users understand what types of HTML are allowed.

The default setting is that all users will have "black list" filtering on by default. This is designed to protect against markup commonly associated with web site attacks. So, if you do not set any filtering options, all users will have "black list" filtering done using the default list of filtered items. If you create a filter here, this overrides the default, and the default filter is no longer in effect.

To access the filtering settings, click on Options and select 'Text filters'

## Column Headings

- **Filter Groups**
  - For each user group on your site you specify which filter is applied to their edits. The default filter is 'Default Black List'.
- **Filter Type**
  - There are five types of filter available:
  - ***Default Black List***
    - This filter excludes the following tags:
      - 'applet', 'body', 'bgsound', 'base', 'basefont', 'embed', 'frame', 'frameset', 'head', 'html', 'id', 'iframe', 'ilayer', 'layer', 'link', 'meta', 'name', 'object', 'script', 'style', 'title', 'xml'.
    - This filter also excludes the following attributes:
      - 'action', 'background', 'codebase', 'dynsrc', 'lowsrc'.
    - You can black list (disallow) additional tags and attributes using the **Filter Tags** and **Filter Attributes** columns.
  - ***Custom Black List***. Only those tags and attributes listed in the **Filter Tags** and **Filter Attributes** columns will be disallowed.
  - ***White List***. Only those tags and attributes listed in the **Filter Tags** and **Filter Attributes** columns will be allowed.
  - ***No HTML***. This is the strictest filter you can apply. All tags and attributes will be disallowed.
  - ***No Filtering***. This is the most permissive filter you can apply. All tags and attributes, including the default black list tags and attributes will be allowed.
- **Filter Tags**
  - A list of tag names to add to the currently selected filter. Separate each tag name with a space or a comma. For the **Default Black List** filter these tags will be added to the default list of tags that are disallowed. For the **Customer Black List** filter these tags are the only ones that are disallowed. For the **White List** filter these are the only tags that are allowed.
- **Filter Attributes**
  - A list of attribute names to add to the currently selected filter. Separate each attribute name with a space or a comma. For the **Default Black List** filter these attributes will be added to the default list of attributes that are disallowed. For the **Customer Black List** filter these attributes are the only ones that are disallowed. For the **White List** filter these are the only attributes that are allowed.

## Notes

- **Combining filters.** If a user belongs to two different groups that have different filter settings, filters will combine in a permissive way. That is, the set of tags the user will be permitted to use will the combination of the tags that each group allows the user to use. So if the user is a member of one group that white lists a specific set of tags and another group that white lists a different set of tags, the user will be able to use both sets of white listed tags. White lists override blacklists, so if a user belongs to one group that black lists a tag and another group that white lists a tag, the user will be able to use that tag. A user that belongs to a group that has no filtering will be able to use any HTML regardless of filtering settings for other groups the user belongs to.
- **Filter Application.** Please note that these settings work regardless of the editor that you are using. Even if you are using a WYSIWYG editor, the filtering settings may strip additional tags and attributes prior to saving information in the database.
- **Developer note**. The filtering parameters in config.xml have the new parameter menu="hide". This hides the filters from the Menu Item's Component pane as you do not want cascading overrides to occur at the menu item level.

## Filter Examples

**Example One:**To allow people in a group to only submit content with basic HTML tags, use the following settings:

- Select White List as the Filter type
- Set the Filter tags to: p, b, i, em, br, a, ul, ol, li, img
- Set the Filter attributes to: href, target, src

**Example Two:** To apply the default black-list filtering to a group, use the following settings:

- Select Black List as the Filter type.
- Leave the Filter Tags and Filter attributes fields empty.

## Quick Tips

- Most, if not all, of these settings can be set once and then left alone.
- If major modifications need to be made, then consider taking the site offline to test it and to make sure everything is in working order.
- The settings are saved in '&amp;lt;root>/app/config/\*.php'. You have to either activate the FTP-layer or make those files writable to save your changes.

<a id="Related_Information"></a>

## Related Information

- Control panel
- [Info about .htaccess](http://httpd.apache.org/docs/2.2/howto/htaccess.html)
- ACL Tutorial

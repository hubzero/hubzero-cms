<!--
status: imported
source: core/components/com_plugins/admin/help/en-GB/plugin.phtml
imported: 2026-09-09
-->
# Plugin

## How to Access

Navigate to the Plug-in Manager. Click on the Plug-in's Name or click on the Plug-in's checkbox and press the Edit button in the toolbar.

## Description

This screen allows you to edit the details and options for plug-ins. Some plug-ins have several configurable options, while others may not have any.

## Details

The Details section is the same for all plug-ins and has the following fields:

- **Plug-in Name.** The Name of the Plug-in.
- **Enabled.** Whether or not this Plug-in is enabled.
- **Access.** Which user 'access levels' have access to this item. You can change an item's Access Level by clicking on its name to open it up for editing. The default user 'access levels' which come preconfigured are:
  - Public: Everyone has access including website visitors who have not logged in
  - Registered: Only users with registered status or higher will have access
  - Special: Only users with author status or higher have access
- **Ordering.** A drop-down list of plug-ins of the same Type. The list of plug-ins is arranged by their current order. You can change this plug-in's order in relation to the other plug-ins by selecting the plug-in in the drop-down list that you want this plug-in to be ordered below.
- **Plug-in Type.** The Type of the Plug-in. This value cannot be changed.
- **Plug-in File.** The name of the Plug-in file. Each Plug-in has two files with this name. One has the file extension '.php' and the other has the file extension '.xml'.
- **ID.** The Plug-in's ID number. This is a unique identification number for this item assigned automatically by the CMS. It is used to identify the item internally, for example in internal links. You cannot change this number.
- **Description.** The description of what this Plug-in does. This cannot be changed. The developer of the plug-in specifies this. This may be blank if the developer did not specify a description for the plug-in.

## Options

If the plug-in has configurable options they will appear to the right of the details section. Usually you will see *Basic Options* which are general options which need to be configured for the basic operation of the plug-in. You may also see *Advanced Options* which are additional configurable options available for the plug-in which allows further customization of the plug-in. Finally you may see *Language Options* which are options related to the language functions of your installation.

See specific Plug-in below for available options.

## Toolbar

At the top right you will see the toolbar:

The functions are:

- **Save**. Saves the plug-in and stays in the current screen.
- **Save & Close**. Saves the plug-in and closes the current screen.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.
- **Help**. Opens this help screen.

## Plug-in Descriptions and Options

When a hub is installed, many plug-ins are included in the installation. These are described below, along with any options for the plug-in.

## Authentication - HUBzero

This plug-in processes the default User Authentication method. It has no options.

## Authentication - GMail

This plug-in processes User Authentication using GMail accounts. It has the following options:

- **Apply Username Suffix.** This options allows you to automatically apply a suffix to the username a person enters when they log into your installation. The suffix is the portion of the email address after the *@* symbol.
- **Username Suffix.** This is the suffix portion of the GMail email addresses which will be used to log into your site. This is the portion of the mail address after the *@* symbol. Usually you will set this to *gmail.com* for regular GMail accounts. This option is present to allow you to use other GMail accounts that don't end in the typical *gmail.com* suffix. This would be the case with Google Apps for Domains email accounts.
- **Verify Peer.** When your installation attempts to verify the entered Gmail username it does so over an SSL connection. This option configures whether or not your installation will check to ensure the CA certificate of the SSL connection is valid before proceeding with verification of the entered username/password. In some situations certificate verification fails in which case you can try setting this option to *No* to try to fix this.
- **User Blacklist.** A comma separated list of usernames not allowed to log into your installation.

**To use the GMail plug-in:**

- Create a user account with the same username as a GMail account user. For example if the GMail email address of the person you want to add is bob1234@gmail.com you would create the user account with the username *bob1234*.
- Enable the GMail Plug-in.
- Logout of the CMS.
- Login using the GMail username (without "@gmail.com") and the GMail password.

## Authentication - LDAP

This plug-in processes User Authentication against an LDAP server. It has the following options:

- **Host.** The host URL. For example, "openldap.mycompany.org".
- **Port.** The port number. The default is 389.
- **LDAP V3.** Whether or not this host uses LDAP version 3. The default is LDAP v2.
- **Negotiate TLS.** Whether or not to use TLS encryption with this host. If set to "Yes", all traffic to and from this server must be encrypted.
- **Follow Referrals.** Whether to set the LDAP_OPT_REFERRALS flag to Yes or No. For Windows 2003 hosts this must be set to No.
- **Authorization Method.** "Bind Directly as User" or "Bind and Search".
- **Base DN.** The Base DN of your LDAP server.
- **Search String.** A query string used to search for a given User. The [search] keyword is replaced by the login typed by the User. For example: "uid=[search]". More than one Search String can be entered. Separate each by a semi-colon ";" character. This is only used when searching.
- **User's DN.** The [username] keyword is dynamically replaced by the username typed by the User. An example string is: "uid=[username], dc=[my-domain], dc=[com]". More than one string can be entered. Separate each with a semi-colon ";" character. This is only used if the Authorization Method above is set to "Bind Directly as User".
- **Connect Username and Connect Password.** These define connection parameters for the DN lookup phase. For anonymous lookup, leave both of these fields blank. For an Administrative Connection, the "Connect Username" is the username of an administrative account (for example, "Administrator"). In this case, the "Connect password" is the actual password to this administrative account.
- **Map: Full Name.** The LDAP attribute that contains the User's full name.
- **Map: E-mail.** The LDAP attribute that contains the User's e-mail address.
- **Map: User ID.** The LDAP attribute that contains the User's login ID. For Active Directory, this is "sAMAccountName".

## Captcha - Recaptcha

This plug-in has the following **required** options or it will not function correctly:

- **Public Key**. Enter the 'Public Key' provided by reCaptcha web site associated with your reCaptcha account.
- **Private Key**. Enter the 'Private Key' provided by reCaptcha web site associated with your reCaptcha account.
- **Theme**. Choose a theme for visual style. Choices are:

- - *Clean* - &amp;lt;default>
  - *White*
  - *BlackGlass*
  - *Red*

## Content - Load Modules

This plug-in allows you to place a Module inside an Article with the syntax: {loadposition xx}, where "xx" is a user-defined position code. For example, if you create a Module with the Position value of "myposition1", then typing the text "{loadposition myposition1}" inside an Article will cause that Module to show at that point in the Article.

This plug-in has the following option:

- **Style.** The Style for the loaded Module, the following are the options:
  - *Wrapped by a table(column)*
  - *Wrapped by a table(horizontal)*
  - *Wrapped by Divs*
  - *Wrapped by Multiple Divs*
  - *No wrapping (raw output)*

## Content - Email Cloaking

This plug-in cloaks all e-mails in content from spambots using JavaScript. This helps prevent e-mails contained in articles from being added to spam e-mail lists. You can disable Email Cloaking inside an article by inserting `{emailcloak=off}` anywhere in the text of the article. In this case, no e-mail addresses in the article will be cloaked by this plug-in.

Email Cloaking has the following options:

- **Mode.** How the e-mails will be displayed. Options are "As linkable mailto address" or as "Non-linkable text".

## Content - Code Highlighter (GeSHi)

This plug-in displays formatted code in Articles based on the GeSHi highlighting engine. It has no options.

```
Syntax usage :
<pre xml:lang="php">
echo $test;
</pre>
```

## Content - Pagebreak

This plug-in adds table of contents functionality to a paginated Article. This is done automatically through the use of the Pagebreak button added to the lower part of the text panel in an Article. The HTML code is included here as a reference of what is available. The Pagebreak will itself display in the text window as a simple horizontal line.

```
Syntax: Usage: <hr class="system-pagebreak" />
<hr class="system-pagebreak" title="The page title" /> or
<hr class="system-pagebreak" alt="The first page" /> or
<hr class="system-pagebreak" title="The page title" alt="The first page" /> or
<hr class="system-pagebreak" alt="The first page" title="The page title" />
```

This plug-in has the following options:

- **Show Site Title.** (Show/Hide) Whether or not the title and heading attributes from the plug-in will be added to the Site Title tag.
- **Article Index Heading.** (Show/Hide) Whether or not to show the heading on top of the table of contents
- **Custom Article Index Heading.** Add a custom Article Index heading here if desired to display on top of the table of contents.
- **Table of Contents.** (Show/Hide) Whether to Hide or Show a table of contents for multi-page Articles.
- **Show all.** (Show/Hide) Whether or not to give Users the option to show all pages of an Article.
- **Presentation Style.** (Pages/Slider/Tabs) Whether to layout the article with separate pages, a slider or tabs.

## Content - Page Navigation

This plug-in allows you to add Next & Previous navigation links to Articles, for example when using a blog or list layout. This feature can be controlled with the following options:

- "Show Navigation" in the Article Manager - Options screen
- "Show Navigation" in the Options - Component section of the Menu Item Manager - New/Edit screen for Article layouts.

Note that, if the Page Navigation plug-in is disabled in this screen, no Page Navigation will show and the options settings above will have no effect.

This plug-in has the following option:

- **Position.** Position of the navigation link. Options are "Above" or "Below" the 'Relative to' setting for article content.
- **Relative to**. Sets relative location of position. Options are "Text" - article content and "Full Article" - above or below the full display of article content including Article title or Readmore.

## Content - Vote

This plug-in adds the Voting functionality to Articles. It has no options.

## Editor - CodeMirror

This plug-in loads the CodeMirror editor. CodeMirror is a code editor which provides an editor more suited for source code. It provides code syntax highlighting for many programming languages. It can show you mismatched tags and also helps you maintain consistent indenting of your code. It has the following options:

- **Line numbers.** Display line numbers in the editor.
- **Tab mode.** Causes tab to adjust the indentation of the selection or current line using the current parser's rules

## Editor - None

This plug-in loads a basic text editor. This option can be used when you are pasting HTML code from another source and you don't want the HTML to be altered by a WYSIWYG editor. This plug-in has no options.

## Editor - TinyMCE

This plug-in loads the TinyMCE editor.

### Basic Options

- **Functionality.** Select "Advanced" or "Simple" functionality. With "Simple" selected, the User has only 9 toolbar buttons: Bold, Italic, Underline, Strikethrough, Undo, Re-do, Clean up messy code, Bullets, and Numbering. With "Advanced" selected, the User has all of the TinyMCE toolbar buttons. "Advanced" is the default setting.
- **Skin.** Choose the skin which will be applied to the TinyMCE editor when displayed in your website.
- **Entity Encoding.** Controls how HTML elements are encoded. Recommended to be set to raw.
- **Automatic Language Selection.** Whether or not to match the selected UI language. Do not set to "Yes" unless the appropriate editor languages are installed. Default is "No".
- **Language Code.** Editor UI language code. This must be entered if Automatic Language Selection is "Off". Default is "en" for British English.
- **Text Direction.** Whether the language reads "Left to Right" or "Right to Left" (for example, like Arabic). Default is "Left to Right".
- **Template CSS classes.** Whether or not to load the "editor.css" file. If no such file is found for the default template, the "editor.css" file from the system template is used. Default is "Yes".
- **Custom CSS Classes.** Optional full URL path to a custom CSS file. If entered, this overrides the Template CSS classes setting.
- **URLs.** Whether to use Relative or Absolute URLs for links. The default is "Relative".
- **New Lines.** Whether to interpret new lines as "P Elements" or "BR Elements". Default is "P Elements".
- **Prohibited Elements.** The elements that will be cleaned from the text. Default is "applet", which will remove applet elements from the text.
- **Extended Valid Elements.** Optional list of valid HTML elements to add to the existing rule set.

### Advanced options

- **Toolbar.** Whether to show the toolbar above or below the editor window.
- **Toolbar align.** Whether to align the toolbar to the left or right
- **HTML Height.** The height, in pixels, of the HTML mode pop-up window.
- **HTML Width.** The width, in pixels, of the HTML mode pop-up window.
- **Resizing.** Enable or disable the resize button.
- **Horizontal Resizing.** Choose whether horizontal resizing should be enabled or not.
- **Element Path.** If set to "On", show the 'Set Classes' button for the marked text.

#### Extended Mode

- The following options only apply if the editor is in Extended Mode.

- **Fonts.** Hide or Show the 'Font' button.
- **Paste.** Hide or Show the 'Paste' button.
- **Search-Replace.** Hide or Show the 'Search and Replace' button.
- **Insert Date.** Hide or Show the 'Insert Date' button.
- **Date Format.** The Date format to use for Insert Date.
- **Insert Time.** Hide or Show the 'Insert Time' button.
- **Time Format.** The Time format to use for Insert Time.
- **Colors.** Hide or Show the 'Colors' control buttons.
- **Table.** Hide or Show the Table buttons.
- **Smilies.** Hide or Show the 'Smilies' button.
- **Media.** Hide or Show the 'Media' button.
- **Horizontal Rule.** Hide or Show the 'Horizontal Rule' button.
- **Directionality.** Hide or Show the 'Directionality' button.
- **Fullscreen.** Hide or Show the 'Fullscreen' button.
- **Style.** Hide or Show the 'CSS Style' button.
- **Layer.** Hide or Show the Layer buttons.
- **XHTMLxtras.** Hide or Show the additional XHTML features.
- **Visualchars.** Show invisible characters, specifically non-breaking spaces.
- **Visualblocks.** Show the outline of html blocks.
- **Nonbreaking.** Insert non-breaking space entities.
- **Template.** Hide or Show the 'Template' button.
- **Blockquote.** Turn on/off block quotes.
- **Wordcount.** Turn on/off word count.
- **Advanced image.** Turn on/off a more advanced image dialog box.
- **Advanced link.** Turn on/off a more advanced link dialog box.
- **Advanced List.** Turn on/off the ability to set number formats and bullet types in ordered and unordered lists.
- **Save Warning.** Whether or not to give a warning if the User cancels without saving the file. The default value is "Off".
- **Context menu.** Turn on/off the context menu.
- **Inline popups.** Turn on/off dialogs opening as floating DIV layers instead of popup windows. This option is useful to allow getting around popup blockers.
- **Custom plugin.** Add custom TinyMCE plug-ins to the editor by specifying them here.
- **Custom button.** Add custom TinyMCE buttons to the editor by specifying them here.

## Button - Article

This plug-in displays the Article button below the editor box when you are using an editor (for example, when writing an Article). It allows you to insert an Article link into an Article. This plug-in has no options.

## Button - Image

This plug-in displays the Image button below the editor box when you are using an editor (for example, when writing an Article). It allows you to insert an image into an Article. This plug-in has no options.

## Button - Pagebreak

This plug-in displays the Pagebreak button below the editor box when you are using an editor (for example, when writing an Article). It inserts a page break in the Article. This plug-in has no options.

## Button - Readmore

This plug-in displays the "Read more..." button below the editor box when you are using an editor (for example, when writing an Article). It inserts a "Read more..." break in the Article that allows you to display just the first portion of an Article on a page. This plug-in has no options.

## Smartsearch - Categories

This plug-in indexes Categories for Smart-Search, it has no options to set.

## Smartsearch - Contacts

This plug-in indexes Contact for Smart-Search, it has no options to set.

## Smartsearch - Content

This plug-in indexes Content for Smart-Search, it has no options to set.

## Smartsearch - Newsfeeds

This plug-in indexes News Feeds for Smart-Search, it has no options to set.

## Smartsearch - Weblinks

This plug-in indexes Web Links for Smart-Search, it has no options to set.

## Quick Icon - Update Notification

- **Group**. The Group of this icon. This value is compared with group value of the "Quick Icons" modules used to inject icons.

## Quick Icon - Extensions Updates Notification

- **Group**. The Group of this icon. This value is compared with group value of the "Quick Icons" modules used to inject icons.

## Search - Categories

This plug-in enables searching for Category information. It has the following options:

- **Search Limit.** The maximum number of search results to return after a search is done.
- **Search Published.** Whether or not to include published items in the search results.
- **Search Archived.** Whether or not to include archived items in the search results.

## Search - Contacts

This plug-in enables searching for Contacts. It has the following options:

- **Search Limit.** The maximum number of search results to return after a search is done.
- **Search Published.** Whether or not to include published items in the search results.
- **Search Archived.** Whether or not to include archived items in the search results.

## Search - Content

This plug-in enables searching for Articles. It has the following options:

- **Search Limit.** The maximum number of search results to return after a search is done.
- **Articles.** Whether or not to enable searching of all articles.
- **Archived Articles.** Whether or not to include archived articles in the search results.

## Search - Newsfeeds

This plug-in enables searching for News Feeds. It has the following options:

- **Search Limit.** The maximum number of search results to return after a search is done.
- **Search Published.** Whether or not to include published items in the search results.
- **Search Archived.** Whether or not to include archived items in the search results.

## Search - Weblinks

This plug-in enables searching for Web Links. It has the following options:

- **Search Limit.** The maximum number of search results to return after a search is done.
- **Search Published.** Whether or not to include published items in the search results.
- **Search Archived.** Whether or not to include archived items in the search results.

## System - Language Filter

This plug-in filters the displayed content depending on language. It must be enabled when the Language Switcher module is published.

This plug-in has the following options(image below shows default settings).

- **Language Selection for new Visitors**. Site language or browser settings. Use site default or try to detect browser language settings to set site language.
- **Automatic Language Change**. Yes or No. Automatically change the content language used in the frontend when a user site language is changed.
- **Menu associations**. Yes or No. Allow menu associations when switching from one language to another language.
- **Remove URL Language Code**. Yes or No. Remove the defined URL language code of the content language when Search Engine Friendly URLs is set to Yes.
- **Cookie Lifetime**. Session or Year. Language cookies set expiration time.
- **Add alternate meta tags**. Yes or No. Add alternate meta tags for menu items with associated menu items in other languages.

## System - P3P Policy

The P3P policy plugin allows the CMS to send a customised string of P3P policy tags in the HTTP header. This is required for the sessions to work on certain browsers (i.e. Internet Explorer 6 and 7).

## System - Logout

The system logout plug-in enables the CMS to redirect the user to the home page if the user chooses to logout while the user is on a protected access page. This plug-in has no options to set.

## System - Debug

This plug-in provides debugging information. The report is shown below the main screen of the front and backend interfaces of your installation. To turn on the system you must activate it in the Global Configuration Page

- It has the following options:

### Basic Options

- **Allowed Groups.** User groups that will see the debug information when enabled.
- **Show Profiling** Whether or not to display the profiling waypoints information.
- **Show Queries.** Whether or not to include the SQL query log in the debug information.
- **Show Memory Usage.** Whether or not to include memory usage data in the debug information.

### Language Debug Options

- **Show errors when parsing language files.** Whether or not to display a list of language files with errors due to their not being in compliance with the language file specification.
- **Show Language Files.** Whether or not to display the language files that have been loaded to generate the page.
- **Show Language String.** Whether or not to include undefined language strings in the debug information.
- **Strip First Word.** Whether or not to always strip the first word in multi-word strings.
- **Strip From Start.** Strips the specified words from the beginning of the language string. For multiple words separate each word with the pipe ( *|* ) character like so: word1|word2
- **Strip From End.** Strips the specified words from the end of the language string. For multiple words separate each word with the pipe ( *|* ) character like so: word1|word2

### Logging Options

- **Log Deprecated API.** Shows any uses of deprecated functions by extensions.

## System - Log

This plug-in enables system logging. A log is a file that contains information about the web site activity. It can be used to see a history of activity and to troubleshoot problems on the site.

- **Log user names**. Log user names when an authentication fails.

## System - Redirect

This plug-in enables the Redirect system to catch missing pages and redirect users. It is required to be enabled to use the Redirect Component. This plug-in has no options.

## System - Highlight

This plug-in enables the highlighting of specified terms. This plug-in has no options.

## System - Remember Me

This plug-in provides "Remember Me" functionality. This allows the website to "remember" your username and password so that you can automatically be logged in when you return to the site. This plug-in has no options.

## System - SEF

This plug-in adds SEF support to links in the document. It operates directly on the HTML and does not require a special tag. This plug-in has no options.

## System - Language Code

This plug-in changes the language code for the generated HTML document.

It has the following options:

- Change the language code as per directions in plug-in to reflect actual language location.

> **Note:** The options will only show when the plugin has been enabled.

## System - Cache

This plug-in enables page caching. Page caching allows the web server to save snapshots of pages and use them when serving web pages. This improves the performance of your web site and reduces the workload of the server. This plug-in has the following options:

- **Use Browser Caching.** Whether or not to use the mechanism for storing a page cache in the local browser. Default is "No".

## User - Profile

This plug-in adds user profile capability to your web site for registration. Web site users will be able to fill out profile fields that you enable in the options section of this plug-in. You can control what profile fields are shown in the new user registration form and/or each user's editable profile screen. To control what fields are shown in the new user registration form, set the field options in the section labeled *User profile information required at registration*. For profile fields shown in the user's profile screen which it editable after login, set the field option in the options section labeled *User profile fields for profile edit form*. For each field you have the following options.

Drop down field choices are the following:

- **Required.** The field is visible in user profiles and users are required to fill it out.
- **Optional.** The field is visible in user profiles but is optional and users do not need to fill it out.
- **Disabled.** The field is disabled and not visible in user profiles.

Button to select:

- **Select TOS Article**. Click to *Select/Change* button to select or change a 'Terms of Service' article from a modal popup window.

### User Front-end

- All profile fields are text fields with the exception of the *Terms of Service*(TOS). The TOS is a radio button the user can click on to signify their agreement with the website's terms of service when registering for a user account. The 'Front-End' or 'Web site' will show a link to click and view the article selected as the TOS article.

### Additional Information

Options can easily be added and removed from the list here. See the Creating a profile Plugin docs page for more information about creating your own profile plugin.

## User - Contact Creator

This plug-in automatically creates contact information for new users. It has the following options:

- **Automatic Webpage.** A formatted string to automatically generate a contact's web page. [name] is replaced with the name, [username] is replaced with the username, [userid] is replaced with the user ID and [email] is replaced with the email.
- **Category.** Category to assign contacts to by default.
- **Autopublish the contact.** Optionally have the automatically created contact set to the published state and visible to your website visitors.

## User - Hubzero

This plug-in handles the default user synchronization. It has the following options:

- **Auto-create Users.** Whether or not to automatically create registered users where possible. Default is "Yes".
- **Notification Mail to User**. Whether or not to send an email to a User with credentials (username and password) for user accounts created by a Administrator.

## Quick Tips

- If you are using the TinyMCE editor, you can control which options appear on the editor's toolbar by setting the parameters in the "Editor - TinyMCE" plug-in.
- Configurable plug-in settings are referred to as 'options'. In previous versions these configurable settings were referred to as 'parameters'. You may see the terms 'options' and 'parameters' used interchangeably in help documentation and tutorials you encounter.
- At least one authentication plugin must be enabled or you will loose all access to your site!

## Related Information

- To install new Plug-in Extensions: Extension Manager - Install Screen
- To uninstall third-party Plug-ins: Extension Manager - Manage

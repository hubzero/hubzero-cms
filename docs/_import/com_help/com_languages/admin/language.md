<!--
status: imported
source: core/components/com_languages/admin/help/en-GB/language.phtml
imported: 2026-09-09
-->
# Language Manager: Edit Content Language

- [Description](#Description)
- [Details & Options](#Details_.26_Options)
- [Toolbar](#Toolbar)
- [Quick Tips](#Quick_Tips)
- [Related Information](#Related_Information)

<a id="Description"></a>

## Description

In the Language Manager: Edit Content Language you can set the title, URL, image, code, and description of the installed languages. You may also update or add meta keywords and description.

<a id="Details_.26_Options"></a>

## Details & Options

## Details

- **Title.** The name of the language as it will appear ion the lists.
- **Title Native.** Language title IN the native language.
- **URL Language Code.** URL Language Code for this language. If 'en' is used then it will be added after the site URL. Example: <http://mysite.com/en>. Note: The prefix must be unique over all the languages.
- **Image Prefix.** Name of the image file for this language when using the "Use Image Flags" Language Switcher basic option. Example: If 'en' is chosen, then the image shall be en.gif. Images and CSS for this module are in media/mod_languages/
- **Language Tag.** Enter here the language tag - example: en-GB for English (UK). This should be the exact prefix used for the language installed or to be installed.
- **Status.** (Published/Unpublished/Archived/Trashed) The published status of the item.
- **Description.** Enter a description for the language
- **ID**. This is a unique identification number for this item assigned automatically. It is used to identify the item internally, and you cannot change this number. When creating a new item, this field displays 0 until you save the new entry, at which point a new ID is assigned to it.

## MetaData Options

- **Meta Description.** An optional paragraph to be used as the description of the page in the HTML output. This will generally display in the results of search engines. If entered, this creates an HTML meta element with a name attribute of "description" and a content attribute equal to the entered text.

- **Meta Keywords.** Optional entry for keywords. Must be entered separated by commas (for example, "cats, dogs, pets") and may be entered in upper or lower case. (For example, "CATS" will match "cats" or "Cats"). Keywords can be used in several ways:

1. To help Search Engines and other systems classify the content of the Article.
2. For articles only, in combination with the Related Articles module, to display Articles that share at least one keyword in common. For example, if the current Article displayed has the keywords "cats, dogs, monkeys", any other Articles with at least one of these keywords will show in the Related Articles module.

## Site Name

- **Custom Site Name.** You can optionally override the site name set in the Global Configuration here.

<a id="Toolbar"></a>

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Save**. Saves the content language and stays in the current screen.
- **Save & Close**. Saves the content language and closes the current screen.
- **Save & New**. Saves the content language and keeps the editing screen open and ready to create another content language.
- **Cancel/Close**. Closes the current screen and returns to the previous screen without saving any modifications you may have made.
- **Help**. Opens this help screen.

<a id="Quick_Tips"></a>

## Quick Tips

- Users can use any Language from the list of installed Languages, either by having it assigned in the User Manager or by filling out a Menu Item Manager - New/Edit - User Form Layout at the Front end. This will cause the system prompts to be generated in this Language just for this User. For example, if a User chooses Spanish as their language, then the Search Module will show with prompts in Spanish.
- This User's choice is not affected by the Default Language set for the Front-end.

- - Changing a User's Language or the Default Language does not affect the web site's Articles and other content.

- **Important**: Do not delete the default language files (for example, with FTP). This will create errors on both the Front-end and Back-end.

- Additional Languages can be added using the Extension Manager - Install Screen.

- If desired, you can show the Front-end site in one Language and show the Back-end administration pages in a different Language. Also, individual articles can be configured to use a different language in the Advanced Parameter pane when editing the Article.

<a id="Related_Information"></a>

## Related Information

- To install more Languages: Extension Manager - Install Screen
- To uninstall a Language: Extension Manager - Manage
- To change the Language for a User: User Manager - New/Edit
- To set the Language of an Article: Article Manager - New/Edit - Parameters - Advanced

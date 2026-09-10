<!--
status: imported
source: core/components/com_help/admin/help/en-GB/glossary.phtml
imported: 2026-09-09
-->
# Glossary

The Glossary is helpful for learning common terms used throughout the hub, in help pages, and in advanced documentation.

- Access Control List
- Alias
- Search Engine Friendly URLs
- Template
- Template style

<a id="Access_Control_List"></a>

## Access Control List

Access Control List or ACL is [“...ACL specifies which users or system processes are granted access to objects, as well as what operations are allowed to be performed on given objects.”](http://en.wikipedia.org/wiki/Access_control_list) In the case of Hubzero there are two separate aspects to its Access Control List which site administrators can control:

- **Which users can gain access to what parts of the website?** For example, will a given menu choice be visible for a given user? A registered user can view, but the public at large cannot. Perhaps the menu choice is hidden from all except an Editor user and higher.
- **What operations (or actions) can a user perform on any given object?** For example, can a user listed as an "Editor" submit an article or only edit an existing article. The ACL settings could allow submitting and editing, or allow a change an article's category, add tags or any combination.

<a id="Alias"></a>

## Alias

Aliases are short pieces of text that represent the title of certain items (Menu items, Articles and Categories) in a machine-friendly format. This format allows only lowercase alpha-numeric characters (letters and numbers), underscores, and dashes (-).

Aliases are used throughout the site to make Search Engine Friendly URLs. There are technical limitations to the types of characters that can be included in URLs, so the CMS prevents problems with invalid characters by allowing editors to specify an alias.

You can fill in an alias yourself. **If you leave the alias field empty**, the CMS will automatically create an alias from the Title field of an item when it is saved. **This means that if you edit the title of an item, but you leave the old alias in its field, the alias (and the URL that is created from it) will not change.** Empty the alias field if you want generate a new alias.

<a id="Search_Engine_Friendly_URLs"></a>

## Search Engine Friendly URLs

Search engine friendly URLs is a term commonly abbreviated as SEF URLs or SEF for short. Normal hub URLs look something like this:

```
http://myhub.org/index.php?option=com_blog&task=view&post=Welcome_to_my_hub
```

You can optionally have URLs display to look like static HTML pages like this:

```
http://myhub.org/faq.html
```

The CMS has built-in options for generating SEF URLs which can be enabled by changing the "SEO Settings" (Search Engine Optimisation) in the Site tab in the Global Configuration screen in the Administrative back-end.

<a id="Template"></a>

## Template

A template is a type of extension that changes the way a site looks. There are two types of templates used by the CMS: [Front-end Templates](https://help.hubzero.org/proxy/index.php?option=com_help&view=help&keyref=Template#Front-end_Templates) and [Back-end Templates](https://help.hubzero.org/proxy/index.php?option=com_help&view=help&keyref=Template#Back-end_Templates). The Front-end Template controls the way your website is presented to the user viewing the website's content. The Back-end Template controls the way your website's administrative tasks are presented for controlling management functions by an Administrator. These would include common tasks such as: user, menu, article, category, module, component, plugin and template management.

<a id="Template_style"></a>

## Template style

**Template style** is a feature that allows users to assign different template styles to individual menu items. By default, the CMS assigns a template style to all menu items upon installation. A yellow star indicates the default template style in use. A default template style can be partially or completely overridden by assigning different template styles to the desired menu items in order to obtain a different look for their respective pages.

A template style can be assigned to menu items one of two ways.

- Template manager **Extensions → Template Manager**
- Editing a menu item under **Menus → Menu Name → Menu item**

<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/foundation/extensions
source-id: 3442
modified: 2015-07-06
imported: 2026-09-09
-->
# Extensions

## Overview

HUBzero CMS is already a rich featured content management system but if you're building a hub and you need extra features which aren't available by default, you can easily extend it with extensions. There are five types of extensions: Components, Modules, Plugins, Templates, and Languages. Each of these extensions handle specific functionality.

## Components

The largest and most complex of the extension types, a component is in fact a separate application. A component is a relatively self-contained portion of code with its own functionality, its own database tables and its own presentation. Examples of components are a forum, a blog, a wiki, a photo gallery, etc. One could easily imagine all of these as separate applications or stand-alone systems. A component will be shown in the main part of the website and only one component will be shown. A menu is then in fact nothing more then a switch between different components.

## Modules

Modules are extensions which present certain pieces or smaller chunks of information on the site. It is not uncommon to have a number of modules on each web page. A module differs from a component in that it doesn't make sense as a standalone application; Rather, it will just present information or add a functionality to an existing application. Common examples would include displaying the latest blog post on the home page or a search box to be present throughout the site. This is a small piece of re-usable HTML that can be placed anywhere desired and in different locations on a template-by-template basis. This allows one site to have the module in the top left of their template, for instance, and another site to have it in the right side-bar.

## Plugins

Plugins serve a variety of purposes. As modules enhance the presentation of the final output of the Web site, plugins enhance the data and can also provide additional, installable functionality. Plugins enable you to execute code in response to certain events, either core events or custom events that are triggered from your own code. This is a powerful way of extending the basic functionality.

## Templates

A template is a series of files within the Joomla! CMS that control the presentation of the content. The template is not a website; it's also not considered a complete website design. The template is the basic foundation design for viewing your website. To produce the effect of a "complete" website, the template works hand-in-hand with the content stored in the database.

Each hub comes with default templates for both the administrator area and the front-end site.

- **administrator** - kameleon
- **site** - kimera

## Languages

Probably the most basic extensions are languages. Languages can be packaged in two ways, either as a core package or as an extension package. In essence, these files consist key/value pairs, these pairs provide the translation of static text strings which are assigned within the source code. These language packs will affect both the front and administrator side. Note: these language packs also include an XML meta file which describes the language and font information to use for PDF content generation.

## Conclusion

If the difference between the three types of extensions is still not completely clear, then it is advisable to go to the admin pages of your installation and check the components menu, the module manager and the plugin manager. A hub comes with a number of core components, modules and plugins. By checking what they're doing, the difference between the three types of building blocks should become clear.

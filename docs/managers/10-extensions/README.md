<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/extensions
source-id: 3405
imported: 2026-09-09
-->
# Extensions

## Overview

HUBzero is already a rich featured content management system but if you're building a website and you need extra features which aren't available by default, you can easily extend it with extensions. There are five types of extensions: Components, Modules, Plugins, Templates, and Languages. Each of these extensions handle specific functionality.

## Components

The largest and most complex of the extension types, a component is in fact a separate application. You can think of a component as something that has its own functionality, its own database tables and its own presentation. So if you install a component, you add an application to your website. Examples of components are a forum, a blog, a community system, a photo gallery, etc. You could think of all of these as being a separate application. Everyone of these would make perfectly sense as a stand-alone system. A component will be shown in the main part of your website and only one component will be shown. A menu is then in fact nothing more then a switch between different components.

## Modules

Modules are extensions which present certain pieces of information on your site. It's a way of presenting information that is already present. This can add a new function to an application which was already part of your website. Think about latest article modules, login module, a menu, etc. Typically you'll have a number of modules on each web page. The difference between a module and a component is not always very clear for everybody. A module doesn't make sense as a standalone application, it will just present information or add a function to an existing application. Take a newsletter for instance. A newsletter is a module. You can have a website which is used as a newsletter only. That makes perfectly sense. Although a newsletter module probably will have a subscription page integrated, you might want to add a subscription module on a sidebar on every page of your website. You can put this subscribe module anywhere on your site.

Another commonly used module would be a search box you wish to be present throughout your site. This is a small piece of re-usable HTML that can be placed anywhere you like and in different locations on a template-by-template basis. This allows one site to have the module in the top left of their template, for instance, and another site to have it in the right side-bar.

## Plugins

Plugins serve a variety of purposes. As modules enhance the presentation of the final output of the Web site, plugins enhance the data and can also provide additional, installable functionality. Plugins enable you to execute code in response to certain events, either core events or custom events that are triggered from your own code. This is a powerful way of extending the basic functionality.

## Templates

A template is a series of files within the CMS that control the presentation of the content. The template is not a website; it's also not considered a complete website design. The template is the basic foundation design for viewing your website. To produce the effect of a **complete** website, the template works hand-in-hand with the content stored in the database.

## Languages

Probably the most basic extensions are languages. Languages can be packaged in two ways, either as a core package or as an extension package. In essence, these files consist key/value pairs, these pairs provide the translation of static text strings which are assigned within the source code. These language packs will affect both the front and administrator side. Note: these language packs also include an XML meta file which describes the language and font information to use for PDF content generation.

## Conclusion

If the difference between the three types of extensions is still not completely clear, then it is advisable to go to the admin pages of your installation and check the components menu, the module manager and the plugin manager. HUBzero comes with a number of core components, modules and plugins. By checking what they are doing, the difference between the three types of building blocks should become clear.

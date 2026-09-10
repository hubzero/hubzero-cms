<!--
status: imported
source: core/components/com_installer/admin/help/en-GB/install.phtml
imported: 2026-09-09
-->
# Extension Manager: Install

This screen is accessed from the back-end administrator panel. It is used to install extensions into your installation. Some examples of extensions are plug-ins, components, and site templates.

## How to Access

- Select **Extensions → Extension Manager** from the drop-down menu of the ***Administrator Panel***. Then select the **Install** menu item in the ***Extension manager*** screen that appears.

## Description

Extensions are add-ons that expand the functionality of the CMS. Extensions are used to add capabilities to the CMS that do not exist in the standard package. Numerous extensions are available, with more being developed all of the time.

Extensions are categorized into five types, as follows:

- A *Component* is a mini-application that renders the main body of the page. Examples of Components are Contacts, the Front Page, and News Feeds.
- A *Module* is a smaller Extension typically used for rendering a small element that displays across multiple pages. Examples of Modules include Menus and Related Items.
- A *Plugin* is a section of code that runs when a pre-defined event happens. For example, editors are Plugins that run when an edit session is opened.
- The *Language* Extension allows for the Front-end and Back-end of the CMS to be presented in any language for which a language Extension exists. This way, the CMS can be released in a new language with no changes to the core program.
- A *Template* controls the way the content of a web site is displayed, including the location and layout of elements, colors, fonts, and so on. Templates allow the appearance of the web site to be separated from its content.

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Options**. Opens the Options window where settings such as default parameters or permissions can be edited.
- **Help**. Opens this help screen.

## Links to Other Screens

At the top left, you will see the following five links:

- **Install.** Links to the **Install Screen**.
- **Update.** Links to the Update Screen.
- **Manage.** Links to the Manage Screen.
- **Discover.** Links to the Discover Screen.
- **Database.** Links to the Database Screen.
- **Warnings.** Links to the Warnings Screen.
- **Install Languages.** Links to the Languages Screen.

## Outline

Extensions can be installed using one of three methods, as indicated below. Only one method is needed to install a given Extension.

The normal procedure for installing an extension is as follows:

1. Download one or more archive files (normally ".zip" or "tar.gz" format) from the Extension provider's web site to a local directory on your computer. Note that some Extensions are installed as one file (for example, one Component or Module) while other Extensions might have two or more files (for example, a Component and a Module). If there are two or more parts, each one will have its own archive file.
2. Choose one of the methods describe below (**usually Package File**)
3. When it is finished, the screen will display the message "Install Component Success". If the installation is not successful, an error message will display.
4. Depending on the Extension, it may be necessary to enable the Extension (for example, in the Module Manager or Plugin Manager).

- **Package File.** (the common way)
  1. Browse to the location where you downloaded the Extension's archive file.
  2. Then press "Upload File & Install". The CMS will read the contents of the archive file and install the Extension.
- **Install Directory.** As an alternative to installing from an archive file, you can follow the steps below:
  1. Create a temporary directory on your local hard drive and unpack the Extension's archive file in this temporary directory.
  2. Using FTP, upload the contents of this directory (including files and subdirectories) to a directory on your server.
  3. In the *Install Directory* field specify the server directory where you uploaded the files and subdirectories of the package.
  4. Click on the *Install* button and the CMS will install the contents of the given directory.
- **Install URL.** A third alternative for installing an Extension is as follows. Instead of downloading the archive file to your local computer, just specify the URL of the target archive file. Then click the "Install" button and th CMS automatically installs it directly from this URL. *Note that, with this method, you will not have a copy of the archive file on your local computer.*

## Quick Tips

- Three alternate installation methods are available, as indicated above. The most common one is the "Upload Package File" method.
- If you want to install a third-party Module or Plugin that belongs to a Component, you will generally need to install the Component as well as the Module or Plugin in order to use the Module or Plugin. This is normally documented in the Extension's installation instructions on the author's web site.
- Similarly, if you uninstall a third-party Component that also has its own Modules or Plugins, these Modules and Plugins can no longer be used. So it is normally recommended to uninstall these dependent Modules and Plugins as well.
- Some Components developed by third party developers may have their own Modules or Plugins included in the installer. In this case, make sure these Module or Plugin directories are writable. Otherwise the Extension will not work properly.
- **SECURITY WARNING:** It is recommended that you use only those third-party Extensions on your site that you really need. Do not use your live site for testing purposes because it may compromise your site and server. Test new extensions on a local test web site before deploying them on your live site.
- Do not install extensions downloaded from warez sites because they may be infected by a virus or malware that cause damage on the server and can contaminate the computer of your visitors!
- Installing from remote URL can be dangerous. For this reason, it is generally recommended that you use the "Upload Package File" or "Install from Directory" options when installing new Extensions.

## Related Information

- If you install a Component Extension, it will be listed as a new Menu Item in the *Components* menu.
- You can assign a Menu Item to an installed Component Extension in the Menu Item Manager by clicking the *New* toolbar button. The new Component will show in the Internal Link list of Menu Item Types.
- If you install a Module Extension, it will be added to the list of the Modules in the Module Manager, where you can enable/disable it. You can also customize it's parameters in the Module Edit screen.
- An installed Plugin Extension will be added to the list of the Plugin Manager, where you can enable/disable it. You can also customize its parameters in the Plugin Edit screen.
- An installed Template Extension will be added to the Site or Administrator list of the Template Manager where you can assign it to all of the pages or to the selected ones. You can also customize its parameters, edit the HTML or CSS source, or preview the available Module positions.
- An installed Language Extension will be added to the Site or Administrator list of the Language Manager, depending on the client attribute of the Extension. This screen let's you assign it as the default language, if desired.

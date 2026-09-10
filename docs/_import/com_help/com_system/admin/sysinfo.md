<!--
status: imported
source: core/components/com_system/admin/help/en-GB/sysinfo.phtml
imported: 2026-09-09
-->
# System Information

Core settings with information useful for management of your website.

<a id="How_to_access"></a>

## How to access

Select **Site → System Information** from the drop-down menu of the ***Administrator Panel***.

<a id="Description"></a>

## Description

This tool provides useful information about your host server environment, including operating system, database and PHP settings, and directory information. You can navigate to five different screen: System Info, PHP Settings, Configuration File, Directory Permissions, and PHP Information. Each screen provides detailed information about that aspect of your website. This information is very helpful when you are troubleshooting setup problems.

- Note that none of these settings can be changed from these screens. This must be done in different locations throughout your installation, depending on the specific setting.
- Many settings on the Configuration File screen can be changed from the [Global Configuration](https://help.hubzero.org/index.php?option=com_help&component=com_config&page=global_config) screen. Some settings shown here depend on your host server configuration and cannot be changed from inside the CMS.

<a id="Toolbar"></a>

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Help**. Opens this help screen.

<a id="Screens"></a>

## Screens

- System Info
- PHP Settings
- Configuration File
- Directory Permissions
- PHP Information.

<a id="System_Info"></a>

## System Info

This screen shows information about the operating environment for your site.

- **PHP Built on:** Provides details of the principle operating system which the webserver that the CMS is running on.
- **Database Version:** Provides the current version of the MySQL database being used by the installation of the CMS.
- **Database Collation:** How the MySQL databased is structured for the information used by the CMS.
- **PHP Version:** Provides the current version of PHP server side script that is being used for this installation of the CMS.
- **Web Server:** Provides the current type and version of web server which the installation of the CMS is running on.
- **Web Server to PHP interface:** The script that permits interaction between the web server (in most cases, Apache) and the PHP scripting language.
- **CMS Version:** Provides the current version of the CMS. It is recommended that it is always up to date and using the current stable release.
- **User Agent:**The summary of the current user's local machine's operating system and browser information which is used to create an unique session ID for access and functionality within the web site.

<a id="PHP_Settings"></a>

## PHP Settings

These screen shows the Relevant PHP Settings information. If any of these is highlighted as incorrect should be taken care of to rectify the situation.

- **Safe Mode:** Recommended setting: ON
- **Open basedir:** Recommended setting: Site dependent
- **Display Errors:** Recommended setting: OFF
- **Short Open Tags:** Recommended setting:
- **File Uploads:** Recommended setting: ON
- **Magic Quotes:** Recommended setting: OFF
- **Register Globals:** Recommended setting: OFF
- **Output Buffering:** Recommended setting: OFF
- **Session Save Path:** Recommended setting: Site dependent
- **Session Auto Start:** Recommended setting: OFF
- **XML Enabled:** Recommended setting: ON
- **Zlib Enabled:** Recommended setting: ON
- **Disabled Functions:** Recommended setting: Site dependent
- **Mbstring Enabled:** Recommended setting: ON
- **Iconv Available:** Recommended setting:
- **WYSIWYG Editor:** Recommended setting: Depends on preference

<a id="Configuration_File"></a>

## Configuration File

This screen shows the contents of the current **root**/app/config/*\*.php* files. These files are created for you automatically when you first install the CMS and where most changes of the Global Configuration section of the CMS are recorded. Please note that none of the settings can be changed from this page. Use [Global Configuration](https://help.hubzero.org/proxy/index.php?option=com_help&view=help&keyref=Help25:Site_Global_Configuration) to see more information about these settings and to make changes.

<a id="Directory_Permissions"></a>

## Directory Permissions

This screen shows a list of the directories that the webserver should have write access to. Please note that all directories listed on this page should say **Writable**. If not, you may need to change the permissions to be able to install and use the CMS successfully.

<a id="PHP_Information"></a>

## PHP Information

This screen displays the full configuration of the PHP server side scripting language that the CMS runs on, together with all the associated system information that goes towards the creation of the web server. It is the output of an integrated php.info script built into the CMS.

PHP is installed, and runs on the server (hence the server side above), and therefore all the settings are made on the server. The visitor to the web site does not need to have anything special running on their local machine in order to view or use any of the extra functionality that PHP gives to the web site.

All the settings that are ever likely to be needed are displayed here. Any changes that are required would be made within the *php.ini* and other configuration files on the web server.

How much control a web site owner has over this information depends on whether they own the server or if the server host is flexible in their customer approach.

It is a good practice to know the limitations of a particular server installation. This screens output is used to find detailed information about how PHP is implemented on the server.

For full details on the information contained within the PHP Info screen visit: <http://php.net/phpinfo>.

<a id="Quick_Tips"></a>

## Quick Tips

- If you are having problems installing extensions, uploading files, or changing configuration options, check the Directory Permissions screen to make sure you have permission to write to files on your web server. The "Status" of the directories should be "Writable". If not, you may be unable to upload or edit files in these directories.
- When you are seeking help with setup problems, for example in a web forum, it is very helpful to post specific information about your installation. This screen is an easy way to find all of this information in one place.

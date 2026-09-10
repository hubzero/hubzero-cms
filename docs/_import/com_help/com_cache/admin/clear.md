<!--
status: imported
source: core/components/com_cache/admin/help/en-GB/clear.phtml
imported: 2026-09-09
-->
# Clear Cache

- [Overview](#overview)
- [Description](#description)
- [Toolbar](#toolbar)
- [List Filters](#list-filters)
- [Column Headers](#column-headers)
- [Quick Tips](#quick-tips)
- [Related Information](#related-info)

<a id="overview"></a>

## Overview

Component to clear cache files from the cache folders if enabled under the Site Global Configuration settings.

<a id="description"></a>

## Description

This tool will delete *all* Cache files from the cache folders - including current ones - from your web server. As admin functions are cached too, some cache folders might appear as if they were not cleaned because they get recreated before cache content is rescanned after this action.

Cache files are temporary files that are created to improve the performance of your site. If you have made significant changes to your web site, such as changing your Template or Language, your cache files may be out of date. To avoid any problems caused by out of date cache files, you can delete all of the cache files.

- Because this process deletes **all** Cache files, the website may be a little slower immediately after running Clean Cache. Once this process is complete, users lose the benefit of using the cache files until they are re-created by the CMS to be up to date with your current site.
- In contrast, the Purge Expired Cache option will check each Cache file individually for being out of date, but the process is slower and requires more system resources. However, the website should perform close to the same speed for users visiting your site, since all up to date files are still available for your current site.

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Delete**  
  Deletes the selected items. Works with one or multiple items selected.
- **Options**  
  Opens the Options window where settings such as default parameters or permissions can be edited.
- **Help**  
  Opens this help screen.

<a id="list-filters"></a>

## List Filters

The drop-down list at the top right allows you to filter the list with the following options:

- **Site**  
  This is the default and allows you to see the cache files for the front-end of the web site.
- **Administrator**  
  Select this option to see the cache files for the Administrator back-end of the web site.

## Page Controls

When the number of items is more than one page, you will see a page control bar as shown below.

- **Display #**  
  Select the number of items to show on one page.
- **Start**  
  Click to go to the first page.
- **Prev**  
  Click to go to the previous page.
- **Page numbers**  
  Click to go to the desired page.
- **Next**  
  Click to go to the next page.
- **End**  
  Click to go to the last page.

<a id="column-headers"></a>

## Column Headers

- **#**  
  An indexing number automatically assigned by the CMS for ease of reference.
- **Checkbox**  
  Check this box to select one or more items. To select all items, check the box in the column heading. After one or more boxes are checked, click a toolbar button to take an action on the selected item or items. Many toolbar actions, such as Publish and Unpublish, can work with multiple items. Others, such as Edit, only work on one item at a time. If multiple items are checked and you press Edit, the first item will be opened for editing.
- **Cache Group**  
  The type of item being cached in this file. This is also the name of the subdirectory where this type of cache file is stored. The cache files are stored in the directory "&amp;lt;path-to-site>/app/cache/&amp;lt;Cache Group Name>".
- **Number of Files**  
  The number of files currently in this cache group.
- **Size**  
  The total size, in KB, of the cache files in this group.

<a id="quick-tips"></a>

## Quick Tips

Normally you want to delete all cache files. To do this, click the check box in the column heading to select all files and then click the Delete icon in the toolbar.

<a id="related-info"></a>

## Related Information

To change the cache settings for your site: Global Configuration - Cache Settings

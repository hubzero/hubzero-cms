<!--
status: imported
source: core/components/com_cache/admin/help/en-GB/purge_expired.phtml
imported: 2026-09-09
-->
# Purge Expired Cache

- [Overview](#overview)
- [Description](#description)
- [Toolbar](#toolbar)
- [Quick Tips](#quick-tips)
- [Related Information](#related-info)

<a id="overview"></a>

Component for deleting the expired cache files if caching is enabled.

<a id="description"></a>

## Description

This tool will purge all expired cache files from the cache folders. Cache files are temporary files that are created to improve the performance of your site. **Cache files that are still current according to what the CMS finds are not purged.**

- Because this process has to check each cache file individually, it is slower and requires more system resources. Once the purge process is complete there should be no loss in speed for your users, because all up to date cache files are still available for your current website.
- In contrast, the Clean Cache option runs more quickly since it deletes all of the files stored in each cache folder. However, the website may be a little slower immediately after running Clean Cache, since *all* cache files -- including current ones -- are removed, so users lose the benefit of using the cache files until they are re-created by the CMS to be up to date with your current site.
- If you have a large number of cache files and your server setting for 'max_input_time' is set to less then 60 seconds, you may receive a PHP warning message saying that the process exceeded the maximum time allowed. In this case, no harm is done but all of the files may not have been purged. It is ok simply to re-run the process.

<a id="toolbar"></a>

## Toolbar

At the top right you will see the toolbar. The functions are:

- **Purge expired**  
  Deletes all expired cache files. Cache files that are still current will not be deleted. WARNING: This operation can be resource intensive on sites with a large number of items.
  
  Options
  
  Opens the Options window where settings such as default parameters or permissions can be edited.
  
  Help
  
  Opens this help screen.

<a id="quick-tips"></a>

## Quick Tips

- It is recommended that this option is run on a regular basis, however this will require more system resources because each file is checked.
- The Clear Cache option is quicker, however **all** cache files will be deleted, not just those that have expired.
- If you receive a PHP timeout error or warning during the process, it is safe to start this process again.

<a id="related-info"></a>

## Related Information

- To change the cache settings for your site: Global Configuration - Cache Settings.
- To clear all cache files: Clean Cache.

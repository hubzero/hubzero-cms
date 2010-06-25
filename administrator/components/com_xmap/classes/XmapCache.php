<?php
/**
 * $Id: XmapCache.php 95 2010-04-14 18:38:36Z guilleva $
 * $LastChangedDate: 2010-04-14 12:38:36 -0600 (mié, 14 abr 2010) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class XmapCache {
	/**
	* @return object A function cache object
	*/
	function &getCache( &$sitemap ) {
		$cache = &JFactory::getCache('com_xmap_'.$sitemap->id);
		$cache->setCaching($sitemap->usecache);
		$cache->setLifeTime($sitemap->cachelifetime);
		return $cache;
	}
	/**
	* Cleans the cache
	*/
	function cleanCache( &$sitemap ) {
		$cache =&XmapCache::getCache( $sitemap );
		if (class_exists('JFactory')) {
			return $cache->clean();
		} else {
			return $cache->clean( $cache->_group );
		}
	}
}

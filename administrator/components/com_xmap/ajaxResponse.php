<?php
/**
 * $Id: ajaxResponse.php 67 2009-11-26 18:56:32Z guilleva $
 * $LastChangedDate: 2009-11-26 12:56:32 -0600 (jue, 26 nov 2009) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/


defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );


$action = JRequest::getVar('action','',"REQUEST");

$database = & JFactory::getDBO();

require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'XmapCache.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'XmapPlugin.php' );

header ('Content-Type: text/xhtml; charset='. _XMAP_CHARSET);
header ("Cache-Control: no-cache, must-revalidate ");

switch ($action) {
	case 'add_sitemap':
		$sitemap = new XmapSitemap($database);
		$sitemap->save();
		XmapAdminHtml::showSitemapInfo($sitemap);
	break;
	case 'delete_sitemap':
		$id = intval (JRequest::getVar('sitemap','',"REQUEST"));
                $config = new XmapConfig();
		$config->load();
		if (!$id || $id != JRequest::getVar('sitemap','',"REQUEST")) {
			die("Invalid Sitemap ID");
		}
                if ( $config->sitemap_default==$id) {
			echo _XMAP_ERROR_DELETE_DEFAULT;
			exit;
		}

		$sitemap = new XmapSitemap($database);
		$sitemap->load($id);
		if ($sitemap->remove()) {
			echo 1;
		} else {
			$database->getErrorMsg();
		}
	break;
	case 'copy_sitemap':
		$id = intval (JRequest::getVar('sitemap','',"REQUEST"));
		if (!$id || $id != JRequest::getVar('sitemap','',"REQUEST")) {
			die("Invalid Sitemap ID");
		}
		$sitemap = new XmapSitemap($database);
		if ( $sitemap->load($id) ) {
			$sitemap->id=NULL;
			$sitemap->name=sprintf(_XMAP_COPY_OF,$sitemap->name);
			$sitemap->save();
			XmapAdminHtml::showSitemapInfo($sitemap);
		}
	break;
	case 'save_property':
		$id = intval (JRequest::getVar('sitemap','',"REQUEST"));
		$property = JRequest::getVar('property','',"REQUEST");
		$value = JRequest::getVar('value','',"REQUEST");
		$sitemap = new XmapSitemap($database);
		if ($sitemap->load($id) ) {
			if (isset($sitemap->$property)) {
				$sitemap->$property = $value;
				if ( $sitemap->save() ) {
					if ( $sitemap->save() ) {
						if ( $sitemap->usecache ) {
							XmapCache::cleanCache($sitemap);
					   	}
						echo 1;
					} else {
						$database->getErrorMsg();
					}
					exit;
				}
			}
		}
		echo _XMAP_MSG_ERROR_SAVE_PROPERTY;
		exit;
	break;
	case 'edit_sitemap_settings':
		$id = intval (JRequest::getVar('sitemap','',"REQUEST"));
		if (!$id || $id != JRequest::getVar('sitemap','',"REQUEST")) {
			die("Invalid Sitemap ID");
		}
		$sitemap = new XmapSitemap($database);
		if ( $sitemap->load($id) ) {
			// images for 'external link' tagging
			$javascript = 'onchange="changeDisplayImage();"';
			$directory = '/components/com_xmap/images';
			$lists['ext_image'] = JHTML::_('list.images', 'ext_image', $sitemap->ext_image, $javascript, $directory);

			// column count selection
			$columns = array (
				JHTML::_('select.option', 1, 1),
				JHTML::_('select.option', 2, 2),
				JHTML::_('select.option', 3, 3),
				JHTML::_('select.option', 4, 4),
			);
			$lists['columns'] = JHTML::_('select.genericlist', $columns, 'columns', 'id="columns" class="inputbox" size="1"', 'value', 'text',  $sitemap->columns);

			// get list of menu entries in all menus
			$query = "SELECT id AS value, name AS text, CONCAT( id, ' - ', name ) AS menu"
			. "\n FROM #__menu"
			. "\n WHERE published != -2"
			. "\n ORDER BY menutype, parent, ordering";
			$database->setQuery( $query );
			$exclmenus = $database->loadObjectList();
			$lists['exclmenus'] = JHTML::_('select.genericlist', $exclmenus, 'excl_menus', 'class="inputbox" size="1"', 'value', 'menu', NULL);

			XmapAdminHtml::showSitemapSettings($sitemap,$lists);
		} else {
			echo _XMAP_MSG_ERROR_LOADING_SITEMAP;
		}
	break;
	case 'save_sitemap_settings':
		$id = intval (JRequest::getVar('id','',"REQUEST"));
		if (!$id || $id != JRequest::getVar('id','',"REQUEST")) {
			die("Invalid Sitemap ID");
		}
		$sitemap = new XmapSitemap($database);
		if ( $sitemap->load($id) ) {
			$_POST['menus']=$sitemap->menus;
			$sitemap->bind($_POST);
			if ( $sitemap->save() ) {
				if ( $sitemap->usecache ) {
					XmapCache::cleanCache($sitemap);
				}
				echo 1;
			} else {
				echo $database->getErrorMsg();
			}
		} else {
			die("Invalid Sitemap ID");
		}
	break;
	case 'save_plugin_settings':
		$id = intval (JRequest::getVar('id','',"REQUEST"));
		if (!$id || $id != JRequest::getVar('id','',"REQUEST")) {
			die("Invalid Plugin ID");
		}
		$plugin = new XmapPlugin($database,$id);
		if ( $plugin->id ) {
			$params = JRequest::getVar('params', '' ,"POST");
			$itemid = JRequest::getVar('itemid', '-1' ,"POST");
			if (is_array( $params )) {
				$plugin->parseParams();
				$txt = array();
				foreach ($params as $k=>$v) {
					$txt[] = "$k=" . str_replace( "\n", '<br />', $v );
				}

				$params = implode("\n",$txt);
				$plugin->setParams($params,$itemid);
				if ( $plugin->store() ) {
					echo 1;
				} else {
					echo $database->getErrorMsg();
				}
			}
		} else {
			die("Invalid Plugin ID");
		}
	break;
	case 'set_default':
		$id = intval (JRequest::getVar('sitemap','',"REQUEST"));
		if (!$id || $id != JRequest::getVar('sitemap','',"REQUEST")) {
			die("Invalid Sitemap ID");
		}
		$config = new XmapConfig();
		# $config->load();
		$config->sitemap_default=$id;
		if ($config->save()) {
			echo '1';
		} else {
			echo $database->getErrorMsg();
		}
	break;
	case 'change_plugin_state':
		$id = intval (JRequest::getVar('plugin','',"REQUEST"));
		if (!$id || $id != JRequest::getVar('plugin','',"REQUEST")) {
			die("Invalid Plugin ID");
		}
		$plugin = new XmapPlugin($database,$id);
		$plugin->published=($plugin->published? 0 : 1);
		if ($plugin->store()) {
			echo $plugin->published;
		} else {
			echo $database->getErrorMsg();
		}
	break;
	case 'clean_cache_sitemap':
		$id = intval (JRequest::getVar('sitemap','',"REQUEST"));
		if (!$id || $id != JRequest::getVar('sitemap','',"REQUEST")) {
			die("Invalid Sitemap ID");
		}
		$sitemap = new XmapSitemap($database);
		if ($sitemap->load($id)) {
			if ( XmapCache::cleanCache($sitemap) )  {
				echo _XMAP_MSG_CACHE_CLEANED;
			} else {
				echo _XMAP_MSG_ERROR_CLEAN_CACHE;
			}
		
		} else {
			echo $database->getErrorMsg();
		}
	break;
	case 'add_menu_sitemap':
		$id = intval (JRequest::getVar('sitemap','',"REQUEST"));
		if (!$id || $id != JRequest::getVar('sitemap','',"REQUEST")) {
			die("Invalid Sitemap ID");
		}
		$sitemap = new XmapSitemap($database);
		if ( $sitemap->load($id) ) {
			$menus = $sitemap->getMenus();	
			$newMenus = JRequest::getVar('menus',array(),"REQUEST");
			$ordering = count($menus);
			foreach ($newMenus as $aMenu) {
				if (empty($menus[$aMenu])) {
					$menu = new stdclass;
					$menu->show = 1;
					$menu->showXML = 1;
					$menu->ordering = $ordering++;
					$menu->priority = '0.5';
					$menu->changefreq = 'daily';
					$menu->module = 'mod_mainmenu';
					$menus[$aMenu] = $menu;
				}
			}
			$sitemap->setMenus($menus);
			if ( $sitemap->save() && $sitemap->usecache) {
					XmapCache::cleanCache($sitemap);
			}
			XmapAdminHtml::printMenusList($sitemap);
		}
	break;
	case 'remove_menu_sitemap':
		$id = intval (JRequest::getVar('sitemap','',"REQUEST"));
		if (!$id || $id != JRequest::getVar('sitemap','',"REQUEST")) {
			die("Invalid Sitemap ID");
		}
		$sitemap = new XmapSitemap($database);
		if ( $sitemap->load($id) ) {
			$menus = $sitemap->getMenus();	
			$menu_delete = JRequest::getVar('menu',array(),"REQUEST");
			$newMenus = array();
			foreach ($menus as $aMenu => $menu) {
				if ($aMenu != $menu_delete) {
					$newMenus[$aMenu] = $menu;
				}
			}
			$sitemap->setMenus($newMenus);
			if ( $sitemap->save() && $sitemap->usecache) {
					XmapCache::cleanCache($sitemap);
			}
			XmapAdminHtml::printMenusList($sitemap);
		}
	break;
	case 'move_menu_sitemap':
		$id = intval (JRequest::getVar('sitemap','',"REQUEST"));
		if (!$id || $id != JRequest::getVar('sitemap','',"REQUEST")) {
			die("Invalid Sitemap ID");
		}
		$sitemap = new XmapSitemap($database);
		if ( $sitemap->load($id) ) {
			$menu_move = JRequest::getVar('menu',array(),"REQUEST");
			$move = intval(JRequest::getVar('move',array(),"REQUEST"));
			$sitemap->orderMenu($menu_move,$move);
			if ( $sitemap->save() && $sitemap->usecache) {
					XmapCache::cleanCache($sitemap);
			}
			
			XmapAdminHtml::printMenusList($sitemap);
		}
	break;
	case 'get_menus_sitemap':
		$id = intval (JRequest::getVar('sitemap','',"REQUEST"));
		if (!$id || $id != JRequest::getVar('sitemap','',"REQUEST")) {
			die("Invalid Sitemap ID");
		}
		$sitemap = new XmapSitemap($database);
		if ( $sitemap->load($id) ) {
			XmapAdminHtml::printMenusList($sitemap);
		}
	break;
	case 'menu_options':
		$id = intval (JRequest::getVar('sitemap','',"REQUEST"));
		$sitemap = new XmapSitemap($database);
		if ( !$sitemap->load($id) ) {
			die('Cannot load sitemap');
		}
		$menutype = JRequest::getVar('menutype','',"REQUEST");
		$menus = $sitemap->getMenus();
		$menu = $menus[$menutype];
		$changefreq = array();
		$changefreq[] = JHTML::_('select.option', 'always', _XMAP_CFG_CHANGEFREQ_ALWAYS);
		$changefreq[] = JHTML::_('select.option', 'hourly', _XMAP_CFG_CHANGEFREQ_HOURLY);
		$changefreq[] = JHTML::_('select.option', 'daily', _XMAP_CFG_CHANGEFREQ_DAILY);
		$changefreq[] = JHTML::_('select.option', 'weekly', _XMAP_CFG_CHANGEFREQ_WEEKLY);
		$changefreq[] = JHTML::_('select.option', 'monthly', _XMAP_CFG_CHANGEFREQ_MONTHLY);
		$changefreq[] = JHTML::_('select.option', 'yearly', _XMAP_CFG_CHANGEFREQ_YEARLY);
		$changefreq[] = JHTML::_('select.option', 'never', _XMAP_CFG_CHANGEFREQ_NEVER);
		$lists['changefreq'] = JHTML::_('select.genericlist', $changefreq, 'changefreq', 'class="inputbox" size="1"', 'value', 'text', $menu->changefreq);
		$priority = array();
		for ($i=0;$i<=9;$i++) {
			$priority[] =  JHTML::_('select.option', '0.'.$i, '0.'.$i );
		}

		$priority[] =  JHTML::_('select.option', '1', '1' );
		$lists['priority'] = JHTML::_('select.genericlist', $priority, 'priority', 'class="inputbox" size="1"', 'value', 'text', $menu->priority);
		XmapAdminHtml::showMenuOptions($sitemap,$menu,$lists);
	break;
	case 'save_menu_options':
		$id = intval (JRequest::getVar('sitemap','',"REQUEST"));
		$sitemap = new XmapSitemap($database);
		if ( !$sitemap->load($id) ) {
			die('Cannot load sitemap');
		}
		$menutype = JRequest::getVar('menutype','',"REQUEST");
		$menus = $sitemap->getMenus();
		if (!empty($menus[$menutype]) ) {
			$menu = &$menus[$menutype];
			$menu->show = JRequest::getVar('show','',"POST");
			$menu->showXML = JRequest::getVar('showXML','',"POST");
			$menu->priority = JRequest::getVar('priority','',"POST");
			$menu->changefreq = JRequest::getVar('changefreq','',"POST");
			$menu->module = JRequest::getVar('module','',"POST");

			# Clean the cache of the sitemap

			
			$sitemap->setMenus($menus);
			if ($sitemap->save()) {
				if ($sitemap->usecache) {
					XmapCache::cleanCache($sitemap);
				}
				echo 1;
			} else {
				echo $database->getErrorMsg();
			}
		}
	break;
	case 'uninstallplugin':
		$id = intval (JRequest::getVar('plugin','',"REQUEST"));
		if ($id != JRequest::getVar('plugin','',"REQUEST")) {  //Security Check!
			die('Cannot load plugin');
		}
		if (xmapUninstallPlugin( $id )) {
			echo 1;
		}
		break;
	case 'edit_plugin_settings':
		$id = intval (JRequest::getVar('plugin','',"REQUEST"));
		$plugin = new XmapPlugin($database);
		if ($id != JRequest::getVar('plugin','',"REQUEST") || !$plugin->load($id)) { 
			die('Cannot load plugin');
		}
		XmapAdminHtml::showPluginSettings($plugin);
		
	break;
}

<?php 
/**
 * $Id: XmapNav.php 67 2009-11-26 18:56:32Z guilleva $
 * $LastChangedDate: 2009-11-26 12:56:32 -0600 (jue, 26 nov 2009) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * A Sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Direct Access to this location is not allowed.'); 

class XmapNav  {
	var $_list;
	/** @var XmapConfig Configuration settings */
	var $config;
	/** @var XmapSitemap Configuration settings */
	var $sitemap;
	/** @var integer The current user's access level */
	var $gid;
	/** @var boolean Is authentication disabled for this website? */
	var $noauth;
	/** @var string Current time as a ready to use SQL timeval */
	var $now;
	/** @var object Access restrictions for user */
	var $access;
	/** @var string Type of sitemap to be generated */
	var $view;
	/** @var string count of links on sitemap */
	var $count=0;
	var $isNews = 0;

	function XmapNav(&$config, &$sitemap) {
		$this->_list=array();
		$this->view='navigator';
		global $mainframe;

		jimport('joomla.utilities.date');

		$user =& JFactory::getUser();

		$access = new stdClass();
		$access->canEdit	= $user->authorize('com_content', 'edit', 'content', 'all');
		$access->canEditOwn     = $user->authorize('com_content', 'edit', 'content', 'own');
		$access->canPublish     = $user->authorize('com_content', 'publish', 'content', 'all');
		$this->access = &$access;

		$date = new JDate();

		$this->noauth 	= $mainframe->getCfg( 'shownoauth' );
		$this->gid	= $user->get('gid');
		$this->now	= $date->toUnix();
		$this->config = &$config;
		$this->sitemap = &$sitemap;
		$this->isNews	= false;
		$this->_isAdmin = ($this->gid == "25");

	}
	
	function printNode( &$node ) {
		if (!isset($node->selectable )) {
			$node->selectable=true;
		}
		// For extentions that doesn't set this property as this is new in Xmap 1.2.3
		if (!isset($node->expandible )) { 
			$node->expandible = true;
		}
		if ( empty($this->_list[$node->uid]) ) { // Avoid duplicated items
			$this->_list[$node->uid] = $node;
		}
		return false;
	}
	function startOutput( &$menus, &$config ) {
	}
	function endOutput( &$menus ) {
	}

	function startMenu(&$menu) {
		return true;
	}
	function changeLevel($level){
		return true;
	}
	function endMenu(&$menu) {
		return true;
	}
	function &expandLink(&$parent,&$extensions)	{
		$items = &JSite::getMenu();
		$rows = null;
		if (strpos($parent->link,'-menu-') === 0 ) {
			$menutype = str_replace('-menu-','',$parent->link);
			// Get Menu Items
			$rows = $items->getItems('menutype', $menutype);
		} elseif ($parent->id) {
			$rows = $items->getItems('parent', $parent->id);
		}
		if ( $rows ) {
			$router = JSite::getRouter();
			foreach ($rows as $item) {
				if ($item->parent == $parent->id) {
					$item->mid = $item->id;
					if ($item->type == 'menulink') {
						$menu = &JSite::getMenu();
						$params = new JParameter($item->params);
						if ($newItem = $menu->getItem($params->get('menu_item'))) {
							$item->type = $newItem->type;
							$item->mid = $newItem->id;
							$item->parent = $newItem->parent;
							$item->link = $newItem->link;
						}
					}
			
					$node = new stdclass;
					$node->name = $item->name;
					$node->id   = $item->id;
					$node->uid  = 'itemid'.$item->id;
					$node->link = $item->link;
					$node->expandible = true;
					$node->selectable=true;
					// Prepare the node link
					XmapPlugins::prepareMenuItem($node,$extensions);
					if ( $item->home ) {
						$node->link = JURI::root();
					} elseif (substr($item->link,0,9) == 'index.php' && $item->type != 'url' && $item->type != 'separator') {
						if ( strpos($node->link,'Itemid=') === FALSE ){
							$node->link = $router->getMode() == JROUTER_MODE_SEF ? 'index.php?Itemid='.$node->id : $node->link.'&Itemid='.$node->id;
						}
					}
					$this->printNode($node);  // Add to the internal list
				}
			}
		} 
		if ($parent->id) {
			$option = null;
			if ( preg_match('#^/?index.php.*option=(com_[^&]+)#',$parent->link,$matches) ) {
				$option = $matches[1];
			}
			$Itemid = JRequest::getInt('Itemid');
			if (!$option && $Itemid) {
				$item = $items->getItem($Itemid);
				if ( preg_match('#^/?index.php.*option=(com_[^&]+)#',$item->link,$matches) ) {
					$option = $matches[1];
					$parent->link = $item->link;
				}
			}
			if ( $option ) {
				if ( !empty($extensions[$option]) ) {
					$parent->uid = $option;
					$className = 'xmap_'.$option;
					$result = call_user_func_array(array($className, 'getTree'),array(&$this,&$parent,$extensions[$option]->getParams()));
				}
			}
		}
		return $this->_list;;
	}

	function &getParam($arr, $name, $def) {
		$var = JArrayHelper::getValue( $arr, $name, $def, '' );
		return $var;
	}
}

<?php 
/**
 * $Id: XmapPlugins.php 67 2009-11-26 18:56:32Z guilleva $
 * $LastChangedDate: 2009-11-26 12:56:32 -0600 (jue, 26 nov 2009) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'XmapPlugin.php');

/** Wraps all extension functions for Xmap */
class XmapPlugins {

	/** list all extension files found in the extensions directory */
	function &loadAvailablePlugins( ) {
		$database = & JFactory::getDBO();
		$list = array();

		$query="select * from `#__xmap_ext` where `published`=1 and extension not like '%.bak'";
		$database->setQuery($query);
		$rows = $database->loadAssocList();
		foreach ($rows as $row) {
//                	ini_set('display_errors','Off');
//			error_reporting(E_ALL);
			$extension = new XmapPlugin($database);
			$extension->bind($row);
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions'.DS.$extension->extension.'.php');
			$list[$extension->extension] = $extension;
		}
		return $list;
	}

	/** Determine which extension-object handles this content and let it generate a tree */
	function &printTree( &$xmap, &$parent, &$cache, &$extensions ) {
		$result = null;

		$matches=array();
		if ( preg_match('#^/?index.php.*option=(com_[^&]+)#',$parent->link,$matches) ) {
			$option = $matches[1];
			if ( !empty($extensions[$option]) ) {
				$parent->uid = $option;
				$className = 'xmap_'.$option;
				$result = call_user_func_array(array($className, 'getTree'),array(&$xmap,&$parent,$extensions[$option]->getParams()));
			}
		}
		return $result;
	}
	
	function prepareMenuItem(&$node,&$extensions) {
		if ( preg_match('#^/?index.php.*option=(com_[^&]+)#',$node->link,$matches) ) {
			$option = $matches[1];
			if ( !empty($extensions[$option]) ) {
				$className = 'xmap_'.$option;
				$obj = new $className;
				if (method_exists($obj,'prepareMenuItem')) {
					$obj->prepareMenuItem($node);
				}
			}
		}
	}
}

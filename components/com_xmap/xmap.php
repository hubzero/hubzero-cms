<?php
/**
 * $Id: xmap.php 114 2010-04-25 13:09:45Z guilleva $
 * $LastChangedDate: 2010-04-25 07:09:45 -0600 (dom, 25 abr 2010) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/


#ini_set('display_errors', 1);
#error_reporting(E_ALL);

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// load Xmap language file


$view = JRequest::getVar('view', 'html' ,"REQUEST");
$task = JRequest::getVar('task', '' ,"REQUEST");
$news = JRequest::getInt('news', 0 ,"REQUEST");

// Security check
$Itemid = JRequest::getInt('Itemid', 0 ,"REQUEST");
JRequest::setVar('Itemid', $Itemid);

if ($view == 'xslfile' || $view == 'xsladminfile') {
	header('Content-Type: application/xml; charset="utf-8"');
	header('Content-Disposition: inline');
	if ($view == 'xslfile') {
		header('Content-Length: ' . filesize(JPATH_COMPONENT_SITE.DS.'gss.xsl'));
		readfile(JPATH_COMPONENT_SITE.DS.'gss.xsl');
	} else {
		header('Content-Length: ' . filesize(JPATH_COMPONENT_SITE.DS.'gssadmin.xsl'));
		readfile(JPATH_COMPONENT_SITE.DS.'gssadmin.xsl');
	}
	exit;
}



$lang =& JFactory::getLanguage();
$language = $lang->getBackwardLang();
$LangPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'language'.DS;
if( file_exists( $LangPath . $language . '.php') ) {
	 require_once( $LangPath . $language. '.php' );
} else {
	 require_once( $LangPath . 'english.php' );
}

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'XmapConfig.php' );
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'XmapSitemap.php' );
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'XmapPlugins.php' );
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'XmapCache.php' );

$user =& JFactory::getUser();

global $xSitemap,$xConfig;
$xConfig = new XmapConfig;
$xConfig->load();

// This is an AJAX request to modify a item of the sitemap
if ($task == 'editElement') {

	if ($user->get('gid') != "25") {
		die('Invalid request!');
	}
	$sitemapid=JRequest::getInt( 'sitemap',0);
	if ($sitemapid) {
		$sitemap = new XmapSitemap($database);
		if ( $sitemap->load($sitemapid) ) {
			$action = JRequest::getCmd('action','');
			$uid = JRequest::getCmd('uid','');
			$itemid = JRequest::getInt('itemid','');
			switch (  $action ) {
				case 'toggleElement':
					if ($uid && $itemid) {
						$state = $sitemap->toggleItem($uid,$itemid);
					}
					break;
				case 'changeProperty':
					$uid      = JRequest::getCmd('uid','');
					$property = JRequest::getCmd('property','');
					$value = JRequest::getCmd('value','');
					if ( $uid && $itemid && $uid && $property ) {
						$state = $sitemap->chageItemPropery($uid,$itemid,'xml',$property,$value);
					}
					break;
			}
		}
	}

	header('Content-Type: text/xml');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	echo "<response>\n";
	echo " <result>OK</result>\n";
	echo " <state>".$state."</state>\n";
	echo "</response>\n";
	exit;
} elseif ($task == 'navigator') {
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'admin.xmap.html.php');
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'XmapAdmin.php');
	xmapShowNavigator($xConfig);
	return;
} elseif ($task == 'navigator-links') {
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'admin.xmap.html.php');
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'XmapAdmin.php');
	xmapShowNavigatorLinks($xConfig);
	return;
}




$Itemid = intval(JRequest::getVar('Itemid', '' ,"REQUEST"));
$sitemapid =  '';

// Firts lets try to get the sitemap's id from the menu's params
if ( $Itemid ) {
	$menu = JTable::getInstance('Menu');
	$menu->load( $Itemid );
	$params = new JParameter($menu->params );
	$sitemapid=intval($params->get( 'sitemap','' ));
}

if (!$sitemapid) { //If the is no sitemap id specificated
	$sitemapid = intval(JRequest::getVar('sitemap','',"REQUEST"));
}

if ( !$sitemapid && $xConfig->sitemap_default ) {
	$sitemapid = $xConfig->sitemap_default;
}
$database= &JFactory::getDBO();
$xSitemap = new XmapSitemap($database);
$xSitemap->load($sitemapid);

//$database->setQuery('alter table #__xmap_sitemap add excluded_items text');
//$database->query();

if (!$xSitemap->id) {
	echo _XMAP_MSG_NO_SITEMAP;
	return;
}
if ( $view=='xml' ) {
	Header("Content-type: text/xml; charset=UTF-8");
	Header("Content-encoding: UTF-8");
}

global $xmap;

$xmapCache = XmapCache::getCache($xSitemap);

$excluded_items = $xSitemap->getExcludedItems();

if ($xSitemap->usecache) {
	$lang = JFactory::getLanguage();
	$xmapCache->call('xmapCallShowSitemap',$view,$xSitemap->id,$excluded_items,$lang->getName(),$mainframe->getCfg('sef'),$user->get('id'),$news);	// call plugin's handler function
} else {
	xmapCallShowSitemap($view,$xSitemap->id,$excluded_items);
}

switch ($view) {
	case 'html':
		$xSitemap->views_html++;
		$xSitemap->lastvisit_html = time();
		$xSitemap->save();
	break;

	case 'xml':
		$xSitemap->views_xml++;
		$xSitemap->lastvisit_xml = time();
		$xSitemap->save();

		$scriptname = basename($_SERVER['SCRIPT_NAME']);
		$no_html = intval(JRequest::getVar('no_html', '0',"REQUEST"));
		if ($view=='xml' && $scriptname != 'index2.php' || $no_html != 1) {
			die();
		}
	break;
}

/**
* Function called to generate and generate the tree. Created specially to
* use with the cache call method
* The params locale and sef are only for cache purppses
*/
function xmapCallShowSitemap($view,$sitemapid,$excluded_items,$locale='',$sef='',$userid=0,$news=0) {
	global $xmapCache,$xSitemap,$xConfig;

	$xSitemap->loadItems($view);

	$live_site = substr_replace(JURI::root(), "", -1, 1);

	switch( $view ) {
		case 'xml': 	// XML Sitemaps output
			// Turn off all error reporting
			@ini_set('display_errors','Off');
			// Set a high time limit to avoid problems. I think 900 seconds should be enough
			@set_time_limit(900);

			require_once(JPATH_COMPONENT_SITE .'/xmap.xml.php' );
			$xmap = new XmapXML( $xConfig, $xSitemap );
			$xmap->generateSitemap($view,$xConfig,$xmapCache);
			$xSitemap->count_xml = $xmap->count;
			break;
		default:	// Html output
			global $mainframe;
			require_once( $mainframe->getPath('front_html') );
			if (!$xConfig->exclude_css) {
				$mainframe->addCustomHeadTag( '<link rel="stylesheet" type="text/css" media="all" href="' . $live_site . '/components/com_xmap/css/xmap.css" />' );
			}
			$xmap = new XmapHtml( $xConfig, $xSitemap );
			$xmap->generateSitemap($view,$xConfig,$xmapCache);
			$xSitemap->count_html = $xmap->count;
			break;
	}
}


class Xmap {
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

	/** Default constructor, requires the config as parameter. */
	function Xmap( &$config, &$sitemap ) {
		global $mainframe;

		jimport('joomla.utilities.date');

		$user =& JFactory::getUser();

		$access = new stdClass();
		$access->canEdit        = $user->authorize('com_content', 'edit', 'content', 'all');
		$access->canEditOwn     = $user->authorize('com_content', 'edit', 'content', 'own');
		$access->canPublish     = $user->authorize('com_content', 'publish', 'content', 'all');
		$this->access = &$access;

		$date = new JDate();

		$this->noauth 	= $mainframe->getCfg( 'shownoauth' );
		$this->gid	= $user->gid;
		$this->now	= $date->toUnix();
		$this->config = &$config;
		$this->sitemap = &$sitemap;
		$this->isNews	= false;
		$this->_isAdmin = ($user->gid == "25");
	}

	/** Generate a full website tree */
	function generateSitemap( $type,&$config, &$cache ) {
		$menus = $this->sitemap->getMenus();
		$extensions = XmapPlugins::loadAvailablePlugins();
		$root = array();
		$this->startOutput($menus,$config);
		foreach ( $menus as $menutype => $menu ) {
			if ( ($type == 'html' && !$menu->show) || ($type == 'xml' && !$menu->showXML ) ) {
				continue;
			}

			$node = new stdclass();
			$menu->id = 0;
			$menu->menutype = $menutype;

			$node->uid = $menu->uid = "menu".$menu->id;
			$node->menutype = $menutype;
			$node->ordering = $menu->ordering;
			$node->priority = $menu->priority;
			$node->changefreq = $menu->changefreq;
			$node->browserNav = 3;
			$node->type = 'separator';
			$node->name = $this->getMenuTitle($menutype,@$menu->module);	// get the mod_mainmenu title from modules table

			$this->startMenu($node);
			$this->printMenuTree($menu,$cache,$extensions);
			$this->endMenu($node);
		}
		$this->endOutput($menus);
		return true;
	}

	/** Get a Menu's tree
	 * Get the complete list of menu entries where the menu is in $menutype.
	 * If the component, that is linked to the menuentry, has a registered handler,
	 * this function will call the handler routine and add the complete tree.
	 * A tree with subtrees for each menuentry is returned.
	 */
	function printMenuTree( &$menu, &$cache, $extensions) {
		$database = &JFactory::getDBO();

		if( strlen($menu->menutype) == 0 ) {
			$result = null;
			return $result;
		}

		$menuExluded	= explode( ',', $this->sitemap->exclmenus ); 		// by mic: fill array with excluded menu IDs
        $lang = JFactory::getLanguage();
		$currentLang = $lang->getTag();

		/* * noauth is true:
			- Will show links to registered content, even if the client is not logged in.
			- The user will need to login to see the item in full.
			* noauth is false:
			- Will show only links to content for which the logged in client has access.
		*/
		$sql = "SELECT m.id, m.name, m.parent, m.link, m.type, m.browserNav, m.menutype, m.ordering, m.params, m.componentid,m.home, c.name AS component"
	 		. "\n FROM #__menu AS m"
	 		. "\n LEFT JOIN #__components AS c ON m.type='components' AND c.id=m.componentid"
	 		. "\n WHERE m.published='1' AND m.parent=".( isset($menu->mid)? $menu->mid : $menu->id )." AND m.menutype = '".$menu->menutype."'"
	 		. ( $this->noauth ? '' : "\n AND m.access <= '". $this->gid ."'" )
	 		. " AND ((m.params LIKE '%\nlang=\n%' OR m.params NOT LIKE '%\nlang=%') OR m.params LIKE '%lang=$currentLang%')"
	 		. "\n ORDER BY m.menutype,m.parent,m.ordering";

		// Load all menuentries
		$database->setQuery( $sql );
		$items = $database->loadObjectList();

		if( count($items) <= 0) {	//ignore empty menus
			$result = null;
			return $result;
		}

		$this->changeLevel(1);
		$router = JSite::getRouter();

		foreach ( $items as $i => $item ) {		// Add each menu entry to the root tree.
			$item->priority   = @$menu->priority;
			$item->changefreq = @$menu->changefreq;
			if( in_array( $item->id, $menuExluded ) ) {	// ignore exluded menu-items
				continue;
			}

			$item->mid = $item->id;
			if ($item->type == 'menulink') {
				$menu = &JSite::getMenu();
				$params = new JParameter($item->params);
				if ($newItem = $menu->getItem($params->get('menu_item'))) {
					$item->type = $newItem->type;
					$item->id = $newItem->id;
					$item->parent = $newItem->parent;
					$item->link = $newItem->link;
					$item->home = $newItem->home;
				}
			}

			$node = new stdclass;

			$node->id 		= $item->id;
			$node->mid 		= $item->mid;
			$node->uid 		= "itemid".(isset($item->mid)? $item->mid : $item->id);
			$node->name 		= $item->name;						// displayed name of node
			$node->parent 		= $item->parent;					// id of parent node
			$node->browserNav 	= $item->browserNav;					// how to open link
			$node->ordering 	= isset( $item->ordering ) ? $item->ordering : $i;	// display-order of the menuentry
			$node->priority 	= $item->priority;
			$node->changefreq 	= $item->changefreq;
			$node->type 		= $item->type;						// menuentry-type
			$node->menutype 	= $item->menutype;					// menuentry-type
			$node->home 		= $item->home;					// menuentry-type
			$node->link 		= isset( $item->link ) ? htmlspecialchars( $item->link ) : '';

			if ( $node->type == 'separator') {
				$node->browserNav=3;
			}

			XmapPlugins::prepareMenuItem($node,$extensions); 	// Let's see if the extension wants to do somenthing with this node before it's printed

			if ( $node->home ) {
				if (isset($_REQUEST['lang']) && !empty($_REQUEST['lang']) ) {
				        $node->link = JURI::base().'index.php?lang='.$_REQUEST['lang'];
				} else {
					$node->link = JURI::base();
				}
			} elseif (substr($item->link,0,9) == 'index.php' && $item->type != 'url' && $item->type != 'separator') {
				if ( strpos($node->link,'Itemid=') === FALSE ){
					$node->link = $router->getMode() == JROUTER_MODE_SEF ? 'index.php?Itemid='.$node->id : $node->link.'&Itemid='.$node->id;
				}
			}

			if ($this->printNode($node) ) {
				if ( preg_match('/option=(com_[a-z0-9_]+)/i',$item->link,$matches ) ) {
	                                // Set the uid of the node to the component uid after print it
					// so its children dont use a wrong uid
					# echo $node->uid = $matches[1];
				}
				$this->printMenuTree($node,$cache,$extensions);
				XmapPlugins::printTree( $this, $item, $cache, $extensions );	// Determine the menu entry's type and call it's handler
			}
		}
		$this->changeLevel(-1);
	}

	/** Look up the title for the module that links to $menutype */
	function getMenuTitle($menutype,$module='mod_mainmenu') {
		$database = &JFactory::getDBO();
		$query = "SELECT * FROM #__modules WHERE published='1' AND module ='$module' AND params LIKE '%menutype=". $menutype ."%'";
		$database->setQuery( $query );
		if( !$row = $database->loadObject() )
			return '';
		return $row->title;
	}

	function getItemLink (&$node) {
		static $live_site;
                if (!isset($live_site)) {
                        $juri = &JURI::getInstance();
                        $live_site = $juri->getScheme().'://'.$juri->getHost();
			if ($juri->getPort() != NULL)
				$live_site = $live_site.':'.$juri->getPort();

                }

		$link = $node->link;
		if ( isset($node->id) ) {
			switch( @$node->type ) {
				case 'separator':
					break;
				default:
					if ( !@$node->home && preg_match( "#^/?index\.php\?#", $link ) ) {
						if ( strpos( $link, 'Itemid=') === FALSE ) {
							if (strpos( $link, '?') === FALSE ) {
								$link .= '?Itemid='.$node->id;
							} else {
								$link .= '&amp;Itemid='.$node->id;
							}
						}
					}
					break;
			}
		}

		if( !preg_match('#^[a-z0-9]+:#i',$link)) {
			if (strcasecmp( substr( $link, 0, 9), 'index.php' ) === 0 ) {
				$link = JRoute::_($link);             // apply SEF transformation
				if ( strcasecmp( substr($link,0,4), 'http' ) && $this->view=='xml') {       // XML sitemap requires full path URL's
					$link = $live_site. (substr($link,0,1) == '/'? '' : '/').$link;
				}
			} else { // Case for internal links not starting with index.php
				if (substr($link, 0, 1) == '/')
					$link = $live_site.$link;
				else
					$link = $live_site. '/' .$link;
			}
		}

		return $link;
	}

	/** called with usort to sort menus */
	function sort_ordering( &$a, &$b) {
		if( $a->ordering == $b->ordering )
			return 0;
		return $a->ordering < $b->ordering ? -1 : 1;
	}

	function &getParam($arr, $name, $def) {
		$var = JArrayHelper::getValue( $arr, $name, $def, '' );
		return $var;
	}
}

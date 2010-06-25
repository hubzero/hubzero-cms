<?php
/**
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for JoomSuite Content component
*/

defined( '_VALID_MOS' ) or defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/** Adds support for JoomSuite Content categories to Xmap */
class xmap_com_resource {
 
	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item
	*/
	function prepareMenuItem(&$node) {
		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$catid = xmap_com_resource::getParam($link_vars,'category_id',0);
		$cid = xmap_com_resource::getParam($link_vars,'article',0);
		if ( !$catid ) {
			$menu =& JSite::getMenu();
			$params = $menu->getParams($node->id);
			$catid = $params->get('category_id',0);
		}
		if ( $cid && $catid ) {
			$node->uid = 'com_resourcec'.$catid.'a'.$cid;
		} elseif ( $catid ) {
			$node->uid = 'com_resourcec'.$catid;
		}
	}

	/** Get the content tree for this kind of content */
	function getTree( &$xmap, &$parent, &$params ) {

		$menu =& JSite::getMenu();
		$db =& JFactory::getDBO();
		$vmparams = $menu->getParams($parent->id);

		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$catid = intval(xmap_com_resource::getParam($link_vars,'category_id',0));
		$cid = intval(xmap_com_resource::getParam($link_vars,'content_id',0));
		$params['Itemid'] = intval(xmap_com_resource::getParam($link_vars,'Itemid',$parent->id));

		$view = xmap_com_resource::getParam($link_vars,'view','');


		if ( !$catid ) {
			$catid = intval($vmparams->get('category_id',0));
		}

		if ($view && $view != 'list') {  // We only expand category menu items or item
			return true;
		}

		// Check if the user has privileges to view this category
		if ( $catid ) {
			$db->setQuery('SELECT `access` FROM #__js_res_category WHERE id='.$catid);
			$access = $db->loadResult();
			if ( $access > $xmap->gid ) {
				return true;
			}
		}

		if ( $cid )
			return $tree;

		$include_articles = xmap_com_resource::getParam($params,'include_articles',1);
		$include_articles = ( $include_articles == 1
				  || ( $include_articles == 2 && $xmap->view == 'xml') 
				  || ( $include_articles == 3 && $xmap->view == 'html')
				  ||   $xmap->view == 'navigator');
		$params['include_articles'] = $include_articles;

		$priority = xmap_com_resource::getParam($params,'cat_priority',$parent->priority);
		$changefreq = xmap_com_resource::getParam($params,'cat_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = xmap_com_resource::getParam($params,'article_priority',$parent->priority);
		$changefreq = xmap_com_resource::getParam($params,'article_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['article_priority'] = $priority;
		$params['article_changefreq'] = $changefreq;


        $params['limit'] = '';
        $limit = intval(JArrayHelper::getValue($params,'max_articles',0,''));

        if ( intval($limit) && $xmap->view != 'navigator' ) {
            $params['limit'] = ' LIMIT '.$limit;
        }
        
        
		xmap_com_resource::getCategoryTree($xmap, $parent, $params, $catid);
		return true;
	}

	/** JoomSuite Content support */
	function &getCategoryTree( &$xmap, &$parent,&$params, $catid=0 ) {

		$database = &JFactory::getDBO();
		$list = array();

		$query  = 
		 "SELECT c.name,c.id,UNIX_TIMESTAMP(c.ctime) as ctime ,UNIX_TIMESTAMP(c.mtime) as mtime "
		."\n FROM #__js_res_category AS c "
		."\n WHERE "
		."\n c.published=1 "
		."\n AND c.access<=".$xmap->gid
		."\n AND c.parent=$catid "
		."\n ORDER BY c.ordering ASC";

		$database->setQuery( $query );
		$database->getQuery();

		$rows = $database->loadObjectList();

		$xmap->changeLevel(1);
		foreach($rows as $row) {
			$node = new stdclass;
			$node->id = $parent->id;
			$node->uid = $parent->uid.'c'.$row->id;
			$node->browserNav = $parent->browserNav;
		    	$node->name = stripslashes($row->name);
			$node->modified = ($row->mtime ? $row->mtime : $row->ctime);
			$node->priority = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->expandible = true;
			$node->link = 'index.php?option=com_resource&amp;view=list&amp;category_id='.$row->id.'&amp;Itemid='.$params['Itemid'];
		    if ( $xmap->printNode($node) !== FALSE ) {
				xmap_com_resource::getCategoryTree( $xmap, $parent, $params, $row->id);
			}
	    }
		$xmap->changeLevel(-1);

		if ( $params['include_articles'] ) {
			$query  = 
		 	"SELECT a.id,a.title,UNIX_TIMESTAMP(a.ctime) as ctime ,UNIX_TIMESTAMP(a.mtime) as mtime "
			."\n FROM #__js_res_record AS a, #__js_res_record_category AS b "
			."\n WHERE a.published=1"
		        ."\n AND a.access<=".$xmap->gid
			."\n AND b.catid=$catid "
			."\n AND a.id=b.record_id "
			."\n ORDER BY a.ordering"
            ."\n ". $params['limit'];

			$database->setQuery( $query );
			$rows = $database->loadObjectList();
			$xmap->changeLevel(1);
			foreach ( $rows as $row ) {
				$node = new stdclass;
				$node->id = $parent->id;
				$node->uid = $parent->uid.'c'.$catid.'a'.$row->id;
				$node->browserNav = $parent->browserNav;
				$node->priority = $params['article_priority'];
				$node->changefreq = $params['article_changefreq'];
				$node->name = $row->title;
				$node->modified = ($row->mtime ? $row->mtime : $row->ctime);
				$node->expandible = false;
				$node->link = 'index.php?option=com_resource&controller=article&article='.$row->id.'&category_id='.$catid.'&amp;Itemid='.$params['Itemid'];
		    	$xmap->printNode($node);
	    	}
			$xmap->changeLevel(-1);
		}

		return $list;
	}

	function &getParam($arr, $name, $def) {
		$var = JArrayHelper::getValue( $arr, $name, $def, '' );
		return $var;
	}
}

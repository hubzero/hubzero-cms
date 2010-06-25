<?php
/**
* Mosets Tree Plug-in by Sam Lewis - Moxie Media, LLC -> GoMoxieMedia.com
* Version 1.0
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/** Handles Mosets Tree component */
class xmap_com_mtree {

	function getTree( &$xmap, &$parent, &$params ) {

		$catid=0;
		if ( strpos($parent->link, 'task=listcats') ) {
			$link_query = parse_url( $parent->link );
			parse_str( html_entity_decode($link_query['query']), $link_vars);
			$catid = xmap_com_mtree::getParam($link_vars,'cat_id',0);
		}

		$include_links = xmap_com_mtree::getParam($params,'include_links',1);
		$include_links = ( $include_links == 1
                                  || ( $include_links == 2 && $xmap->view == 'xml') 
                                  || ( $include_links == 3 && $xmap->view == 'html')
								  ||   $xmap->view == 'navigator');
		$params['include_links'] = $include_links;

		$priority = xmap_com_mtree::getParam($params,'cat_priority',$parent->priority);
		$changefreq = xmap_com_mtree::getParam($params,'cat_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = xmap_com_mtree::getParam($params,'link_priority',$parent->priority);
		$changefreq = xmap_com_mtree::getParam($params,'link_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;

		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['link_priority'] = $priority;
		$params['link_changefreq'] = $changefreq;

		$ordering = xmap_com_mtree::getParam($params,'links_order','cat_name');
		if ( !in_array($ordering,array('ordering','cat_name','cat_created')) )
			$ordering = 'cat_name';

		$params['cats_order'] = $ordering;

		if ( $include_links ) {
			$ordering = xmap_com_mtree::getParam($params,'links_order','ordering');
			if ( !in_array($ordering,array('ordering','link_name','link_modified','link_created','link_hits')) )
				$ordering = 'ordering';

			$params['links_order'] = $ordering;

			$params['limit'] = '';
			$params['days'] = '';
			$limit = xmap_com_mtree::getParam($params,'max_links','');
			if ( intval($limit) )
				$params['limit'] = ' LIMIT '.$limit;

			$days = xmap_com_mtree::getParam($params,'max_age','');
			if ( intval($days) )
				$params['days'] = ' AND time >='.($xmap->now - ($days*86400)) ." ";
		}


		xmap_com_mtree::getMtreeCategory($xmap,$parent,$params,$catid);
	}

	/* Returns URLs of all Categories and links in of one category using recursion */
	function getMtreeCategory (&$xmap, &$parent, &$params, &$catid ) {
		$database =& JFactory::getDBO();

		$query = "SELECT cat_name, cat_id, UNIX_TIMESTAMP(cat_created) as `created` ".
			 "FROM #__mt_cats WHERE cat_published='1' AND cat_approved='1' AND cat_parent = $catid " .
			 "ORDER BY `" . $params['cats_order'] ."`"; 

		$database->setQuery($query);
		$rows = $database->loadObjectList();

		$xmap->changeLevel(1);
		foreach($rows as $row) {
			if( !$row->created ) {
				$row->created = $xmap->now;
			}

			$node = new stdclass;
			$node->name = $row->cat_name;
			$node->link = 'index.php?option=com_mtree&amp;task=listcats&amp;cat_id='.$row->cat_id;
			$node->id = $parent->id;
			$node->uid = $parent->uid .'c'.$row->cat_id;
			$node->browserNav = $parent->browserNav;
			$node->modified = $row->created;
			$node->priority = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->expandible = true;

			if ( $xmap->printNode($node) !== FALSE) {
				xmap_com_mtree::getMtreeCategory($xmap,$parent,$params,$row->cat_id);
			}
		}

		/* Returns URLs of all listings in the current category */
		if ($params['include_links']) {
			$query = " SELECT a.link_name, a.link_id, UNIX_TIMESTAMP(a.link_created) as `created`,  UNIX_TIMESTAMP(a.link_modified) as `modified` \n".
			 	" FROM #__mt_links AS a, #__mt_cl as b \n".
			 	" WHERE a.link_id = b.link_id \n".
                         	" AND b.cat_id = $catid " .
                         	" AND ( link_published='1' AND link_approved='1' ) " .
			 	$params['days'] .
			 	" ORDER BY `" . $params['links_order'] ."` " .
			 	$params['limit'];

			$database->setQuery($query);

			$rows = $database->loadObjectList();

			foreach($rows as $row) {
				if( !$row->modified ) {
					$row->modified = $row->created;
				}

				$node = new stdclass;
				$node->name = $row->link_name;
				$node->link = 'index.php?option=com_mtree&amp;task=viewlink&amp;link_id='.$row->link_id;
				$node->id = $parent->id;
				$node->uid = $parent->uid.'l'.$row->link_id;
				$node->browserNav = $parent->browserNav;
				$node->modified = ($row->modified? $row->modified : $row->created);
				$node->priority = $params['link_priority'];
				$node->changefreq = $params['link_changefreq'];
				$node->expandible = false;
				$xmap->printNode($node);
			}
		}
		$xmap->changeLevel(-1);
	    
	}
	function &getParam($arr, $name, $def) {
		$var = JArrayHelper::getValue( $arr, $name, $def, '' );
		return $var;
	}
}

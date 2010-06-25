<?php
/**
 * $Id: com_rapidrecipe.php 64 2009-11-26 17:22:16Z guilleva $
 * $LastChangedDate: 2009-11-26 11:22:16 -0600 (jue, 26 nov 2009) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/** 
*  Handles Mosets Tree component 
*/
class xmap_com_rapidrecipe {
	
	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item
	*/
	function prepareMenuItem(&$node) {
		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$catid = intval(JArrayHelper::getValue($link_vars,'category_id',0));
		$id = intval(JArrayHelper::getValue($link_vars,'recipe_id',0));
		$page = JArrayHelper::getValue( $link_vars, 'page', '', '' );
		if ( $page == 'viewcategory' && $catid ) {
			$node->uid = 'com_rapidrecipec'.$catid;
			$node->expandible = true;
		} elseif ($page == 'viewrecipe' && $id) {
			$node->uid = 'com_rapidreciper'.$id;
			$node->expandible = false;
		}
	}

	function getTree( &$xmap, &$parent, &$params ) {

		$catid=0;
		if ( strpos($parent->link, 'page=viewcategory') ) {
			$link_query = parse_url( $parent->link );
			parse_str( html_entity_decode($link_query['query']), $link_vars);
			$catid = JArrayHelper::getValue( $link_vars,'category_id',0);
		}
		
		if ( strpos($parent->link, 'page=viewrecipe') ) {
			return;
		}

		$include_recipes = JArrayHelper::getValue($params,'include_recipes',1);
		$include_recipes = ( $include_recipes == 1
                                  || ( $include_recipes == 2 && $xmap->view == 'xml') 
                                  || ( $include_recipes == 3 && $xmap->view == 'html')
				  ||   $xmap->view == 'navigator');
		$params['include_recipes'] = $include_recipes;

		$priority = JArrayHelper::getValue($params,'cat_priority',$parent->priority);
		$changefreq = JArrayHelper::getValue($params,'cat_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = JArrayHelper::getValue($params,'recipe_priority',$parent->priority);
		$changefreq = JArrayHelper::getValue($params,'recipe_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;

		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['recipe_priority'] = $priority;
		$params['recipe_changefreq'] = $changefreq;

		$ordering = JArrayHelper::getValue($params,'recipes_order','ordering');
		if ( !in_array($ordering,array('ordering','title')) )
			$ordering = 'ordering';

		$params['cats_order'] = $ordering;

		if ( $include_recipes ) {
			$ordering = JArrayHelper::getValue($params,'recipes_order','ordering');
			if ( !in_array($ordering,array('ordering','title','hits','created')) )
				$ordering = 'ordering';

			$params['recipes_order'] = $ordering;

			$params['limit'] = '';
			$params['days'] = '';
			$limit = JArrayHelper::getValue($params,'max_recipes','');
			if ( intval($limit) )
				$params['limit'] = ' LIMIT '.$limit;

			$days = JArrayHelper::getValue($params,'max_age','');
			if ( intval($days) )
				$params['days'] = ' AND time >='.($xmap->now - ($days*86400)) ." ";
		}

		xmap_com_rapidrecipe::getCategory($xmap,$parent,$params,$catid);
	}

	/* Returns URLs of all Categories and recipes in of one category using recursion */
	function getCategory (&$xmap, &$parent, &$params, &$catid ) {
		$db =& JFactory::getDBO();

		$query = "SELECT title, category_id ".
			 "FROM #__rr_categories AS a WHERE a.parent_id = $catid " .
			 "     AND  a.published=1 AND a.child_all_recipes>0 " .
			 "ORDER BY `" . $params['cats_order'] ."`"; 

		$db->setQuery($query);
		//echo $db->getQuery();
		$rows = $db->loadObjectList();

		$xmap->changeLevel(1);
		foreach($rows as $row) {
			$row->created = $xmap->now;

			$node = new stdclass;
			$node->name = $row->title;
			$node->link = 'index.php?option=com_rapidrecipe&amp;page=viewcategory&amp;category_id='.$row->category_id;
			$node->id = $parent->id;
			$node->uid = $parent->uid .'c'.$row->category_id;
			$node->browserNav = $parent->browserNav;
			$node->modified = null;
			$node->priority = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->expandible = true;

			if ( $xmap->printNode($node) !== FALSE) {
				xmap_com_rapidrecipe::getCategory($xmap,$parent,$params,$row->category_id);
			}
		}

		/* Returns URLs of all listings in the current category */
		if ($params['include_recipes']) {
			$query = " SELECT a.title, a.recipe_id, UNIX_TIMESTAMP(a.created) as `created` \n".
			 	" FROM #__rr_recipes AS a, #__rr_recipecategory AS b \n".
			 	" WHERE a.recipe_id = b.recipe_id \n".
                         	" AND b.category_id = $catid " .
                         	" AND a.published=1 AND a.user_group <=".$xmap->gid .
			 	$params['days'] .
			 	" ORDER BY `" . $params['links_order'] ."` " .
			 	$params['limit'];
			$db->setQuery($query);
			//echo $db->getQuery();

			$rows = $db->loadObjectList();

			foreach($rows as $row) {
				$row->modified = $row->created;

				$node = new stdclass;
				$node->name = $row->title;
				$node->link = 'index.php?option=com_rapidrecipe&amp;page=viewrecipe&amp;recipe_id='.$row->recipe_id;
				$node->id = $parent->id;
				$node->uid = $parent->uid.'r'.$row->recipe_id;
				$node->browserNav = $parent->browserNav;
				$node->modified = $row->created;
				$node->priority = $params['recipe_priority'];
				$node->changefreq = $params['recipe_changefreq'];
				$node->expandible = false;
				$xmap->printNode($node);
			}
		}
		$xmap->changeLevel(-1);
	    
	}
}
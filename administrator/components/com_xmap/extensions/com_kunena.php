<?php
/**
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @version $Id: com_kunena.php 58 2009-10-28 03:26:21Z guilleva $
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Kunena Forum Component. 
*/

/** Handles Kunena forum structure */
class xmap_com_kunena {

	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item
	*/
	function prepareMenuItem(&$node) {
		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$catid = intval(JArrayHelper::getValue($link_vars,'catid',0));
		$id = intval(JArrayHelper::getValue($link_vars,'id',0));
		$func = JArrayHelper::getValue( $link_vars, 'func', '', '' );
		if ( $func = 'showcat' && $catid ) {
			$node->uid = 'com_kunenac'.$catid;
			$node->expandible = false;
		} elseif ($func = 'view' && $id) {
			$node->uid = 'com_kunenaf'.$id;
			$node->expandible = false;
		}
	}

	function getTree ( &$xmap, &$parent, &$params ) {
		$catid=0;
		if ( strpos($parent->link, 'func=showcat') ) {
			$link_query = parse_url( $parent->link );
			parse_str( html_entity_decode($link_query['query']), $link_vars);
			$catid = $xmap->getParam($link_vars,'catid',0);
		} elseif (strpos($parent->link, 'func=view') ) {
			return true;   // Do not expand links to posts
		}

		$include_topics = $xmap->getParam($params,'include_topics',1);
		$include_topics = ( $include_topics == 1
				  || ( $include_topics == 2 && $xmap->view == 'xml') 
				  || ( $include_topics == 3 && $xmap->view == 'html')
				  || $xmap->view == 'navigator');
		$params['include_topics'] = $include_topics;

		$priority = $xmap->getParam($params,'cat_priority',$parent->priority);
		$changefreq = $xmap->getParam($params,'cat_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = $xmap->getParam($params,'topic_priority',$parent->priority);
		$changefreq = $xmap->getParam($params,'topic_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;

		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['topic_priority'] = $priority;
		$params['topic_changefreq'] = $changefreq;

		if ( $include_topics ) {
			$ordering = $xmap->getParam($params,'topics_order','ordering');
			if ( !in_array($ordering,array('ordering','time','subject','hits')) )
				$ordering = 'ordering';
			$params['topics_order'] = $ordering = $ordering;

			$params['limit'] = '';
			$params['days'] = '';
			$limit = $xmap->getParam($params,'max_topics','');
			if ( intval($limit) )
				$params['limit'] = ' LIMIT '.$limit;

			$days = $xmap->getParam($params,'max_age','');
			if ( intval($days) )
				$params['days'] = ' AND time >='.($xmap->now - ($days*86400)) ." ";
		}

		xmap_com_kunena::getCategoryTree($xmap, $parent, $params, $catid);
	}

	/* Return category/forum tree */
	function getCategoryTree( &$xmap, &$parent, &$params, $parentCat ) 
	{
		$database =& JFactory::getDBO();
		$list = array();
		$query = "SELECT id as cat_id, name as cat_title, ordering FROM #__fb_categories WHERE parent=$parentCat AND published=1 and pub_access <=".$xmap->gid." ORDER BY name";
		$database->setQuery($query);
		# echo $database->getQuery();
		$cats = $database->loadObjectList();

		/*get list of categories*/
		$xmap->changeLevel(1);
		foreach ( $cats as $cat ) {
			$node = new stdclass;
			$node->id   = $parent->id;
			$node->browserNav = $parent->browserNav;
			$node->uid   = $parent->uid.'c'.$cat->cat_id;
			$node->name = $cat->cat_title;
			$node->priority = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->link = 'index.php?option=com_kunena&amp;func=showcat&amp;catid='.$cat->cat_id;
			$node->expandible = true;
			if ( $xmap->printNode($node) !== FALSE ) {
				xmap_com_kunena::getCategoryTree($xmap,$parent,$params,$cat->cat_id);
			}
		}

		if ( $params['include_topics'] ) {
			$query = "SELECT id as forum_id, catid as cat_id, subject as forum_name, time as modified ".
			 	"FROM #__fb_messages ".
				"WHERE catid=$parentCat ".
				"AND parent=0 " .
				$params['days'] .
				"ORDER BY `". $params['topics_order'] . "`" .
				$params['limit'];

			$database->setQuery($query);
			$forums = $database->loadObjectList();

			//get list of forums
			foreach($forums as $forum) {
				$node = new stdclass;
				$node->id   = $parent->id;
				$node->browserNav = $parent->browserNav;
				$node->uid = $parent->uid.'t'.$forum->forum_id;
				$node->name = $forum->forum_name;
				$node->priority = $params['topic_priority'];
				$node->changefreq = $params['topic_changefreq'];
				$node->modified = intval($forum->modified);
				$node->link = 'index.php?option=com_kunena&amp;func=view&amp;id='.$forum->forum_id.'&amp;catid='.$forum->cat_id;
				$node->expandible = false;
				$xmap->printNode($node);
			}
		}
		$xmap->changeLevel(-1);
	}
}
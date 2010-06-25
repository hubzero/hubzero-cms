<?php
/**
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @version $Id: com_cmsshopbuilder.php 95 2010-04-14 18:38:36Z guilleva $
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for CMS Shop Builder component
*/

defined( '_JEXEC' ) or die( 'Restricted access.' );

class xmap_com_cmsshopbuilder {

	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item
	*/
	function prepareMenuItem(&$node) {
		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$catid = intval(JArrayHelper::getValue($link_vars,'catid',0));
		$id = intval(JArrayHelper::getValue($link_vars,'id',0));
		$view = JArrayHelper::getValue( $link_vars, 'view', '', '' );
		if ( $view == 'category' && $id && !$catid) {
			$node->uid = 'com_cmsshopbuilderc'.$id;
			$node->expandible = true;
		} elseif ($view == 'category' && $catid) {
			$node->uid = 'com_cmsshopbuilderi'.$id;
			$node->expandible = false;
		} elseif ($view == 'categories')  {
            $node->uid = 'com_cmsshopbuilder'.$view;
            $node->expandible = true;
        }
	}

	function getTree( &$xmap, &$parent, &$params)
	{
		$link_query = parse_url( $parent->link );
        parse_str( html_entity_decode($link_query['query']), $link_vars );
        $catid = JArrayHelper::getValue($link_vars,'catid',0);
        $id = JArrayHelper::getValue($link_vars,'id',0);
        $view = JArrayHelper::getValue($link_vars,'view',0);

		if ( $view != 'categories' && ($view  != 'category' || $catid) ) {
			return;
		}

        $catid = $id;        

		$include_items = JArrayHelper::getValue( $params, 'include_items',1,'' );
		$include_items = ( $include_items == 1
                                  || ( $include_items == 2 && $xmap->view == 'xml')
                                  || ( $include_items == 3 && $xmap->view == 'html')
				  ||   $xmap->view == 'navigator');
		$params['include_items'] = $include_items;

		$priority = JArrayHelper::getValue($params,'cat_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'cat_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = JArrayHelper::getValue($params,'item_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'item_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;

		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['item_priority'] = $priority;
		$params['item_changefreq'] = $changefreq;

		if ( $include_items ) {
			$params['limit'] = '';
			$params['days'] = '';
			$limit = JArrayHelper::getValue($params,'max_items','','');

			if ( intval($limit) )
				$params['limit'] = ' LIMIT '.$limit;

			$days = JArrayHelper::getValue($params,'max_age','','');
			if ( intval($days) )
				$params['days'] = ' AND filedate >= \''.date('Y-m-d H:m:s', ($xmap->now - ($days*86400)) ) ."' ";
		}

		xmap_com_cmsshopbuilder::getCategoriesTree( $xmap, $parent, $params, $catid );
	}

	function getCategoriesTree ( &$xmap, &$parent, &$params, &$catid )
	{
		$db = JFactory::getDBO();
		$db->setQuery(
            "select c.cat_id, c.cat_name, ".
            "CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(':', c.cat_id, c.alias) ELSE c.cat_id END as slug ".
            "FROM `#__ms_cat` AS c, `#__ms_catref` AS x ".
            "WHERE x.cat_pid=$catid and c.cat_id=x.cat_cid AND cat_publish=1 ".
            "ORDER BY c.ordering,c.cat_name"
        );
		$cats = $db->loadObjectList();
		$xmap->changeLevel(1);

		foreach($cats as $cat) {
			$node = new stdclass;
			$node->id   = $parent->id;
			$node->uid  = $parent->uid.'c'.$cat->cat_id;   // Uniq ID for the category
            $node->name = $cat->cat_name;
			$node->priority   = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->link = 'index.php?option=com_cmsshopbuilder&amp;view=category&amp;id='.$cat->slug;
			$node->expandible = true;

			if ($xmap->printNode($node) !== FALSE ) {
				xmap_com_cmsshopbuilder::getCategoriesTree($xmap, $parent, $params, $cat->cat_id);
			}
		}

		if ( $params['include_items'] ) {
			$db->setQuery (
                "SELECT p.product_id, p.product_name,p.alias, ".
                "CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(':', c.cat_id, c.alias) ELSE c.cat_id END as catslug ".
                "FROM `#__ms_product` AS p, `#__ms_cat` AS c ".
                "WHERE p.cat_id=$catid and c.cat_id = p.cat_id AND ".
                "      p.product_publish=1 ".
                "ORDER BY p.ordering, p.product_name"
            );
            //echo $db->getQuery();
			$items = $db->loadObjectList();
			foreach($items as $item) {
				$node = new stdclass;
				$node->id   = $parent->id;  // Itemid
				$node->uid  = $parent->uid .'d'.$item->product_id; // Uniq ID for the download
				$node->name = $item->product_name;
				$node->link = 'index.php?option=com_cmsshopbuilder&amp;view=category&amp;catid='.$item->catslug.'&id='.$item->product_id.'-'.$item->alias;
				$node->priority   = $params['item_priority'];
				$node->changefreq = $params['item_changefreq'];
				$node->expandible = false;
				$xmap->printNode($node);
			}
		}
		$xmap->changeLevel(-1);
	}
}
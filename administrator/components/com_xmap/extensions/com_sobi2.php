<?php
/**
* @author Guillermo Vargas guille@vargas.co.cr
* @version $Id: com_sobi2.php 86 2010-01-03 06:20:10Z guilleva $
* @package xmap
* @license GNU/GPL
* @authorSite http://joomla.vargas.co.cr
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/** Adds support for Sobi2 categories to Xmap */
class xmap_com_sobi2 {

	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item and indicate whether the node is expandible or not
	*/
	function prepareMenuItem(&$node) {
		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$catid = JArrayHelper::getValue($link_vars,'catid',0);
		$sobi2Id = JArrayHelper::getValue($link_vars,'sobi2Id',0);
		$task = JArrayHelper::getValue($link_vars,'sobi2Task','');
		if ( $sobi2Id && $catid ) {
			$node->uid = 'com_sobi2e'.$sobi2Id;
			$node->expandible = false;
		} elseif ( $catid ) {
			$node->uid = 'com_sobi2c'.$catid;
			$node->expandible = true;
		} elseif ( $task ) {
			$node->uid = 'com_sobi2'.$task;
			$node->expandible = false;
		}
	}

	/** Get the content tree for this kind of content */
	function &getTree( &$xmap, &$parent, &$params ) {
		$tree = array();

		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$catid =JArrayHelper::getValue($link_vars,'catid',1);
		$entrieid =JArrayHelper::getValue($link_vars,'sobi2Id',0);
		$task = JArrayHelper::getValue($link_vars,'sobi2Task','');

		if ( $entrieid || $task != '')
			return $tree;

		$include_entries =JArrayHelper::getValue($params,'include_entries',1);
		$include_entries = ( $include_entries == 1
		                    || ( $include_entries == 2 && $xmap->view == 'xml')
				    		|| ( $include_entries == 3 && $xmap->view == 'html')
							||   $xmap->view == 'navigator');
		$params['include_entries'] = $include_entries;

		$priority =JArrayHelper::getValue($params,'cat_priority',$parent->priority);
                $changefreq =JArrayHelper::getValue($params,'cat_changefreq',$parent->changefreq);
		if ($priority  == '-1')
		$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority =JArrayHelper::getValue($params,'entry_priority',$parent->priority);
                $changefreq =JArrayHelper::getValue($params,'entry_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['entry_priority'] = $priority;
		$params['entry_changefreq'] = $changefreq;

		if ( $include_entries ) {
			$ordering = $xmap->getParam($params,'entries_order','a.ordering');
			$orderdir = $xmap->getParam($params,'entries_orderdir','ASC');
			if ( !in_array($ordering,array('a.ordering','a.visits','a.hits','a.publish_up','a.last_update')) ){
				$ordering = 'a.ordering';
			}
			if ( !in_array($orderdir,array('ASC','DESC')) ){
				$orderdir = 'ASC';
			}
			$params['ordering'] = $ordering. ' '. $orderdir;

			$params['limit'] = '';
			$params['days'] = '';
			$limit = $xmap->getParam($params,'max_entries','');
			if ( intval($limit) )
				$params['limit'] = ' LIMIT '.$limit;

			$days = $xmap->getParam($params,'max_age','');
			if ( intval($days) )
				$params['days'] = ' AND a.publish_up >=\''.strftime("%Y-%m-%d %H:%M:%S",$xmap->now - ($days*86400)) ."' ";
		}

		xmap_com_sobi2::getCategoryTree($xmap, $parent, $catid, $params);
		return $tree;
	}

	/** SOBI2 support */
	function getCategoryTree( &$xmap, &$parent, $catid, &$params ) {
		$database =& JFactory::getDBO();

		$query  =
		 "SELECT a.catid, a.name, b.parentid as pid "
		."\n FROM #__sobi2_categories AS a, #__sobi2_cats_relations AS b "
		."\n WHERE b.parentid=$catid"
	        ."   AND a.published=1 "
		."\n AND a.catid=b.catid "
		."\n ORDER BY a.ordering ASC";

		$database->setQuery($query);

		$database->setQuery( $query );
		$rows = $database->loadObjectList();

		$modified = time();
		$xmap->changeLevel(1);
		foreach($rows as $row) {
			$node = new stdclass;
			$node->id = $parent->id;
			$node->uid = $parent->uid.'c'.$row->catid; // Unique ID
			$node->browserNav = $parent->browserNav;
			$node->name = html_entity_decode($row->name);
			$node->modified = $modified;
			$node->link = 'index.php?option=com_sobi2&amp;catid='.$row->catid;
			$node->priority = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->expandible = true;
			if ( $xmap->printNode($node) !== FALSE ) {
				xmap_com_sobi2::getCategoryTree($xmap, $parent, $row->catid, $params);
			}
		}

		if ( $params['include_entries'] ) {
			$query  =
		 	"SELECT a.itemid, a.title,UNIX_TIMESTAMP(a.last_update) as modified,UNIX_TIMESTAMP(a.publish_up) as publish_up, b.catid "
			."\n FROM #__sobi2_item AS a, #__sobi2_cat_items_relations AS b"
			."\n WHERE a.published=1 "
			."\n AND b.catid = $catid"
			."\n AND a.approved=1 "
			."\n AND a.publish_up<=now() "
			."\n AND (a.publish_down>=now() or a.publish_down='0000-00-00 00:00:00' ) "
			."\n AND a.itemid=b.itemid "
			. $params['days']
			."\n ORDER BY " . $params['ordering']
			. $params['limit'];


			$database->setQuery( $query );
			$rows = $database->loadObjectList();
			foreach($rows as $row) {
				$node = new stdclass;
				$node->id = $parent->id;
				$node->uid = $parent->uid.'e'.$row->itemid; // Unique ID
				$node->browserNav = $parent->browserNav;
				$node->name = html_entity_decode($row->title);
				$node->modified = $row->modified? $row->modified : $row->publish_up;
				$node->priority = $params['entry_priority'];
				$node->changefreq = $params['entry_changefreq'];
				$node->expandible = false;
				// &sobi2Task=sobi2Details&catid=2&sobi2Id=1&Itemid=31
				$node->link = 'index.php?option=com_sobi2&amp;sobi2Task=sobi2Details&amp;catid='.$row->catid . '&amp;sobi2Id=' . $row->itemid;
				$xmap->printNode($node);
			}

		}
		$xmap->changeLevel(-1);

	}

}

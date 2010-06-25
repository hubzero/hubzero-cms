<?php
/**
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @version $Id: com_jcalpro.php 52 2009-10-24 22:35:11Z guilleva $
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for JCal Pro component 
*/

defined('_JEXEC') or defined ('_VALID_MOS') or die ('Restricted Access');

class xmap_com_jcalpro {

	function getTree( &$xmap, &$parent, &$params ) {
		$catid = 0;

		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$catid = intval(!empty($link_vars['cat_id'])? $link_vars['cat_id'] : 0);


		$include_events = !empty($params['include_events'])? $params['include_events'] : 1;
		$include_events = ( $include_events == 1
				|| ( $include_events == 2 && $xmap->view == 'xml')
				|| ( $include_events == 3 && $xmap->view == 'html')
				||   $xmap->view == 'navigator');
		$params['include_events'] = $include_events;

		$priority = !empty($params['cat_priority'])? $params['cat_priority'] : $parent->priority;
		$changefreq = !empty($params['cat_changefreq'])? $params['cat_changefreq'] : $parent->changefreq;
		if ( $priority  == '-1' )
			$priority = $parent->priority;
		if ( $changefreq  == '-1' )
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = !empty($params['events_priority'])? $params['events_priority'] : $parent->priority;
		$changefreq = !empty($params['events_priority'])? $params['events_changefreq'] : $parent->changefreq;
		if ( $priority  == '-1' )
			$priority = $parent->priority;
		if ( $changefreq  == '-1' )
			$changefreq = $parent->changefreq;

		$params['events_priority'] = $priority;
		$params['events_changefreq'] = $changefreq;

		xmap_com_jcalpro::getJCALPro($xmap, $parent, $params, $catid);
	}

	function getJCALPro(&$xmap, &$parent, &$params, $catid) {
		if (defined('JPATH_SITE') && defined('_JEXEC')) {
			$database = &JFactory::getDBO();
			$mosConfig_absolute_path = JPATH_SITE;
		} else {
			global $database, $mosConfig_absolute_path;
		}

		$query = "SELECT cat_name, cat_id, cat_parent FROM #__jcalpro_categories WHERE cat_parent=$catid and published='1' ORDER BY cat_name";

		$database->setQuery($query);
		$rows = $database->loadObjectList();

		$xmap->changeLevel(1);
		$list = array();
		foreach($rows as $row) {
			if( $row->cat_parent == 0 ) {
				$row->cat_parent = '';
			}
			$node = new stdclass;
			$node->name = $row->cat_name;
			$node->link = 'index.php?option=com_jcalpro&amp;extmode=cat&amp;cat_id='.$row->cat_id;
			$node->id   = $parent->id;
			$node->uid  = $parent->uid.'c'.$row->cat_id;   // unique id of the element in all the component
			$node->pid  = $row->cat_parent;		// parent id
			$node->priority   = $params['cat_priority'];		
			$node->changefreq = $params['cat_changefreq'];
			$node->expandible = true;
			$xmap->printNode($node);
			xmap_com_jcalpro::getJCALPro($xmap, $parent, $params, $row->cat_id);
		}

		/* Returns URLs of all listings in JCal Pro */
		$query = "SELECT extid, title, cat FROM #__jcalpro_events  WHERE cat=$catid  AND ( published ='1' AND approved='1' )";

		$database->setQuery($query);
		$rows = $database->loadObjectList();

		$xmap->changeLevel(1);
		foreach($rows as $row) {
			if( $row->cat == 0 ) {
				$row->cat = '';
			}
			$node = new stdclass;
			$node->name = $row->title;
			$node->uid  = $parent->uid.'e'.$row->extid; // unique id of the element in all the component
			$node->link = 'index.php?option=com_jcalpro&amp;extmode=view&amp;extid='.$row->extid;
			$node->id   = $parent->id;
			$node->priority   = $params['events_priority'];		
			$node->changefreq = $params['events_changefreq'];
			$node->expandible = false;
		    $xmap->printNode($node);
		}
		$xmap->changeLevel(-2);
	}
}


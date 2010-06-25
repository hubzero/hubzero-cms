<?php
/**
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @version $Id: com_joomdoc.php 64 2009-11-26 17:22:16Z guilleva $
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Seyret Video component
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
	
class xmap_com_joomdoc {

	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item
	*/
	function prepareMenuItem(&$node) {
		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$task = JArrayHelper::getValue($link_varas,'task','');
		$gid = intval(JArrayHelper::getValue($link_vars,'gid',0));
		if ( $task == 'view_cat' && $gid ) {
			$node->uid = 'com_joomdocc'.$gid;
			$node->expandible = true;
		} elseif (($task == 'doc_details' || $task == 'doc_download') && $gid) {
			$node->uid = 'com_joomdocd'.$gid;
			$node->expandible = false;
		}
	}
	
	function getTree ( &$xmap, &$parent, &$params ) {

		//DOCMan core interaction API
		xmap_com_joomdoc::includeDependencies();
		$docman = &DocmanFactory::getDocman ();

		// get the parameters from the query string
		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$task = JArrayHelper::getValue($link_varas,'task',0);
		$catid = intval(JArrayHelper::getValue($link_vars,'gid',0));

		if ($task != '' && $task != 'cat_view') {
			return true;
		}
		
		if ( !$catid ) {
			// get the parameters
			$menu =& JSite::getMenu();
			$queryparams = $menu->getParams($parent->id);
			$catid=intval($queryparams->get('cat_id',NULL));
			if (!$catid) {
				$link_query = parse_url( $parent->link );
				parse_str( html_entity_decode($link_query['query']), $link_vars);
				$catid = JArrayHelper::getValue($link_vars,'gid',0);
			}
		}

		$include_docs = JArrayHelper::getValue($params,'include_docs',1);
		$include_docs = ( $include_docs == 1
                                  || ( $include_docs == 2 && $xmap->view == 'xml') 
                                  || ( $include_docs == 3 && $xmap->view == 'html'));
		$params['include_docs'] = $include_docs;

		$doc_task = JArrayHelper::getValue($params,'doc_task','doc_details');
		$params['doc_task'] = $doc_task;

		$priority = JArrayHelper::getValue($params,'cat_priority',$parent->priority);
		$changefreq = JArrayHelper::getValue($params,'cat_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = JArrayHelper::getValue($params,'doc_priority',$parent->priority);
		$changefreq = JArrayHelper::getValue($params,'doc_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['doc_priority'] = $priority;
		$params['doc_changefreq'] = $changefreq;

		$menuid = $parent->id;

		$params['tpl'] = $docman->getCfg('icon_theme');

		xmap_com_joomdoc::getCategoryTree($xmap,$parent,$params,$catid,$menuid);

	}

	function getCategoryTree(&$xmap,&$parent,&$params,$catid=0,$menuid) {
		$db =& JFactory::getDBO();
		$limits	 = 1000;
		$list = array();

		$query = 'select id,title from #__categories where parent_id='.$catid . ' and section=\'com_joomdoc\' and published=1';
		$db->setQuery($query);
		$rows = $db->loadRowList();
		// Get sub-categories list
		$xmap->changeLevel(1);
		foreach ($rows as $row) {
			$node = new stdclass;
			$node->id = $menuid;
			$node->uid = $parent->uid . 'c'.$row[0]; // should be unique on component
			$node->name = $row[1];
			$node->browserNav = $parent->browserNav;
			$node->priority = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->link = 'index.php?option=com_joomdoc&amp;task=cat_view&amp;gid='.$row[0];
			$node->expandible = true;
			if ($xmap->printNode($node)) {
				xmap_com_joomdoc::getCategoryTree($xmap,$parent,$params,$row[0],$menuid);
			}
		}
		$xmap->changeLevel(-1);

		$include_docs = @$params['include_docs'];
		if ( $catid > 0 &&  $params['include_docs'] ) {
			$xmap->changeLevel(1);
			$rows = DOCMAN_Docs::getDocsByUserAccess ( $catid, '', '', 10000, 0 );
			// Get documents list
			foreach ($rows as $row) {
				$node = new stdclass;
				$node->id = $menuid;
				$node->uid = $parent->uid . 'd'.$row->id; // should be unique on component
				$node->link = 'index.php?option=com_joomdoc&amp;task='.$params['doc_task'].'&amp;gid='.$row->id. '&amp;Itemid='.$menuid;
				$node->browserNav = $parent->browserNav;
				$node->priority = $params['doc_priority'];
				$node->changefreq = $params['doc_changefreq'];
				$node->name = $row->dmname;
				$node->expandible = false;
				$xmap->printNode($node);
			}
			$xmap->changeLevel(-1);
		}
		return true;
	}
	
	function includeDependencies()
	{
		if (!defined('JPATH_COMPONENT_HELPERS')) {
			define ( 'JPATH_COMPONENT_HELPERS', JPATH_SITE.DS.'components'.DS.'com_joomdoc'.DS.'helpers' );
			define ( 'JPATH_COMPONENT_AHELPERS', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdoc' . DS . 'helpers' );
			require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdoc'. DS . 'docman.class.php');
			require_once (JPATH_COMPONENT_AHELPERS . DS . 'factory.php');

			define ( 'C_DOCMAN_DEFAULT_THEME', JPATH_SITE.'components'.DS.'com_joomdoc' . DS . 'views' . DS . 'themes' . DS);

			$docman = &DocmanFactory::getDocman ();
			
			define ( 'C_DOCMAN_HTML', $docman->getPath ( 'classes', 'html' ) );
			define ( 'C_DOCMAN_UTILS', $docman->getPath ( 'classes', 'utils' ) );
			define ( 'C_DOCMAN_FILE', $docman->getPath ( 'classes', 'file' ) );
			
			require_once (C_DOCMAN_HTML);
			require_once (C_DOCMAN_UTILS);
			require_once (C_DOCMAN_FILE);
		}
	}
}
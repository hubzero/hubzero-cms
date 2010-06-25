<?php
/**
 * @version $Id: com_jevents.php 52 2009-10-24 22:35:11Z guilleva $
 * @author Guillermo Vargas, http://joomla.vargas.co.cr
 * @email guille@vargas.co.cr
 * @package xmap
 * @license GNU/GPL
 * @description Xmap plugin for JEvents Component
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class xmap_com_jevents {

	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item
	*/
	function prepareMenuItem(&$node) {

		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$evid = intval(JArrayHelper::getValue($link_vars,'evid',0));
		$catid = intval(JArrayHelper::getValue($link_vars,'category_fv',0));
		$task = JArrayHelper::getValue($link_vars,'task',0);
		if ( !$catid ) {
			$menu =& JSite::getMenu();
			$params = $menu->getParams($node->id);
			$catids = array();
			for ( $i=0; $i<=10;$i++ ) {
				if ($catid =  $params->get("catid$i",0) ) {
					$catids[] = $catid;
				}
			}
			$catid = implode(',',$catids);
		}
		if ( $task == 'icalrepeat.detail' &&  $evid ) {
			$node->uid = 'com_jeventse'.$evid;
			$node->expandible = false;
		} elseif ( $task == 'cat.listevents' && $catid ) {
			$node->expandible = true;
			$node->uid = 'com_jeventsc'.$catid;
		} 
	}

	function getTree( &$xmap, &$parent, &$params) {

		if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_jevents'.DS."jevents.defines.php") ) {
			return;
		}


		// get the parameters
		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$catid	  = $xmap->getParam($link_vars,'category_fv',0);
		$task	  = $xmap->getParam($link_vars,'task','cat.listevents');
		if ( !in_array($task,array('cat.listevents')) ) { // Only expand some kind of items
			return;
		}

		if ( !$catid ) {
			$catids=array();
			$menu =& JSite::getMenu();
			$mparams = $menu->getParams($parent->id);
			for ( $i=0; $i<=10;$i++ ) {
				if ($catid =  $mparams->get("catid$i",0) ) {
					$catids[] = $catid;
				}
			}
			if ( !count($catids)) {
				$catids[]=0;
			}
		} else {
			$catids = array ($catid);
		}

		$include_events = $xmap->getParam($params,'include_events',1);
		$include_events = ( $include_events == 1
				  || ( $include_events == 2 && $xmap->view == 'xml')
				  || ( $include_events == 3 && $xmap->view == 'html')
				  ||   $xmap->view == 'navigator' );

		$params['include_events'] = $include_events;

		if (!defined('JEV_COM_COMPONENT') ) {
			include_once(JPATH_SITE.DS.'components'.DS.'com_jevents'.DS."jevents.defines.php");
			require_once(JPATH_SITE.DS.'components'.DS.'com_jevents'.DS.'libraries'.DS.'datamodel.php');
		}
		// Backup the original datamodel to restore it later
		$reg = & JevRegistry::getInstance("jevents");
		$datamodel = $reg->getReference("jevents.datamodel",false);

		xmap_com_jevents::getCategoryTree ( $xmap, $parent, $params, $catids );

		// Restore the original datamodel to avoid problems with modules or plugins
		$reg->setReference("jevents.datamodel",$datamodel);
	}

	function getCategoryTree ( &$xmap, &$parent, &$params, $catids) {
		$menuid = $parent->id;
		$database =& JFactory::getDBO();
		$my =& JFactory::getUser();
	
		$content = "";
		$enum	= 0;
	
		$xmap->changeLevel(1);
		if ( count($catids) != 1 ) {
			foreach ( $catids as $i => $catid) {
				$query = 'SELECT id,name,title FROM ' .
			 		'#__categories '.
			 		' WHERE id = ' . $catid .
			 		' AND section = \''.JEV_COM_COMPONENT.'\'' .
			 		' AND access<='.$my->gid. ' ' .
			 		'ORDER BY ordering ';
		
				$database->setQuery($query);
				$rows = $database->loadRowList();
				$now = time();
				foreach ($rows as $num => $row) {
					$node = new stdclass;
					$node->link = "index.php?option=com_jevents&task=cat.listevents&offset=1&category_fv=".$row[0];
					$node->name = $row[2];
					$node->id = $menuid;
					$node->uid = $parent->uid.'c'.$row[0]; //Unique ID
					$node->expandible = true;
					if ($xmap->printNode($node) !== FALSE) {
						xmap_com_jevents::getCategoryTree ($xmap, $parent, $params, array($row[0]) );
					}
				}
			}
		} else {

		$query = 'SELECT id,name,title FROM ' .
	 		'#__categories '.
	 		' WHERE parent_id = ' . $catids[0] .
	 		' AND section = \''.JEV_COM_COMPONENT.'\'' .
	 		' AND access<='.$my->gid. ' ' .
	 		'ORDER BY ordering ';

		$database->setQuery($query);
		$rows = $database->loadRowList();
		$now = time();
		foreach ($rows as $num => $row) {
			$node = new stdclass;
			$node->link = "index.php?option=com_jevents&task=cat.listevents&offset=1&category_fv=".$row[0];
			$node->name = $row[2];
			$node->id = $menuid;
			$node->uid = $parent->uid.'c'.$row[0]; //Unique ID
			$node->expandible = true;
			if ($xmap->printNode($node) !== FALSE) {
				xmap_com_jevents::getCategoryTree ($xmap, $parent, $params, array($row[0]) );
			}
		}

		if ( $params['include_events']  && count($catids) == 1 && $catids[0] ) {
			$rows =& xmap_com_jevents::getCategoryEvents($menuid, $catids);
			foreach ($rows as $row ) {
				$node = new stdclass;
				$node->link = $row->viewDetailLink($row->yup(),$row->mup(),$row->dup(),false);
				$node->name = $row->title();
				$node->id = $menuid;
				$node->uid = $parent->uid.'e'.$row->id(); //Unique ID
				$node->priority = $parent->priority;
				$node->changefreq = $parent->changefreq;
				$node->expandible = false;
				$xmap->printNode($node);
			}
		}
		}
		$xmap->changeLevel(-1);
	}

	function &getCategoryEvents($Itemid,&$catids) {
		static $datamodel;
		if (!isset($datamodel)) {
			$datamodel  =  new JEventsDataModel();

			$reg = & JevRegistry::getInstance("jevents");
			$reg->setReference("jevents.datamodel",$datamodel);
		}
		$datamodel->catidList = JEVHelper::forceIntegerArray($catids,true);
		$datamodel->catids = $catids;
		$datamodel->catidsOut = implode('|',$catids);
		
		JRequest::setVar('category_fv', $datamodel->catidList);
		$filter = new jevFilterProcessing(array('category'));
		$rows     = $datamodel->queryModel->listEventsByCat( $catids, 0, 10000 );
		$icalrows = $datamodel->queryModel->listIcalEventsByCat( $catids,false,0, 0, 10000," ORDER BY rpt.startrepeat asc",$filter,'','' );
		$rows = array_merge($icalrows,$rows);

		return $rows;
	}
}

<?php
/**
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @version $Id: com_docman.php 76 2009-12-19 19:41:55Z guilleva $
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for DOCman component
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class xmap_com_docman {

        /*
        * This function is called before a menu item is printed. We use it to set the
        * proper uniqueid for the item
        */
        function prepareMenuItem(&$node) {
                $link_query = parse_url( $node->link );
                parse_str( html_entity_decode($link_query['query']), $link_vars);
                $task = JArrayHelper::getValue($link_vars,'task','');
                $id = intval(JArrayHelper::getValue($link_vars,'gid',0));

                switch( $task ) {
                        case 'doc_details': case 'doc_download':
                                $node->uid = 'com_docmand'.$id;
                                $node->false=true;
                                break;
                        case 'cat_view':
                                $node->uid = 'com_docmanc'.$id;
                                $node->expandible=true;
                                break;
                }
        }

	function &getTree ( &$xmap, &$parent, &$params ) {
		$db =& JFactory::getDBO();

		//DOCMan core interaction API
		include_once( JPATH_SITE."/administrator/components/com_docman/docman.class.php");
		global $_DOCMAN, $_DMUSER;
		if(!is_object($_DOCMAN)) {
		    $_DOCMAN = new dmMainFrame();
		}

		if(!is_object($_DMUSER)) {
			$_DMUSER =$_DOCMAN->getUser();
		}

		$_DOCMAN->setType(_DM_TYPE_MODULE);
		$_DOCMAN->loadLanguage('modules');

		require_once($_DOCMAN->getPath('classes', 'utils'));
		require_once($_DOCMAN->getPath('classes', 'file'));
		require_once($_DOCMAN->getPath('classes', 'model'));

		// get the parameters
		$menu = JSite::getMenu();
		$queryparams = $menu->getParams($parent->id);
		$catid=intval($queryparams->get('cat_id',NULL));
		if (!$catid) {
			$link_query = parse_url( $parent->link );
			parse_str( html_entity_decode($link_query['query']), $link_vars);
			$catid = JArrayHelper::getValue($link_vars,'gid',0);
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
		$list = xmap_com_docman::getCategoryTree($xmap,$parent,$params,$catid,$menuid);

		return $list;
	}

	function getCategoryTree(&$xmap,&$parent,&$params,$catid=0,$menuid) {
		$db =& JFactory::getDBO();
		$limits	 = 25;
		$list = array();

		$query = 'select id,title from #__categories where parent_id='.$catid . ' and section=\'com_docman\' and published=1';
		$db->setQuery($query);
		$rows = $db->loadRowList();
		// Get sub-categories list
		$xmap->changeLevel(1);
		foreach ($rows as $row) {
			$node = new stdclass;
			$node->id = $menuid;
			$node->uid = $parent->uid . 'c'.$row[0]; // should be uniq on component
			$node->name = $row[1];
			$node->browserNav = $parent->browserNav;
			$node->priority = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->expandible = true;
			$node->link = 'index.php?option=com_docman&amp;task=cat_view&amp;gid='.$row[0];
			if ($xmap->printNode($node)  !== FALSE) {
				xmap_com_docman::getCategoryTree($xmap,$parent,$params,$row[0],$menuid);
			}
		}
		$xmap->changeLevel(-1);

		$include_docs = @$params['include_docs'];
		if ( $catid > 0 &&  $params['include_docs'] ) {
			$xmap->changeLevel(1);
			$rows = DOCMAN_Docs::getDocsByUserAccess($catid, '', '', $limits);
			// Get documents list
			foreach ($rows as $row) {
				$doc = new DOCMAN_Document($row->id);

				$node = new stdclass;
				$node->id = $menuid;
				$node->uid = $parent->uid . 'd'.$row->id; // should be uniq on component
				$node->link = 'index.php?option=com_docman&amp;task='.$params['doc_task'].'&amp;gid='.$row->id. '&amp;Itemid='.$menuid;
				$node->browserNav = $parent->browserNav;
				$node->priority = $params['doc_priority'];
				$node->changefreq = $params['doc_changefreq'];
				$node->name = $doc->getData('dmname');
				$node->type = 'separator';
				$node->expandible = false;
				$xmap->printNode($node);
			}
			$xmap->changeLevel(-1);
		}
		return true;
	}
}
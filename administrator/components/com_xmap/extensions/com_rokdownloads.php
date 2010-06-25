<?php
/**
* @author Guillermo Vargas
* @email guille@vargas.co.cr
* @version $Id: com_rokdownloads.php 56 2009-10-28 02:56:55Z guilleva $
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Rokdownloads component. Based on the work of  Jan Moehrke, http://www.joomla-cbe.de
*/

defined( '_JEXEC' ) or die( 'Restricted access.' );

class xmap_com_rokdownloads {

	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item and indicate whether the node is expandible or not
	* 
	* @param object $node
	*/
	function prepareMenuItem(&$node) {
		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$view = JArrayHelper::getValue($link_vars,'view','');
		if ( $view == 'folder') {
			$catid = JArrayHelper::getValue($link_vars,'id',0);
			if ( !$catid ) {
				$menu =& JSite::getMenu();
				$params = $menu->getParams($node->id);
				$catid = $params->get('top_level_folder',0);
			}
		}else{
			$catid = 0;
		}
		if ( $catid ) {
			$node->uid = 'com_rokdownloadso'.$catid;
			$node->expandible = true;
		}

	}

	/**
	* This function is called from Xmap's component and it's expected to
	* expand the given menu item ($parent)
	* 
	* @param object $xmap
	* @param object $parent
	* @param array $params
	* @param int $catid
	*/
	function getTree( &$xmap, &$parent, &$params) {

		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars );
		$view = JArrayHelper::getValue($link_vars,'view',0);
		if ( $view == 'folder' ) {
			$catid = intval(JArrayHelper::getValue($link_vars,'id',0));

			if ( !$catid ) {
				$menu =& JSite::getMenu();
				$mparams = $menu->getParams($parent->id);
				$catid = $mparams->get('top_level_folder',0);
			}
		}  else {
			$catid = 1;
		}

		$include_files = JArrayHelper::getValue( $params, 'include_files',1,'' );
		$include_files = ( $include_files == 1
				  || ( $include_files == 2 && $xmap->view == 'xml') 
				  || ( $include_files == 3 && $xmap->view == 'html')
				  ||   $xmap->view == 'navigator');
		$params['include_files'] = $include_files;

		$priority = JArrayHelper::getValue($params,'cat_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'cat_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = JArrayHelper::getValue($params,'file_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'file_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;

		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['file_priority'] = $priority;
		$params['file_changefreq'] = $changefreq;

		if ( $include_files ) {
			$params['limit'] = '';
			$params['days'] = '';
			$limit = JArrayHelper::getValue($params,'max_files','','');

			if ( intval($limit) )
				$params['limit'] = ' LIMIT '.$limit;

			$days = JArrayHelper::getValue($params,'max_age','','');
			if ( intval($days) )
				$params['days'] = ' AND filedate >= \''.date('Y-m-d H:m:s', ($xmap->now - ($days*86400)) ) ."' ";
		}

		xmap_com_rokdownloads::getRokdownloadsTree($xmap, $parent, $params, $catid );

	}

	/**
	* Generates the elements tree for Rokdownloads component
	* 
	* @param object $xmap
	* @param object $parent
	* @param array $params
	* @param int $catid
	*/
	function getRokdownloadsTree ( &$xmap, &$parent, &$params, $catid) {
		$db = &JFactory::getDBO();

		($catid)? $cats = xmap_com_rokdownloads::getDBFolders($catid):$cats = xmap_com_rokdownloads::getDBFolders();

		$xmap->changeLevel(1);
		foreach($cats as $cat) {
			$node = new stdclass;
			$node->id   = $parent->id;
			$node->uid  = $parent->uid.'o'.$cat->id;
			$node->name = $cat->displayname? $cat->displayname : $cat->name;
			$node->priority   = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->expandible = true;
			$node->link = 'index.php?option=com_rokdownloads&amp;view=folder&amp;id=' . $cat->id . ":" . strtolower($cat->name);
			$node->tree = array();

			if ($xmap->printNode($node) !== FALSE) {
				xmap_com_rokdownloads::getRokdownloadsTree($xmap, $parent, $params, $cat->id);
			}

		}

		if ($params['include_files'] && $catid) {
			$files = xmap_com_rokdownloads::getDBFilesForFolder($catid);
			foreach($files as $file) {
				$node = new stdclass;
				$node->id   = $parent->id;
				$node->uid  = $parent->uid .$catid . 'd' . $file->id;
				$node->name = ($file->displayname ? $file->displayname : $file->name);
				$node->link = 'index.php?option=com_rokdownloads&amp;view=file&amp;id='.$file->id . ":" . strtolower($file->name);
				$node->priority   = $params['file_priority'];
				$node->changefreq = $params['file_changefreq'];
				$node->expandible = false;
				$xmap->printNode($node);
			}
		}
		$xmap->changeLevel(-1);
	}

	/**
	* Retrieve the published folder for a specific parent
	* 
	* @param int $parentFolderId
	* @param int $depth
	* @return array
	*/
	function getDBFolders($parentFolderId = 1, $depth = 1){
		$db = &JFactory::getDBO();

		$query = "SELECT node.*, (COUNT(parent.name) - (sub_tree.depth + 1)) AS depth "
			."FROM #__rokdownloads AS node,#__rokdownloads AS parent,#__rokdownloads AS sub_parent,"
					."(SELECT node.id, (COUNT(parent.name) - 1) AS depth FROM #__rokdownloads AS node,#__rokdownloads AS parent "
					."WHERE node.lft BETWEEN parent.lft AND parent.rgt "
				."AND node.id = " . $parentFolderId . " "
				."GROUP BY node.id ORDER BY node.lft ) AS sub_tree "
			."WHERE node.published=1 and node.lft BETWEEN parent.lft AND parent.published=1 AND parent.rgt AND node.lft BETWEEN sub_parent.lft "
			."AND sub_parent.rgt AND sub_parent.id = sub_tree.id and node.folder = 1 "
			."GROUP BY node.id HAVING depth = $depth ORDER BY node.lft";
		$db->setQuery($query);
		$folders = $db->loadObjectList();
		return $folders;
	}

	/**
	* Retrieve the published files for a specific folder
	* 
	* @param int $catid
 	* @return array
	*/
	function getDBFilesForFolder($catid){
		$db = &JFactory::getDBO();
		$query = "SELECT node.*, (COUNT(parent.name) - (sub_tree.depth + 1)) AS depth "
			."FROM #__rokdownloads AS node,#__rokdownloads AS parent,#__rokdownloads AS sub_parent,"
				."(SELECT node.id, (COUNT(parent.name) - 1) AS depth FROM #__rokdownloads AS node,#__rokdownloads AS parent "
				."WHERE node.lft BETWEEN parent.lft AND parent.rgt "
			."AND node.id = " . $catid . " "
			."GROUP BY node.id ORDER BY node.lft ) AS sub_tree "
			."WHERE  node.published=1 AND node.lft BETWEEN parent.lft AND parent.rgt AND node.lft BETWEEN sub_parent.lft "
			."AND sub_parent.rgt AND sub_parent.id = sub_tree.id and node.folder = 0 "
			."GROUP BY node.id HAVING depth = 1 ORDER BY node.lft";

		$db->setQuery($query);
		$files = $db->loadObjectList();
		return $files;
	}

}
<?php
/**
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @version $Id: com_rsgallery2.php 52 2009-10-24 22:35:11Z guilleva $
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for RSGallery2 component
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/** Adds support for RSGallery2 component to Xmap */
class xmap_com_rsgallery2 {

	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item
	*/
	function prepareMenuItem(&$node) {
		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$letter = JArrayHelper::getValue($link_vars,'letter','');
		$id = intval(JArrayHelper::getValue($link_vars,'id',0));
		$catid = JArrayHelper::getValue( $link_vars, 'catid', '', '' );
		if ( $id ) {
			$node->uid = 'com_rsgallery2i'.$id;
			$node->expandible = false;
		} elseif ( $catid) {
			$node->uid = 'rsgallery2c'.$catid;
			$node->expandible = true;
		}
	}

	/** Get the content tree for this kind of content */
	function getTree( &$xmap, &$parent, &$params ) {
		global $rsgConfig, $rsgAccess, $rsgVersion, $rsgOption;

		if (!file_exists(JPATH_SITE.'/administrator/components/com_rsgallery2/init.rsgallery2.php')) {
			return $list;
		}

		require_once(JPATH_SITE.'/administrator/components/com_rsgallery2/init.rsgallery2.php');

		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$catid = JArrayHelper::getValue($link_vars,'catid',0);
		$id = JArrayHelper::getValue($link_vars,'id',0);

		if ( $id )
			return $tree;

		$include_images = JArrayHelper::getValue($params,'include_images',1);
		$include_images = ( $include_images == 1
				  || ( $include_images == 2 && $xmap->view == 'xml')
				  || ( $include_images == 3 && $xmap->view == 'html')
								  ||   $xmap->view == 'navigator');
		$params['include_images'] = $include_images;

		$priority = JArrayHelper::getValue($params,'cat_priority',$parent->priority);
		$changefreq = JArrayHelper::getValue($params,'cat_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = JArrayHelper::getValue($params,'image_priority',$parent->priority);
		$changefreq = JArrayHelper::getValue($params,'image_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['image_priority'] = $priority;
		$params['image_changefreq'] = $changefreq;


		if ( $include_images ) {
			$ordering = JArrayHelper::getValue($params,'images_order','ordering');
			$orderModes = array('ordering'=>'ASC','date'=>'DESC','title'=>'ASC','hits'=>'DESC');
			if ( empty($orderModes[$ordering]) )
				$ordering = 'ordering';

			$params['images_order'] = $ordering . ' '. $orderModes[$ordering];

			$params['limit'] = '';
			$params['days'] = '';
			$limit = JArrayHelper::getValue($params,'max_images','');
			if ( intval($limit) )
				$params['limit'] = ' LIMIT '.$limit;

			$days = JArrayHelper::getValue($params,'max_age','');
			if ( intval($days) )
				$params['days'] = ' AND `date` >=\''.date('Y-m-d H:i:s',$xmap->now - ($days*86400)) ."' ";
		}

		xmap_com_rsgallery2::getGalleries($xmap,$parent,$params,$catid);
	}

	/** RSGallery support */
	function getGalleries ( &$xmap, &$parent,&$params, $catid ) {
		global $rsgConfig, $rsgAccess, $rsgVersion, $rsgOption;
		
		$database = &JFactory::getDBO();

		$gid=0;
		$query = "SELECT id,name,unix_timestamp(`date`) as `date` FROM #__rsgallery2_galleries".
			 " WHERE published=1".
			 " AND parent=$catid".
			 " ORDER BY ordering ASC";
		$database->setQuery($query);

		$rows = $database->loadAssocList();
		$xmap->changeLevel(1);
		foreach($rows as $row) {
			// check if user has view access
			if( !$rsgAccess->checkGallery( 'view', $row['id'] ))
			       continue;

			$node = new stdclass;

			$node->id = $parent->id;
			$node->uid = $parent->uid.'c'.$row['id'];
			$node->browserNav = $parent->browserNav;
			$node->name = $row['name'];
			$node->modified = $xmap->now;
			$node->link = 'index.php?option=com_rsgallery2&amp;catid='.$row['id'];
			$node->priority = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->expandible = true;
			if ($xmap->printNode($node) !== FALSE) { 
				xmap_com_rsgallery2::getGalleries($xmap,$parent,$row['id'],$params);
			}
	    	}

		if ( $params['include_images'] ) {
			$query  = 
		 	"SELECT id,name,title,UNIX_TIMESTAMP(`date`) as `date` "
			."\n FROM #__rsgallery2_files"
			."\n WHERE gallery_id = $catid" 
			."\n AND published=1 "
			."\n AND approved=1 "
			.$params['days']
			."\n ORDER BY ".$params['images_order'] . ' '
			.$params['limit'];

			$database->setQuery( $query );
			$database->getQuery( );
			$rows = $database->loadAssocList();
			foreach($rows as $row) {
				$node = new stdclass;
				$node->id = $parent->id;
				$node->uid = $parent->uid.'i'.$row['id'];
				$node->browserNav = $parent->browserNav;
				$node->name = $row['title'];
				$node->modified = intval($row['date']);
				$node->priority = $params['image_priority'];
				$node->changefreq = $params['image_changefreq'];
				$node->link = 'index.php?option=com_rsgallery2&amp;page=inline&amp;catid=' . $catid . '&amp;id=' . $row['id'];	// parent id
				$node->expandible = false;
				$xmap->printNode($node);
			}
		}
		$xmap->changeLevel(-1);
	}
}


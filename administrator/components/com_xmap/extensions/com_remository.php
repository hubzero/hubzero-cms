<?php
/**
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @version $Id: com_remository.php 52 2009-10-24 22:35:11Z guilleva $
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Remository component
*/

defined( '_JEXEC' ) or die( 'Restricted access.' );

class xmap_com_remository {

	function getTree( &$xmap, &$parent, &$params) {
		if ( strpos($parent->link, 'func=fileinfo') ) {
			return $list;	
		}

		$link_query = parse_url( $parent->link );
                parse_str( html_entity_decode($link_query['query']), $link_vars );
                $catid = JArrayHelper::getValue($link_vars,'id',0);

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

		xmap_com_remository::getRemositoryTree( $xmap, $parent, $params, $catid );
	}

	function getRemositoryTree ( &$xmap, &$parent, &$params, &$catid ) {
		$db = JFactory::getDBO();

		$db->setQuery("select id, windowtitle,name, parentid from #__downloads_containers where parentid=$catid and published = '1' and groupid = '0' order by windowtitle");
		$cats = $db->loadObjectList();
		$xmap->changeLevel(1);
		foreach($cats as $cat) {
			$node = new stdclass;
			$node->id   = $parent->id;
			$node->uid  = $parent->uid.'c'.$cat->id;
			$node->pid  = $cat->parentid;
			$node->name = $cat->name? $cat->name : $cat->windowtitle;
			$node->priority   = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->link = 'index.php?option=com_remository&amp;func=select&amp;id='.$cat->id;
			$node->expandible = true;

			if ($xmap->printNode($node) !== FALSE) {
				xmap_com_remository::getRemositoryTree($xmap, $parent, $params, $cat->id);
			}
		}

		if ($params['include_files']) {
			$db->setQuery ("select id, windowtitle,filetitle, containerid from #__downloads_files where containerid=$catid and published = '1' and groupid = '0' ".$params['days']." order by windowtitle" . $params['limit']);
			$cats = $db->loadObjectList();
			foreach($cats as $file) {
				$node = new stdclass;
				$node->id   = $parent->id;
				$node->uid  = $parent->uid .'d'.$file->id;
				$node->name = ($file->windowtitle ? $file->windowtitle : $file->filetitle);
				$node->link = 'index.php?option=com_remository&amp;func=fileinfo&amp;id='.$file->id;
				$node->priority   = $params['file_priority'];
				$node->changefreq = $params['file_changefreq'];
				$node->expandible = false;
				$xmap->printNode($node);
			}
		}
		$xmap->changeLevel(-1);
	}

}
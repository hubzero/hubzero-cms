<?php
/**
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @version $Id: com_jmovies.php 95 2010-04-14 18:38:36Z guilleva $
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for JMovies component
*/

defined( '_JEXEC' ) or die( 'Restricted access.' );

class xmap_com_jmovies {

	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item
	*/
	function prepareMenuItem(&$node) {
		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$catid = intval(JArrayHelper::getValue($link_vars,'catid',0));
		$cid = intval(JArrayHelper::getValue($link_vars,'id',0));
		$task = JArrayHelper::getValue( $link_vars, 'task', '', '' );
		if ( $task == 'showcategory' && $cid ) {
			$node->uid = 'com_jmoviesc'.$cid;
			$node->expandible = true;
		} elseif ($task = 'detail' && $cid) {
			$node->uid == 'com_jmoviesm'.$cid;
			$node->expandible = false;
		}
	}

	function getTree( &$xmap, &$parent, &$params)
	{
		$link_query = parse_url( $parent->link );
                parse_str( html_entity_decode($link_query['query']), $link_vars );
                $catid = JArrayHelper::getValue($link_vars,'catid',0);
                $task = JArrayHelper::getValue($link_vars,'task',0);

		if ( $task  != 'showcategory' ) {
			return $list;
		}

		$include_movies = JArrayHelper::getValue( $params, 'include_movies',1,'' );
		$include_movies = ( $include_movies == 1
                                  || ( $include_movies == 2 && $xmap->view == 'xml')
                                  || ( $include_movies == 3 && $xmap->view == 'html')
				  ||   $xmap->view == 'navigator');
		$params['include_movies'] = $include_movies;

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

		if ( $include_movies ) {
			$params['limit'] = '';
			$params['days'] = '';
			$limit = JArrayHelper::getValue($params,'max_movies','','');

			if ( intval($limit) )
				$params['limit'] = ' LIMIT '.$limit;

			$days = JArrayHelper::getValue($params,'max_age','','');
			if ( intval($days) )
				$params['days'] = ' AND m.created >= \''.date('Y-m-d H:m:s', ($xmap->now - ($days*86400)) ) ."' ";
		}

		xmap_com_jmovies::getCategoriesTree( $xmap, $parent, $params, $catid );
	}

	function getCategoriesTree ( &$xmap, &$parent, &$params, $catid=0 )
	{
		$db = JFactory::getDBO();
		
        $xmap->changeLevel(1);
		if ( !$catid ) {
			$db->setQuery("select id, title from #__categories where section='com_jmovies' and published=1 order by ordering");
			$cats = $db->loadObjectList();

			foreach($cats as $cat) {
				$node = new stdclass;
				$node->id   = $parent->id;
				$node->uid  = 'com_jmoviesc'.$cat->id;   // Uniq ID for the category
				$node->name = $cat->title;
				$node->priority   = $params['cat_priority'];
				$node->changefreq = $params['cat_changefreq'];
				$node->link = 'index.php?option=com_jmovies&amp;task=showcategory&amp;catid='.$cat->id;
				$node->expandible = true;

				if ($xmap->printNode($node) !== FALSE ) {
					xmap_com_jmovies::getCategoriesTree($xmap, $parent, $params, $cat->id);
				}
			}
		}

		if ( $params['include_movies'] && $catid ) {
			$db->setQuery ("select m.id, m.titolo, m.titolo2, UNIX_TIMESTAMP(created) as created, UNIX_TIMESTAMP(modified) as modified from `#__jmovies` AS m,`#__jmovies_categories` AS c where c.jmoviesid=m.id and c.catid=$catid and m.published=1 and m.access<={$xmap->gid} ".$params['days']." order by ordering " . $params['limit']);
			$movies = $db->loadObjectList();
			foreach($movies as $movie) {
				$node = new stdclass;
				$node->id   = $parent->id;  // Itemid
				$node->uid  = 'com_jmoviesm'.$movie->id; // Uniq ID for the download
				$node->name = $movie->titolo;
				$node->link = 'index.php?option=com_jmovies&amp;task=detail&amp;id='.$movie->id;
				$node->priority   = $params['file_priority'];
				$node->changefreq = $params['file_changefreq'];
				$node->modified = (@$movie->modified? $movie->modified : $movie->created);
				$node->expandible = false;
				$xmap->printNode($node);
			}
		}
		$xmap->changeLevel(-1);
	}
}
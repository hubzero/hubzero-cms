<?php
/**
 * $Id: com_sectionex.php 52 2009-10-24 22:35:11Z guilleva $
 * $LastChangedDate: 2009-10-24 16:35:11 -0600 (sáb, 24 oct 2009) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' ); 

/** Handles standard Joomla Content */
class xmap_com_sectionex {

	/** return a node-tree */
	function getTree(&$xmap, &$parent, &$params) {
		$result = null;

		/***
                * Parameters Initialitation
                **/
		//----- Set expand_categories param
                $expand_categories = xmap_com_sectionex::getParam($params,'expand_categories',1);
                $expand_categories = ( $expand_categories == 1
                                  || ( $expand_categories == 2 && $xmap->view == 'xml')
                                  || ( $expand_categories == 3 && $xmap->view == 'html')
								  ||   $xmap->view == 'navigator');
                $params['expand_categories'] = $expand_categories;

		//----- Set expand_sections param
                $expand_sections = xmap_com_sectionex::getParam($params,'expand_sections',1);
                $expand_sections = ( $expand_sections == 1
                                  || ( $expand_sections == 2 && $xmap->view == 'xml')
                                  || ( $expand_sections == 3 && $xmap->view == 'html')
								  ||   $xmap->view == 'navigator');
                $params['expand_sections'] = $expand_sections;

		//----- Set show_unauth param
                $show_unauth = xmap_com_sectionex::getParam($params,'show_unauth',1);
                $show_unauth = ( $show_unauth == 1
                                  || ( $show_unauth == 2 && $xmap->view == 'xml')
                                  || ( $show_unauth == 3 && $xmap->view == 'html'));
                $params['show_unauth'] = $show_unauth;

		//----- Set cat_priority and cat_changefreq params
                $priority = xmap_com_sectionex::getParam($params,'cat_priority',$parent->priority);
                $changefreq = xmap_com_sectionex::getParam($params,'cat_changefreq',$parent->changefreq);
                if ($priority  == '-1')
                        $priority = $parent->priority;
                if ($changefreq  == '-1')
                        $changefreq = $parent->changefreq;

                $params['cat_priority'] = $priority;
                $params['cat_changefreq'] = $changefreq;

		//----- Set art_priority and art_changefreq params
                $priority = xmap_com_sectionex::getParam($params,'art_priority',$parent->priority);
                $changefreq = xmap_com_sectionex::getParam($params,'art_changefreq',$parent->changefreq);
                if ($priority  == '-1')
                        $priority = $parent->priority;
                if ($changefreq  == '-1')
                        $changefreq = $parent->changefreq;

                $params['art_priority'] = $priority;
                $params['art_changefreq'] = $changefreq;

		$menuparams = xmap_com_sectionex::paramsToArray( $parent->params );

		$id = $menuparams['se_show_section_name'];
		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$id = isset($link_vars['id'])? $link_vars['id'] : 0;

		if (defined('JPATH_SITE') && defined('_JEXEC')) {
			require_once (JPATH_SITE.DS.'components'.DS.'com_sectionex'.DS.'helpers'.DS.'route.php');
		}

		if ( $id ) {
			xmap_com_sectionex::getContentSection($xmap, $parent, $id, $params, $menuparams);
		}
	}

	/** Get all content items within a content category.
	 * Returns an array of all contained content items. */
	function getContentCategory(&$xmap, &$parent, $catid, &$params, &$menuparams) {
		$database = & JFactory::getDBO();
		$orderby = !empty($menuparams['se_orderby_article']) ?  $menuparams['se_orderby_article'] : 'rdate';
		$orderby = xmap_com_sectionex::orderby_sec( $orderby );

		$query =
		  "SELECT a.id, a.title, a.modified, a.created, a.sectionid"
                . ',CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug' .
                             ',CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug'
		. "\n FROM #__content AS a,#__categories AS c"
		. "\n WHERE a.catid=(".$catid.")"
		. "\n AND a.catid=c.id"
		. "\n AND a.state='1'"
		. "\n AND ( a.publish_up = '0000-00-00 00:00:00' OR a.publish_up <= '". date('Y-m-d H:i:s',$xmap->now) ."' )"
		. "\n AND ( a.publish_down = '0000-00-00 00:00:00' OR a.publish_down >= '". date('Y-m-d H:i:s',$xmap->now) ."' )"
		. ( $params['show_unauth'] ? '' : "\n AND a.access<='". $xmap->gid ."'" )	// authentication required ?
		. ( $xmap->view != 'xml'?"\n ORDER BY ". $orderby ."": '' )
		;
		$database->setQuery( $query );
		$items = $database->loadObjectList();

		if (count($items) > 0) {
			$xmap->changeLevel(1);
			foreach($items as $item) {
				$node = new stdclass();
				$node->id = $parent->id;
				$node->uid = $parent->uid.'a'.$item->id;
				$node->browserNav = $parent->browserNav;
				$node->priority = $params['art_priority'];
				$node->changefreq = $params['art_changefreq'];
				$node->name = $item->title;
				$node->expandible = false;
				
				if( $item->modified == '0000-00-00 00:00:00' )
					$item->modified = $item->created;

				$node->modified = xmap_com_sectionex::toTimestamp( $item->modified ); 
				$node->link = ContentHelperRouteX::getArticleRoute($item->slug, $item->catslug, $item->sectionid);
				$xmap->printNode($node);
	    		}
			$xmap->changeLevel(-1);
	    	}
	    	return true;
	}

	/** Get all Categories within a Section.
	 * Also call getCategory() for each Category to include it's items */
	function getContentSection(&$xmap, &$parent, $secid, &$params, &$menuparams ) {
		$database = & JFactory::getDBO();

		$orderby = isset($menuparams['se_orderby_cat']) ? $menuparams['se_orderby_cat'] : '';
		$orderby = xmap_com_sectionex::orderby_sec( $orderby );

		$query =
		  "SELECT a.id, a.title, a.name, a.params,a.section,a.alias"
                . ',CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug'
		. "\n FROM #__categories AS a"
		. "\n LEFT JOIN #__content AS b ON b.catid = a.id "
		. "\n AND b.state = '1'"
		. "\n AND ( b.publish_up = '0000-00-00 00:00:00' OR b.publish_up <= '". date('Y-m-d H:i:s',$xmap->now) ."' )"
		. "\n AND ( b.publish_down = '0000-00-00 00:00:00' OR b.publish_down >= '". date('Y-m-d H:i:s',$xmap->now) ."' )"
		. ( $params['show_unauth'] ? '' : "\n AND b.access <= ". $xmap->gid )		// authentication required ?
		. "\n WHERE a.section = '". $secid ."'"
		. "\n AND a.published = '1'"
		. ( $params['show_unauth'] ? '' : "\n AND a.access <= ". $xmap->gid )		// authentication required ?
		. "\n GROUP BY a.id"
		. ( @$menuparams['empty_cat'] ? '' : "\n HAVING COUNT( b.id ) > 0" )	// hide empty categories ?
		. ( $xmap->view != 'xml'? "\n ORDER BY ". $orderby: '')
		;
		$database->setQuery( $query );
		$items = $database->loadObjectList();

		$xmap->changeLevel(1);
		foreach($items as $item) {
			$node = new stdclass();
			$node->id = $parent->id;
			$node->uid = $parent->uid.'c'.$item->id;
			$node->name = $item->title;
			$node->browserNav = $parent->browserNav;
			$node->priority = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->link = ContentHelperRouteX::getCategoryRoute($item->slug, $item->section);
			$node->expandible = true;
			if ($xmap->printNode($node) !== FALSE) {
				if( $params['expand_categories'] ) {
					xmap_com_sectionex::getContentCategory($xmap, $parent, $item->id, $params, $menuparams);
				}
			}
		}
		$xmap->changeLevel(-1);
		return true;
	}

	/***************************************************/
	/* copied from /components/com_content/content.php */
	/***************************************************/

	/** convert a menuitem's params field to an array */
	function paramsToArray( &$menuparams ) {
		$tmp = explode("\n", $menuparams);
		$res = array();
		foreach($tmp AS $a) {
			@list($key, $val) = explode('=', $a, 2);
			$res[$key] = $val;
		}
		return $res;
	}
	/** Translate Joomla datestring to timestamp */
	function toTimestamp( &$date ) {
		if ( $date && ereg( "([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs ) ) {
			return mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return FALSE;
	}

	/** translate primary order parameter to sort field */
	function orderby_pri( $orderby ) {
		switch ( $orderby ) {
			case 'alpha':
				$orderby = 'cc.title, ';
				break;
	
			case 'ralpha':
				$orderby = 'cc.title DESC, ';
				break;
	
			case 'order':
				$orderby = 'cc.ordering, ';
				break;
	
			default:
				$orderby = '';
				break;
		}

		return $orderby;
	}

	/** translate secondary order parameter to sort field */
	function orderby_sec( $orderby ) {
		switch ( $orderby ) {
			case 'date':
				$orderby = 'a.created';
				break;
	
			case 'rdate':
				$orderby = 'a.created DESC';
				break;
	
			case 'alpha':
				$orderby = 'a.title';
				break;
	
			case 'ralpha':
				$orderby = 'a.title DESC';
				break;
	
			case 'hits':
				$orderby = 'a.hits';
				break;
	
			case 'rhits':
				$orderby = 'a.hits DESC';
				break;
	
			case 'order':
				$orderby = 'a.ordering';
				break;
	
			case 'author':
				$orderby = 'a.created_by_alias, u.name';
				break;
	
			case 'rauthor':
				$orderby = 'a.created_by_alias DESC, u.name DESC';
				break;
	
			case 'front':
				$orderby = 'f.ordering';
				break;
	
			default:
				$orderby = 'a.ordering';
				break;
		}

		return $orderby;
	}
	/** @param int 0 = Archives, 1 = Section, 2 = Category */
	function where( $type=1, &$access, &$noauth, $gid, $id, $now=NULL, $year=NULL, $month=NULL ) {
		$database = & JFactory::getDBO();
		
		$nullDate = $database->getNullDate();
		$where = array();
	
		// normal
		if ( $type > 0) {
			$where[] = "a.state = '1'";
			if ( !$access->canEdit ) {
				$where[] = "( a.publish_up = '$nullDate' OR a.publish_up <= '$now' )";
				$where[] = "( a.publish_down = '$nullDate' OR a.publish_down >= '$now' )";
			}
			if ( !$noauth ) {
				$where[] = "a.access <= $gid";
			}
			if ( $id > 0 ) {
				if ( $type == 1 ) {
					$where[] = "a.sectionid IN ( $id ) ";
				} else if ( $type == 2 ) {
					$where[] = "a.catid IN ( $id ) ";
				}
			}
		}

		// archive
		if ( $type < 0 ) {
			$where[] = "a.state='-1'";
			if ( $year ) {
				$where[] = "YEAR( a.created ) = '$year'";
			}
			if ( $month ) {
				$where[] = "MONTH( a.created ) = '$month'";
			}
			if ( !$noauth ) {
				$where[] = "a.access <= $gid";
			}
			if ( $id > 0 ) {
				if ( $type == -1 ) {
					$where[] = "a.sectionid = $id";
				} else if ( $type == -2) {
					$where[] = "a.catid = $id";
				}
			}
		}

		return $where;
	}
	function &getParam($arr, $name, $def) {
		$var = JArrayHelper::getValue( $arr, $name, $def, '' );
		return $var;
	}
}
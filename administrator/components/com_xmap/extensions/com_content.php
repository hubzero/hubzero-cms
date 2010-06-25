<?php
/**
 * $Id: com_content.php 111 2010-04-24 17:43:05Z guilleva $
 * $LastChangedDate: 2010-04-24 11:43:05 -0600 (Sat, 24 Apr 2010) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/


defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

/** Handles standard Joomla Content */
class xmap_com_content {

	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item
	*/
	function prepareMenuItem(&$node) {
		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$view = JArrayHelper::getValue($link_vars,'view','');
		$id = intval(JArrayHelper::getValue($link_vars,'id',0));
		$layout = JArrayHelper::getValue($link_vars,'layout','');

		switch( $view ) {
			case 'category':
				$node->uid = 'com_contentc'.$id;
				$node->expandible=true;
				break;
			case 'section':
				$node->uid = 'com_contents'.$layout.$id;
				$node->expandible=true;
				break;
			case 'article':
				$node->expandible=false;
				if ( $id ) {
					$db = & JFactory::getDBO();
					$node->uid = 'com_contenta'.$id;
					$db = & JFactory::getDBO();
					$db->setQuery("SELECT UNIX_TIMESTAMP(modified) as modified, UNIX_TIMESTAMP(created) as created FROM #__content WHERE id=". $id);
					$item  = $db->loadObject();
					if( !$item->modified )
						$item->modified = $item->created;
					$node->modified = $item->modified;
					$node->expandible = false;
				}
			case 'archive':
				$node->expandible=true;
		}
	}

	/** return a node-tree */
	function getTree(&$xmap, &$parent, &$params) {
		$result = null;

		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$view = JArrayHelper::getValue($link_vars,'view','');
		$layout = JArrayHelper::getValue($link_vars,'layout','');
		$id = intval(JArrayHelper::getValue($link_vars,'id',0));

		$menu =& JSite::getMenu();
		$menuparams = $menu->getParams($parent->id);

		/***
		* Parameters Initialitation
		**/
		//----- Set expand_categories param
		$expand_categories = JArrayHelper::getValue($params,'expand_categories',1);
		$expand_categories = ( $expand_categories == 1
				  || ( $expand_categories == 2 && $xmap->view == 'xml')
				  || ( $expand_categories == 3 && $xmap->view == 'html')
								  ||   $xmap->view == 'navigator');
		$params['expand_categories'] = $expand_categories;

		//----- Set expand_sections param
		$expand_sections = JArrayHelper::getValue($params,'expand_sections',1);
		$expand_sections = ( $expand_sections == 1
				  || ( $expand_sections == 2 && $xmap->view == 'xml')
				  || ( $expand_sections == 3 && $xmap->view == 'html')
								  ||   $xmap->view == 'navigator');
		$params['expand_sections'] = $expand_sections;

		//----- Set show_unauth param
		$show_unauth = JArrayHelper::getValue($params,'show_unauth',1);
		$show_unauth = ( $show_unauth == 1
				  || ( $show_unauth == 2 && $xmap->view == 'xml')
				  || ( $show_unauth == 3 && $xmap->view == 'html'));
		$params['show_unauth'] = $show_unauth;

        //----- Set add_images param
        $add_images = JArrayHelper::getValue($params,'add_images',0);
        $add_images = ( $add_images == 1 && $xmap->view == 'xml');
        $params['add_images'] = $add_images;
        $params['max_images'] = JArrayHelper::getValue($params,'max_images',1000);
        
        
                //----- Set expand_sections param
        $add_pagebreaks = JArrayHelper::getValue($params,'add_pagebreaks',1);
        $add_pagebreaks = ( $add_pagebreaks == 1
                  || ( $add_pagebreaks == 2 && $xmap->view == 'xml')
                  || ( $add_pagebreaks == 3 && $xmap->view == 'html')
                                  ||   $xmap->view == 'navigator');
        $params['add_pagebreaks'] = $add_pagebreaks;
        
        if ( $params['add_pagebreaks'] ) {
            $lang =& JFactory::getLanguage();
            $lang->load('plg_content_pagebreak');
        }

		//----- Set cat_priority and cat_changefreq params
		$priority = JArrayHelper::getValue($params,'cat_priority',$parent->priority);
		$changefreq = JArrayHelper::getValue($params,'cat_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		//----- Set art_priority and art_changefreq params
		$priority = JArrayHelper::getValue($params,'art_priority',$parent->priority);
		$changefreq = JArrayHelper::getValue($params,'art_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['art_priority'] = $priority;
		$params['art_changefreq'] = $changefreq;

		$params['keywords'] = JArrayHelper::getValue($params,'keywords','metakey');
		$params['max_art'] = intval(JArrayHelper::getValue($params,'max_art',0));
		$params['max_art_age'] = intval(JArrayHelper::getValue($params,'max_art_age',0));
		$params['articles_order'] = JArrayHelper::getValue($params,'articles_order','menu');

		if ($xmap->isNews) {
//			$params['max_art_age'] = 2;
			$params['show_unauth'] = 0;
		}

		switch( $view ) {
			case 'category':
				if( $params['expand_categories'] ) {
					xmap_com_content::getContentCategory( $xmap, $parent, $id, $params, $menuparams );
				}
			break;
			case 'section':
				if( $params['expand_sections'] ) {
					if ($layout == 'blog') {
						xmap_com_content::getContentBlogSection($xmap, $parent, $id, $params, $menuparams);
					} else {
						xmap_com_content::getContentSection($xmap, $parent, $id, $params, $menuparams);
					}
				}
			break;
			case 'archive':
				xmap_com_content::getArchivedArticles($xmap, $parent,$params,$menuparams);
			break;
		}
		return true;
	}

	/** Get all content items within a content category.
	 * Returns an array of all contained content items. */
	function getContentCategory(&$xmap, &$parent, $catid, &$params, &$menuparams) {
		$db = & JFactory::getDBO();
		if ($params['articles_order'] == 'menu') {
			$orderby = $menuparams->get('orderby');
			if ( !$orderby ) {
				$orderby = $menuparams->get('orderby_sec','rdate');
			}
		} else {
			$orderby = $params['articles_order'];
		}

		$orderby = xmap_com_content::orderby_sec( $orderby );

		$query =
		  "SELECT a.id, a.title, a.metakey,a.access, UNIX_TIMESTAMP(a.modified) as modified, CASE WHEN strcmp(a.created,a.publish_up)<0 THEN UNIX_TIMESTAMP(a.publish_up) ELSE UNIX_TIMESTAMP(a.created) END as `created`, a.sectionid" .
		  ',CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug' .
		  ',CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug'.
		  ',c.title as category' .
          (($params['add_images'] || $params['add_pagebreaks'])? ',a.introtext, a.fulltext': '').
		  "\n FROM #__content AS a,#__categories AS c" .
		  "\n WHERE a.catid=(".$catid.")" .
		  "\n AND a.catid=c.id" .
		  "\n AND a.state='1'" .
		  "\n AND ( a.publish_up = '0000-00-00 00:00:00' OR a.publish_up <= '". date('Y-m-d H:i:s',$xmap->now) ."' )" .
		  "\n AND ( a.publish_down = '0000-00-00 00:00:00' OR a.publish_down >= '". date('Y-m-d H:i:s',$xmap->now) ."' )" .
		  ( $params['max_art_age'] ? "\n AND ( a.created >= '".date('Y-m-d H:i:s',time() - $params['max_art_age'] * 86400)."' ) " : '') .
		  ( $params['show_unauth'] ? '' : "\n AND a.access<='". $xmap->gid ."'" ) .	// authentication required ?
		  ( $xmap->view != 'xml'?"\n ORDER BY ". $orderby ."": '' ) .
		  ( $params['max_art'] ? "\n LIMIT {$params['max_art']}" : '');
		;
		return xmap_com_content::showArticles($xmap,$parent,$params,$query);
	}

	/** Get all content items within a content category.
	 * Returns an array of all contained content items. */
	function getArchivedArticles(&$xmap, &$parent, &$params, &$menuparams) {
		if ($params['articles_order'] == 'menu') {
			$orderby = $menuparams->get('orderby','');
		} else {
			$orderby = $params['articles_order'];
		}
		$orderby = xmap_com_content::orderby_sec( $orderby );

		$query =
		  "SELECT a.id, a.title, a.metakey, a.access, UNIX_TIMESTAMP(a.modified) as modified, CASE WHEN strcmp(a.created,a.publish_up)<0 THEN UNIX_TIMESTAMP(a.publish_up) ELSE UNIX_TIMESTAMP(a.created) END as `created`, a.sectionid" .
		  ',CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug' .
		  ',CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug' .
		  ',c.title as category' .
          (($params['add_images'] || $params['add_pagebreaks'])? ',a.introtext, a.fulltext':'')
		. "\n FROM #__content AS a,#__categories AS c"
		. "\n WHERE  a.catid=c.id"
		. "\n AND a.state='-1'"
		. "\n AND ( a.publish_up = '0000-00-00 00:00:00' OR a.publish_up <= '". date('Y-m-d H:i:s',$xmap->now) ."' )"
		. "\n AND ( a.publish_down = '0000-00-00 00:00:00' OR a.publish_down >= '". date('Y-m-d H:i:s',$xmap->now) ."' )"
		. ( $params['max_art_age'] ? "\n AND ( created >= '".date('Y-m-d H:i:s',time() - $params['max_art_age'] * 86400)."' ) " : '')
		. ( $params['show_unauth'] ?  '':"\n AND a.access<='". $xmap->gid ."'" )	// authentication required ?
		. ( $xmap->view != 'xml'?"\n ORDER BY ". $orderby ."": '' )
		. ( $params['max_art'] ? "\n LIMIT {$params['max_art']}" : '');
		;
		return xmap_com_content::showArticles($xmap,$parent,$params,$query);
	}

	function showArticles(&$xmap, &$parent,$params, $query)
	{
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$db->getQuery(  );
		$items = $db->loadObjectList();
        
        static $urlBase;
        
        if (!isset($urlBase)){
            $urlBase = JURI::base();
            $urlBaseLen = strlen($urlBase);
        }

		if ( count($items) > 0 ) {
			$xmap->changeLevel(1);
			foreach($items as $item) {
				// Ignore old items for news sitemap
				if ($xmap->isNews && $item->created < ($xmap->now-(2* 86400))) {
					continue;
				}
                $subnodes = array();
				$node = new stdclass();
				$node->id = $parent->id;
				$node->uid = $parent->uid.'a'.$item->id;
				$node->browserNav = $parent->browserNav;
				$node->priority = $params['art_priority'];
				$node->changefreq = $params['art_changefreq'];
				$node->name = $item->title;
				$node->access = $item->access;
				$node->expandible = false;
				// TODO: Should we include category name or metakey here?
				switch ( $params['keywords'] ) {
					case 'metakey':
						$node->keywords = $item->metakey;
					case 'category':
						$node->keywords = $item->category;
					case 'both':
						$node->keywords = $item->metakey . ($item->metakey? ',':'').$item->category;
				}
				$node->newsItem = 1;

				// For the google news we should use te publication date instead
				// the last modification date. See
				$node->modified = (@$item->modified? $item->modified : $item->created);
				// $node->link = 'index.php?option=com_content&amp;view=article&amp;catid='.$item->catslug.'&amp;id='.$item->slug;
				$node->link = ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, @$item->sectionid);

                // Add images to the article
                $text = @$item->introtext.@$item->fulltext;
                if ($params['add_images']) {
                    $matches = array();
                    if (preg_match_all('/<img[^>]*?(?:(?:[^>]*src="(?P<src>[^"]+)")|(?:[^>]*alt="(?P<alt>[^"]+)")|(?:[^>]*title="(?P<title>[^"]+)"))+[^>]*>/i',$text,$matches,PREG_SET_ORDER)) {
                        $node->images = array();

                        $count = count($matches);
                        $j=0;
                        for ($i=0; $i<$count && $j<$params['max_images']; $i++){
                            if ( substr($matches[$i]['src'],0,1) == '/' || !preg_match('/^https?:\/\//i',$matches[$i]['src']) || substr($matches[$i]['src'],0,$urlBaseLen) == $urlBase ) {
                                $src = $matches[$i]['src'];
                                if (substr($src) == '/'){
                                    $src = substr($src,1);
                                }
                                if (!preg_match('/^https?:\//')){
                                    $src =  $urlBase.$src;
                                }
                                $image = new stdClass;
                                $image->src = $src;
                                $image->title = (isset($matches[$i]['title'])? $matches[$i]['title'] : @$matches[$i]['alt']);
                                $node->images[] = $image;
                                $j++;
                            }
                        }
                    }
                }
                
                if ($params['add_pagebreaks']) {
                    $matches = array();
                    if (preg_match_all('/<hr\s*[^>]*?(?:(?:\s*alt="(?P<alt>[^"]+)")|(?:\s*title="(?P<title>[^"]+)"))+[^>]*>/i',$text,$matches,PREG_SET_ORDER)) {
                        $i = 2;
                        foreach ( $matches as $match )
                        {
                            if (strpos($match[0],'class="system-pagebreak"')!== FALSE) {
                                $link = $node->link.'&limitstart='. ($i-1);

                                if ( @$match['alt'] )
                                {
                                    $title    = stripslashes( $match['alt'] );
                                }
                                elseif ( @$match['title'] )
                                {
                                    $title    = stripslashes( $match['title'] );
                                }
                                else
                                {
                                    $title    = JText::sprintf( 'Page #', $i );
                                }
                                $subnode = new stdclass();
                                $subnode->id = $parent->id;
                                $subnode->uid = $parent->uid.'a'.$item->id.'p'.$i;
                                $subnode->browserNav = $parent->browserNav;
                                $subnode->priority = $params['art_priority'];
                                $subnode->changefreq = $params['art_changefreq'];
                                $subnode->name = $title;
                                $subnode->access = $item->access;
                                $subnode->expandible = false;
                                $subnode->link = $link;
                                $subnodes[]=$subnode;
                                $i++;
                            }
                        }
                        $node->expandible = (count($subnodes)> 0); // This article has children

                    }
                }
				if ($xmap->printNode($node) && $node->expandible) {
                    $xmap->changeLevel(1);
                    foreach ($subnodes as $subnode) {
                        $xmap->printNode($subnode);
                    }
                    $xmap->changeLevel(-1);
                }
	    	}
			$xmap->changeLevel(-1);
	    }
	    return true;
	}


	/** Get all Categories within a Section.
	 * Also call getCategory() for each Category to include it's items */
	function getContentSection(&$xmap, &$parent, $secid, &$params, &$menuparams ) {
		$db = & JFactory::getDBO();

		$orderby = $menuparams->get('orderby');
		$orderby = xmap_com_content::orderby_sec( $orderby );
		$query =
		  'SELECT a.id, a.title, a.name, a.params,a.section,a.alias'
		. ',CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug'
		. "\n FROM #__categories AS a"
		. "\n LEFT JOIN #__content AS b ON b.catid = a.id "
		. "\n AND b.state = '1'"
		. "\n AND ( b.publish_up = '0000-00-00 00:00:00' OR b.publish_up <= '". date('Y-m-d H:i:s',$xmap->now) ."' )"
		. "\n AND ( b.publish_down = '0000-00-00 00:00:00' OR b.publish_down >= '". date('Y-m-d H:i:s',$xmap->now) ."' )"
		. ( $params['show_unauth']  ? '' : "\n AND b.access <= ". $xmap->gid )		// authentication required ?
		. "\n WHERE a.section = '". $secid ."'"
		. "\n AND a.published = '1'"
		. ( $params['show_unauth']  ? '' : "\n AND a.access <= ". $xmap->gid )		// authentication required ?
		. "\n GROUP BY a.id"
		. ( $menuparams->get('empty_cat') ? '' : "\n HAVING COUNT( b.id ) > 0" )	// hide empty categories ?
		. ( $xmap->view != 'xml'? "\n ORDER BY ". $orderby: '');

		$db->setQuery( $query );
		$items = $db->loadObjectList();

		$layout = '';

		$xmap->changeLevel(1);
		foreach($items as $item) {
			$node = new stdclass();
			$node->id = $parent->id;
			$node->uid = $parent->uid.'c'.$item->id;
			$node->name = $item->title;
			$node->browserNav = $parent->browserNav;
			$node->priority = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->expandible = true;
			$node->link = ContentHelperRoute::getCategoryRoute($item->slug, $item->section);
			# $node->link = 'index.php?option=com_content&amp;view=category'.$layout.'&amp;id='.$item->slug;
			if( ($xmap->printNode($node) !== FALSE) && $params['expand_categories'] ) {
				xmap_com_content::getContentCategory($xmap, $parent, $item->id, $params, $menuparams);
			}
		}
		$xmap->changeLevel(-1);
		return true;
	}

	/** Return an array with all Items in a Section */
	function getContentBlogSection(&$xmap, &$parent, $secid, &$params, &$menuparams ) {
		$db = & JFactory::getDBO();
		if ($params['articles_order'] == 'menu') {
			$order_sec = $menuparams->get('orderby_sec','rdate');
		} else {
			$order_sec = $params['articles_order'];
		}
		$order_pri = $menuparams->get('orderby_pri');

		$order_pri	= xmap_com_content::orderby_pri( $order_pri );
		$order_sec	= xmap_com_content::orderby_sec( $order_sec );

		$now = date('Y-m-d H:i:s',$xmap->now);
		$query =
		  "SELECT a.id, a.title,a.access,a.metakey, UNIX_TIMESTAMP(a.modified) AS modified, UNIX_TIMESTAMP(a.created) AS created,a.sectionid"
		. ',CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug'
		. ',CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
		  ',cc.title as category'
		. "\n FROM #__content AS a"
		. "\n INNER JOIN #__categories AS cc ON cc.id = a.catid"
		. "\n LEFT JOIN #__users AS u ON u.id = a.created_by"
		. "\n LEFT JOIN #__content_rating AS v ON a.id = v.content_id"
		. "\n LEFT JOIN #__sections AS s ON a.sectionid = s.id"
		. "\n LEFT JOIN #__groups AS g ON a.access = g.id"
		. "\n WHERE a.sectionid = '". $secid ."'"
		. "\n AND s.access <= ".$xmap->gid
		. "\n AND cc.access <= ".$xmap->gid
		. "\n AND a.state = '1'"
		. "\n AND ( a.publish_up = '0000-00-00 00:00:00' OR a.publish_up <= '$now' )"
		. "\n AND ( a.publish_down = '0000-00-00 00:00:00' OR a.publish_down >= '$now' )"
		. ( $params['show_unauth'] ? '' : "\n AND a.access <= ". $xmap->gid )		// authentication required ?
		. ( $params['max_art_age'] ? "\n AND ( a.created >= '".date('Y-m-d H:i:s',time() - $params['max_art_age'] * 86400)."' ) " : '')
		. "\n AND s.published = 1"
		. "\n AND cc.published = 1"
		. ($xmap->view!='xmal'?"\n ORDER BY $order_pri $order_sec":'');
		return xmap_com_content::showArticles($xmap,$parent,$params,$query);

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

}

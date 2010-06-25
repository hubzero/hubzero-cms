<?php
/**
* @author Guillermo Vargas
* @email guille@vargas.co.cr
* @version $Id: com_weblinks.php
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Joomla's web links component
*/

defined( '_JEXEC' ) or die( 'Restricted access.' );

class xmap_com_weblinks {

	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item and indicate whether the node is expandible or not
	*/
	function prepareMenuItem(&$node) {
		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$view = JArrayHelper::getValue($link_vars,'view','');
		if ( $view == 'weblink') {
			$id = intval(JArrayHelper::getValue($link_vars,'id',0));
			if ( $id ) {
				$node->uid = 'com_weblinksi'.$id;
				$node->expandible = false;
			}
		} elseif ( $view == 'categories') {
            $catid = intval(JArrayHelper::getValue($link_vars,'catid',0));
            $node->uid = 'com_weblinkscategories';
            $node->expandible = true;
        } elseif ( $view == 'category' ) {
			$catid = intval(JArrayHelper::getValue($link_vars,'catid',0));
			$node->uid = 'com_weblinksc'.$catid;
			$node->expandible = true;
		}
	}

	function getTree( &$xmap, &$parent, &$params) {
		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars );
		$view = JArrayHelper::getValue($link_vars,'view',0);

		$menu =& JSite::getMenu();
		$menuparams = $menu->getParams($parent->id);

		if ( $view == 'category' ) {
			    $catid = intval(JArrayHelper::getValue($link_vars,'id',0));
		} elseif ( $view == 'categories' ) {  
                $catid = 0;
        } else { // Only expand category menu items
			return;
		}

		$include_links = JArrayHelper::getValue( $params, 'include_links',1,'' );
		$include_links = ( $include_links == 1
				  || ( $include_links == 2 && $xmap->view == 'xml')
				  || ( $include_links == 3 && $xmap->view == 'html')
				  ||   $xmap->view == 'navigator');
		$params['include_links'] = $include_links;

		$priority = JArrayHelper::getValue($params,'cat_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'cat_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = JArrayHelper::getValue($params,'link_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'link_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;

		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['link_priority'] = $priority;
		$params['link_changefreq'] = $changefreq;

		$params['limit'] = '';
		$limit = JArrayHelper::getValue($params,'max_links','','');

		if ( intval($limit) && $xmap->view != 'navigator' ) {
			$params['limit'] = ' LIMIT '.$limit;
		}

		xmap_com_weblinks::getCategoryTree($xmap, $parent, $params, $catid );

	}

	function getCategoryTree ( &$xmap, &$parent, &$params, $catid) {
		$db = &JFactory::getDBO();

        if (!$catid) { // view=categories
            $query = ' SELECT a.id,a.title'.
                     ' ,CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug' .
                     ' FROM #__categories a '.
                     ' WHERE a.published=1 and a.section=\'com_weblinks\' and  a.access<='.$xmap->gid.
                     ' ORDER by a.ordering ';

            $db->setQuery($query);
            $cats = $db->loadObjectList();
            $xmap->changeLevel(1);
            foreach($cats as $cat) {
                $node = new stdclass;
                $node->id   = $parent->id;
                $node->uid  = $parent->uid .'c'.$cat->id;
                $node->name = $cat->title;
                $node->link = 'index.php?option=com_weblinks&amp;view=category&amp;id='.$cat->slug;
                $node->priority   = $params['cat_priority'];
                $node->changefreq = $params['cat_changefreq'];
                $node->expandible = true;
                if ( $xmap->printNode($node) !== FALSE ) {
                    xmap_com_weblinks::getCategoryTree( $xmap, $parent, $params, $cat->id );
                }
            }
            $xmap->changeLevel(-1);
        } elseif ( $params['include_links'] ) { //view=category&catid=...
        
		    $query = ' SELECT a.id,a.title'.
		             ' ,CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug' .
		             ' ,CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug'.
		             ' FROM #__weblinks a,#__categories c '.
		             ' WHERE a.catid = c.id AND c.published=1 and c.access<='.$xmap->gid.' AND '.
		             ' a.published=1 AND a.archived=0 AND a.approved=1'.
		             ( $catid? ' AND a.catid='.$catid.' ' : ' ') .
		             ' ORDER by a.ordering '.
		             $params['limit'];

		    $db->setQuery($query);
		    $links = $db->loadObjectList();
		    $xmap->changeLevel(1);
		    foreach($links as $link) {
			    $node = new stdclass;
			    $node->id   = $parent->id;
			    $node->uid  = $parent->uid .'i'.$link->id;
			    $node->name = $link->title;
			    $node->link = 'index.php?option=com_weblinks&amp;view=weblink&amp;id='.$link->slug . "&amp;catid=" . $link->catslug;
			    $node->priority   = $params['link_priority'];
			    $node->changefreq = $params['link_changefreq'];
			    $node->expandible = false;
			    $xmap->printNode($node);
		    }
		    $xmap->changeLevel(-1);
        }
	}

}

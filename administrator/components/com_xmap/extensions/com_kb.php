<?php
/**
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @version $Id: com_kb.php 52 2009-10-24 22:35:11Z guilleva $
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Joomla KnowledgeBase component
*/

defined( '_JEXEC' ) or die( 'Restricted access.' );

class xmap_com_kb {

	function getTree( &$xmap, &$parent, &$params) {
		if ( strpos($parent->link, 'task=article') ) {
			return true;	
		}

		$link_query = parse_url( $parent->link );
                parse_str( html_entity_decode($link_query['query']), $link_vars );
                $catid = JArrayHelper::getValue($link_vars,'category',0);

		$include_articles = JArrayHelper::getValue( $params, 'include_articles',1,'' );
		$include_articles = ( $include_articles == 1
                                  || ( $include_articles == 2 && $xmap->view == 'xml') 
                                  || ( $include_articles == 3 && $xmap->view == 'html'));
		$params['include_articles'] = $include_articles;

		$params['include_feeds'] = JArrayHelper::getValue( $params, 'include_feeds',1,'' );
		if ($xmap->view != 'xml' ) {
			$params['include_feeds']=0;
		}

		$priority = JArrayHelper::getValue($params,'cat_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'cat_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;
		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = JArrayHelper::getValue($params,'article_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'article_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;
		$params['article_priority'] = $priority;
		$params['article_changefreq'] = $changefreq;

		$priority = JArrayHelper::getValue($params,'feed_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'feed_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;
		$params['feed_priority'] = $priority;
		$params['feed_changefreq'] = $changefreq;

		if ( $include_articles ) {
			$params['limit'] = '';
			$params['days'] = '';
			$limit = JArrayHelper::getValue($params,'max_articles','','');

			if ( intval($limit) )
				$params['limit'] = ' LIMIT '.$limit;

			$days = JArrayHelper::getValue($params,'max_age','','');
			if ( intval($days) )
				$params['days'] = ' AND a.`created` >= \''.date('Y-m-d H:m:s', ($xmap->now - ($days*86400)) ) ."' ";
		}
		
		if ( $params['include_feeds'] ) {
			// Include the latest listings feed
			$node = new stdclass;
			$node->id   = $parent->id;
			$node->uid  = $parent->uid.'flatest';
			$node->name = 'Latest Listings';
			$node->priority   = $params['feed_priority'];
			$node->changefreq = $params['feed_changefreq'];
			$node->link = 'index.php?option=com_kb&amp;task=rss&amp;format=RSS2.0&amp;no_html=1&amp;pop=1&amp;type=latestlistings';
			$node->expandible = false;
			$xmap->printNode($node);

			// Include the Top 10 Most Popular Listings feed
			$node = new stdclass;
			$node->id   = $parent->id;
			$node->uid  = $parent->uid.'ftop10';
			$node->name = 'Top 10 Most Popular Listings';
			$node->priority   = $params['feed_priority'];
			$node->changefreq = $params['feed_changefreq'];
			$node->expandible = false;
			$node->link = 'index.php?option=com_kb&amp;task=rss&amp;format=RSS2.0&amp;no_html=1&amp;pop=1&amp;type=mostpopular';
			$xmap->printNode($node);
		}

		xmap_com_kb::getKBTree( $xmap, $parent, $params, $catid );
	}

	function getKBTree ( &$xmap, &$parent, &$params, &$catid ) {
		$db = JFactory::getDBO();

		$db->setQuery("select `id`, `title`,UNIX_TIMESTAMP(`created`) as `created`,UNIX_TIMESTAMP(`modified`) as `modified` from #__kb_categorys where parentid=$catid and published = '1' and access<=".$xmap->gid." order by ordering");
		# echo $db->getQuery();
		$cats = $db->loadObjectList();
		$xmap->changeLevel(1);
		foreach($cats as $cat) {
			$node = new stdclass;
			$node->id   = $parent->id;
			$node->uid  = $parent->uid.'c'.$cat->id;
			$node->name = $cat->title;
			$node->modified = ($cat->modified? $cat->modified : $cat->created);
			$node->priority   = $params['cat_priority'];
			$node->changefreq = $params['cat_changefreq'];
			$node->expandible = true;
			$node->link = 'index.php?option=com_kb&amp;task=category&amp;category='.$cat->id;

			if ( $xmap->printNode($node) !== FALSE ) { // To allow the items exclusion feature
				if ( $params['include_feeds'] ) {
					$node = new stdclass;
					$node->id   = $parent->id;
					$node->uid  = $parent->uid.'f'.$cat->id;
					$node->name = $cat->title . ' feed';
					$node->priority   = $params['feed_priority'];
					$node->changefreq = $params['feed_changefreq'];
					$node->link  = 'index.php?option=com_kb&amp;task=rss&amp;format=RSS2.0&amp;no_html=1&amp;pop=1&amp;type=latestlistingspercategory&amp;category='.$cat->id;
					$node->expandible = false;
					$xmap->printNode($node);
				}
				xmap_com_kb::getKBTree($xmap, $parent, $params, $cat->id);
			}
		}

		if ($params['include_articles']) {
			$db->setQuery ("select a.`id`, a.`title`,UNIX_TIMESTAMP(a.`created`) as `created`,UNIX_TIMESTAMP(a.`modified`) as `modified` from #__kb_articles a, #__kb_category_map m where m.category_id=$catid and m.article_id=a.id and a.`published` = '1'  and a.`access`<=".$xmap->gid." ".$params['days']." order by m.ordering" . $params['limit']);
			$cats = $db->loadObjectList();
			foreach($cats as $article) {
				$node = new stdclass;
				$node->id   = $parent->id;
				$node->uid  = $parent->uid .'c'.$catid.'a'.$article->id;
				$node->name = $article->title;
				$node->modified = ($article->modified? $article->modified : $article->created);
				$node->link = 'index.php?option=com_kb&amp;task=article&amp;article='.$article->id;
				$node->priority   = $params['article_priority'];
				$node->changefreq = $params['article_changefreq'];
				$node->expandible = false;
				$xmap->printNode($node);
			}
		}
		$xmap->changeLevel(-1);

	}
	
}

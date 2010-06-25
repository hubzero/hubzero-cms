<?php
/**
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @version $Id: com_agora.php 76 2009-12-19 19:41:55Z guilleva $
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Agora Forumn Component.
*/

/** Handles Agora forum structure */
class xmap_com_agora {

	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item
	*/
	function prepareMenuItem(&$node) {
		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$id = intval(JArrayHelper::getValue($link_vars,'id',0));
		$task = JArrayHelper::getValue( $link_vars, 'task', '', '' );
		if ( $task == '' && $id ) {
			$node->uid = 'com_agorac'.$catid;
			$node->expandible = true;
		} elseif ($task == 'forum' && $id) {
			$node->uid = 'com_agoraf'.$id;
			$node->expandible = true;
		} elseif ($task == 'topic' && $id) {
			$node->uid = 'com_agorat'.$id;
			$node->expandible = false;
		}
	}

	function getTree ( &$xmap, &$parent, &$params ) {
		if (strpos($parent->link, 'task=topic') ) {
			return true;   // Do not expand links to posts
		}
		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$task = $xmap->getParam($link_vars,'task','');
		$id = $xmap->getParam($link_vars,'id',0);
		$forumid = $catid = 0;
		if ($task == 'forum') {
			$forumid = $id;
		} else {
			$catid = $id;
                }

		$include_forums = $xmap->getParam($params,'include_forums',1);
		$include_forums = ( $include_forums == 1
				  || ( $include_forums == 2 && $xmap->view == 'xml')
				  || ( $include_forums == 3 && $xmap->view == 'html')
				  || $xmap->view == 'navigator');
		$params['include_forums'] = $include_forums;

		$include_topics = $xmap->getParam($params,'include_topics',1);
		$include_topics = ( $include_topics == 1
				  || ( $include_topics == 2 && $xmap->view == 'xml')
				  || ( $include_topics == 3 && $xmap->view == 'html')
				  || $xmap->view == 'navigator');
		$params['include_topics'] = $include_topics;

		$priority = $xmap->getParam($params,'cat_priority',$parent->priority);
		$changefreq = $xmap->getParam($params,'cat_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		// Forums Properties
		$priority = $xmap->getParam($params,'forum_priority',$parent->priority);
		$changefreq = $xmap->getParam($params,'forum_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;

		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['forum_priority'] = $priority;
		$params['forum_changefreq'] = $changefreq;

		// Topics Properties
		$priority = $xmap->getParam($params,'topic_priority',$parent->priority);
		$changefreq = $xmap->getParam($params,'topic_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;

		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['topic_priority'] = $priority;
		$params['topic_changefreq'] = $changefreq;

		if ( $include_topics ) {
			$ordering = $xmap->getParam($params,'topics_order','ordering');
			if ( !in_array($ordering,array('last_post','subject','num_views')) )
				$ordering = 'last_post desc';
			$params['topics_order'] = $ordering;

			$params['limit'] = '';
			$params['days'] = '';
			$limit = $xmap->getParam($params,'max_topics','');
			if ( intval($limit) )
				$params['limit'] = ' LIMIT '.$limit;

			$days = $xmap->getParam($params,'max_age','');
			if ( intval($days) )
				$params['days'] = ' AND last_post >='.($xmap->now - ($days*86400)) ." ";
		}

		xmap_com_agora::getCategoryTree($xmap, $parent, $params, $catid, $forumid);
	}

	/* Return category/forum tree */
	function getCategoryTree( &$xmap, &$parent, &$params, $parentCat, $parentForum=0 )
	{
		$database =& JFactory::getDBO();

		/*get list of categories*/
		$xmap->changeLevel(1);

		if ( !$parentCat && !$parentForum ) {
			$query = "SELECT id, cat_name FROM #__agora_categories WHERE enable=1 ORDER BY disp_position";
			$database->setQuery($query);
			# echo $database->getQuery();
			$cats = $database->loadObjectList();

			foreach ( $cats as $cat ) {
				$node = new stdclass;
				$node->id   = $parent->id;
				$node->browserNav = $parent->browserNav;
				$node->uid   = $parent->uid.'c'.$cat->id;
				$node->name = $cat->cat_name;
				$node->priority = $params['cat_priority'];
				$node->changefreq = $params['cat_changefreq'];
				$node->link = 'index.php?option=com_agora&amp;id='.$cat->id;
				$node->expandible = true;
				if ( $xmap->printNode($node) !== FALSE ) {
					xmap_com_agora::getCategoryTree($xmap,$parent,$params,$cat->id,0);
				}
			}
		} else {
			if ( $params['include_forums'] ) {
				$query = "SELECT f.id,f.forum_name,f.last_post as modified ".
				         "FROM #__agora_forums AS f ".
				         "WHERE cat_id=$parentCat ".
				         "AND parent_forum_id=$parentForum AND enable=1";
				$database->setQuery($query);
				$forums = $database->loadObjectList();
				//get list of forums
				foreach($forums as $forum) {
					$node = new stdclass;
					$node->id   = $parent->id;
					$node->browserNav = $parent->browserNav;
					$node->uid = $parent->uid.'f'.$forum->id;
					$node->name = $forum->forum_name;
					$node->priority = $params['forum_priority'];
					$node->changefreq = $params['forum_changefreq'];
					$node->modified = intval($forum->modified);
					$node->link = 'index.php?option=com_agora&amp;task=forum&amp;id='.$forum->id;
					$node->expandible = true;
					if ( $xmap->printNode($node) !== FALSE ) {
						xmap_com_agora::getCategoryTree($xmap,$parent,$params,$parentCat,$forum->id);
					}
				}

					if ( $params['include_topics'] ) {
						$query = "SELECT id, subject, last_post as modified ".
						 	"FROM #__agora_topics ".
							"WHERE forum_id=$parentForum ".
							$params['days'] .
							"ORDER BY ". $params['topics_order'] .
							$params['limit'];

						$database->setQuery($query);
						$topics = $database->loadObjectList();

						//get list of topics
						foreach($topics as $topic) {
							$node = new stdclass;
							$node->id   = $parent->id;
							$node->browserNav = $parent->browserNav;
							$node->uid = $parent->uid.'t'.$topic->id;
							$node->name = $topic->subject;
							$node->priority = $params['topic_priority'];
							$node->changefreq = $params['topic_changefreq'];
							$node->modified = intval($topic->modified);
							$node->link = 'index.php?option=com_agora&amp;task=topic&amp;id='.$topic->id;
							$node->expandible = false;
							$xmap->printNode($node);
						}
					}
			}
		}
		$xmap->changeLevel(-1);
	}
}

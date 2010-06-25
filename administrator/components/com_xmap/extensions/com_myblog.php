<?php
/**
* @author Guillermo Vargas
* @email guille@vargas.co.cr
* @version $Id: com_contact.php
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Joomla's contact component
*/

defined( '_JEXEC' ) or die( 'Restricted access.' );

class xmap_com_myblog {

    function getMyBlog( &$xmap, &$parent,&$params )
    {
        $database = &JFactory::getDBO();
        $my =  &JFactory::getUser();

        require_once (JPATH_SITE . '/components/com_myblog/functions.myblog.php');
        require_once (JPATH_SITE . '/administrator/components/com_myblog/config.myblog.php');
        $_MY_CONFIG = new MYBLOG_Config();

        $managed_sections = $_MY_CONFIG->get('managedSections');

        // include popular bloggers by default
        $query = "SELECT a.created_by, sum(hits) AS hits,u.username FROM #__content AS a LEFT JOIN #__users AS u ON a.created_by=u.id  WHERE a.sectionid IN (".$managed_sections.") AND a.state=1 group by a.created_by order by hits desc";
        if ($params['number_of_bloggers'])
            $query .= " limit 0, ". $params['number_of_bloggers'];
        $database->setQuery($query);
        $rows = $database->loadObjectList();
        $modified = time();

        if ($params['include_bloggers'] || $params['include_blogger_posts']) {

            if ($params['include_bloggers'] ) {
                $xmap->changeLevel(1);
                $node = new stdclass;
                $node->browserNav = $parent->browserNav;
                $node->id = $parent->id;
                $node->uid = $parent->uid.'b';
                $node->priority = $params['blogger_priority'];
                $node->changefreq = $params['blogger_changefreq'];
                $node->name = $params["text_bloggers"];
                $node->link = "index.php?option=com_myblog&task=blogs";
                $node->modified = time();
                $node->expandible = true;
                $xmap->printNode($node);

                $xmap->changeLevel(1);
            }
            foreach($rows as $row)
            {
                $node = new stdclass;

                if ($row->username) {
                    if ($params['include_bloggers'] ) {
                        $node->id = $parent->id;
                        $node->uid = $parent->uid.'b'.$row->username;
                        $node->priority = $params['blogger_priority'];
                        $node->changefreq = $params['blogger_changefreq'];
                        $node->browserNav = $parent->browserNav;
                        $node->name = $row->username;
                        $node->modified = $modified;
                        $node->link = "index.php?option=com_myblog&blogger=".$node->name;
                        $node->expandible = true;
                        $xmap->printNode($node);
                    }
                    if ($params['include_blogger_posts'] )
                    {
                        $sql = "SELECT a.id, a.title, a.created_by, a.modified,b.permalink from #__content a LEFT JOIN #__myblog_permalinks AS b on a.id=b.contentid WHERE created_by=".$row->created_by.  " and sectionid in (".$managed_sections.") and
                        state=1 order by modified desc";
                        if ($params['number_of_post_per_blogger']) {
                            $sql .= " limit 0, ". $params['number_of_post_per_blogger'];
                        }
                        $res = $database->setQuery($sql);
                        $posts = $database->loadObjectList();
                        $xmap->changeLevel(1);
                        foreach ($posts as $post)
                        {
                            $node = new stdclass;
                            $node->id = $post->id;
                            $node->uid = $parent->uid.'p'.$post->id;
                            $node->priority = $params['entry_priority'];
                            $node->changefreq = $params['entry_changefreq'];
                            $node->browserNav = $parent->browserNav;
                            $node->modified = intval($post->modified);
                            $node->name = $post->title;
                            $node->link = "index.php?option=com_myblog&show=".$post->permalink;
                            $node->expandible = false;
                            $xmap->printNode($node);
                        }
                        $xmap->changeLevel(-1);
                    }
                }
            }
            if ($params['include_bloggers'] ) {
                $xmap->changeLevel(-1);
                $xmap->changeLevel(-1);
            }
        }

        // retrieve tag clouds
        if ($params['include_tag_clouds']) {
            $xmap->changeLevel(1);
            $node = new stdclass;
            $node->browserNav = $parent->browserNav;
            $node->id = $parent->id;
            $node->uid = $parent->uid.'t';
            $node->priority = $params['cats_priority'];
            $node->changefreq = $params['cats_changefreq'];
            $node->name = "Tag Clouds";
            $node->link = "index.php?option=com_myblog&task=categories";
            $node->expandible = true;
            $xmap->printNode($node);

            // http://archive/index.php?option=com_myblog&category=sports&Itemid=8

            $query = "SELECT * from #__myblog_categories";
            $database->setQuery($query);
            $tagrows = $database->loadObjectList();

            $tag_clouds=array();
            $j=count($tagrows);
            $i=0;
            $xmap->changeLevel(1);
            while ( $i<$j )
            {
                $node = new stdclass;
                $node->id = $parent->id;
                $node->uid = $parent->uid.'t'.$tagrows[$i]->name;
                $node->priority = $params['tag_priority'];
                $node->changefreq = $params['tag_changefreq'];
                $node->browserNav = $parent->browserNav;
                $node->name = $tagrows[$i]->name;
                $node->modified = $modified;
                $node->link = "index.php?option=com_myblog&category=".$node->name;
                $node->expandible = false;
                $xmap->printNode($node);

                if ($params['include_feed']) {
                    $node = new stdclass;
                    $node->id = $parent->id;
                    $node->uid = $parent->uid.'f'.$tagrows[$i]->name;
                    $node->priority = $params['feed_priority'];
                    $node->changefreq = $params['feed_changefreq'];
                    $node->browserNav = $parent->browserNav;
                    $node->name = $tagrows[$i]->name . ' Feed';
                    $node->modified = $modified;
                    $node->link = "index.php?option=com_myblog&category=".$tagrows[$i]->name. "&task=rss";
                    $xmap->printNode($node);
                }
                $i++;

            }
            $xmap->changeLevel(-1);
            $xmap->changeLevel(-1);
        }

        // time to retrieve archives now
        if ( $params['include_archives'] ) {
            $xmap->changeLevel(1);
            $query = 'SELECT DISTINCT (date_format(jc.created,"%M-%Y")) as archive FROM #__content as jc WHERE jc.sectionid IN('.$managed_sections.') and state = 1 ORDER BY jc.created DESC';
            $database->setQuery($query);
            $objList = $database->loadObjectList();
            foreach ($objList as $obj)
            {
                $node = new stdclass;
                $node->browserNav = $parent->browserNav;
                $node->id = $parent->id;
                $node->uid = $parent->uid.'a'.$obj->archive;
                $node->priority = $params['arc_priority'];
                $node->changefreq = $params['arc_changefreq'];
                $node->name = $obj->archive;
                $node->link = "index.php?option=com_myblog&archive=".$obj->archive;
                $node->expandible = false;
                $xmap->printNode($node);
            }
            $xmap->changeLevel(-1);
        }

        if ( $params['include_feed'] ) {
            $xmap->changeLevel(1);
            $node = new stdclass;
            $node->browserNav = $parent->browserNav;
            $node->id = $parent->id;
            $node->uid = $parent->uid.'f';
            $node->priority = $params['feed_priority'];
            $node->changefreq = $params['feed_changefreq'];
            $node->name = 'Feed';
            $node->link = "index.php?option=com_myblog&task=rss";
            $node->modified = time();
            $node->expandible = false;
            $xmap->printNode($node);
            $xmap->changeLevel(-1);
        }
    }

    /**   Get   the   content   tree for this kind of content */
    function getTree( &$xmap, &$parent, &$params   )
    {
        
        $include_bloggers = JArrayHelper::getValue($params,'include_bloggers',1);
        $include_bloggers = ( $include_bloggers == 1
        || ( $include_bloggers == 2 && $xmap->view == 'xml')
        || ( $include_bloggers == 3 && $xmap->view == 'html')
        ||   $xmap->view == 'navigator');
        $params['include_bloggers'] = $include_bloggers;

        $include_tag_clouds = JArrayHelper::getValue($params,'include_tag_clouds',1);
        $include_tag_clouds = ( $include_tag_clouds == 1
        || ( $include_tag_clouds == 2 && $xmap->view == 'xml')
        || ( $include_tag_clouds == 3 && $xmap->view == 'html')
        ||   $xmap->view == 'navigator');
        $params['include_tag_clouds'] = $include_tag_clouds;

        $include_archives = JArrayHelper::getValue($params,'include_archives',1);
        $include_archives = ( $include_archives == 1
        || ( $include_archives == 2 && $xmap->view == 'xml')
        || ( $include_archives == 3 && $xmap->view == 'html')
        ||   $xmap->view == 'navigator');
        $params['include_archives'] = $include_archives;

        $include_feed = JArrayHelper::getValue($params,'include_feed',1);
        $include_feed = ( $include_feed == 1
        || ( $include_feed == 2 && $xmap->view == 'xml')
        || ( $include_feed == 3 && $xmap->view == 'html'));
        $params['include_feed'] = $include_feed;

        $number_of_bloggers = intval(JArrayHelper::getValue($params,'number_of_bloggers',8));
        $params['number_of_bloggers'] = $number_of_bloggers;

        $text_bloggers = JArrayHelper::getValue($params,'text_bloggers','Bloggers');
        $params['text_bloggers'] = $text_bloggers;

        $include_blogger_posts = JArrayHelper::getValue($params,'include_blogger_posts',1);
        $include_blogger_posts = ( $include_blogger_posts == 1
        || ( $include_blogger_posts == 2 && $xmap->view == 'xml')
        || ( $include_blogger_posts == 3 && $xmap->view == 'html')
        ||   $xmap->view == 'navigator');
        $params['include_blogger_posts'] = $include_blogger_posts;

        $number_of_post_per_blogger = intval(JArrayHelper::getValue($params,'number_of_post_per_blogger',32));
        $params['number_of_post_per_blogger'] = $number_of_post_per_blogger;

        //----- Set tag_priority and tag_changefreq params
        $priority = JArrayHelper::getValue($params,'tag_priority',$parent->priority);
        $changefreq = JArrayHelper::getValue($params,'tag_changefreq',$parent->changefreq);
        if ($priority  == '-1')
            $priority = $parent->priority;
        if ($changefreq  == '-1')
            $changefreq = $parent->changefreq;

        $params['tag_priority'] = $priority;
        $params['tag_changefreq'] = $changefreq;

        //----- Set feed_priority and feed_changefreq params
        $priority = JArrayHelper::getValue($params,'feed_priority',$parent->priority);
        $changefreq = JArrayHelper::getValue($params,'feed_changefreq',$parent->changefreq);
        if ($priority  == '-1')
            $priority = $parent->priority;
        if ($changefreq  == '-1')
            $changefreq = $parent->changefreq;

        $params['feed_priority'] = $priority;
        $params['feed_changefreq'] = $changefreq;

        //----- Set cats_priority and cats_changefreq params
        $priority = JArrayHelper::getValue($params,'cats_priority',$parent->priority);
        $changefreq = JArrayHelper::getValue($params,'cats_changefreq',$parent->changefreq);
        if ($priority  == '-1')
            $priority = $parent->priority;
        if ($changefreq  == '-1')
            $changefreq = $parent->changefreq;

        $params['cats_priority'] = $priority;
        $params['cats_changefreq'] = $changefreq;

        //----- Set blogger_priority and blogger_changefreq params
        $priority = JArrayHelper::getValue($params,'blogger_priority',$parent->priority);
        $changefreq = JArrayHelper::getValue($params,'blogger_changefreq',$parent->changefreq);
        if ($priority  == '-1')
            $priority = $parent->priority;
        if ($changefreq  == '-1')
            $changefreq = $parent->changefreq;

        $params['blogger_priority'] = $priority;
        $params['blogger_changefreq'] = $changefreq;

        //----- Set entry_priority and entry_changefreq params
        $priority = JArrayHelper::getValue($params,'entry_priority',$parent->priority);
        $changefreq = JArrayHelper::getValue($params,'entry_changefreq',$parent->changefreq);
        if ($priority  == '-1')
            $priority = $parent->priority;
        if ($changefreq  == '-1')
            $changefreq = $parent->changefreq;

        $params['entry_priority'] = $priority;
        $params['entry_changefreq'] = $changefreq;

        //----- Set arc_priority and arc_changefreq params
        $priority = JArrayHelper::getValue($params,'arc_priority',$parent->priority);
        $changefreq = JArrayHelper::getValue($params,'arc_changefreq',$parent->changefreq);
        if ($priority  == '-1')
            $priority = $parent->priority;
        if ($changefreq  == '-1')
            $changefreq = $parent->changefreq;

        $params['arc_priority'] = $priority;
        $params['arc_changefreq'] = $changefreq;

        xmap_com_myblog::getMyBlog($xmap,  $parent, $params);
    }

}
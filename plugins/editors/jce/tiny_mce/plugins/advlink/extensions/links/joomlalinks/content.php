<?php
/**
* @version		$Id: content.php 46 2009-05-26 16:59:42Z happynoodleboy $
* @package      JCE Advlink
* @copyright    Copyright (C) 2008 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
// no direct access
defined('_JCE_EXT') or die('Restricted access');
class AdvlinkContent 
{
	function getOptions()
	{
		$advlink =& AdvLink::getInstance();
		$list = '';
		if ($advlink->checkAccess('content', '1')) {
			$list = '<li id="index.php?option=com_content"><div class="tree-row"><div class="tree-image"></div><span class="folder content nolink"><a href="javascript:;">' . JText::_('CONTENT') . '</a></span></div></li>';
		}
		return $list;	
	}
	function getItems($args)
	{		
		global $mainframe;	
		
		$advlink =& AdvLink::getInstance();
		
		require_once(JPATH_SITE .DS. 'components' .DS. 'com_content' .DS. 'helpers' .DS. 'route.php');
		
		$sections 	= AdvlinkContent::_section();
		$items 		= array();
		$view		= isset($args->view) ? $args->view : '';
		switch ($view) {
		default:
			foreach ($sections as $section) {
				$items[] = array(
					'id'		=>	ContentHelperRoute::getSectionRoute($section->id),
					'name'		=>	$section->title,
					'class'		=>	'folder content'
				);
			}
			// Check Static/Uncategorized permissions
			if ($advlink->checkAccess('advlink_static', '1')) {
				$items[] = array(
					'id'		=>	'option=com_content&amp;view=uncategorized',
					'name'		=>	JText::_('UNCATEGORIZED'),
					'class'		=>	'folder content nolink'
				);
			}
			break;
		case 'section':			
			$categories = AdvLink::getCategory($args->id);
			foreach ($categories as $category) {
				$items[] = array(
					'id'		=>	ContentHelperRoute::getCategoryRoute($category->slug, $args->id),
					'name'		=>	$category->title . ' / ' . $category->alias,
					'class'		=>	'folder content'
				);
			}
			break;
		case 'category':
			$articles = AdvlinkContent::_articles($args->id);
			foreach ($articles as $article) {
				$items[] = array(
					'id' 	=> ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid),
					'name' 	=> $article->title . ' / ' . $article->alias,
					'class'	=> 'file'
				);
			}
			break;
		case 'uncategorized':			
			$statics = AdvlinkContent::_statics();
			foreach ($statics as $static) {
				$items[] = array(
					'id' 	=> ContentHelperRoute::getArticleRoute($static->id), 
					'name' 	=> 	$static->title . ' / ' . $static->alias,
					'class'	=>	'file'
				);
			}
			break;
		}
		return $items;
	}
	function _section()
	{
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		
		$query = 'SELECT id, title, alias'
		. ' FROM #__sections'
		. ' WHERE published = 1'
		. ' AND access <= '.(int) $user->get('aid')
		//. ' GROUP BY id'
		. ' ORDER BY title'
		;

		$db->setQuery($query);
		return $db->loadObjectList();		
	}
	function _articles($id)
	{
		$db			=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$advlink 	=& AdvLink::getInstance();
	
		$query = 'SELECT a.id AS slug, b.id AS catslug, a.alias, a.title AS title, u.id AS sectionid';
		if ($advlink->getPluginParam('advlink_article_alias', '1') == '1') {
			$query .= ', CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug';
		}
		$query .= ' FROM #__content AS a'
		. ' INNER JOIN #__categories AS b ON b.id = '.(int) $id
		. ' INNER JOIN #__sections AS u ON u.id = a.sectionid'
		. ' WHERE a.catid = '.(int) $id
		. ' AND a.state = 1'
		. ' AND a.access <= '.(int) $user->get('aid')
		. ' ORDER BY a.title'
		;
		$db->setQuery($query, 0);
		return $db->loadObjectList();
	}
	function _statics()
	{
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		
		$query = 'SELECT id, title, alias'
		. ' FROM #__content'
		. ' WHERE state = 1'
		. ' AND access <= '.(int) $user->get('aid')
		. ' AND sectionid = 0'
		. ' AND catid = 0'
		. ' ORDER BY title'
		;
		$db->setQuery($query, 0);
		return $db->loadObjectList();
	}
}
?>
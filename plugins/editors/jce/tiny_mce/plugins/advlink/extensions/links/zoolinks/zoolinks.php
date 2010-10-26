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
class AdvlinkZoo
{
	function getOptions()
	{
		$advlink =& AdvLink::getInstance();
		$list = '';
		if ($advlink->checkAccess('zoo', '1')) {
			$list = '<li id="index.php?option=com_zoo"><div class="tree-row"><div class="tree-image"></div><span class="folder content nolink"><a href="javascript:;">' . JText::_('Zoo') . '</a></span></div></li>';
		}
		return $list;	
	}
	function getItems($args)
	{		
		global $mainframe;	
		
		$advlink =& AdvLink::getInstance();
		
		$links 		= array();
		$view		= isset($args->view) ? $args->view : (isset($args->task) ? $args->task : '');

		switch ($view) {
		default:
			$apps 	= AdvlinkZoo::_application();
			foreach ($apps as $app) {
				$links[] = array(
					'id'		=>	'index.php?option=com_zoo&task=application&application_id=' . $app->id,
					'name'		=>	$app->name,
					'class'		=>	'folder application nolink'
				);
			}
			break;
		case 'application':			
			$categories = AdvlinkZoo::_category(0, $args->application_id);
			
			foreach ($categories as $category) {
				$itemid = AdvLink::getItemId('com_zoo', array('categories' => null, 'category' => $category->id));
				$links[] = array(
					'id'		=>	'index.php?option=com_zoo&task=category&category_id=' . $category->id . $itemid,
					'name'		=>	$category->name . ' / ' . $category->alias,
					'class'		=>	'folder category'
				);
			}
			break;
		case 'category':
			$categories = AdvlinkZoo::_category($args->category_id);
			foreach ($categories as $category) {
				$itemid = AdvLink::getItemId('com_zoo', array('categories' => null, 'category' => $category->id));
				
				$links[] = array(
					'id'		=>	'index.php?option=com_zoo&task=category&category_id=' . $category->id . $itemid,
					'name'		=>	$category->name . ' / ' . $category->alias,
					'class'		=>	'folder category'
				);
				
				$items = AdvlinkZoo::_item($category->id);
				
				if (!empty($items)) {
					foreach ($items as $item) {
						$itemid = AdvLink::getItemId('com_zoo', array('categories' => null, 'category' => $category->id));
						$links[] = array(
							'id'		=>	'index.php?option=com_zoo&task=item&item_id=' . $item->id . $itemid,
							'name'		=>	$item->name . ' / ' . $item->alias,
							'class'		=>	'file'
						);
					}	
				}
			}
			break;
		}
		return $links;
	}
	function _application()
	{
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		
		$query = 'SELECT id, name'
		. ' FROM #__zoo_application'
		. ' ORDER BY name'
		;

		$db->setQuery($query);
		return $db->loadObjectList();		
	}
	function _category($parent = 0, $app = 0)
	{
		$db			=& JFactory::getDBO();

		$where 		= $app ? ' AND application_id = '.(int) $app : '';
	
		$query = 'SELECT *'
		. ' FROM #__zoo_category'
		. ' WHERE published = 1'
		. $where
		. ' AND parent = '.(int) $parent
		. ' ORDER BY ordering'
		;

		$db->setQuery($query);
		return $db->loadObjectList();
	}
	function _children($id) {
		$db			=& JFactory::getDBO();
	
		$query = 'SELECT COUNT(id)'
		. ' FROM #__zoo_category'
		. ' WHERE parent = '.(int) $id
		. ' AND published = 1'
		;

		$db->setQuery($query);
		return $db->loadResult();
	}
	function _item($id)
	{
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		
		$query = 'SELECT i.*'
		. ' FROM #__zoo_item AS i'
		. ' INNER JOIN #__zoo_category_item AS c'
		. ' WHERE c.category_id = '.(int) $id
		. ' AND i.id = c.item_id'
		. ' AND i.state = 1'
		;
		$db->setQuery($query, 0);
		return $db->loadObjectList();
	}
}
?>
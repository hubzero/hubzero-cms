<?php
/**
* @version		$Id: contact.php 88 2009-06-17 16:49:39Z happynoodleboy $
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
class AdvlinkContact 
{
	function getOptions()
	{
		//Reference to JConentEditor (JCE) instance
		$advlink =& AdvLink::getInstance();
		$list = '';
		if ($advlink->checkAccess('contacts', '1')) {	
			$list .= '<li id="index.php?option=com_contact"><div class="tree-row"><div class="tree-image"></div><span class="folder contact nolink"><a href="javascript:;">' . JText::_('CONTACTS') . '</a></span></div></li>';
		}
		return $list;	
	}
	function getItems($args)
	{
		$items 	= array();
		$view	= isset($args->view) ? $args->view : '';
		switch ($view) {
		default:
			$categories = AdvLink::getCategory('com_contact_details');
			foreach ($categories as $category) {
				$itemid 	= AdvLink::getItemId('com_contact', array('categories' => null, 'category' => $category->slug));
				$items[] 	= array(
					'id' 	=> 'index.php?option=com_contact&view=category&catid='. $category->slug . $itemid,
					'name' 	=> $category->title . ' / ' . $category->alias,
					'class'	=> 'folder contact'
				);
			}
			break;
		case 'category':
			$contacts = AdvlinkContact::_contacts($args->catid);
			foreach ($contacts as $contact) {
				$catid 		= $args->catid ? '&catid='. $args->catid : '';
				$itemid 	= AdvLink::getItemId('com_contact', array('categories' => null, 'category' => $catid));
				$items[] 	= array(
					'id' 	=> 'index.php?option=com_contact&view=contact'. $catid .'&id='.$contact->id . $itemid. '-' .$contact->alias,
					'name' 	=> $contact->name . ' / ' . $contact->alias,
					'class'	=> 'file'
				);
			}
			break;
		}
		return $items;
	}
	function _contacts($id)
	{
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();	
		
		$query	= 'SELECT id, name, alias'
		. ' FROM #__contact_details'
		. ' WHERE catid = '.(int) $id
		. ' AND published = 1'
		. ' AND access <= '.(int) $user->get('aid')
		//. ' GROUP BY id'
		. ' ORDER BY name'
		;
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
?>

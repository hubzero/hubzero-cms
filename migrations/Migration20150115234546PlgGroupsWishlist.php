<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for remove rogue wishlists
 **/
class Migration20150115234546PlgGroupsWishlist extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// include com_wishlist files
		require_once PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'models' . DS . 'wishlist.php';

		// Load some objects
		$wishlist = new Wishlist($this->db);
		$wish     = new Wish($this->db);

		// Get records
		$lists = $wishlist->getRecords(array(
			'category' => 'group'
		));

		// vars to hold counts
		$deletedLists  = 0;
		$deletedWishes = 0;

		// check to make sure each group wishlist has a valid group
		foreach ($lists as $list)
		{
			// load group
			$group = \Hubzero\User\Group::getInstance($list->referenceid);

			// if group doesnt exist we need to remove the list and wishes
			if (!$group || !is_object($group))
			{
				// Get wishes
				$wishes = $wish->get_wishes($list->id, array(
					'filterby' => 'all',
					'sortby'   => ''
				), 1);

				// delete each wish
				foreach ($wishes as $item)
				{
					$wish->load($item->id);
					$wish->delete();
					$deletedWishes++;
				}

				// delete wishlist
				$wishlist->load($list->id);
				$wishlist->delete();
				$deletedLists++;
			}
		}
	}
}
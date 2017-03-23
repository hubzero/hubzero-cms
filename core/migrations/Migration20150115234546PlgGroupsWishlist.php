<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

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
		if ($this->db->tableExists('#__wishlist'))
		{
			// Get records
			$this->db->setQuery("SELECT * FROM `#__wishlist` WHERE `category`='group'");
			$lists = $this->db->loadObjectList();

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
					if ($this->db->tableExists('#__wishlist_item'))
					{
						$this->db->setQuery("SELECT * FROM `#__wishlist_item` WHERE `wishlist`=" . $list->id);
						$wishes = $this->db->loadObjectList();

						foreach ($wishes as $wish)
						{
							if ($this->db->tableExists('#__wishlist_implementation'))
							{
								$this->db->setQuery("DELETE FROM `#__wishlist_implementation` WHERE `wishid`=" . $wish->id);
								$this->db->query();
							}

							if ($this->db->tableExists('#__wish_attachments'))
							{
								$this->db->setQuery("DELETE FROM `#__wish_attachments` WHERE `wish`=" . $wish->id);
								$this->db->query();
							}

							if ($this->db->tableExists('#__wishlist_vote'))
							{
								$this->db->setQuery("DELETE FROM `#__wishlist_vote` WHERE `wishid`=" . $wish->id);
								$this->db->query();
							}
						}

						$this->db->setQuery("DELETE FROM `#__wishlist_item` WHERE `wishlist`=" . $list->id);
						$this->db->query();
					}

					if ($this->db->tableExists('#__wishlist_owners'))
					{
						$this->db->setQuery("DELETE FROM `#__wishlist_owners` WHERE `wishlist`=" . $list->id);
						$this->db->query();
					}

					if ($this->db->tableExists('#__wishlist_ownergroups'))
					{
						$this->db->setQuery("DELETE FROM `#__wishlist_ownergroups` WHERE `wishlist`=" . $list->id);
						$this->db->query();
					}

					$this->db->setQuery("DELETE FROM `#__wishlist` WHERE `id`=" . $list->id);
					$this->db->query();

					$deletedLists++;
				}
			}
		}
	}
}

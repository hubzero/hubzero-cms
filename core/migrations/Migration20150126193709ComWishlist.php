<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing old wishlist names
 **/
class Migration20150126193709ComWishlist extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->db->setQuery("UPDATE `#__wishlist` SET `title` = REPLACE(`title`, 'WISHLIST_NAME_GROUP', 'Group');");
		$this->db->query();
	}
}
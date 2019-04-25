<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

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
		if ($this->db->tableExists('#__wishlist'))
		{
			$this->db->setQuery("UPDATE `#__wishlist` SET `title` = REPLACE(`title`, 'WISHLIST_NAME_GROUP', 'Group');");
			$this->db->query();
		}
	}
}

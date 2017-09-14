<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_wishlist
 **/
class Migration20170831000000ComWishlist extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('wishlist');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('wishlist');
	}
}

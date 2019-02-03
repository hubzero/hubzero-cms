<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing wishlist module
 **/
class Migration20190109000000ModWishlist extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_wishlist', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_wishlist');
	}
}

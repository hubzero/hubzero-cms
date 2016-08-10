<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for enabling the plugin by default.
 **/
class Migration20160810121746PlgContentFormathtml extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = new \Hubzero\Database\Query;
		$plugin = $query->update('#__extensions')
                    ->set(['enabled' => 1])
                    ->whereEquals('name', 'plg_content_formathtml')
                    ->execute();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = new \Hubzero\Database\Query;
		$plugin = $query->update('#__extensions')
                    ->set(['enabled' => 0])
                    ->whereEquals('name', 'plg_content_formathtml')
                    ->execute();
	}
}

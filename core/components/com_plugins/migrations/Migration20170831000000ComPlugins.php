<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_plugins
 **/
class Migration20170831000000ComPlugins extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Create component entry but do NOT create a menu item as
		// com_plugins is purposely separate from the 'components' list.
		//
		// string  $name            Component name
		// string  $option          com_xyz
		// int     $enabled         Whether or not the component should be enabled
		// string  $params          Component params (if already known)
		// bool    $createMenuItem  Create an admin menu item for this component
		$this->addComponentEntry('plugins', null, 1, '', false);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('plugins');
	}
}

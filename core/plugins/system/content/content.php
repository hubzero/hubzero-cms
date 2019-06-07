<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin for content events
 */
class plgSystemContent extends \Hubzero\Plugin\Plugin
{
	/**
	 * Hook for after parsing route
	 *
	 * @param   object  $table
	 * @param   object  $model
	 * @return  void
	 */
	public function onContentSave($table, $model)
	{
		//@TODO: Add check for isIndexable
		Event::trigger('search.onAddIndex', array($table, $model));
	}

	/**
	 * Hook for after parsing route
	 *
	 * @param   object  $table
	 * @param   object  $model
	 * @return  void
	 */
	public function onContentDestroy($table, $model)
	{
		//@TODO: Add check for isIndexable
		Event::trigger('search.onRemoveIndex', array($table, $model));
	}
}

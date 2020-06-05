<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Resources\Models\Entry;

require_once Component::path('com_resources') . DS . 'models' . DS . 'entry.php';

/**
 * Plugin class for Newsletter resources
 */
class plgNewsletterResource extends \Hubzero\Plugin\Plugin
{
	/**
	 * Event call to get the name
	 *
	 * @return  string
	 */
	public function onGetEnabledDigests()
	{
		$name = 'resource';
		return $name;
	}

	/**
	 * Event call to get the latest records
	 *
	 * @param   integer  $num
	 * @param   string   $dateField
	 * @param   string   $sort
	 * @return  array
	 */
	public function onGetLatest($num = 5, $dateField = 'created', $sort = 'DESC')
	{
		$model = Entry::getLatest($num, $dateField, $sort)->rows()->toObject();

		$objects = array();

		foreach ($model as $m)
		{
			$object = new stdClass;
			$object->title = $m->title;
			$object->body  = htmlspecialchars_decode($m->introtext);
			$object->date  = Date::of($m->publish_up)->toLocal("F j, Y");
			$object->path  = 'resources/' . $m->id;
			$object->id    = $m->id;

			array_push($objects, $object);
		}
		return $objects;
	}
}

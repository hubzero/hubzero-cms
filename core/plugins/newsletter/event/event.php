<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Events\Models\Orm\Event as CalEvent;

require_once \Component::path('com_events') . DS . 'models' . DS . 'orm' . DS . 'event.php';

/**
 * Plugin class for Newsletter event
 */
class plgNewsletterEvent extends \Hubzero\Plugin\Plugin
{
	/**
	 * Event call to get the name
	 *
	 * @return  string
	 */
	public function onGetEnabledDigests()
	{
		$name = 'event';
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
		$model = CalEvent::getLatest($num, $dateField, $sort)->rows()->toObject();

		$objects = array();

		foreach ($model as $m)
		{
			$object = new stdClass;
			$object->title = $m->title;
			$object->body  = htmlspecialchars_decode($m->content);
			$object->date  = Date::of($m->publish_up)->toLocal("F j, Y");
			$object->path  = 'events/details/' . $m->id;
			$object->id    = $m->id;

			array_push($objects, $object);
		}
		return $objects;
	}
}

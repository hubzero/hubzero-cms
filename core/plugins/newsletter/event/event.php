<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Newsletter\Event;

use Hubzero\Plugin\Plugin;
use Components\Events\Models\Orm\Event as CalEvent;
use Hubzero\Facades\Date;

/**
 * Plugin class for Newsletter event
 */
class Event extends Plugin
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

        foreach ($model as $m) {
            $object = new \stdClass();
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

<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Newsletter\Resource;

use Hubzero\Plugin\Plugin;
use Components\Resources\Models\Entry;

/**
 * Plugin class for Newsletter resources
 */
class Resource extends Plugin
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

        foreach ($model as $m) {
            $object = new stdClass();
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

<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Newsletter\Resource;

use Hubzero\Plugin\Plugin;
use Components\Resources\Models\Entry;
use Hubzero\Facades\Date;

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
        return 'resource';
    }

    /**
     * Event call to get the latest records
     *
     * @param   integer  $num
     * @param   string   $dateField
     * @param   string   $sort
     * @return  array<mixed>
     */
    public function onGetLatest($num = 5, $dateField = 'created', $sort = 'DESC')
    {
        $model = Entry::getLatest($num, $dateField, $sort)->rows()->toObject();

        $objects = [];

        foreach ($model as $m) {
            $object = new \stdClass();
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

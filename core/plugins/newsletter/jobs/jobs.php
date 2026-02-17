<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Newsletter\Jobs;

use Hubzero\Plugin\Plugin;

// phpcs:disable PSR1.Files.SideEffects

use Components\Feedaggregator\Models\Orm\Post;

require_once \Component::path('com_feedaggregator') . DS . 'models' . DS . 'orm' . DS . 'post.php';

/**
 * Plugin class for Newsletter jobs
 */
class Jobs extends Plugin
{
    /**
     * Event call to get the name
     *
     * @return  string
     */
    public function onGetEnabledDigests()
    {
        $name = 'feedaggregator';
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
        $model = Post::getLatest($num, $dateField, $sort)->rows()->toObject();

        $objects = array();

        foreach ($model as $m) {
            $object = new stdClass();
            $object->title = $m->title;
            $object->body  = preg_replace('/[^ .,;a-zA-Z0-9_-]|[,;]/', '', $m->description);
            $object->date  = Date::of($m->created)->toLocal("F j, Y");
            $object->path  = $m->url;
            $object->id    = $m->id;


            array_push($objects, $object);
        }
        return $objects;
    }
}

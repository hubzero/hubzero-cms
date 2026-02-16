<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to html entity decode each event title.
  *
**/
class Migration20140826120656ComEvents extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        // select each event
        $events = $this->db->getQuery(true)
            ->select(['id', 'title'])
            ->from('#__events')
            ->loadObjectList();

        // update each event
        foreach ($events as $event) {
            $fixedTitle = html_entity_decode($event->title);
            $this->db->getQuery(true)
                ->update('#__events')
                ->set(['title' => $this->db->quote($fixedTitle)])
                ->where('id', '=', $event->id)
                ->execute();
        }
    }
}

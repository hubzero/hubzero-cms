<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        // select each event
        $events = $this->db
            ->getQuery()
            ->select(['id', 'title'])
            ->from('#__events')
            ->loadObjectList() ?? [];

        // update each event
        foreach ($events as $event) {
            $fixedTitle = html_entity_decode($event->title);
            $this->db
                ->getQuery()
                ->update('#__events')
                ->set(['title' => $this->db->quote($fixedTitle)])
                ->where('id', '=', $event->id)
                ->execute();
        }
    }
}

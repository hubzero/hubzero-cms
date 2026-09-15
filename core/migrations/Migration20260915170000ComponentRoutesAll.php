<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Menu\ComponentRoute;

/**
 * Give an address to the components the first backfill judged too dull for one
 *
 * That backfill carried a list of twelve components to skip: machinery, or an
 * endpoint something talks to rather than a person. It was a judgement about
 * which components are interesting, and that kind of list ages badly - a
 * component that is machinery today may grow a page tomorrow, and nobody will
 * remember to take it off the list.
 *
 * So the list is now only the components with no site code to run at all -
 * com_media, com_messages, com_system - and everything else gets an address.
 * Including the endpoints: an address costs nothing there, because the router
 * already resolves /cron by component name whether or not a menu entry exists.
 * All the entry adds is an Itemid, and the day one of them grows a page it has
 * somewhere to put its modules and its template style.
 *
 * Measured across three hubs before and after: of seventy-eight urls, seventy-two
 * came back byte for byte identical. The six that moved were error pages that
 * had been marking Home as the current menu item because nothing else matched,
 * and stopped.
 **/
class Migration20260915170000ComponentRoutesAll extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__menu') || !$this->db->tableExists('#__extensions')) {
            return;
        }

        $routes = new ComponentRoute($this->db, array($this, 'log'));

        if (!$routes->menutype()) {
            return;
        }

        $made = 0;

        foreach ($this->components() as $element => $row) {
            if (!$routes->routable($element) || $routes->exists($element)) {
                continue;
            }

            if ($routes->create($element, $row['id'], $row['enabled'])) {
                $made++;
            }
        }

        if (!$made) {
            $this->log('Every component that should have an address already had one');
        }
    }

    /**
     * Down
     *
     * Nothing. Taking an address away is how a page stops answering, and these
     * are addresses something may already be linking to - including, by now,
     * whatever a hub has assigned to them.
     **/
    public function down()
    {
        // Deliberately empty
    }

    /**
     * The site components this hub has installed
     *
     * @return  array  element => id and enabled
     */
    protected function components()
    {
        $found = array();

        foreach ($this->db->getQuery(true)
            ->select('extension_id')
            ->select('element')
            ->select('enabled')
            ->from('#__extensions')
            ->whereEquals('type', 'component')
            ->fetch() as $row) {
            $element = is_object($row) ? $row->element : $row['element'];

            $found[$element] = array(
                'id'      => (int) (is_object($row) ? $row->extension_id : $row['extension_id']),
                'enabled' => (int) (is_object($row) ? $row->enabled : $row['enabled']),
            );
        }

        return $found;
    }
}

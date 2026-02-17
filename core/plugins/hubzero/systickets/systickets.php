<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * HUBzero plugin class for system overview
 */
namespace Plugins\Hubzero\Systickets;

use Hubzero\Plugin\Plugin;

class Systickets extends Plugin
{
    /**
     * Return information about this hub
     *
     * @param   string  $values
     * @return  array
     */
    public function onSystemOverview($values = 'all')
    {
        if ($values != 'all') {
            return;
        }

        $response = new \stdClass();
        $response->name  = 'tickets';
        $response->label = 'Support Tickets';
        $response->data  = array();

        $database = App::get('db');

        $database->setQuery("SELECT COUNT(*) FROM `#__support_tickets` AS f WHERE f.`type` = '0'");
        $response->data['total'] = $this->obj('Total', intval($database->loadResult()));

        $database->setQuery(
            "SELECT count(DISTINCT f.id) FROM `#__support_tickets` AS f " .
            "WHERE f.`open` = '1' AND f.`type` = '0'"
        );
        $response->data['open'] = $this->obj('Open', intval($database->loadResult()));

        $database->setQuery(
            "SELECT count(DISTINCT f.id) FROM `#__support_tickets` AS f " .
            "WHERE f.`open` = '1' AND f.`type` = '0' AND f.`status` = '0'"
        );
        $response->data['open_new'] = $this->obj('(open) New', intval($database->loadResult()));

        $database->setQuery(
            "SELECT count(DISTINCT f.id) FROM `#__support_tickets` AS f " .
            "WHERE f.`open` = '1' AND f.`type` = '0' AND " .
            "(f.`owner` = '' OR f.`owner` IS NULL)"
        );
        $response->data['open_unassigned'] = $this->obj('(open) Unassigned', intval($database->loadResult()));

        $database->setQuery(
            "SELECT count(DISTINCT f.id) FROM `#__support_tickets` AS f " .
            "WHERE f.`open` = '1' AND f.`type` = '0' AND f.`status` = '1'"
        );
        $response->data['open_waiting'] = $this->obj('(open) Waiting', intval($database->loadResult()));

        $database->setQuery(
            "SELECT f.`created` FROM `#__support_tickets` AS f " .
            "WHERE f.`open` = '1' AND f.`type` = '0' ORDER BY f.`created` ASC LIMIT 1"
        );
        $response->data['open_oldest'] = $this->obj('(open) Oldest', $database->loadResult());

        $database->setQuery(
            "SELECT f.`created` FROM `#__support_tickets` AS f " .
            "WHERE f.`open` = '1' AND f.`type` = '0' ORDER BY f.`created` DESC LIMIT 1"
        );
        $response->data['open_newest'] = $this->obj('(open) Newest', $database->loadResult());

        $database->setQuery(
            "SELECT count(DISTINCT f.id) FROM `#__support_tickets` AS f " .
            "WHERE f.`open` = '0' AND f.`type` = '0'"
        );
        $response->data['closed'] = $this->obj('Closed', intval($database->loadResult()));

        return $response;
    }

    /**
     * Assign label and data to an object
     *
     * @param   string $label
     * @param   mixed  $value
     * @return  object
     */
    private function obj($label, $value)
    {
        $obj = new \stdClass();
        $obj->label = $label;
        $obj->value = $value;

        return $obj;
    }
}

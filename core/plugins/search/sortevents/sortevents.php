<?php

namespace Plugins\Search\Sortevents;

use Hubzero\Plugin\Plugin;

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Short description for 'plgSearchSortEvents'
 *
 * Long description (if any) ...
 */
/**
 */
class Sortevents extends Plugin
{
    /**
     * Short description for 'onYSearchSort'
     *
     * Long description (if any) ...
     *
     * @param      object $a Parameter description (if any) ...
     * @param      object $b Parameter description (if any) ...
     * @return     mixed Return description (if any) ...
     */
    public static function onSearchSort($a, $b)
    {
        if (!isset($_GET['dbg'])) {
            return 0;
        }

        if ($a->get_plugin() !== 'events' || $b->get_plugin() !== 'events' || $a->get_date() === $b->get_date()) {
            return 0;
        }

        return $a->get_date() > $b->get_date() ? 1 : -1;
    }
}

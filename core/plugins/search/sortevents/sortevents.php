<?php

namespace Plugins\Search\Sortevents;

use Hubzero\Plugin\Plugin;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

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

        if ($a->getPlugin() !== 'events' || $b->getPlugin() !== 'events' || $a->getDate() === $b->getDate()) {
            return 0;
        }

        return $a->getDate() > $b->getDate() ? 1 : -1;
    }
}

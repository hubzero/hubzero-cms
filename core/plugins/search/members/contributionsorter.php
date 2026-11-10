<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Search\Members;

/**
 * Contributions sorter
 *
 */
class ContributionSorter
{
    /**
     * Sorting helper
     *
     * @param   object   $a
     * @param   object   $b
     * @return  integer
     */
    public static function sort($a, $b)
    {
        $sec_diff = strcmp($a->getSection(), $b->getSection());
        if ($sec_diff < 0) {
            return -1;
        }
        if ($sec_diff > 0) {
            return 1;
        }
        $a_ord = $a->get('ordering');
        $b_ord = $b->get('ordering');
        return $a_ord == $b_ord ? 0 : ($a_ord < $b_ord ? -1 : 1);
    }

    /**
     * Sort by weight
     *
     * @param   object   $a
     * @param   object   $b
     * @return  integer
     */
    public static function sortWeight($a, $b)
    {
        $aw = $a->getWeight();
        $bw = $b->getWeight();
        if ($aw == $bw) {
            return 0;
        }
        return $aw > $bw ? -1 : 1;
    }

    /**
     * Sort by title
     *
     * @param   object  $a
     * @param   object  $b
     * @return  object
     */
    public static function sortTitle($a, $b)
    {
        return strcmp($a->getTitle(), $b->getTitle());
    }
}

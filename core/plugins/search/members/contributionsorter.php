<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */


namespace Plugins\Search\Members;
// No direct access
defined('_HZEXEC_') or die();

/**
 * Contributions sorter
 *
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
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
        $sec_diff = strcmp($a->get_section(), $b->get_section());
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
    public static function sort_weight($a, $b)
    {
        $aw = $a->get_weight();
        $bw = $b->get_weight();
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
    public static function sort_title($a, $b)
    {
        return strcmp($a->get_title(), $b->get_title());
    }
}

// BC alias for non-namespaced code
class_alias('Plugins\Search\Members\ContributionSorter', 'ContributionSorter');

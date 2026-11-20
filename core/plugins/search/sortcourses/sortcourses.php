<?php

namespace Plugins\Search\Sortcourses;

use Hubzero\Plugin\Plugin;

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Short description for 'plgSearchSortCourses'
 *
 * Long description (if any) ...
 */
/**
 */
class Sortcourses extends Plugin
{
    /**
     * Short description for 'onYSearchSort'
     *
     * Long description (if any) ...
     *
     * @param      \Components\Search\Models\Basic\Result $a Parameter description (if any) ...
     * @param      \Components\Search\Models\Basic\Result $b Parameter description (if any) ...
     * @return     mixed Return description (if any) ...
     */
    public static function onSearchSort($a, $b)
    {
        if (
            $a->getPlugin() !== 'resources' || $b->getPlugin() !== 'resources'
            || $a->getSection() !== 'Courses' || $b->getSection() !== 'Courses'
        ) {
            return 0;
        }

        // Compare the leading parts of the resources to guess whether they
        // refer to the same course
        $title_a = (string) preg_replace('/[^a-z]/', '', strtolower($a->getTitle()));
        $title_b = (string) preg_replace('/[^a-z]/', '', strtolower($a->getTitle()));
        $match_threshold = 10;
        $match = true;
        for ($idx = 0; $idx < min($match_threshold, min(strlen($title_a), strlen($title_b))); ++$idx) {
            if ($title_a[$idx] !== $title_b[$idx]) {
                $match = false;
                break;
            }
        }
        if (!$match) {
            return 0;
        }

        return $a->getDate() > $b->getDate() ? 1 : -1;
    }
}

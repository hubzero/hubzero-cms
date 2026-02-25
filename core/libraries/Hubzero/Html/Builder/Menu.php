<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Builder;

/**
 * Utility class for building menu tree structures
 */
class Menu
{
    /**
     * Build a flattened tree list from a parent-child hierarchy
     *
     * @param   integer  $id         Parent item ID to start from
     * @param   string   $indent     Current indentation string
     * @param   array    $list       Accumulator for flattened results
     * @param   array    &$children  Map of parent_id => child items
     * @param   integer  $maxlevel   Maximum recursion depth
     * @param   integer  $level      Current recursion level
     * @param   integer  $type       Display type: 1 = HTML tree prefix, 0 = text dashes
     * @return  array
     */
    public static function treerecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1)
    {
        if (@$children[$id] && $level <= $maxlevel) {
            foreach ($children[$id] as $v) {
                $id = $v->id;

                if ($type) {
                    $pre = '<sup>|_</sup>&#160;';
                    $spacer = '.&#160;&#160;&#160;&#160;&#160;&#160;';
                } else {
                    $pre = '- ';
                    $spacer = '&#160;&#160;';
                }

                if ($v->parent_id == 0) {
                    $txt = $v->title;
                } else {
                    $txt = $pre . $v->title;
                }

                $list[$id] = $v;
                $list[$id]->treename = "$indent$txt";
                $list[$id]->children = count(@$children[$id]);

                $list = self::treerecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type);
            }
        }
        return $list;
    }
}

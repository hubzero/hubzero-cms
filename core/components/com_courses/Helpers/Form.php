<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Helpers;

/**
 * Form Helper class
 */
class Form
{
    /**
     * Time remaining (in human readable language)
     *
     * @param   float|int  $secs
     * @return  string
     */
    public static function timeDiff($secs)
    {
        $seconds = [1, 'second'];
        $minutes = [60 * $seconds[0], 'minute'];
        $hours   = [60 * $minutes[0], 'hour'];
        $days    = [24 * $hours[0],   'day'];
        $weeks   = [7  * $days[0],    'week'];
        $rv      = [];

        foreach ([$weeks, $days, $hours, $minutes, $seconds] as $step) {
            list($sec, $unit) = $step;
            $times = floor($secs / $sec);

            if ($times > 0) {
                $secs -= $sec * $times;
                $rv[] = $times . ' ' . $unit . ($times == 1 ? '' : 's');

                if (count($rv) == 2) {
                    break;
                }
            } elseif (count($rv)) {
                break;
            }
        }

        return implode(', ', $rv);
    }

    /**
     * Convert integer to ordinal number
     *
     * @param   mixed  $int
     * @return  string
     */
    public static function toOrdinal($int)
    {
        $ends = ['th','st','nd','rd','th','th','th','th','th','th'];

        if (($int % 100) >= 11 && ($int % 100) <= 13) {
            $abbreviation = $int . 'th';
        } else {
            $abbreviation = $int . $ends[$int % 10];
        }

        return $abbreviation;
    }
}

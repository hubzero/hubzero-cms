<?php

namespace Bootstrap\Cli\Language;

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * en-GB localise class
 */
abstract class EnGBLocalise
{
    /**
     * Returns the potential suffixes for a specific number of items
     *
     * @param   integer  $count  The number of items.
     * @return  array<mixed>    An array of potential suffixes.
     */
    public static function getPluralSuffixes($count)
    {
        if ($count == 0) {
            $return =  ['0'];
        } elseif ($count == 1) {
            $return =  ['1'];
        } else {
            $return = ['MORE'];
        }
        return $return;
    }

    /**
     * Returns the ignored search words
     *
     * @return  array<mixed>  An array of ignored search words.
     */
    public static function getIgnoredSearchWords()
    {
        return [
            'and',
            'in',
            'on',
        ];
    }

    /**
     * Returns the lower length limit of search words
     *
     * @return  integer  The lower length limit of search words.
     */
    public static function getLowerLimitSearchWord()
    {
        return 3;
    }

    /**
     * Returns the upper length limit of search words
     *
     * @return  integer  The upper length limit of search words.
     */
    public static function getUpperLimitSearchWord()
    {
        return 20;
    }

    /**
     * Returns the number of chars to display when searching
     *
     * @return  integer  The number of chars to display when searching.
     */
    public static function getSearchDisplayedCharactersNumber()
    {
        return 200;
    }
}

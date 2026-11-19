<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

/**
 * Solr helper class
 */
class UrlQueryHelper
{
    /**
     * @param   mixed  $url
     * @param   mixed  $values
     * @param   mixed  $excludes
     * @return  string
     */
    public static function buildQueryString($url, $values, $excludes = [])
    {
        $queryString = '';
        if (is_string($excludes)) {
            $excludes = [$excludes];
        }
        foreach ($values as $type => $fields) {
            if (in_array($type, $excludes)) {
                continue;
            }
            foreach ($fields as $field => $value) {
                $queryString .= '&childTerms[' . $type . '][' . $field . ']=' . $value;
            }
        }
        return $url . $queryString;
    }
}

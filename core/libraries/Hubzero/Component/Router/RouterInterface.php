<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Router;

/**
 * Component router interface
 */
interface RouterInterface
{
    /**
     * Prepare-method for URLs
     * This method is meant to validate and complete the URL parameters.
     * For example it can add the Itemid or set a language parameter.
     * This method is executed on each URL, regardless of SEF mode switched
     * on or not.
     *
     * @param   array<string, mixed>  $query  An associative array of URL arguments
     * @return  array<string, mixed>  The URL arguments to use to assemble the subsequent URL.
     */
    public function preprocess($query);

    /**
     * Build method for URLs
     * This method is meant to transform the query parameters into a more human
     * readable form. It is only executed when SEF mode is switched on.
     *
     * @param   array<string, mixed>  &$query  An array of URL arguments
     * @return  array<int, mixed>  The URL arguments to use to assemble the subsequent URL.
     */
    public function build(&$query);

    /**
     * Parse method for URLs
     * This method is meant to transform the human readable URL back into
     * query parameters. It is only executed when SEF mode is switched on.
     *
     * @param   array<int, string>  &$segments  The segments of the URL to parse.
     * @return  array<string, mixed>  The URL attributes to be used by the application.
     */
    public function parse(&$segments);
}

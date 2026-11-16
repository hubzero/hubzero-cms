<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Router;

/**
 * Default routing class for missing or legacy component routers
 */
class Legacy implements RouterInterface
{
    /**
     * Name of the component
     *
     * @var  string
     */
    protected $component;

    /**
     * Constructor
     *
     * @param   string  $component  Component name without the com_ prefix this router should react upon
     * @return  void
     */
    public function __construct($component)
    {
        $this->component = $component;
    }

    /**
     * Generic preprocess function for missing or legacy component router
     *
     * @param   array<mixed>  $query  An associative array of URL arguments
     * @return  array<mixed>  The URL arguments to use to assemble the subsequent URL.
     */
    public function preprocess($query)
    {
        return $query;
    }

    /**
     * Generic build function for missing or legacy component router
     *
     * @param   array<mixed>  &$query  An array of URL arguments
     * @return  array<mixed>  The URL arguments to use to assemble the subsequent URL.
     */
    public function build(&$query)
    {
        $function = $this->component . 'BuildRoute';

        if (function_exists($function)) {
            return $function($query);
        }

        return [];
    }

    /**
     * Generic parse function for missing or legacy component router
     *
     * @param   array<mixed>  &$segments  The segments of the URL to parse.
     * @return  array<mixed>  The URL attributes to be used by the application.
     */
    public function parse(&$segments)
    {
        $function = $this->component . 'ParseRoute';

        if (function_exists($function)) {
            return $function($segments);
        }

        return [];
    }
}

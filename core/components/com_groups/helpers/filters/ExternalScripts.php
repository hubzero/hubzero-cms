<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 *
 */

namespace Components\Groups\Helpers\Filters;

class ExternalScripts extends \HTMLPurifier_Filter
{
    /**
     * Name
     *
     * @var  string
     */
    public $name = 'ExternalScripts';

    /**
     * Pre-filter hook
     *
     * @param   string  $html
     * @param   \HTMLPurifier_Config   $config
     * @param   \HTMLPurifier_Context  $context
     * @return  string
     */
    public function preFilter($html, $config, $context)
    {
        $pre_regex   = '#<script(.)*src="([^"]*)"([^>]*)></script>#';
        $pre_replace = '[externalscript src="$2"]';
        return (string) preg_replace($pre_regex, $pre_replace, $html);
    }

    /**
     * Post-filter hook
     *
     * @param   string  $html
     * @param   \HTMLPurifier_Config   $config
     * @param   \HTMLPurifier_Context  $context
     * @return  string
     */
    public function postFilter($html, $config, $context)
    {
        $pre_regex   = '#\[externalscript src="([^"]*)"\]#';
        $pre_replace = '<script src="$1"></script>';
        return (string) preg_replace($pre_regex, $pre_replace, $html);
    }
}

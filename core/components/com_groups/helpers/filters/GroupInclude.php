<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 *
 */

namespace Components\Groups\Helpers\Filters;

class GroupInclude extends \HTMLPurifier_Filter
{
    /**
     * Name
     *
     * @var  string
     */
    public $name = 'GroupInclude';

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
        $pre_regex   = '#<group:include([^>]*)/>#';
        $pre_replace = '[group:include$1]';
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
        $pre_regex   = '#\[group:include([^\]]*)\]#';
        $pre_replace = '<group:include$1/>';
        return (string) preg_replace($pre_regex, $pre_replace, $html);
    }
}

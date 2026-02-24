<?php

/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

/**
 * Migration macro to save plugin params
 **/
class SavePluginParams extends SaveParams
{
    /**
     * Save plugin params
     *
     * @param   string  $folder   Plugin folder
     * @param   string  $element  Plugin element
     * @param   array   $params   Plugin params (if already known)
     * @return  bool
     **/
    public function __invoke($element, $params = null)
    {
        // When called with 3 args: ($folder, $element, $params)
        // When called with 2 args: ($element, $params) — delegates directly
        if ($params === null) {
            return parent::__invoke($element, []);
        }

        $args = func_get_args();
        if (count($args) === 3) {
            return parent::__invoke('plg_' . $args[0] . '_' . $args[1], $args[2]);
        }

        return parent::__invoke($element, $params);
    }
}

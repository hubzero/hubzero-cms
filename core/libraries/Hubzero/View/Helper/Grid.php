<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

/**
 * Helper for calling HTML Grid methods.
 */
class Grid extends AbstractHelper
{
    /**
     * Display the editor area.
     *
     * @param   string  $method  The control name.
     * @return  string
     */
    public function __invoke($method)
    {
        $args = func_get_args();

        if (!count($args)) {
            throw new \InvalidArgumentException(__METHOD__ . '(); No arguments passed.');
        }

        $method = array_shift($args);

        $callback = [\Hubzero\Html\Builder\Grid::class, $method];
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(__METHOD__ . '(); Unknown grid helper "' . $method . '".');
        }

        return call_user_func_array($callback, $args);
    }
}

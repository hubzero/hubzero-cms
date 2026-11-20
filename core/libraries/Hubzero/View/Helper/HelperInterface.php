<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use Hubzero\View\View;

/**
 * Interface for view helpers
 */
interface HelperInterface
{
    /**
     * Set the View object
     *
     * @param   \Hubzero\View\View $view
     * @return  object
     */
    public function setView(View $view);

    /**
     * Get the View object
     *
     * @return  object
     */
    public function getView();
}

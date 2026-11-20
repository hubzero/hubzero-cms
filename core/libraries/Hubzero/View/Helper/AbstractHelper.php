<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use Hubzero\View\View;

/**
 * Abstract view helper class
 */
abstract class AbstractHelper implements HelperInterface
{
    /**
     * View object instance
     *
     * @var  \Hubzero\View\View
     */
    protected $view;

    /**
     * Set the View object
     *
     * @param   \Hubzero\View\View $view
     * @return  $this
     */
    public function setView(View $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get the view object
     *
     * @return  \Hubzero\View\View|null
     */
    public function getView()
    {
        return $this->view;
    }
}

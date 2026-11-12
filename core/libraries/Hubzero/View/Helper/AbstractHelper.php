<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
     * @var  object
     */
    protected $view = null;

    /**
     * Set the View object
     *
     * @param   \Hubzero\View\View $view
     * @return  object
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

<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Site\Controllers;

use Hubzero\Component\SiteController;

/**
 * Base component controller class
 */
class ComponentController extends SiteController
{
    /**
     * View engines this controller accepts.
     *
     * @var  array
     */
    protected $viewEngines = ['blade', 'php'];

    /**
     * CSS frameworks this controller accepts.
     *
     * @var  array
     */
    protected $cssFrameworks = ['daisyui', 'classic'];
}

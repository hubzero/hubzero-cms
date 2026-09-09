<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Site\Controllers;

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

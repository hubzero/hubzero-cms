<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{course: mixed, model: mixed} $this
 */

$route = $this->course->offering()->link() . '&task=form.complete&crumb=' . $this->model->get('url');

App::redirect(Route::url($route, false, false));

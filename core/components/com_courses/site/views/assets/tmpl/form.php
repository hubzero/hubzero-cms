<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$route = $this->course->offering()->link() . '&task=form.complete&crumb=' . $this->model->get('url');

App::redirect(Route::url($route, false, false));

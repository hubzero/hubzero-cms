<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Request;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{model: mixed} $this
 */

// @TODO: check for set and valid links
$toolPath = $this->model->get('url');
$filePath = Request::getString('file', '');
$toolPath = !empty($filePath) ? $toolPath . '?params=file:' . $filePath : $toolPath;
App::redirect($toolPath);

<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// @TODO: check for set and valid links
$xappPath = $this->model->get('url');
$filePath = Request::getString('file', '');
$xappPath = !empty($filePath) ? $xappPath . '?params=file:' . $filePath : $xappPath;
App::redirect($xappPath);

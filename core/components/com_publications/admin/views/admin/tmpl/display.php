<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$link = Route::url('index.php?option=' . $this->option);
$pubsLabel = Lang::txt('COM_PUBLICATIONS_PUBLICATIONS');
$adminLabel = Lang::txt('COM_PUBLICATIONS_PUBLICATIONS_ADMIN_CONTROLS');
Toolbar::title(
    '<a href="' . $link . '">' . $pubsLabel . '</a>: ' . $adminLabel,
    'publications'
);

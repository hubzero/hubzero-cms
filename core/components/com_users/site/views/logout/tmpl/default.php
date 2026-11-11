<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Route;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{return: mixed} $this
 */

App::redirect(
    Route::url(
        'index.php?option=' . $this->option . '&task=user.logout&return=' . $this->return,
        false,
    ),
);

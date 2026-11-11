<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Toolbar;

// No direct access.
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{elements: mixed} $this
 */

$elements = $this->elements;

foreach ($elements as $name => $data) :
    Toolbar::$name(...$data);
endforeach;

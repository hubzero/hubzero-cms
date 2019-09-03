<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$elements = $this->elements;

foreach ($elements as $name => $data):
	Toolbar::$name(...$data);
endforeach;

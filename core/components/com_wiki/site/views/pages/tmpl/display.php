<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if ($this->page->isStatic())
{
	echo $this->loadTemplate('static');
}
else
{
	echo $this->loadTemplate('default');
}

<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

if ($this->poll->title) {
	echo stripslashes($this->poll->title);
} else {
	echo stripslashes($this->params->get('message'));
}

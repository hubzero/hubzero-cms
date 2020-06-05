<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

if ($this->user->get('guest')):
	// The user is not logged in.
	echo $this->loadTemplate('login');
else:
	// The user is already logged in.
	echo $this->loadTemplate('logout');
endif;

<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

$errors = $this->getError();

if (!empty($errors))
{
	if (!is_array($errors))
	{
		$errors = array($errors);
	}

	echo '<div class="messages errors">';
	foreach ($errors as $error)
	{
		echo '<p>' . $error . '</p>';
	}
	echo '</div>';
}

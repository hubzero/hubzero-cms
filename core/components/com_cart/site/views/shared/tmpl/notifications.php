<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

if (!empty($this->notifications))
{
	echo '<div class="section notifications">';
	foreach ($this->notifications as $n)
	{
		echo '<p class="' . $n[1] . '">' . $n[0] . '</p>';
	}
	echo '</div>';
}

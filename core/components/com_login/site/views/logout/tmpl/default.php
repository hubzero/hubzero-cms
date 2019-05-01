<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

App::redirect(
	Route::url(
		'index.php?option=' . $this->user . '&task=logout&return=' . $this->return,
		false
	)
);

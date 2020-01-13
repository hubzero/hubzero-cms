<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$forms = $this->forms;
$prereqs = $this->prereqs;

if (count($prereqs) > 0):
	$this->view('_prereqs_list')
		->set('forms', $forms)
		->set('prereqs', $prereqs)
		->display();
else:
	$this->view('_prereqs_none_notice')
		->display();
endif;

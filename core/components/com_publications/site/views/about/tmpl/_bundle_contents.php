<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

foreach ($this->contents as $element):
	if ($element['isDirectory']):
		$this->view('_bundle_directory')
			->set('directory', $element)
			->display();
	else:
		$this->view('_bundle_file')
			->set('file', $element)
			->display();
	endif;
endforeach;

<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$pages = $this->pages;

if (count($pages) > 0):
	$this->view('_pages_list')
		->set('pages', $pages)
		->display();
else:
	$this->view('_pages_none_notice')
		->display();
endif;

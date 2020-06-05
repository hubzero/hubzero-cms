<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if (!Request::getInt('no_html') && Request::getWord('format') != 'raw')
{
	$this->css()
		->js();
}

$this->view('list', 'medialist')
	->set('folder', $this->folder)
	->set('children', $this->children)
	->set('active', ($this->layout == 'list' ? true : false))
	->display();

$this->view('thumbs', 'medialist')
	->set('folder', $this->folder)
	->set('children', $this->children)
	->set('active', ($this->layout == 'thumbs' ? true : false))
	->display();

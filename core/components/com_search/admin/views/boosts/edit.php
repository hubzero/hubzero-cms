<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('boostsEdit');

$toolbarElements = [
	'title' => [Lang::txt('COM_SEARCH_HEADING_BOOST_EDIT')],
	'apply' => ['update'],
	'cancel' => ['list'],
	'custom' => ['destroy', 'trash', 'destroy', 'COM_SEARCH_DELETE', false],
	'spacer' => [],
	'help' => ['boost']
];

$this->view('_toolbar', 'shared')
	->set('elements', $toolbarElements)
	->display();

$boost = $this->boost;
$typeOptions = $this->typeOptions;

$this->view('_boost_form_edit')
	->set('boost', $boost)
	->set('typeOptions', $typeOptions)
	->display();

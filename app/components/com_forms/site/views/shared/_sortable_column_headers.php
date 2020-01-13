<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$columns = $this->columns;
$sortingCriteria = $this->sortingCriteria;

foreach ($columns as $title => $field):
	$this->view('_sortable_column_header')
		->set('field', $field)
		->set('title', $title)
		->set('sortingCriteria', $sortingCriteria)
		->display();
endforeach;

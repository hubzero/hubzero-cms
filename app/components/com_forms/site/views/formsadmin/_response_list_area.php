<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$formId = $this->formId;
$responses = $this->responses->rows();
$sortingAction = $this->sortingAction;
$sortingCriteria = $this->sortingCriteria;

if (count($responses) > 0):
	$this->view('_response_list', 'shared')
		->set('formId', $formId)
		->set('responses', $responses)
		->set('sortingAction', $sortingAction)
		->set('sortingCriteria', $sortingCriteria)
		->display();
else:
	$this->view('_response_list_none_notice')
		->display();
endif;

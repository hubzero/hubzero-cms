<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$formId = $this->formId;

$this->view('_protected_link', 'shared')
	->set('authMethod', 'currentCanCreate')
	->set('classes', 'btn btn-success')
	->set('textKey', 'COM_FORMS_LINKS_PREREQ_NEW')
	->set('urlFunction', 'formsPrereqsNewUrl')
	->set('urlFunctionArgs', [$formId])
	->display();

<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$form = $this->form;
$formId = $form->get('id');

$this->view('_protected_link', 'shared')
	->set('authArgs', [$form])
	->set('authMethod', 'canCurrentUserEditForm')
	->set('textKey', 'COM_FORMS_FIELDS_VALUES_EDIT_FORM')
	->set('urlFunction', 'formsEditUrl')
	->set('urlFunctionArgs', [$formId])
	->display();

<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$form = $this->form;
$id = $form->get('id');
$isActive = $form->isActive();
$pages = $form->getPages();
$prereqsIncomplete = !$form->prereqsAccepted(User::get('id'));
$response = $this->get('response');
$responseIsNew = $response->isNew();
$userCanStartResponse = $responseIsNew && $isActive;

if ($prereqsIncomplete):
	$this->view('_form_response_link_prereqs_incomplete')
		->display();
elseif ($userCanStartResponse):
	$this->view('_form_response_link_start')
		->set('formId', $id)
		->display();
elseif ($responseId = $response->get('id')):
	$this->view('_link_lang', 'shared')
		->set('classes', 'user-response-link')
		->set('textKey', 'COM_FORMS_LINKS_MY_RESPONSE')
		->set('urlFunction', 'responseFeedUrl')
		->set('urlFunctionArgs', [$responseId])
		->display();
elseif (!$form->get('disabled')):
	$this->view('_form_response_link_pages')
		->set('formId', $id)
		->set('pages', $pages)
		->display();
endif;

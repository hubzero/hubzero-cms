<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$element = $this->element;
$elementType = $element->get('type');
$elementTypeMap = [
	'checkbox-group' => '_form_field',
	'date' => '_form_field',
	'header' => '_form_decoration',
	'hidden' => '_form_decoration',
	'number' => '_form_field',
	'paragraph' => '_form_decoration',
	'radio-group' => '_form_field',
	'select' => '_form_field',
	'text' => '_form_field',
	'textarea' => '_form_field'
];

$partialName = $elementTypeMap[$elementType];

$this->view($partialName)
	->set('element', $element)
	->set('type', $elementType)
	->display();

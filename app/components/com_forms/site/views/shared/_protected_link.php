<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$componentPath = \Component::path('com_forms');

require_once "$componentPath/helpers/formsAuth.php";

use Components\Forms\Helpers\FormsAuth;

$formsAuth = new FormsAuth();

$authArgs = isset($this->authArgs) ? $this->authArgs : [];
$authMethod = $this->authMethod;
$classes = isset($this->classes) ? $this->classes : '';
$isAuthorized = $formsAuth->$authMethod(...$authArgs);
$textKey = $this->textKey;
$urlFunction = $this->urlFunction;
$urlFunctionArgs = isset($this->urlFunctionArgs) ? $this->urlFunctionArgs : [];

if ($isAuthorized):
	$this->view('_link_lang')
		->set('classes', $classes)
		->set('textKey', $textKey)
		->set('urlFunction', $urlFunction)
		->set('urlFunctionArgs', $urlFunctionArgs)
		->display();
endif;

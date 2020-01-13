<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->view('_protected_link', 'shared')
	->set('authMethod', 'currentCanCreate')
	->set('textKey', 'COM_FORMS_LINKS_FORM_CREATE')
	->set('urlFunction', 'formsNewUrl')
	->display();

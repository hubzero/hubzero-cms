<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$formId = $this->formId;
$pages = $this->pages;
?>

<span>
	<?php
		echo Lang::txt('COM_FORMS_HEADINGS_PAGES');

		$this->view('_form_pages_pagination', 'shared')
			->set('formId', $formId)
			->set('pages', $pages)
			->display();
?>
</span>

<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/models/formPage.php";

use Hubzero\Test\Basic;
use Components\Forms\Models\FormPage;

class FormPageTest extends Basic
{

	public function testInitiateHasCreated()
	{
		$page = FormPage::blank();

		$initiate = $page->initiate;
		$hasCreated = in_array('created', $initiate);

		$this->assertEquals(true, $hasCreated);
	}

	public function testRulesRequiresFormId()
	{
		$page = FormPage::blank();

		$validation = $page->rules['form_id'];

		$this->assertEquals('positive', $validation);
	}

	public function testRulesRequiresOrder()
	{
		$page = FormPage::blank();

		$validation = $page->rules['order'];

		$this->assertEquals('positive', $validation);
	}

}

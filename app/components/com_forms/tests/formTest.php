<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/models/form.php";

use Hubzero\Test\Basic;
use Components\Forms\Models\Form;

class FormTest extends Basic
{

	public function testInitiateHasCreated()
	{
		$form = Form::blank();

		$initiate = $form->initiate;
		$hasCreated = in_array('created', $initiate);

		$this->assertEquals(true, $hasCreated);
	}

	public function testRulesRequiresName()
	{
		$form = Form::blank();

		$validation = $form->rules['name'];

		$this->assertEquals('notempty', $validation);
	}

	public function testRulesRequiresOpeningTime()
	{
		$form = Form::blank();

		$validation = $form->rules['opening_time'];

		$this->assertEquals('notempty', $validation);
	}

	public function testRulesRequiresClosingTime()
	{
		$form = Form::blank();

		$validation = $form->rules['closing_time'];

		$this->assertEquals('notempty', $validation);
	}

}

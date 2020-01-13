<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/models/formPrerequisite.php";

use Hubzero\Test\Basic;
use Components\Forms\Models\FormPrerequisite;

class FormPrerequisiteTest extends Basic
{

	public function testInitiateHasCreated()
	{
		$prereq = FormPrerequisite::blank();

		$initiate = $prereq->initiate;
		$hasCreated = in_array('created', $initiate);

		$this->assertEquals(true, $hasCreated);
	}

	public function testRulesRequireFormId()
	{
		$prereq = FormPrerequisite::blank();

		$validation = $prereq->rules['form_id'];

		$this->assertEquals('notempty', $validation);
	}

	public function testRulesRequirePrerequisiteId()
	{
		$prereq = FormPrerequisite::blank();

		$validation = $prereq->rules['prerequisite_id'];

		$this->assertEquals('notempty', $validation);
	}

	public function testRulesRequirePrerequisiteScope()
	{
		$prereq = FormPrerequisite::blank();

		$validation = $prereq->rules['prerequisite_scope'];

		$this->assertEquals('notempty', $validation);
	}

	public function testRulesRequireOrder()
	{
		$prereq = FormPrerequisite::blank();

		$validation = $prereq->rules['order'];

		$this->assertEquals('notempty', $validation);
	}

}

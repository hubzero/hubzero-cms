<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/models/pageField.php";

use Hubzero\Test\Basic;
use Components\Forms\Models\PageField;

class PageFieldTest extends Basic
{

	public function testInitiateHasCreated()
	{
		$field = PageField::blank();

		$hasCreated = in_array('created', $field->initiate);

		$this->assertEquals(true, $hasCreated);
	}

	public function testRulesRequiresPageId()
	{
		$field = PageField::blank();

		$validation = $field->rules['page_id'];

		$this->assertEquals('positive', $validation);
	}

	public function testRulesRequiresOrder()
	{
		$field = PageField::blank();

		$validation = $field->rules['order'];

		$this->assertEquals('positive', $validation);
	}

	public function testRulesRequiresType()
	{
		$field = PageField::blank();

		$validation = $field->rules['type'];

		$this->assertEquals('notempty', $validation);
	}

	public function testIsFillableReturnsTrueForCheckboxGroup()
	{
		$field = PageField::blank();

		$field->set('type', 'checkbox-group');
		$isFillable = $field->isFillable();

		$this->assertEquals(true, $isFillable);
	}

	public function testIsFillableReturnsTrueForDate()
	{
		$field = PageField::blank();

		$field->set('type', 'date');
		$isFillable = $field->isFillable();

		$this->assertEquals(true, $isFillable);
	}

	public function testIsFillableReturnsTrueForHidden()
	{
		$field = PageField::blank();

		$field->set('type', 'hidden');
		$isFillable = $field->isFillable();

		$this->assertEquals(true, $isFillable);
	}

	public function testIsFillableReturnsTrueForNumber()
	{
		$field = PageField::blank();

		$field->set('type', 'number');
		$isFillable = $field->isFillable();

		$this->assertEquals(true, $isFillable);
	}

	public function testIsFillableReturnsTrueForRadioGroup()
	{
		$field = PageField::blank();

		$field->set('type', 'radio-group');
		$isFillable = $field->isFillable();

		$this->assertEquals(true, $isFillable);
	}

	public function testIsFillableReturnsTrueForSelect()
	{
		$field = PageField::blank();

		$field->set('type', 'select');
		$isFillable = $field->isFillable();

		$this->assertEquals(true, $isFillable);
	}

	public function testIsFillableReturnsTrueForText()
	{
		$field = PageField::blank();

		$field->set('type', 'text');
		$isFillable = $field->isFillable();

		$this->assertEquals(true, $isFillable);
	}

	public function testIsFillableReturnsTrueForTextArea()
	{
		$field = PageField::blank();

		$field->set('type', 'textarea');
		$isFillable = $field->isFillable();

		$this->assertEquals(true, $isFillable);
	}

	public function testIsFillableReturnsFalseForHeader()
	{
		$field = PageField::blank();

		$field->set('type', 'header');
		$isFillable = $field->isFillable();

		$this->assertEquals(false, $isFillable);
	}

	public function testIsFillableReturnsFalseForParagraph()
	{
		$field = PageField::blank();

		$field->set('type', 'paragraph');
		$isFillable = $field->isFillable();

		$this->assertEquals(false, $isFillable);
	}

}

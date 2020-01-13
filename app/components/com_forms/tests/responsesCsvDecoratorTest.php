<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/responsesCsvDecorator.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\ResponsesCsvDecorator as Decorator;
use Components\Forms\Tests\Traits\canMock;

class ResponsesCsvDecoratorTest extends Basic
{
	use canMock;

	public function testGetColumnsGetsForm()
	{
		$form = $this->mock([
			'class' => 'Form', 'methods' => ['getFieldsOrdered' => []]
		]);
		$formId = 845;
		$formsHelper = $this->mock([
			'class' => 'Form', 'methods' => ['oneOrNew' => $form]
		]);
		$response = $this->mock([
			'class' => 'Relational', 'methods' => ['get' => $formId]
		]);
		$responses = $this->mock([
			'class' => 'Relational',
			'methods' => ['count' => 1, 'first' => $response]
		]);
		$csvResponses = new Decorator([
			'forms' => $formsHelper, 'responses' => $responses
		]);

		$formsHelper->expects($this->once())
			->method('oneOrNew')
			->with($formId);

		$columns = $csvResponses->getColumns();
	}

	public function testGetColumnsReturnsCorrectColumnsWithForm()
	{
		$expectedColumns = ['user_id', 'user_name', 'modified', 'text_a', 'select_a'];
		$fieldA = $this->mock([
			'class' => 'Field',
			'methods' => ['get' => 'text_a', 'isFillable' => true]
		]);
		$fieldB = $this->mock([
			'class' => 'Field',
			'methods' => ['get' => 'paragraph', 'isFillable' => false]
		]);
		$fieldC = $this->mock([
			'class' => 'Field',
			'methods' => ['get' => 'select_a', 'isFillable' => true]
		]);
		$fields = [$fieldA, $fieldB, $fieldC];
		$form = $this->mock([
			'class' => 'Form', 'methods' => ['getFieldsOrdered' => $fields]
		]);
		$responses = $this->mock(['class' => 'Relational']);
		$csvResponses = new Decorator(['form' => $form, 'responses' => $responses]);

		$columns = $csvResponses->getColumns();

		$this->assertEquals($expectedColumns, $columns);
	}

	public function testCurrentReturnsResponseAtCurrentIndex()
	{
		$responseA = $this->mock(['class' => 'Response']);
		$responseB = $this->mock(['class' => 'Response']);
		$responses = [$responseA, $responseB];
		$decorator = $this->mock([
			'class' => 'ResponseDecorator', 'methods' => ['create' => $responseA]
		]);
		$csvResponses = $this->mock([
			'class' => 'Relational', 'methods' => ['raw' => $responses, 'count']
		]);
		$csvResponses = new Decorator([
			'responses' => $csvResponses, 'decorator' => $decorator
		]);

		$current = $csvResponses->current();

		$this->assertEquals($responseA, $current);
	}

	public function testKeyReturnsCurrentPosition()
	{
		$responseA = $this->mock(['class' => 'Response', 'methods' => ['get']]);
		$responseB = $this->mock(['class' => 'Response', 'methods' => ['get']]);
		$data = [$responseA, $responseB];
		$responses = $this->mock([
			'class' => 'Relational', 'methods' => ['raw' => $data, 'count']
		]);
		$csvResponses = new Decorator(['responses' => $responses]);
		$count = 0;

		foreach ($csvResponses as $i => $response)
		{
			$this->assertEquals($count, $i);
			++$count;
		}
	}

}


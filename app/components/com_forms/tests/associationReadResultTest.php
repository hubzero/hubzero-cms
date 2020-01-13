<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/associationReadResult.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\AssociationReadResult;
use Components\Forms\Tests\Traits\canMock;

class AssociationReadResultTest extends Basic
{
	use canMock;

	public function testSucceededReturnsFalseWhenModelIsNew()
	{
		$model = $this->mock([
			'class' => 'Relational', 'methods' => ['isNew' => true, 'test']
		]);
		$result = new AssociationReadResult(['model' => $model, 'accessor' => 'test']);

		$succeeded = $result->succeeded();

		$this->assertEquals(false, $succeeded);
	}

	public function testSucceededReturnsFalseWhenAccessorThrows()
	{
		$this->markTestSkipped();
		$model = $this->mock([
			'class' => 'Relational', 'methods' => ['isNew' => false, 'test']
		]);
		$model->method('test')->will($this->throwException(new \Exception()));
		$result = new AssociationReadResult(['model' => $model, 'accessor' => 'test']);

		$succeeded = $result->succeeded();

		$this->assertEquals(false, $succeeded);
	}

	public function testSucceededReturnsTrueWhenModelNotNewAndAccessorSucceeds()
	{
		$model = $this->mock([
			'class' => 'Relational', 'methods' => ['isNew' => false, 'test']
		]);
		$result = new AssociationReadResult(['model' => $model, 'accessor' => 'test']);

		$succeeded = $result->succeeded();

		$this->assertEquals(true, $succeeded);
	}

	public function testGetDataReturnsModelsAccessorReturn()
	{
		$accessor = 'test';
		$expectedReturn = ['foo', 3.14];
		$model = $this->mock([
			'class' => 'Relational', 'methods' => [$accessor => $expectedReturn]
		]);
		$result = new AssociationReadResult(['model' => $model, 'accessor' => $accessor]);

		$actualReturn = $result->getData();

		$this->assertEquals($expectedReturn, $actualReturn);
	}

}

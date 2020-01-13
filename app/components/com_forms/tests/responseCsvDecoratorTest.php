<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/responseCsvDecorator.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\ResponseCsvDecorator as Decorator;
use Components\Forms\Tests\Traits\canMock;

class ResponseCsvDecoratorTest extends Basic
{
	use canMock;

	public function testCreateReturnsInstance()
	{
		$response = $this->mock(['class' => 'Response', 'methods' => ['get']]);
		$decorator = Decorator::create(['response' => $response, 'order' => []]);

		$class = get_class($decorator);

		$this->assertEquals('Components\Forms\Helpers\ResponseCsvDecorator', $class);
	}

}

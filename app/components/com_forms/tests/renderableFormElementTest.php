<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/renderableFormElement.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\RenderableFormElement;
use Components\Forms\Tests\Traits\canMock;

class RenderableFormElementTest extends Basic
{
	use canMock;

	public function testGetReturnsCorrectName()
	{
		$id = 823;
		$element = $this->mock([
			'class' => 'Relational',
			'methods' => ['get' => $id]
		]);

		$renderableElement = new RenderableFormElement([
			'element' => $element,
			'respondent_id' => 0
		]);
		$name = $renderableElement->get('name');

		$this->assertEquals("responses[$id]", $name);
	}

	public function testGetForwardsGetsByDefault()
	{
		$element = $this->mock([
			'class' => 'Relational', 'methods' => ['get']
		]);
		$unhandledGetDefault = [];
		$unhandledGetKey = 'test';
		$renderableElement = new RenderableFormElement([
			'element' => $element,
			'respondent_id' => 0
		]);

		$element->expects($this->once())
			->method('get')
			->with($unhandledGetKey, $unhandledGetDefault);

		$renderableElement->get($unhandledGetKey, $unhandledGetDefault);
	}

}

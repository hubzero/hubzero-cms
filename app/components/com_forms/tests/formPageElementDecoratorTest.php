<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/formPageElementDecorator.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\FormPageElementDecorator;
use Components\Forms\Tests\Traits\canMock;

class FormPageElementDecoratorTest extends Basic
{
	use canMock;

	public function testDecorateForRenderingReturnsRenderableFormElements()
	{
		$elements = ['a'];
		$decorator = new FormPageElementDecorator();

		$decoratedElements = $decorator->decorateForRendering($elements, 2);
		$element = $decoratedElements[0];
		$elementClass = get_class($element);

		$this->assertEquals('Components\Forms\Helpers\RenderableFormElement', $elementClass);
	}

}

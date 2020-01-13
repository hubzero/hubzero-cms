<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/formResponseEventsHelper.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\FormResponseEventsHelper as EventsHelper;
use Components\Forms\Tests\Traits\canMock;

class FormResponseEventsHelperTest extends Basic
{
	use canMock;

	public function testFieldResponsesUpdateInvokesDispatch()
	{
		$dispatcher = $this->mock([
			'class' => 'Dispatcher', 'methods' => ['dispatch']
		]);
		$helper = new EventsHelper(['dispatcher' => $dispatcher]);
		$fieldResponses = ['a', 3];

		$dispatcher->expects($this->once())
			->method('dispatch')
			->with('onFieldResponsesUpdate', [$fieldResponses]);

		$helper->fieldResponsesUpdate($fieldResponses);
	}

}

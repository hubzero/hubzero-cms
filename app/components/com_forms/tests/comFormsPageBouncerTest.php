<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/comFormsPageBouncer.php";
require_once "$componentPath/helpers/formsRouter.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\ComFormsPageBouncer;
use Components\Forms\Helpers\FormsRouter;
use Components\Forms\Tests\Traits\canMock;

class ComFormsPageBouncerTest extends Basic
{
	use canMock;

	public function testRedirectIfPrereqsIncompleteRedirectsToCorrectDefaultUrl()
	{
		$id = 4;
		$form = $this->mock([
			'class' => 'Form',
		 	'methods' => ['get' => $id, 'prereqsAccepted' => false]
		]);
		$notify = $this->mock([
			'class' => 'Notify', 'methods' => ['warning']]
		);
		$router = $this->mock([
			'class' => 'Router', 'methods' => ['redirect']]
		);
		$user = $this->mock([
			'class' => 'User', 'methods' => ['get']]
		);
		$bouncer = new ComFormsPageBouncer([
			'notify' => $notify, 'router' => $router, 'user' => $user
		]);
		$correctUrl = (new FormsRouter)->formsDisplayUrl($id);

		$router->expects($this->once())
			->method('redirect')
			->with($correctUrl);

		$bouncer->redirectIfPrereqsNotAccepted($form);
	}

	public function testRedirectIfPrereqsIncompleteDisplaysCorrectMessage()
	{
		$form = $this->mock([
			'class' => 'Form', 'methods' => ['get', 'prereqsAccepted' => false]
		]);
		$notify = $this->mock([
			'class' => 'Notify', 'methods' => ['warning']]
		);
		$router = $this->mock([
			'class' => 'Router', 'methods' => ['redirect']]
		);
		$user = $this->mock([
			'class' => 'User', 'methods' => ['get']]
		);
		$bouncer = new ComFormsPageBouncer([
			'notify' => $notify, 'router' => $router, 'user' => $user
		]);
		$correctMessage = Lang::txt('COM_FORMS_NOTICES_FORMS_PREREQS_INCOMPLETE');

		$notify->expects($this->once())
			->method('warning')
			->with($correctMessage);

		$bouncer->redirectIfPrereqsNotAccepted($form);
	}

}

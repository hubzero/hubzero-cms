<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/crudHelper.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\CrudHelper;
use Components\Forms\Tests\Traits\canMock;

class CrudHelperTest extends Basic
{
	use canMock;

	public function testSuccessfulCreateInvokesRedirect()
	{
		$url = 'url';
		$controller = $this->mock(['class' => 'SiteController']);
		$notify = $this->mock(['class' => 'Notify', 'methods' => ['success']]);
		$router = $this->mock(['class' => 'App', 'methods' => ['redirect']]);
		$crudHelper = new CrudHelper([
			'controller' => $controller,
			'notify' => $notify,
			'router' => $router
		]);

		$router->expects($this->once())
			->method('redirect')
			->with($this->equalTo($url));

		$crudHelper->successfulCreate($url, 'message');
	}

	public function testFailedCreateInvokesGetErrors()
	{
		$controller = $this->mock([
			'class' => 'SiteController',
			'methods' => ['setView', 'newTask']
		]);
		$notify = $this->mock(['class' => 'Notify', 'methods' => ['error']]);
		$record = $this->mock([
			'class' => 'Relational', 'methods' => ['getErrors' => ['']]
		]);
		$crudHelper = new CrudHelper([
			'controller' => $controller,
			'notify' => $notify
		]);

		$record->expects($this->once())
			->method('getErrors');

		$crudHelper->failedCreate($record, '');
	}

	public function testFailedCreateInvokesError()
	{
		$controller = $this->mock([
			'class' => 'SiteController',
			'methods' => ['setView', 'newTask']
		]);
		$notify = $this->mock(['class' => 'Notify', 'methods' => ['error']]);
		$record = $this->mock([
			'class' => 'Relational', 'methods' => ['getErrors' => []]
		]);
		$crudHelper = new CrudHelper([
			'controller' => $controller,
			'notify' => $notify
		]);

		$notify->expects($this->once())
			->method('error');

		$crudHelper->failedCreate($record, '');
	}

	public function testSuccessfulUpdateInvokesRedirect()
	{
		$router = $this->mock(['class' => 'App', 'methods' => ['redirect']]);
		$crudHelper = new CrudHelper([
			'router' => $router
		]);

		$router->expects($this->once())
			->method('redirect');

		$crudHelper->successfulUpdate('url');
	}

	public function testFailedUpdateInvokesGetErrors()
	{
		$notify = $this->mock(['class' => 'Notify', 'methods' => ['error']]);
		$record = $this->mock([
			'class' => 'Relational', 'methods' => ['getErrors' => ['']]
		]);
		$crudHelper = new CrudHelper([
			'notify' => $notify
		]);

		$record->expects($this->once())
			->method('getErrors');

		$crudHelper->failedUpdate($record);
	}

	public function testFailedUpdateInvokesError()
	{
		$notify = $this->mock(['class' => 'Notify', 'methods' => ['error']]);
		$record = $this->mock([
			'class' => 'Relational', 'methods' => ['getErrors' => ['']]
		]);
		$crudHelper = new CrudHelper([
			'notify' => $notify
		]);

		$notify->expects($this->once())
			->method('error');

		$crudHelper->failedUpdate($record);
	}

	public function testSuccessfulCreateInvokesSuccess()
	{
		$controller = $this->mock(['class' => 'SiteController']);
		$notify = $this->mock(['class' => 'Notify', 'methods' => ['success']]);
		$router = $this->mock(['class' => 'App', 'methods' => ['redirect']]);
		$crudHelper = new CrudHelper([
			'controller' => $controller,
			'notify' => $notify,
			'router' => $router
		]);

		$notify->expects($this->once())
			->method('success');

		$crudHelper->successfulCreate('url', 'message');
	}

	public function testSuccessfulCreateDoesNotInvokeSuccessIfNoMessage()
	{
		$controller = $this->mock(['class' => 'SiteController']);
		$notify = $this->mock(['class' => 'Notify', 'methods' => ['success']]);
		$router = $this->mock(['class' => 'App', 'methods' => ['redirect']]);
		$crudHelper = new CrudHelper([
			'controller' => $controller,
			'notify' => $notify,
			'router' => $router
		]);

		$notify->expects($this->never())
			->method('success');

		$crudHelper->successfulCreate('url');
	}

}

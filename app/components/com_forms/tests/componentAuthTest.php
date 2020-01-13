<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/componentAuth.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\ComponentAuth;
use Components\Forms\Tests\Traits\canMock;

class ComponentAuthTest extends Basic
{
	use canMock;

	public function testCurrentIsAuthorizedInvokesAuthorize()
	{
		$permitter = $this->mock([
			'class' => 'User',
			'methods' => ['authorize']
		]);
		$auth = new ComponentAuth([
			'component' => 'com_forms',
			'permitter' => $permitter
		]);

		$permitter->expects($this->once())
			->method('authorize');

		$auth->currentIsAuthorized('test');
	}

	public function testCurrentCanCreateInvokesAuthorize()
	{
		$permitter = $this->mock([
			'class' => 'User',
			'methods' => ['authorize']
		]);
		$auth = new ComponentAuth([
			'component' => 'com_forms',
			'permitter' => $permitter
		]);

		$permitter->expects($this->once())
			->method('authorize')
			->with('core.create');

		$auth->currentCanCreate();
	}

}

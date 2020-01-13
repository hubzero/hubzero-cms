<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/respondentsHelper.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\RespondentsHelper;
use Components\Forms\Tests\Traits\canMock;

class ResondentsHelperTest extends Basic
{
	use canMock;

	public function testGetEmailsReturnsCorrectEmails()
	{
		$userIds = [['user_id' => 3], ['user_id' => 6], ['user_id' => 7]];
		$expectedEmails = [['email' => 'a@e'], ['email' => 'b@e'], ['email' => 'c@e']];
		$responseRelational = $this->getMockBuilder('Relational')
			->setMethods(['select', 'whereIn', 'rows', 'toArray'])
			->getMock();
		$responseRelational->method('select')->willReturn($responseRelational);
		$responseRelational->method('whereIn')->willReturn($responseRelational);
		$responseRelational->method('rows')->willReturn($responseRelational);
		$responseRelational->method('toArray')->willReturn($userIds);
		$responseHelper = $this->mock([
			'class' => 'FormResponse', 'methods' => ['all' => $responseRelational]
		]);
		$userRelational = $this->getMockBuilder('Relational')
			->setMethods(['select', 'whereIn', 'rows', 'toArray'])
			->getMock();
		$userRelational->method('select')->willReturn($userRelational);
		$userRelational->method('whereIn')->willReturn($userRelational);
		$userRelational->method('rows')->willReturn($userRelational);
		$userRelational->method('toArray')->willReturn($expectedEmails);
		$userHelper = $this->mock([
			'class' => 'User', 'methods' => ['all' => $userRelational]
		]);
		$helper = new RespondentsHelper([
			'responses' => $responseHelper, 'users' => $userHelper
		]);

		$actualEmails = $helper->getEmails([]);

		$this->assertEquals(['a@e', 'b@e', 'c@e'], $actualEmails);
	}


}

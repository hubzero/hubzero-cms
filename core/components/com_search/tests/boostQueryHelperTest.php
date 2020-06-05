<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

$componentPath = Component::path('com_search');

require_once "$componentPath/helpers/boostQueryHelper.php";
require_once "$componentPath/tests/traits/canMock.php";

use Components\Search\Helpers\BoostQueryHelper as Helper;
use Components\Search\Tests\Traits\canMock;
use Hubzero\Test\Basic;

class BoostQueryHelperTest extends Basic
{
	use canMock;

	public function testGetAllQueriesInvokesOne()
	{
		$boosts = [1, 1, 1];
		$factoryMock = $this->mock([
			'class' => 'BoostQueries', 'methods' => ['one']
		]);
		$ormMock = $this->mock([
			'class' => 'Boost', 'methods' => ['all' => $boosts]
		]);
		$helper = new Helper([
			'boosts' => $ormMock,
		 	'queries' => $factoryMock
		]);

		$factoryMock->expects($this->once())
			->method('one')
			->with(['boosts' => $boosts]);

		$boostQueries = $helper->getAllQueries();
	}

}

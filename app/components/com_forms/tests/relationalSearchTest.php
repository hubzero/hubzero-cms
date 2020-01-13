<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/criterion.php";
require_once "$componentPath/helpers/relationalSearch.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\Criterion;
use Components\Forms\Helpers\RelationalSearch;

class RelationalSearchTest extends Basic
{

	public function testFindBy()
	{
		$criteria = [
			new Criterion([
				'name' => 'disabled',
				'operator' => '=',
				'value' => 1
			]),
			new Criterion([
				'name' => 'priority',
				'operator' => '>',
				'value' => 4
			])
		];
		$relational = $this->getMockBuilder('Relational')
			->setMethods(['all', 'where'])
			->getMock();
		$relational->method('all')->willReturn($relational);
		$search = new RelationalSearch(['class' => $relational]);

		$relational->expects($this->once())
			->method('all');

		$relational->expects($this->exactly(2))
			->method('where')
			->withConsecutive(
				[
					$this->equalTo($criteria[0]->getName()),
					$this->equalTo($criteria[0]->getOperator()),
					$this->equalTo($criteria[0]->getValue())
				],
				[
					$this->equalTo($criteria[1]->getName()),
					$this->equalTo($criteria[1]->getOperator()),
					$this->equalTo($criteria[1]->getValue())
				]
			);

		$search->findBy($criteria);
	}

}

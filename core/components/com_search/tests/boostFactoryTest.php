<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

$componentPath = Component::path('com_search');

require_once "$componentPath/helpers/boostFactory.php";
require_once "$componentPath/tests/traits/canMock.php";

use Components\Search\Helpers\BoostFactory;
use Components\Search\Tests\Traits\canMock;
use Hubzero\Test\Basic;

class BoostFactoryTest extends Basic
{
	use canMock;

	public function testOneSetsNonDocumentSpecificData()
	{
		$now = Date::toSql();
		$strength = 11;
		$userId = -33;
		$userHelper = $this->mock([
			'class' => 'User', 'methods' => ['get' => $userId]]
		);
		$factory = new BoostFactory(['user' => $userHelper]);
		$boostData = [
			'document_type' => 'test', 'strength' => $strength
		];

		$boost = $factory->one($boostData);

		$this->assertEquals($strength, $boost->getStrength());
		$this->assertEquals($userId, $boost->getCreatedBy());
		$this->assertEquals($now, $boost->getCreated());
	}

	public function testOneReturnsCorrectBoostForCitationDocuments()
	{
		$userHelper = $this->mock(['class' => 'User', 'methods' => ['get']]);
		$factory = new BoostFactory(['user' => $userHelper]);
		$citationsType = Lang::txt('COM_SEARCH_BOOST_DOCUMENT_TYPE_CITATION');
		$boostData = ['document_type' => $citationsType];

		$boost = $factory->one($boostData);

		$this->assertEquals('hubtype', $boost->getField());
		$this->assertEquals('citation', $boost->getFieldValue());
	}

	public function testOneReturnsCorrectBoostForResourceDocuments()
	{
		$documentType = 'Tools';
		$userHelper = $this->mock(['class' => 'User', 'methods' => ['get']]);
		$factory = new BoostFactory(['user' => $userHelper]);
		$boostData = ['document_type' => $documentType];

		$boost = $factory->one($boostData);

		$this->assertEquals('type', $boost->getField());
		$this->assertEquals($documentType, $boost->getFieldValue());
	}

}

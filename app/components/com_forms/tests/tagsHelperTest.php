<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/tagsHelper.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\TagsHelper;
use Components\Forms\Tests\Traits\canMock;

class TagsHelperTest extends Basic
{
	use canMock;

	public function testAddTagsSetsTagCreatorScope()
	{
		$tableName = 'table';
		$creator = $this->mock([
			'class' => 'TagCreator', 'methods' => ['set']
		]);
		$records = $this->mock([
			'class' => 'Relational',
			'methods' => ['getTableName' => $tableName]
		]);
		$tagsHelper = new TagsHelper(['creator' => $creator]);

		$creator->expects($this->once())
			->method('set')
			->with('scope', $tableName);

		$tagsHelper->addTags($records, 'tag', 99);
	}

	public function testAddTagsReturnsAddTagResult()
	{
		$creator = $this->mock([
			'class' => 'TagCreator', 'methods' => ['set']
		]);
		$records = $this->mock([
			'class' => 'Relational', 'methods' => ['getTableName']
		]);
		$tagsHelper = new TagsHelper(['creator' => $creator]);

		$result = $tagsHelper->addTags($records, 'tag', 99);

		$this->assertEquals('Components\Forms\Helpers\AddTagsResult', get_class($result));
	}

	public function testUpdateTagsSetsTagCreatorScope()
	{
		$tableName = 'table';
		$creator = $this->mock([
			'class' => 'TagCreator', 'methods' => ['set']
		]);
		$records = $this->mock([
			'class' => 'Relational',
			'methods' => ['getTableName' => $tableName]
		]);
		$tagsHelper = new TagsHelper(['creator' => $creator]);

		$creator->expects($this->once())
			->method('set')
			->with('scope', $tableName);

		$tagsHelper->updateTags($records, 'tag', 99);
	}

	public function testUpdateTagsReturnsUpdateTagResult()
	{
		$creator = $this->mock([
			'class' => 'TagCreator', 'methods' => ['set']
		]);
		$records = $this->mock([
			'class' => 'Relational', 'methods' => ['getTableName']
		]);
		$tagsHelper = new TagsHelper(['creator' => $creator]);

		$result = $tagsHelper->updateTags($records, 'tag', 99);

		$this->assertEquals('Components\Forms\Helpers\UpdateTagsResult', get_class($result));
	}

}

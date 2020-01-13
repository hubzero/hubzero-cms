<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');
$tagComponentPath = Component::path('com_tags');

require_once "$componentPath/helpers/tagResultFactory.php";
require_once "$tagComponentPath/models/cloud.php";

use Components\Forms\Helpers\TagResultFactory;
use Components\Tags\Models\Cloud as TagCreator;
use Hubzero\Utility\Arr;

class TagsHelper
{

	/**
	 * Constructs TagsHelper instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_resultFactory = Arr::getValue(
			$args,
		 	'result_factory',
			new TagResultFactory()
		);
		$this->_tagCreator = Arr::getValue(
			$args, 'creator', new TagCreator()
		);
	}

	/**
	 * Associates given records with provided tags
	 *
	 * @param    object   $records      Records to tag
	 * @param    string   $tagsString   Comma-separated list of tags
	 * @param    int      $taggerId     ID of user associating tags
	 * @return   object
	 */
	public function addTags($records, $tagsString, $taggerId)
	{
		$this->_setTaggingData($records, $tagsString, $taggerId);

		$this->_addTagsToRecords();

		return $this->_result;
	}

	/**
	 * Stores required tagging data
	 *
	 * @param    object   $record       Record to tag
	 * @param    string   $tagsString   Comma-separated list of tags
	 * @param    int      $taggerId     ID of user associating tags
	 */
	protected function _setTaggingData($records, $tagsString, $taggerId)
	{
		$this->_records = $records;
		$this->_tagString = $tagsString;
		$this->_taggerId = $taggerId;

		$this->_setCreatorScope();
	}

	/**
	 * Adds tags to all records tracking the result
	 *
	 * @return   object
	 */
	protected function _addTagsToRecords()
	{
		$this->_result = $this->_resultFactory->addResult();

		foreach ($this->_records as $record)
		{
			$this->_addTagsToRecord($record);
		}
	}

	/**
	 * Adds tags to record tracking the result
	 *
	 * @param    object   $record       Record to tag
	 * @return   void
	 */
	protected function _addTagsToRecord($record)
	{
		$recordId = $record->get('id');
		$this->_tagCreator->set('scope_id', $recordId);

		$tagsAdded = $this->_tagCreator->add($this->_tagString, $this->_taggerId);

		$this->_updateAddResult($record, $tagsAdded);
	}

	/**
	 * Updates results based on whether or not tag association was created
	 *
	 * @param    object   $record      Record that should have been tagged
	 * @param    bool     $tagsAdded   Indicates if tags were associated
	 * @return   void
	 */
	protected function _updateAddResult($record, $tagsAdded)
	{
		if ($tagsAdded)
		{
			$this->_result->addSuccess($record);
		}
		else
		{
			$this->_result->addFailure($record, $this->_tagCreator->getErrors());
			$this->_tagCreator->setErrors([]);
		}
	}

	/**
	 * Sets tag creator scope
	 *
	 * @return   void
	 */
	protected function _setCreatorScope()
	{
		$scope = $this->_getRecordsScope();

		$this->_tagCreator->set('scope', $scope);
	}

	/**
	 * Generates scope based on record type
	 *
	 * @return   string
	 */
	protected function _getRecordsScope()
	{
		$recordScope = $this->_records->getTableName();

		$formattedScope = ltrim($recordScope, '#__');

		return $formattedScope;
	}

	/**
	 * Updates given records tags
	 *
	 * @param    object   $records      Records to tag
	 * @param    string   $tagsString   Comma-separated list of tags
	 * @param    int      $taggerId     ID of user associating tags
	 * @return   object
	 */
	public function updateTags($records, $tagString, $taggerId)
	{
		$this->_setTaggingData($records, $tagString, $taggerId);

		$this->_updateRecordsTags();

		return $this->_result;
	}

	/**
	 * Updates records' tags tracking the result
	 *
	 * @return   object
	 */
	protected function _updateRecordsTags()
	{
		$this->_result = $this->_resultFactory->updateResult();

		foreach ($this->_records as $record)
		{
			$this->_updateRecordTags($record);
		}
	}

	/**
	 * Updates record's tags tracking the result
	 *
	 * @return   object
	 */
	protected function _updateRecordTags($record)
	{
		$recordId = $record->get('id');
		$this->_tagCreator->set('scope_id', $recordId);

		$tagsUpdated = $this->_tagCreator->setTags($this->_tagString, $this->_taggerId);

		$this->_updateUpdateResult($record, $tagsUpdated);
	}

	/**
	 * Updates result based on whether or not record's tags were updated
	 *
	 * @param    object   $record        Record that should have been tagged
	 * @param    bool     $tagsUpdated   Indicates if tags were updated
	 * @return   void
	 */
	protected function _updateUpdateResult($record, $tagsUpdated)
	{
		if ($tagsUpdated)
		{
			$this->_result->addSuccess($record);
		}
		else
		{
			$this->_result->addFailure($record, $this->_tagCreator->getErrors());
			$this->_tagCreator->setErrors([]);
		}
	}

}

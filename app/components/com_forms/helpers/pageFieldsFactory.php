<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$compnentPath = Component::path('com_forms');

require_once "$componentPath/helpers/associationReadResult.php";
require_once "$componentPath/helpers/factory.php";

use Components\Forms\Helpers\AssociationReadResult;
use Components\Forms\Helpers\Factory;

class PageFieldsFactory extends Factory
{

	protected $_modelName;

	/**
	 * Constructs PageFieldsFactory instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$args['model_name'] = 'Components\Forms\Models\PageField';

		parent::__construct($args);
	}

	/**
	 * Retrieves given pages associated fields
	 *
	 * @param    object   $page   Given page
	 * @return   object
	 */
	public function readPagesFields($page)
	{
		$readResult = new AssociationReadResult([
			'model' => $page,
			'accessor' => 'getFieldsInArray',
		]);

		return $readResult;
	}

	/**
	 * Updates given pages associated fields
	 *
	 * @param    object   $currentFields   Page's current fields
	 * @param    array    $submittedData   Submitted fields' data
	 * @return   object
	 */
	public function updatePagesFields($currentFields, $submittedData)
	{
		$augmentedData = $this->_addModifiedIfAltered($submittedData);

		$updateResult = parent::batchUpdate($currentFields, $augmentedData);

		return $updateResult;
	}

}

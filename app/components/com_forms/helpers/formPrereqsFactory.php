<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$compnentPath = Component::path('com_forms');

require_once "$componentPath/helpers/factory.php";

use Components\Forms\Helpers\Factory;

class FormPrereqsFactory extends Factory
{

	protected $_modelName;

	/**
	 * Constructs FormPrereqsFactory instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$args['model_name'] = 'Components\Forms\Models\FormPrerequisite';

		parent::__construct($args);
	}

	/**
	 * Updates given forms associated prerequisites
	 *
	 * @param    object   $currentPrereqs   Form's current prerequisites
	 * @param    array    $newPrereqsData   Submitted prerequisites' data
	 * @return   object
	 */
	public function updateFormsPrereqs($currentPrereqs, $newPrereqsData)
	{
		return parent::batchUpdate($currentPrereqs, $newPrereqsData);
	}

}


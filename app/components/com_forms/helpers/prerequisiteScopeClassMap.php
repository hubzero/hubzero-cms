<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

class PrerequisiteScopeClassMap
{

	/**
	 * Maps prerequisite scope to class name
	 *
	 * @param    string   $scope   Prerequisites scope
	 * @return   string
	 */
	public function getClass($scope)
	{
		switch ($scope)
		{
			case 'forms_forms':
				$prereqClass = 'Components\Forms\Models\Form';
				break;
		}

		return $prereqClass;
	}

}

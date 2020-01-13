<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/renderableFormElement.php";

use Components\Forms\Helpers\RenderableFormElement;
use Hubzero\Utility\Arr;

class FormPageElementDecorator
{

	/**
	 * Decorates given for fields to facilitate in rendering
	 *
	 * @param    object   $pageElements   Page elements
	 * @param    int      $respondentId   ID of responding user
	 * @return   array
	 */
	public function decorateForRendering($pageElements, $respondentId)
	{
		$decoratedElements = [];

		foreach ($pageElements as $element)
		{
			$decoratedElements[] = $this->_decorateElement($element, $respondentId);
		}

		return $decoratedElements;
	}

	/**
	 * Decorates given for fields to facilitate in rendering
	 *
	 * @param    object   $pageElements   Page elements
	 * @param    int      $respondentId   ID of responding user
	 * @return   array
	 */
	protected function _decorateElement($element, $respondentId)
	{
		return new RenderableFormElement([
			'element' => $element,
			'respondent_id' => $respondentId
		]);
	}

}

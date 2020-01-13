<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/formsRouter.php";

use Components\Forms\Helpers\FormsRouter as RoutesHelper;
use Hubzero\Utility\Arr;

class PagesRouter
{

	/**
	 * Constructs PagesRouter instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_routes = Arr::getValue($args, 'routes', new RoutesHelper());
	}

	/**
	 * Returns URL for the next page
	 *
	 * @param    object   $page     Form page
	 * @return   string
	 */
	public function nextPageUrl($page)
	{
		$formId = $page->getFormId();

		if ($page->isLast())
		{
			$url = $this->_routes->formResponseReviewUrl($formId);
		}
		else
		{
			$nextPagePosition = $page->ordinalPosition() + 1;
			$url = $this->_routes->formsPageResponseUrl([
				'form_id' => $formId, 'ordinal' => $nextPagePosition
			]);
		}

		return $url;
	}

}

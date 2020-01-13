<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/pagesRouter.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\PagesRouter;
use Components\Forms\Tests\Traits\canMock;

class PagesRouterTest extends Basic
{
	use canMock;

	public function testNextPageUrlInvokesFormResponseReviewUrlIfPageIsLast()
	{
		$formId = 99;
		$page = $this->mock([
			'class' => 'Page',
			'methods' => ['isLast' => true, 'getFormId' => $formId]
		]);
		$routesHelper = $this->mock([
			'class' => 'FormsRouter', 'methods' => ['formResponseReviewUrl']
		]);
		$pagesRouter = new PagesRouter(['routes' => $routesHelper]);

		$routesHelper->expects($this->once())
			->method('formResponseReviewUrl')
			->with($formId);

		$pagesRouter->nextPageUrl($page);
	}

	public function testNextPageUrlInvokesFormsPageResponseUrlIfPageIsNotLast()
	{
		$formId = 99;
		$pagePosition = 22;
		$nextPagePosition = 22 + 1;
		$page = $this->mock([
			'class' => 'Page',
			'methods' => [
				'isLast' => false,
				'getFormId' => $formId,
				'ordinalPosition' => $pagePosition,
			]
		]);
		$routesHelper = $this->mock([
			'class' => 'FormsRouter', 'methods' => ['formsPageResponseUrl']
		]);
		$pagesRouter = new PagesRouter(['routes' => $routesHelper]);

		$routesHelper->expects($this->once())
			->method('formsPageResponseUrl')
			->with(['form_id' => $formId, 'ordinal' => $nextPagePosition]);

		$pagesRouter->nextPageUrl($page, 0);
	}

}

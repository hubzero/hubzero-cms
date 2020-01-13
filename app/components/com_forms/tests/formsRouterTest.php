<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/formsRouter.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\FormsRouter;

class FormsRouterTest extends Basic
{

	public function testFormsNewUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$expectedUrl = '/forms/forms/new';

		$generatedUrl = $routes->formsNewUrl();

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormsCreateUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$expectedUrl = '/forms/forms/create';

		$generatedUrl = $routes->formsCreateUrl();

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormsEditUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$testId = 99;
		$expectedUrl = "/forms/forms/$testId/manage";

		$generatedUrl = $routes->formsEditUrl($testId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormsUpdateUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$testId = 99;
		$expectedUrl = "/forms/forms/$testId/update";

		$generatedUrl = $routes->formsUpdateUrl($testId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormListUrlCorrectUrl()
	{
		$routes = new FormsRouter();
		$expectedUrl = '/forms/forms/list';

		$generatedUrl = $routes->formListUrl();

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testQueryUpdateUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$expectedUrl = '/forms/queries/update';

		$generatedUrl = $routes->queryUpdateUrl();

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormDisplayUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$testId = 99;
		$expectedUrl = "/forms/forms/$testId/display";

		$generatedUrl = $routes->formsDisplayUrl($testId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormsPagesUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$testId = 99;
		$expectedUrl = "/forms/pages?form_id=$testId";

		$generatedUrl = $routes->formsPagesUrl($testId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormsPagesNewUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$testId = 99;
		$expectedUrl = "/forms/pages/new?form_id=$testId";

		$generatedUrl = $routes->formsPagesNewUrl($testId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormsPagesCreateUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$testId = 99;
		$expectedUrl = "/forms/pages/create?form_id=$testId";

		$generatedUrl = $routes->formsPagesCreateUrl($testId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testPagesEditUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$testId = 99;
		$expectedUrl = "/forms/pages/$testId/edit";

		$generatedUrl = $routes->pagesEditUrl($testId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testPagesUpdateUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$testId = 99;
		$expectedUrl = "/forms/pages/$testId/update";

		$generatedUrl = $routes->pagesUpdateUrl($testId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testPagesFieldsEditUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$testId = 99;
		$expectedUrl = "/forms/fields?page_id=$testId";

		$generatedUrl = $routes->pagesFieldsEditUrl($testId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormResponseStartUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$testId = 99;
		$expectedUrl = "/forms/responses/start?form_id=$testId";

		$generatedUrl = $routes->formResponseStartUrl($testId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormsPageResponseUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$queryParams = ['form_id' => 99, 'page' => 1];
		$expectedUrl = '/forms/fill?form_id=99&page=1';

		$generatedUrl = $routes->formsPageResponseUrl($queryParams);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormsPrereqsUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$id = 99;
		$expectedUrl = "/forms/steps?form_id=$id";

		$generatedUrl = $routes->formsPrereqsUrl($id);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testPrereqsEditUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$id = 99;
		$expectedUrl = "/forms/steps/$id/edit";

		$generatedUrl = $routes->prereqsEditUrl($id);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testPrereqsNewUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$id = 99;
		$expectedUrl = "/forms/steps/new?form_id=$id";

		$generatedUrl = $routes->formsPrereqsNewUrl($id);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testPrereqsUpdateUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$id = 99;
		$expectedUrl = "/forms/steps/update?form_id=$id";

		$generatedUrl = $routes->prereqsUpdateUrl($id);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testPrereqsCreateUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$expectedUrl = "/forms/steps/create";

		$generatedUrl = $routes->prereqsCreateUrl();

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testBatchPagesUpdateUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$expectedUrl = "/forms/pages/batchupdate";

		$generatedUrl = $routes->batchPagesUpdateUrl();

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFieldsResponsesCreateUrlReturnsCorrectUrl()
	{
		$routes = new FormsRouter();
		$expectedUrl = "/forms/fill/create";

		$generatedUrl = $routes->fieldsResponsesCreateUrl();

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormResponseReviewReturnsCorrectUrl()
	{
		$expectedUrl = '/forms/responses/review?form_id=1';
		$formId = 1;
		$routes = new FormsRouter();

		$generatedUrl = $routes->formResponseReviewUrl($formId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormResponseSubmitReturnsCorrectUrl()
	{
		$expectedUrl = '/forms/responses/submit';
		$routes = new FormsRouter();

		$generatedUrl = $routes->formResponseSubmitUrl();

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormResponseListReturnsCorrectUrl()
	{
		$formId = 976;
		$expectedUrl = "/forms/admin/responses?form_id=$formId";
		$routes = new FormsRouter();

		$generatedUrl = $routes->formsResponseList($formId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testUserProfileUrlReturnsCorrectUrl()
	{
		$userId = 91;
		$expectedUrl = "/members/$userId";
		$routes = new FormsRouter();

		$generatedUrl = $routes->userProfileUrl($userId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testResponseFeedUrlReturnsCorrectUrlWithoutParams()
	{
		$responseId = 1;
		$expectedUrl = "/forms/responses/feed?response_id=$responseId";
		$routes = new FormsRouter();

		$generatedUrl = $routes->responseFeedUrl($responseId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testResponseFeedUrlReturnsCorrectUrlWithComment()
	{
		$responseId = 1;
		$urlParams = ['comment' => 'test comment'];
		$expectedUrl = "/forms/responses/feed?comment=test+comment&response_id=$responseId";
		$routes = new FormsRouter();

		$generatedUrl = $routes->responseFeedUrl($responseId, $urlParams);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testResponseFeedUrlReturnsCorrectUrlWithTagString()
	{
		$responseId = 1;
		$expectedUrl = "/forms/responses/feed?tag_string=a%2Cb%2Cc&response_id=$responseId";
		$urlParams = ['tag_string' => 'a,b,c'];
		$routes = new FormsRouter();

		$generatedUrl = $routes->responseFeedUrl($responseId, $urlParams);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testResponseApprovalUrlReturnsCorrectUrl()
	{
		$expectedUrl = "/forms/admin/response/approve";
		$routes = new FormsRouter();

		$generatedUrl = $routes->responseApprovalUrl();

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testUsersResponsesUrlReturnsCorrectUrl()
	{
		$expectedUrl = "/forms/responses/list";
		$routes = new FormsRouter();

		$generatedUrl = $routes->usersResponsesUrl();

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormResponsesExportUrl()
	{
		$id = 65;
		$expectedUrl = "/forms/admin/exportResponses?form_id=$id";
		$routes = new FormsRouter();

		$generatedUrl = $routes->formResponsesExportUrl($id);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormResponsesEmailUrl()
	{
		$formId = 65;
		$responseIds = [1, 2];
		$queryString = http_build_query([
			'form_id' => $formId, 'response_ids' => $responseIds
		]);
		$expectedUrl = "/forms/emailRespondents/responses?$queryString";
		$routes = new FormsRouter();

		$generatedUrl = $routes->responsesEmailUrl($formId, $responseIds);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testResponseEmailSendingUrl()
	{
		$expectedUrl = "/forms/emailRespondents/send";
		$routes = new FormsRouter();

		$generatedUrl = $routes->sendResponsesEmailUrl();

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testFormResponsesTaggingUrl()
	{
		$formId = 65;
		$responseIds = [1, 2];
		$queryString = http_build_query([
			'form_id' => $formId, 'response_ids' => $responseIds
		]);
		$expectedUrl = "/forms/tagResponses/responses?$queryString";
		$routes = new FormsRouter();

		$generatedUrl = $routes->responsesTagsUrl($formId, $responseIds);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testResponseTaggingUrl()
	{
		$expectedUrl = "/forms/tagResponses/addToManyResponses";
		$routes = new FormsRouter();

		$generatedUrl = $routes->tagResponsesUrl();

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testUserFieldResponsesUrl()
	{
		$responseId = 8;
		$expectedUrl = "/forms/admin/fieldresponses?response_id=$responseId";
		$routes = new FormsRouter();

		$generatedUrl = $routes->userFieldResponsesUrl($responseId);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testUpdateResponsesTagsUrl()
	{
		$expectedUrl = "/forms/tagResponses/updateResponseTags";
		$routes = new FormsRouter();

		$generatedUrl = $routes->updateResponsesTagsUrl();

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testCreateReponseCommentUrl()
	{
		$expectedUrl = "/forms/feedComments/createResponseComment";
		$routes = new FormsRouter();

		$generatedUrl = $routes->createResponseCommentUrl();

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testUsersFormPrereqsUrl()
	{
		$expectedUrl = "/forms/usersSteps/list?form_id=1&user_id=2";
		$routes = new FormsRouter();

		$generatedUrl = $routes->usersFormPrereqsUrl(1, 2);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testUsersFormPagesUrl()
	{
		$expectedUrl = "/forms/usersPages/list?form_id=1&user_id=2";
		$routes = new FormsRouter();

		$generatedUrl = $routes->usersFormPagesUrl(1, 2);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

}

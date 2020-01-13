<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/componentRouter.php";

class FormsRouter extends ComponentRouter
{

	/**
	 * Constructs FormsRouter instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$args['base_segment'] = 'forms';

		parent::__construct($args);
	}

	/**
	 * Generates forms new URL
	 *
	 * @return   string
	 */
	public function formsNewUrl()
	{
		$segments = ['forms', 'new'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates forms create URL
	 *
	 * @return   string
	 */
	public function formsCreateUrl()
	{
		$segments = ['forms', 'create'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates forms edit URL
	 *
	 * @param    int      $formId   ID of form to edit
	 * @return   string
	 */
	public function formsEditUrl($formId)
	{
		$segments = ['forms', $formId, 'manage'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates forms edit URL
	 *
	 * @param    int      $formId   ID of form to edit
	 * @return   string
	 */
	public function formsUpdateUrl($formId)
	{
		$segments = ['forms', $formId, 'update'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates form list URL
	 *
	 * @return   string
	 */
	public function formListUrl()
	{
		$segments = ['forms', 'list'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates search update url
	 *
	 * @return   string
	 */
	public function queryUpdateUrl()
	{
		$segments = ['queries', 'update'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates form display url
	 *
	 * @param    int      $formId   ID of form to edit
	 * @return   string
	 */
	public function formsDisplayUrl($formId)
	{
		$segments = ['forms', $formId, 'display'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates form's pages list url
	 *
	 * @param    int      $formId   ID of form associated with pages
	 * @return   string
	 */
	public function formsPagesUrl($formId)
	{
		$segments = ['pages'];
		$parameters = ['form_id' => $formId];

		$url = $this->_generateComponentUrl($segments, $parameters);

		return $url;
	}

	/**
	 * Generates form's pages new url
	 *
	 * @param    int      $formId   ID of form associated with pages
	 * @return   string
	 */
	public function formsPagesNewUrl($formId)
	{
		$segments = ['pages', 'new'];
		$parameters = ['form_id' => $formId];

		$url = $this->_generateComponentUrl($segments, $parameters);

		return $url;
	}

	/**
	 * Generates form's pages create url
	 *
	 * @param    int      $formId   ID of form associated with pages
	 * @return   string
	 */
	public function formsPagesCreateUrl($formId)
	{
		$segments = ['pages', 'create'];
		$parameters = ['form_id' => $formId];

		$url = $this->_generateComponentUrl($segments, $parameters);

		return $url;
	}

	/**
	 * Generates page's edit url
	 *
	 * @param    int      $pageId   ID of page to edit
	 * @return   string
	 */
	public function pagesEditUrl($pageId)
	{
		$segments = ['pages', $pageId, 'edit'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates page's update url
	 *
	 * @param    int      $pageId   ID of page to update
	 * @return   string
	 */
	public function pagesUpdateUrl($pageId)
	{
		$segments = ['pages', $pageId, 'update'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates page's field editing url
	 *
	 * @param    int      $pageId   ID of page to edit fields of
	 * @return   string
	 */
	public function pagesFieldsEditUrl($pageId)
	{
		$segments = ['fields'];
		$parameters = ['page_id' => $pageId];

		$url = $this->_generateComponentUrl($segments, $parameters);

		return $url;
	}

	/**
	 * Generates form response start URL
	 *
	 * @param    int      $formId   ID of form user is starting on
	 * @return   string
	 */
	public function formResponseStartUrl($formId)
	{
		$segments = ['responses', 'start'];
		$parameters = ['form_id' => $formId];

		$url = $this->_generateComponentUrl($segments, $parameters);

		return $url;
	}

	/**
	 * Generates URL to page response page
	 *
	 * @param    int      $params   Query params
	 * @return   string
	 */
	public function formsPageResponseUrl($params)
	{
		$segments = ['fill'];

		$url = $this->_generateComponentUrl($segments, $params);

		return $url;
	}

	/**
	 * Generates URL to forms prereqs page
	 *
	 * @param    int      $formId   Form ID
	 * @return   string
	 */
	public function formsPrereqsUrl($formId)
	{
		$segments = ['steps'];
		$params = ['form_id' => $formId];

		$url = $this->_generateComponentUrl($segments, $params);

		return $url;
	}

	/**
	 * Generates URL to forms prereqs page
	 *
	 * @param    int      $prereqId   ID of prereq to edit
	 * @return   string
	 */
	public function prereqsEditUrl($prereqId)
	{
		$segments = ['steps', $prereqId, 'edit'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates form's prereqs new url
	 *
	 * @param    int      $formId   ID of form associated with pages
	 * @return   string
	 */
	public function formsPrereqsNewUrl($formId)
	{
		$segments = ['steps', 'new'];
		$parameters = ['form_id' => $formId];

		$url = $this->_generateComponentUrl($segments, $parameters);

		return $url;
	}

	/**
	 * Generates form's prereqs update url
	 *
	 * @param    int      $formId   ID of form to update prereqs for
	 * @return   string
	 */
	public function prereqsUpdateUrl($formId)
	{
		$segments = ['steps', 'update'];
		$parameters = ['form_id' => $formId];

		$url = $this->_generateComponentUrl($segments, $parameters);

		return $url;
	}

	/**
	 * Generates prereqs create url
	 *
	 * @return   string
	 */
	public function prereqsCreateUrl()
	{
		$segments = ['steps', 'create'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates URL to field response creation task
	 *
	 * @return   string
	 */
	public function fieldsResponsesCreateUrl()
	{
		$segments = ['fill', 'create'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates form's prereqs update url
	 *
	 * @param    int      $formId   ID of form to update pages for
	 * @return   string
	 */
	public function batchPagesUpdateUrl()
	{
		$segments = ['pages', 'batchupdate'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates URL to form response review page
	 *
	 * @param    int      $formId   Given form's ID
	 * @return   string
	 */
	public function formResponseReviewUrl($formId)
	{
		$segments = ['responses', 'review'];
		$params = ['form_id' => $formId];

		$url = $this->_generateComponentUrl($segments, $params);

		return $url;
	}

	/**
	 * Generates form response submission URL
	 *
	 * @return   string
	 */
	public function formResponseSubmitUrl()
	{
		$segments = ['responses', 'submit'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates admin response review URL
	 *
	 * @param    int      $formId   Form ID
	 * @return   string
	 */
	public function formsResponseList($formId)
	{
		$params = ['form_id' => $formId];
		$segments = ['admin', 'responses'];

		$url = $this->_generateComponentUrl($segments, $params);

		return $url;
	}

	/**
	 * Generates URL to response feed page
	 *
	 * @param    int         $responseId   Given response's ID
	 * @param    array       $params       URL parameters
	 * @return   string
	 */
	public function responseFeedUrl($responseId, $params = [])
	{
		$segments = ['responses', 'feed'];
		$params['response_id'] = $responseId;

		$url = $this->_generateComponentUrl($segments, $params);

		return $url;
	}

	/**
	 * Generates URL for given user's profile
	 *
	 * @param    int   $userId   Given user's ID
	 * @return   string
	 */
	public function userProfileUrl($userId)
	{
		$segments = ['members', $userId];

		$url = $this->_generateHubUrl($segments);

		return $url;
	}

	/**
	 * Generates URL to form response approval task
	 *
	 * @return   string
	 */
	public function responseApprovalUrl()
	{
		$segments = ['admin', 'response', 'approve'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates URL to user's responses list
	 *
	 * @return   string
	 */
	public function usersResponsesUrl()
	{
		$segments = ['responses', 'list'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates URL to form response export task
	 *
	 * @param    int      $formId   Form's ID
	 * @return   string
	 */
	public function formResponsesExportUrl($formId)
	{
		$segments = ['admin', 'exportResponses'];
		$params = ['form_id' => $formId];

		$url = $this->_generateComponentUrl($segments, $params);

		return $url;
	}

	/**
	 * Generates URL to respondent email page
	 *
	 * @param    int        $formId        Form's ID
	 * @param    array      $responseIds   Responses' IDs
	 * @return   string
	 */
	public function responsesEmailUrl($formId, $responseIds)
	{
		$segments = ['emailRespondents', 'responses'];
		$params = ['form_id' => $formId, 'response_ids' => $responseIds];

		$url = $this->_generateComponentUrl($segments, $params);

		return $url;
	}

	/**
	 * Generates URL to send emails to respondents
	 *
	 * @return   string
	 */
	public function sendResponsesEmailUrl()
	{
		$segments = ['emailRespondents', 'send'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates URL to respondent tagging page
	 *
	 * @param    int        $formId        Form's ID
	 * @param    array      $responseIds   Responses' IDs
	 * @return   string
	 */
	public function responsesTagsUrl($formId, $responseIds)
	{
		$segments = ['tagResponses', 'responses'];
		$params = ['form_id' => $formId, 'response_ids' => $responseIds];

		$url = $this->_generateComponentUrl($segments, $params);

		return $url;
	}

	/**
	 * Generates URL to responses tagging action
	 *
	 * @return   string
	 */
	public function tagResponsesUrl()
	{
		$segments = ['tagResponses', 'addToManyResponses'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates URL to user's field responses for given response
	 *
	 * param     int      $responseId   Response's ID
	 * @return   string
	 */
	public function userFieldResponsesUrl($responseId)
	{
		$segments = ['admin', 'fieldresponses'];
		$params = ['response_id' => $responseId];

		$url = $this->_generateComponentUrl($segments, $params);

		return $url;
	}

	/**
	 * Generates URL to task to update a response's tags
	 *
	 * @return   string
	 */
	public function updateResponsesTagsUrl()
	{
		$segments = ['tagResponses', 'updateResponseTags'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates URL to task to update a response's tags
	 *
	 * @return   string
	 */
	public function createResponseCommentUrl()
	{
		$segments = ['feedComments', 'createResponseComment'];

		$url = $this->_generateComponentUrl($segments);

		return $url;
	}

	/**
	 * Generates URL to user's form prereqs list
	 *
	 * param     int      $formId   Form's ID
	 * param     int      $userId   User's ID
	 * @return   string
	 */
	public function usersFormPrereqsUrl($formId, $userId)
	{
		$segments = ['usersSteps', 'list'];
		$params = [
			'form_id' => $formId,
			'user_id' => $userId
		];

		$url = $this->_generateComponentUrl($segments, $params);

		return $url;
	}

	/**
	 * Generates URL to pages list for respondents
	 *
	 * param     int      $formId   Form's ID
	 * param     int      $userId   User's ID
	 * @return   string
	 */
	public function usersFormPagesUrl($formId, $userId)
	{
		$segments = ['usersPages', 'list'];
		$params = [
			'form_id' => $formId,
			'user_id' => $userId
		];

		$url = $this->_generateComponentUrl($segments, $params);

		return $url;
	}

}

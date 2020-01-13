<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Site\Controllers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/formsRouter.php";
require_once "$componentPath/helpers/mockProxy.php";
require_once "$componentPath/helpers/pageBouncer.php";
require_once "$componentPath/helpers/params.php";
require_once "$componentPath/helpers/query.php";
require_once "$componentPath/helpers/virtualCrudHelper.php";

use Components\Forms\Helpers\FormsRouter as RoutesHelper;
use Components\Forms\Helpers\MockProxy;
use Components\Forms\Helpers\PageBouncer;
use Components\Forms\Helpers\Params;
use Components\Forms\Helpers\Query;
use Components\Forms\Helpers\VirtualCrudHelper as CrudHelper;
use Hubzero\Component\SiteController;
use Hubzero\Utility\Arr;

class Queries extends SiteController
{

	/**
	 * Task mapping
	 *
	 * @var  array
	 */
	protected $_taskMap = [
		'__default' => 'update'
	];

	/**
	 * Parameter whitelist
	 *
	 * @var  array
	 */
	protected static $_paramWhitelist = [
		'archived',
		'fuzzy_end',
		'name',
		'closing_time',
		'closing_time_relative_operator',
		'disabled',
		'opening_time',
		'opening_time_relative_operator',
		'responses_locked',
	];

	/**
	 * Executes the requested task
	 *
	 * @return   void
	 */
	public function execute()
	{
		$this->bouncer = new PageBouncer([
			'component' => $this->_option
		]);
		$this->crudHelper = new CrudHelper([
			'errorSummary' => Lang::txt('COM_FORMS_QUERY_UPDATE_ERROR')
		]);
		$this->params = new Params(
			['whitelist' => self::$_paramWhitelist]
		);
		$this->router = new MockProxy(['class' => 'App']);
		$this->routes = new RoutesHelper();

		parent::execute();
	}

	/**
	 * Updates search query
	 *
	 * @return   void
	 */
	public function updateTask()
	{
		Request::checkToken();
		$this->bouncer->redirectUnlessAuthorized('core.create');

		$forwardingUrl = $this->routes->formListUrl();
		$queryData = $this->_getNonEmptyCriteria();

		$query = new Query();
		$query->setAssociative($queryData);

		if ($query->save())
		{
			$this->crudHelper->successfulCreate($forwardingUrl);
		}
		else
		{
			$this->crudHelper->failedCreate($query, $forwardingUrl);
		}
	}

	/**
	 * Filters out criteria without operator or value data
	 *
	 * @return
	 */
	protected function _getNonEmptyCriteria()
	{
		$criteria = $this->params->getArray('query');

		$filteredCriteria = array_filter($criteria, function($criterion) use($criteria) {
			$operatorPresent = $criterion['operator'] !== '';
			$valuePresent = $criterion['value'] !== '';

			return $operatorPresent && $valuePresent;
		});

		return $filteredCriteria;
	}

}

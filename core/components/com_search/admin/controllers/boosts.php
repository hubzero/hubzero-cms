<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Admin\Controllers;

$componentPath = Component::path('com_search');

require_once "$componentPath/helpers/boostFactory.php";
require_once "$componentPath/helpers/recordProcessingHelper.php";
require_once "$componentPath/helpers/typeOptionsHelper.php";
require_once "$componentPath/models/solr/boost.php";

use Components\Search\Helpers\BoostFactory;
use Components\Search\Helpers\RecordProcessingHelper;
use Components\Search\Helpers\TypeOptionsHelper;
use Components\Search\Models\Solr\Boost;
use Hubzero\Component\AdminController;

class Boosts extends AdminController
{

	protected $_taskMap = [
		'__default' => 'list'
	];

	public function execute()
	{
		$this->_crudHelper = new RecordProcessingHelper([
			'controller' => $this
		]);
		$this->_factory = new BoostFactory();
		$this->_typeOptionsHelper = new TypeOptionsHelper();

		parent::execute();
	}

	public function listTask()
	{
		$boosts = Boost::all();

		$this->view
			->set('boosts', $boosts)
			->display();
	}

	public function newTask($boost = null)
	{
		$boost = $boost ? $boost : Boost::blank();
		$typeOptions = $this->_typeOptionsHelper->getAllSorted();

		$this->view
			->set('boost', $boost)
			->set('typeOptions', $typeOptions)
			->display();
	}

	public function createTask()
	{
		$boostArray = Request::getArray('boost');
		$boost = $this->_factory->one($boostArray);

		if ($boost->save())
		{
			$message = Lang::txt('COM_SEARCH_CRUD_MESSAGES_BOOST_CREATE_SUCCESS');
			$redirectUrl = '/administrator/index.php?option=com_search&controller=boosts';
			$this->_crudHelper->handleSaveSuccess($message, $redirectUrl);
		}
		else
		{
			$this->_crudHelper->handleSaveFail($boost);
		}
	}

	public function editTask($boost = null)
	{
		$id = Request::getInt('id');
		$boost = $boost ? $boost : Boost::oneOrFail($id);
		$typeOptions = $this->_typeOptionsHelper->getAllSorted();

		$this->view
			->set('boost', $boost)
			->set('typeOptions', $typeOptions)
			->display();
	}

	public function updateTask()
	{
		$id = Request::getInt('id');
		$boost = Boost::oneOrFail($id);
		$params = Request::getArray('boost');

		$boost->set($params);

		if ($boost->save())
		{
			$message = Lang::txt('COM_SEARCH_CRUD_MESSAGES_BOOST_UPDATE_SUCCESS');
			$redirectUrl = '/administrator/index.php?option=com_search&controller=boosts';
			$this->_crudHelper->handleUpdateSuccess($message, $redirectUrl);
		}
		else
		{
			$this->_crudHelper->handleUpdateFail($boost);
		}
	}

}

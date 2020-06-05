<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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

	protected $crudHelper,
		$factory,
		$typeHelper;

	protected $_taskMap = [
		'__default' => 'list'
	];

	public function execute()
	{
		$this->crudHelper = new RecordProcessingHelper([
			'controller' => $this
		]);
		$this->factory = new BoostFactory();
		$this->typeHelper = new TypeOptionsHelper();

		parent::execute();
	}

	public function listTask()
	{
		$sortField = Request::getState(
			"$this->_option.$this->_controller.sort",
			'filter_order',
			'id'
		);
		$sortDirection = Request::getState(
			"$this->_option.$this->_controller.sortdir",
			'filter_order_Dir',
			'ASC'
		);

		$boosts = Boost::all()
			->order($sortField, $sortDirection)
			->paginated('limitstart', 'limit')
			->rows();

		$this->view
			->set('boosts', $boosts)
			->set('sortField', $sortField)
			->set('sortDirection', $sortDirection)
			->display();
	}

	public function newTask($boost = null)
	{
		$boost = $boost ? $boost : Boost::blank();
		$typeOptions = $this->typeHelper->getAllSorted();

		$this->view
			->set('boost', $boost)
			->set('typeOptions', $typeOptions)
			->display();
	}

	public function createTask()
	{
		$boostArray = Request::getArray('boost');
		$boost = $this->factory->one($boostArray);

		if ($boost->save())
		{
			$message = Lang::txt('COM_SEARCH_CRUD_MESSAGES_BOOST_CREATE_SUCCESS');
			$redirectUrl = '/administrator/index.php?option=com_search&controller=boosts';
			$this->crudHelper->handleSaveSuccess($message, $redirectUrl);
		}
		else
		{
			$this->crudHelper->handleSaveFail($boost);
		}
	}

	public function editTask($boost = null)
	{
		$id = Request::getInt('id');
		$boost = $boost ? $boost : Boost::oneOrFail($id);
		$typeOptions = $this->typeHelper->getAllSorted();

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
			$this->crudHelper->handleUpdateSuccess($message, $redirectUrl);
		}
		else
		{
			$this->crudHelper->handleUpdateFail($boost);
		}
	}

	public function destroyTask()
	{
		$id = Request::getInt('id');
		$boost = Boost::oneOrFail($id);

		if ($boost->destroy())
		{
			$message = Lang::txt('COM_SEARCH_CRUD_MESSAGES_BOOST_DESTROY_SUCCESS');
			$redirectUrl = '/administrator/index.php?option=com_search&controller=boosts';
			$this->crudHelper->handleDestroySuccess($message, $redirectUrl);
		}
		else
		{
			$message = Lang::txt('COM_SEARCH_CRUD_MESSAGES_BOOST_DESTROY_FAIL');
			$redirectUrl = "/administrator/index.php?option=com_search&controller=boosts&task=edit&id=$id";
			$this->crudHelper->handleDestroyFail($message, $redirectUrl);
		}
	}

}

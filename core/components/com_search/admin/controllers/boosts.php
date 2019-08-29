<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Admin\Controllers;

$resourceComponentPath = Component::path('com_resources');
$componentPath = Component::path('com_search');

require_once "$resourceComponentPath/models/type.php";
require_once "$componentPath/helpers/boostFactory.php";
require_once "$componentPath/helpers/errorMessageHelper.php";
require_once "$componentPath/models/solr/boost.php";

use Components\Resources\Models\Type;
use Components\Search\Helpers\BoostFactory;
use Components\Search\Helpers\ErrorMessageHelper;
use Components\Search\Models\Solr\Boost;
use Hubzero\Component\AdminController;

class Boosts extends AdminController
{

	protected $_taskMap = [
		'__default' => 'list'
	];

	public function execute()
	{
		$this->_factory = new BoostFactory();
		$this->_errorMessageHelper = new ErrorMessageHelper();

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
		$resourceTypes = Type::all()
			->rows()
			->fieldsByKey('type');
		$supplementaryDocumentTypes = [
			Lang::txt('COM_SEARCH_BOOST_DOCUMENT_TYPE_CITATION')
		];

		$typeOptions = array_merge($resourceTypes, $supplementaryDocumentTypes);

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
			Notify::success('Boost created');
			App::redirect('/administrator/index.php?option=com_search&controller=boosts');
		}
		else
		{
			$errors = $boost->getErrors();
			$errorMessage = $this->_errorMessageHelper->generateErrorMessage($errors);
			Notify::error($errorMessage);
			$this->setView($this->name, 'new');
			$this->newTask($boost);
		}
	}

}

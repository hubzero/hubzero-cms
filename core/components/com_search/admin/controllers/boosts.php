<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Admin\Controllers;

$componentPath = Component::path('com_search');

require_once "$componentPath/models/solr/boost.php";

use Components\Search\Models\Solr\Boost;
use Hubzero\Component\AdminController;

class Boosts extends AdminController
{

	protected $_taskMap = [
		'__default' => 'list'
	];

	public function listTask()
	{
		$boosts = Boost::all();

		$this->view
			->set('boosts', $boosts)
			->display();
	}

}

<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Search\Models\Solr\Blacklist;
use Components\Search\Models\Solr\Facet;
use Components\Search\Models\Solr\SearchComponent;
use \Hubzero\Search\Query;
use \Hubzero\Search\Index;
use Components\Search\Helpers\SolrHelper;
use Components\Search\Helpers\DiscoveryHelper;
use Components\Developer\Models\Application;
use Hubzero\Access\Group as Accessgroup;
use stdClass;
use Hubzero\Utility\Inflector as Inflector;

require_once Component::path('com_search') . DS . 'helpers' . DS . 'solr.php';
require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'blacklist.php';
require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'searchcomponent.php';
require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'facet.php';
require_once Component::path('com_developer') . DS . 'models' . DS . 'application.php';

/**
 * Search AdminController Class
 */
class Searchable extends AdminController
{

	/**
	 * Manage facets
	 * 
	 * @return  void
	 */
	public function displayTask()
	{
		// Load the subfacets, if applicable
		$components = SearchComponent::all()
			->rows();
		$this->view
			->set('components', $components)
			->display();
	}

	public function activateIndexTask()
	{
		$ids = Request::getArray('id', array());
		$offset = Request::getInt('offset', 0);
		$numProcess = Request::getInt('numprocess');
		$components = SearchComponent::all()
			->whereIn('id', $ids)
			->where('state', 'IS', null, 'AND', 1)
			->orWhereEquals('state', 0, 1)
			->rows();
		foreach ($components as $component)
		{
			$recordsIndexed = $component->indexSearchResults($offset);
			if (!$recordsIndexed)
			{
				$component->set('state', 1);
				$recordsIndexed['state'] = 1;
				$recordsIndexed['link'] = Route::url('index.php?option=' . $this->_option . '&controller=searchable&task=deleteIndex&id=' . $component->get('id'), false);
			}
			else
			{
				$component->set('indexed_records', $recordsIndexed['offset']);
				$recordsIndexed['state'] = 0;
				$recordsIndexed['numprocess'] = empty($numProcess) ? $component->getSearchCount() : $numProcess;
				$recordsIndexed['numprocess'] .= ' Batches';
			}
			$component->save();
			header('Content-Type: application/json');
			echo json_encode($recordsIndexed);
			exit();
		}

		App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=searchable', false));
	}

	public function deleteIndexTask()
	{
		$ids = Request::getArray('id', array());
		$components = SearchComponent::all()
			->whereIn('id', $ids)
			->whereEquals('state', 1)
			->rows();
		foreach ($components as $component)
		{
			$searchIndex = new \Hubzero\Search\Index($this->config);
			$componentName = Inflector::singularize($component->name);
			$deleteQuery = array('hubtype' => $componentName);
			$searchIndex->delete($deleteQuery);
			$component->set('state', 0);
			$date = Date::of()->toSql();
			$component->set('indexed', null);
			$component->set('indexed_records', 0);
			if ($component->save())
			{
				Notify::success(Lang::txt('COM_SEARCH_DELETE_COMPONENT_SUCCESS', ucfirst($component->name)));
			}
		}

		App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=searchable', false));
	}

	public function newTagsTask()
	{
		$resources = \Components\Publications\Models\Orm\Publication::one(1);
		print_r($resources->searchResult());
	}

	public function discoverTask()
	{
		$componentModel = new \Components\Search\Models\Solr\SearchComponent();
		$components = $componentModel->getNewComponents();
		if ($components->count() > 0)
		{
			if ($components->save())
			{
				\Notify::success('New Searchable Components found');
			}
		}
		else
		{
			\Notify::warning('No new components found.');
		}

		App::redirect(
			Route::url('index.php?option=com_search&task=display&controller=searchable', false)
		);
	}
}

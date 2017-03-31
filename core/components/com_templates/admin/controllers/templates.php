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

namespace Components\Templates\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Templates\Models\Style;
use Components\Templates\Models\Template;
use Request;
use Notify;
use Route;
use Lang;
use App;

/**
 * Templates controller for templates
 */
class Templates extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('apply', 'save');
		$this->registerTask('save2copy', 'save');

		parent::execute();
	}

	/**
	 * List all entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'filter_search',
				''
			)),
			'client_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.client_id',
				'filter_client_id',
				'*'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'name'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$model = Template::all()
			->whereEquals('type', 'template');

		if ($filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);

			if (stripos($filters['search'], 'id:') === 0)
			{
				$model->whereEquals('id', (int) substr($filters['search'], 3));
			}
			else
			{
				$model->whereLike('element', $filters['search'], 1)
					->orWhereLike('name', $filters['search'], 1)
					->resetDepth();
			}
		}

		if ($filters['client_id'] && $filters['client_id'] != '*')
		{
			$model->whereEquals('client_id', (int)$filters['client_id']);
		}

		// Get records
		$rows = $model
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$preview = $this->config->get('template_positions_display');

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->set('preview', $preview)
			->display();
	}

	/**
	 * Display template details and files
	 *
	 * @return  void
	 */
	public function filesTask()
	{
		// Access checks.
		if (!User::authorise('core.edit', $this->option)
		 || !User::authorise('core.create', $this->option))
		{
			App::abort(403, Lang::txt('JERROR_CORE_ACTION_NOT_PERMITTED'));
		}

		Request::setVar('hidemainmenu', 1);

		// Incoming
		$id = Request::getVar('id', array(0));

		if (is_array($id) && !empty($id))
		{
			$id = $id[0];
		}

		$template = Template::oneOrNew(intval($id));

		$files = $template->files();

		// Output the HTML
		$this->view
			->set('template', $template)
			->set('files', $files)
			->setLayout('files')
			->display();
	}
}

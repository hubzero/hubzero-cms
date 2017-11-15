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
use Hubzero\Form\Form;
use Components\Templates\Models\Style;
use Filesystem;
use Request;
use Notify;
use Route;
use Lang;
use App;

/**
 * Templates controller for styles
 */
class Styles extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
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
			'template' => Request::getState(
				$this->_option . '.' . $this->_controller . '.template',
				'filter_template',
				'0'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$model = Style::all();

		$e = '#__extensions';
		$l = '#__languages';
		$m = '#__menu';
		$s = $model->getTableName();

		$model
			->select($s . '.id')
			->select($s . '.template')
			->select($s . '.title')
			->select($s . '.home')
			->select($s . '.client_id')
			->select('0', 'assigned');

		// Join on menus.
		$model
			->select($m . '.template_style_id', 'assigned', true)
			->join($m, $m . '.template_style_id', $s . '.id', 'left');

		// Join over the language
		$model
			->select($l . '.title', 'language_title')
			->select($l . '.image')
			->join($l, $l . '.lang_code', $s . '.home', 'left');

		// Filter by extension enabled
		$model
			->select($e . '.extension_id', 'e_id')
			->join($e, $e . '.element', $s . '.template', 'left')
			->whereRaw($e . '.client_id = ' . $s . '.client_id')
			->whereEquals($e . '.enabled', 1)
			->whereEquals($e . '.type', 'template');

		if ($filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);

			if (stripos($filters['search'], 'id:') === 0)
			{
				$model->whereEquals($s . '.id', (int) substr($filters['search'], 3));
			}
			else
			{
				$model->whereLike($s . '.title', $filters['search'], 1)
					->orWhereLike($s . '.template', $filters['search'], 1)
					->resetDepth();
			}
		}

		if ($filters['client_id'] != '*')
		{
			$model->whereEquals($s . '.client_id', (int)$filters['client_id']);
		}

		if ($filters['template'])
		{
			$model->whereEquals($s . '.template', (int)$filters['template']);
		}

		$model
			->group($s . '.id')
			->group($s . '.template')
			->group($s . '.title')
			->group($s . '.home')
			->group($s . '.client_id')
			->group($l . '.title')
			->group($l . '.image')
			->group($e . '.extension_id');

		// Get records
		$rows = $model
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Check if there are no matching items
		if (!$rows->count())
		{
			Notify::warning(Lang::txt('COM_TEMPLATES_MSG_MANAGE_NO_STYLES'));
		}

		$preview = $this->config->get('template_positions_display');

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->set('preview', $preview)
			->display();
	}

	/**
	 * Edit an entry
	 *
	 * @param   object  $style
	 * @return  void
	 */
	public function editTask($style=null)
	{
		// Access checks.
		if (!User::authorise('core.edit', $this->option)
		 || !User::authorise('core.create', $this->option))
		{
			App::abort(403, Lang::txt('JERROR_CORE_ACTION_NOT_PERMITTED'));
		}

		Request::setVar('hidemainmenu', 1);

		// Load a tag object if one doesn't already exist
		if (!($style instanceof Style))
		{
			// Incoming
			$id = Request::getVar('id', array(0));

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			$style = Style::oneOrNew(intval($id));
		}

		$client   = \Hubzero\Base\ClientManager::client($style->get('client_id'));

		$template = $style->get('template');

		$base = PATH_CORE;
		if (is_dir(PATH_APP . '/templates/' . $template))
		{
			$base = PATH_APP;
		}

		$formFile = Filesystem::cleanPath($base . '/templates/' . $template . '/templateDetails.xml');

		$form = new Form('style', array('control' => 'fields'));

		// Load the core and/or local language file(s).
		Lang::load('tpl_' . $template, $base . '/bootstrap/' . $client->name, null, false, true) ||
		Lang::load('tpl_' . $template, $base . '/templates/' . $template, null, false, true);

		if (file_exists($formFile))
		{
			// Get the template form.
			if (!$form->loadFile($formFile, false, '//config'))
			{
				App::abort(500, Lang::txt('JERROR_LOADFILE_FAILED'));
			}
		}

		$data = array();
		$data['params'] = with(new \Hubzero\Config\Registry($style->get('params')))->toArray();

		$form->bind($data);

		// Output the HTML
		$this->view
			->set('item', $style)
			->set('form', $form)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Access checks.
		if (!User::authorise('core.edit', $this->option)
		 || !User::authorise('core.create', $this->option))
		{
			App::abort(403, Lang::txt('JERROR_CORE_ACTION_NOT_PERMITTED'));
		}

		$fields = Request::getVar('fields', array(), 'post');

		$style = Style::oneOrNew(intval($fields['id']))->set($fields);

		$method = 'save';
		if ($this->getTask() == 'save2copy')
		{
			$method = 'duplicate';
		}

		$result = Event::trigger(
			'extension.onExtensionBeforeSave',
			array('com_templates.style', $style, ($fields['id'] ? false : true))
		);

		if (in_array(false, $result, true))
		{
			Notify::error($style->getError());
			return $this->editTask($style);
		}

		// Store new content
		if (!$style->$method())
		{
			Notify::error($style->getError());
			return $this->editTask($style);
		}

		if (User::authorise('core.edit', 'com_menus') && $style->get('client_id') == 0)
		{
			$assigned = Request::getVar('assigned', array(), 'post');

			$n  = 0;
			$db = App::get('db');

			if (!empty($assigned) && is_array($assigned))
			{
				\Hubzero\Utility\Arr::toInteger($assigned);

				// Update the mapping for menu items that this style IS assigned to.
				$query = $db->getQuery()
					->update('#__menu')
					->set(array('template_style_id' => (int) $style->get('id')))
					->whereIn('id', $assigned)
					->where('template_style_id', '!=', (int) $style->get('id'))
					->whereIn('checked_out', array(0, (int) User::get('id')));

				$db->setQuery($query->toString());
				$db->query();
				$n += $db->getAffectedRows();
			}

			// Remove style mappings for menu items this style is NOT assigned to.
			// If unassigned then all existing maps will be removed.
			$query = $db->getQuery()
				->update('#__menu')
				->set(array('template_style_id' => 0));
			if (!empty($assigned))
			{
				$query->whereRaw('id', 'NOT IN (' . implode(',', $assigned) . ')');
			}
			$query->whereEquals('template_style_id', (int) $style->get('id'));
			$query->whereIn('checked_out', array(0, (int) User::get('id')));

			$db->setQuery($query->toString());
			$db->query();

			$n += $db->getAffectedRows();
			if ($n > 0)
			{
				Notify::success(Lang::txts('COM_TEMPLATES_MENU_CHANGED', $n));
			}
		}

		// Clean the cache.
		$this->cleanCache();

		// Trigger the onExtensionAfterSave event.
		Event::trigger(
			'extension.onExtensionAfterSave',
			array('com_templates.style', $style, ($fields['id'] ? false : true))
		);

		Notify::success(Lang::txt('COM_TEMPLATES_STYLE_SAVE_SUCCESS'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($style);
		}

		$this->cancelTask();
	}

	/**
	 * Remove one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Access checks.
		if (!User::authorise('core.delete', $this->option))
		{
			App::abort(403, Lang::txt('JERROR_CORE_DELETE_NOT_PERMITTED'));
		}

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$i = 0;

		foreach ($ids as $id)
		{
			$style = Style::oneOrFail(intval($id));

			if ($style->get('home'))
			{
				Notify::warning(Lang::txt('COM_TEMPLATES_STYLE_CANNOT_DELETE_DEFAULT_STYLE'));
				continue;
			}

			if (!$style->destroy())
			{
				Notify::error($style->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			// Clean the cache.
			$this->cleanCache();

			Notify::success(Lang::txt('COM_TEMPLATES_STYLES_REMOVED'));
		}

		$this->cancelTask();
	}

	/**
	 * Set the style as default
	 *
	 * @return  void
	 */
	public function setDefaultTask()
	{
		$id = Request::getVar('id', array(0));
		$id = is_array($id) ? $id[0] : $id;

		if ($id)
		{
			$style = Style::oneOrFail(intval($id));
			$style->set('home', 1);

			if (!$style->save())
			{
				Notify::error($style->getError());
			}

			// Clean the cache.
			$this->cleanCache();
		}

		$this->cancelTask();
	}

	/**
	 * Duplicate an entry
	 *
	 * @return  void
	 */
	public function duplicateTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Access checks.
		if (!User::authorise('core.create', $this->option))
		{
			App::abort(403, Lang::txt('JERROR_CORE_ACTION_NOT_PERMITTED'));
		}

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$i = 0;

		foreach ($ids as $id)
		{
			$style = Style::oneOrFail(intval($id));

			if (!$style->duplicate())
			{
				Notify::error($style->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			// Clean the cache.
			$this->cleanCache();
		}

		$this->cancelTask();
	}

	/**
	 * Custom clean cache method
	 *
	 * @param   string   $group
	 * @param   integer  $client_id
	 * @return  void
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		Cache::clean('com_templates');
		Cache::clean('_system');

		Event::trigger('system.onCleanCache', array($group, $client_id));
	}
}

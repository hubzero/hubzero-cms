<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Admin\Controllers;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'sponsor.php';

use Hubzero\Component\AdminController;
use Components\Citations\Models\Sponsor;
use Request;
use Notify;
use Lang;
use App;

/**
 * Controller class for citation types
 */
class Sponsors extends AdminController
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

		parent::execute();
	}

	/**
	 * List types
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$sponsors = Sponsor::all();

		// Output the HTML
		$this->view
			->set('sponsors', $sponsors)
			->display();
	}

	/**
	 * Edit an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!($row instanceof Sponsor))
		{
			// Incoming
			$id = Request::getArray('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			$row = Sponsor::oneOrNew($id);
		}

		// Output the HTML
		$this->view
			->set('config', $this->config)
			->set('sponsor', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a type
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$s = Request::getArray('sponsor', array(), 'post');
		$sponsorId = !empty($s['id']) ? $s['id'] : null;
		unset($s['id']);

		$row = Sponsor::oneOrNew($sponsorId);
		$row->set($s);

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('CITATION_SPONSOR_SAVED'));

		$this->cancelTask();
	}

	/**
	 * Remove one or more types
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$removed = 0;

		foreach ($ids as $id)
		{
			$sponsor = Sponsor::oneOrFail(intval($id));

			if (!$sponsor->destroy())
			{
				Notify::error(Lang::txt('CITATION_SPONSOR_NOT_REMOVED', $id));
				continue;
			}

			$sponsor->citations()->sync(array());

			$removed++;
		}

		if ($removed)
		{
			Notify::success(Lang::txt('CITATION_SPONSOR_REMOVED'));
		}

		$this->cancelTask();
	}
}

<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Story\Models\Comment;
use Components\Story\Models\Discussion;
use Request;
use Notify;
use Lang;
use User;
use App;

/**
 * Discussions, and the repair that keeps their trees honest
 */
class Comments extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('__default', 'display');

		parent::execute();
	}

	/**
	 * The discussions, with what is actually in them
	 *
	 * The stored count and the counted count are shown side by side on
	 * purpose: a denormalised number that nobody ever checks is how a column
	 * drifts for a year without anyone noticing.
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$rows = Discussion::all()
			->order('last_activity', 'desc')
			->paginated('limitstart', 'limit')
			->rows();

		$this->view
			->set('rows', $rows)
			->display();
	}

	/**
	 * Put `path` and `depth` back in step with `parent`
	 *
	 * Safe to run at any time and against any state of the two derived
	 * columns, which is the property that makes storing them defensible in
	 * the first place. It reports how many rows it had to correct, so a zero
	 * is a meaningful answer rather than a silent one.
	 *
	 * @return  void
	 */
	public function rebuildTask()
	{
		Request::checkToken(array('get', 'post'));

		if (!User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$ids = Request::getArray('id', array());

		if (!$ids && $id = Request::getInt('discussion', 0))
		{
			$ids = array($id);
		}

		if (!$ids)
		{
			foreach (Discussion::all()->rows() as $row)
			{
				$ids[] = $row->get('id');
			}
		}

		$corrected = 0;

		foreach ($ids as $id)
		{
			$corrected += Comment::rebuildPaths((int) $id);

			Discussion::oneOrNew((int) $id)->recount();
		}

		Notify::success(Lang::txt('COM_STORY_TREES_REBUILT', count($ids), $corrected));

		$this->cancelTask();
	}
}

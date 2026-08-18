<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Saml\Models\SamlSession;
use Components\Saml\Models\ServiceProvider;
use Request;
use Notify;
use User;
use Lang;
use App;

/**
 * Controller class for the SSO session audit trail
 *
 * Rows are never pruned automatically (DESIGN.md §13.3) — cleanup happens
 * here, via "delete expired" or selected-row delete.
 */
class Sessions extends AdminController
{
	/**
	 * Display the SSO session audit trail
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'sp' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sp',
				'sp',
				0,
				'int'
			),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				-1,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'created'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			)
		);

		$records = SamlSession::all();

		if ($filters['sp'])
		{
			$records->whereEquals('sp_id', (int) $filters['sp']);
		}

		if ($filters['state'] >= 0)
		{
			$records->whereEquals('state', (int) $filters['state']);
		}

		$rows = $records
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		$sps = ServiceProvider::all()
			->ordered()
			->rows();

		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->set('sps', $sps)
			->set('sessionTableIsAuthoritative', SamlSession::sessionTableIsAuthoritative())
			->display();
	}

	/**
	 * Remove selected entries
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

		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$i = 0;

		foreach ($ids as $id)
		{
			// A stale id must not abort the rest of the batch
			$row = SamlSession::oneOrNew(intval($id));

			if (!$row->get('id'))
			{
				continue;
			}

			if (!$row->destroy())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_SAML_ITEMS_REMOVED', $i));
		}

		$this->cancelTask();
	}

	/**
	 * Bulk-remove expired rows: ended sessions plus rows whose hub session
	 * no longer exists
	 *
	 * @return  void
	 */
	public function deleteexpiredTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$i = 0;

		foreach (SamlSession::allExpired()->rows() as $row)
		{
			if ($row->destroy())
			{
				$i++;
			}
		}

		Notify::success(Lang::txt('COM_SAML_SESSIONS_EXPIRED_REMOVED', $i));

		$this->cancelTask();
	}
}

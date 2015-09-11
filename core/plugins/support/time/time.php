<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'record.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'permissions.php';

use Components\Time\Models\Record;
use Components\Time\Models\Permissions;

/**
 * Plugin for adding time records from a support ticket
 */
class plgSupportTime extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Called on the ticket comment form
	 *
	 * @param   object  $ticket
	 * @return  string
	 */
	public function onTicketComment($ticket)
	{
		// Check permissions
		$permissions = new Permissions('com_time');
		if (!$permissions->can('save.records'))
		{
			return;
		}

		$view         = $this->view('default', 'create');
		$view->ticket = $ticket;
		$view->row    = Record::blank();

		return $view->loadTemplate();
	}

	/**
	 * Called after updating a ticket
	 *
	 * @param   object  $ticket
	 * @param   object  $comment
	 * @return  void
	 */
	public function onTicketUpdate($ticket, $comment)
	{
		// Check permissions
		$permissions = new Permissions('com_time');
		if (!$permissions->can('save.records'))
		{
			return;
		}

		// Create object
		$record = Record::blank()->set(
		[
			'task_id'     => Request::getInt('task_id'),
			'user_id'     => User::get('id'),
			'time'        => Request::getInt('htime') . '.' . Request::getInt('mtime'),
			'date'        => Date::of(Request::getVar('date') . ' 8:00:00', Config::get('offset'))->toSql(),
			'description' => $comment->get('comment')
		]);

		// Set end based on start + time length
		$record->set('end', date('Y-m-d H:i:s', (strtotime($record->date) + ($record->time*3600))));

		// Don't attempt to save a record if no time or task was chosen
		if (!$record->time || $record->time == 0.0 || !$record->task_id)
		{
			return;
		}

		if (!$record->save())
		{
			if (Config::get('debug'))
			{
				// Something went wrong...return errors
				foreach ($record->getErrors() as $error)
				{
					Notify::error($error);
				}
			}
			else
			{
				Notify::error(Lang::txt('Failed to save time record.'));
			}
		}
	}
}
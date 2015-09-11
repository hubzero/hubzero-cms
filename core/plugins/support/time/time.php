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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
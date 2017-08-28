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
 * @author    Zach Weidner <zweidner@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Components\Members\Models\Member;
use Components\Members\Models\Profile\Field;
use Components\Members\Helpers\Permissions;
use Hubzero\Component\AdminController;
use Request;
use Config;
use Event;
use User;
use Date;
use Lang;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'permissions.php';
include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'profile' . DS . 'field.php';

/**
 * Member exporter
 */
class Exports extends AdminController
{
	/**
	 * Determine task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		if (!Permissions::getActions('component')->get('core.admin'))
		{
			App::redirect(
				Route::url('index.php?option=com_members', false),
				Lang::txt('Not authorized'),
				'warning'
			);
		}

		Lang::load($this->_option . '.export', dirname(__DIR__));

		parent::execute();
	}

	/**
	 * Display exports
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view
			->setLayout('display')
			->display();
	}

	/**
	 * Run Export
	 *
	 * @return  void
	 */
	public function runTask()
	{
		$skip = array('password', 'params', 'usertype');
		$keys = array();
		$tags = array();

		$members = Member::blank();
		$attribs = $members->getStructure()->getTableColumns($members->getTableName());

		foreach ($attribs as $key => $desc)
		{
			if (in_array(strtolower($key), $skip))
			{
				continue;
			}

			$keys[$key] = $key;
		}

		$attribs = Field::all()
			->ordered()
			->rows();

		foreach ($attribs as $attrib)
		{
			if ($attrib->get('type') == 'tags')
			{
				array_push($tags, $attrib->get('name'));
			}

			if ($attrib->get('type') == 'address')
			{
				$keys[$attrib->get('name')] = $attrib->get('name') . ' Street 1';
				$keys['_' . $attrib->get('name') . '_address2']   = $attrib->get('name') . ' Street 2';
				$keys['_' . $attrib->get('name') . '_city']      = $attrib->get('name') . ' City';
				$keys['_' . $attrib->get('name') . '_postal']    = $attrib->get('name') . ' Post Code';
				$keys['_' . $attrib->get('name') . '_region']    = $attrib->get('name') . ' Region';
				$keys['_' . $attrib->get('name') . '_country']   = $attrib->get('name') . ' Country';
				$keys['_' . $attrib->get('name') . '_latitude']  = $attrib->get('name') . ' Latitude';
				$keys['_' . $attrib->get('name') . '_longitude'] = $attrib->get('name') . ' Longitude';
				continue;
			}

			$keys[$attrib->get('name')] = $attrib->get('name');
		}

		$results = Event::trigger('members.onExportMemberKeys', array($keys));
		foreach ($results as $result)
		{
			if (!is_array($result))
			{
				continue;
			}

			foreach ($result as $k => $v)
			{
				$keys[$k] = $v;
			}
		}

		// Get request vars
		$delimiter = Request::getVar('delimiter', ',');

		$path = Config::get('tmp_path') . DS . 'members.csv';
		$file = fopen($path, 'w');
		fputcsv($file, $keys);

		// Get filters
		$filters = array(
			'search'       => urldecode(Request::getVar('search', '')),
			'sort'         => Request::getWord('filter_order', 'registerDate'),
			'sort_Dir'     => Request::getWord('filter_order_Dir', 'DESC'),
			'registerDate' => Request::getVar('registerDate', ''),
			'activation'   => Request::getInt('activation', 0),
			'state'        => Request::getVar('state', '*'),
			'access'       => Request::getInt('access', 0),
			'approved'     => Request::getVar('approved', '*'),
			'group_id'     => Request::getInt('group_id', 0),
			'range'        => Request::getVar('range', '')
		);

		$a = $members->getTableName();
		$b = '#__user_usergroup_map';

		$members
			->select($a . '.*')
			->including(['accessgroups', function ($accessgroup){
				$accessgroup
					->select('*');
			}])
			->including(['notes', function ($note){
				$note
					->select('id')
					->select('user_id');
			}]);

		if ($filters['group_id'])
		{
			$members
				->join($b, $b . '.user_id', $a . '.id', 'left')
				->whereEquals($b . '.group_id', (int)$filters['group_id']);
		}

		if ($filters['search'])
		{
			if (is_numeric($filters['search']))
			{
				$members->whereEquals($a . '.id', (int)$filters['search']);
			}
			else
			{
				$members->whereLike($a . '.name', strtolower((string)$filters['search']), 1)
					->orWhereLike($a . '.username', strtolower((string)$filters['search']), 1)
					->orWhereLike($a . '.email', strtolower((string)$filters['search']), 1)
					->resetDepth();
			}
		}

		if ($filters['registerDate'])
		{
			$members->where($a . '.registerDate', '>=', $filters['registerDate']);
		}

		if ($filters['access'] > 0)
		{
			$members->whereEquals($a . '.access', (int)$filters['access']);
		}

		if (is_numeric($filters['state']))
		{
			$members->whereEquals($a . '.block', (int)$filters['state']);
		}

		if (is_numeric($filters['approved']))
		{
			$members->whereEquals($a . '.approved', (int)$filters['approved']);
		}

		if ($filters['activation'] < 0)
		{
			$members->where($a . '.activation', '<', 0);
		}
		if ($filters['activation'] > 0)
		{
			$members->where($a . '.activation', '>', 0);
		}

		// Apply the range filter.
		if ($filters['range'])
		{
			// Get UTC for now.
			$dNow = Date::of('now');
			$dStart = clone $dNow;

			switch ($filters['range'])
			{
				case 'past_week':
					$dStart->modify('-7 day');
					break;

				case 'past_1month':
					$dStart->modify('-1 month');
					break;

				case 'past_3month':
					$dStart->modify('-3 month');
					break;

				case 'past_6month':
					$dStart->modify('-6 month');
					break;

				case 'post_year':
				case 'past_year':
					$dStart->modify('-1 year');
					break;

				case 'today':
					// Ranges that need to align with local 'days' need special treatment.
					$offset = Config::get('offset');

					// Reset the start time to be the beginning of today, local time.
					$dStart = Date::of('now', $offset);
					$dStart->setTime(0, 0, 0);

					// Now change the timezone back to UTC.
					$tz = new \DateTimeZone('GMT');
					$dStart->setTimezone($tz);
					break;
			}

			if ($filters['range'] == 'post_year')
			{
				$members->where($a . '.registerDate', '<', $dStart->format('Y-m-d H:i:s'));
			}
			else
			{
				$members->where($a . '.registerDate', '>=', $dStart->format('Y-m-d H:i:s'));
				$members->where($a . '.registerDate', '<=', $dNow->format('Y-m-d H:i:s'));
			}
		}

		// Get records
		$rows = $members
			->order($a . '.' . $filters['sort'], $filters['sort_Dir'])
			->rows();

		// Convert to array and bind to object below
		// This may seem counter-intuitive but it's for
		// performance reasons. Otherwise, all the circular
		// references eat up memery.
		$rows = $rows->toArray();

		// Gather up member information
		foreach ($rows as $row)
		{
			$member	= Member::blank()->set($row);

			$tmp = array();

			foreach ($keys as $key => $label)
			{
				if (substr($key, 0, 1) == '_')
				{
					if (!isset($tmp[$key]))
					{
						$tmp[$key] = '';
					}
					continue;
				}

				if (in_array($key, $tags))
				{
					$val = $member->tags('string');
				}
				else
				{
					$val = $member->get($key);
				}

				if (is_array($val))
				{
					$val = implode(';', $val);
				}
				else
				{
					if (strstr($val, '{'))
					{
						$v = json_decode((string)$val, true);

						if (!$v || json_last_error() !== JSON_ERROR_NONE)
						{
							// Nothing else to do
						}
						else
						{
							$i = 0;
							foreach ($v as $nm => $vl)
							{
								$k = '_' . $key . '_' . $nm;
								if ($i == 0)
								{
									$k = $key;
								}
								$tmp[$k] = $vl;
								$i++;
							}
							continue;
						}
					}
				}

				$tmp[$key] = $val;
			}

			$results = Event::trigger('members.onExportMemberData', array($member, $tmp));
			foreach ($results as $result)
			{
				if (!is_array($result))
				{
					continue;
				}

				foreach ($result as $k => $v)
				{
					$tmp[$k] = $v;
				}
			}

			unset($member);

			fputcsv($file, $tmp);
		}

		fclose($file);

		$server = new \Hubzero\Content\Server();
		$server->filename($path);
		$server->disposition('attachment');
		$server->acceptranges(false); // @TODO fix byte range support

		if (!$server->serve())
		{
			// Should only get here on error
			App::abort(500, Lang::txt('Error serving file.'));
		}

		exit;
	}
}

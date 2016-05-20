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
use Components\Members\Models\Profile;
use Components\Members\Helpers\Permissions;
use Hubzero\Component\AdminController;
use User;
use Date;
use Lang;
use App;

// No direct access
defined('_HZEXEC_') or die();

include_once (dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'permissions.php');

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
	 * @param   integer  $dryRun
	 * @return  void
	 */
	public function runTask()
	{
		// Get request vars
		$delimiter = Request::getVar('delimiter', array(0));
		$id = 1010;
		$members = Member::all();

		$members_fields = array();
		$keys = array();

		//gather up member information
		foreach ($members->rows() as $member)
		{
				$fields = $member->toArray();
				unset($fields['password']);
				unset($fields['params']);
				$fields = array_merge($fields, Profile::collect($member->profiles()->rows()));
				array_push($members_fields, $fields);
				//list of fields will not always be static, so make sure we have all of them to align the CSV properly
				foreach (array_keys($fields) as $key)
				{
					array_push($keys, $key);
				}
		}
		$keys = array_unique($keys);

		$csv = array();

		foreach ($members_fields as $member)
		{
			$tmp = array();
			foreach ($keys as $key)
			{
				if (isset($member[$key]))
				{
					array_push($tmp, $member[$key]);
				} else {
					array_push($tmp, '');
				}
			}
			array_push($csv, $tmp);
		}

		//output csv directly as a download
		@ob_end_clean();

		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");

		header("Content-Transfer-Encoding: binary");
		header('Content-type: text/comma-separated-values');
		header('Content-disposition: attachment; filename="members.csv"');

		$out = fopen('php://output', 'w');
		fputcsv($out, $keys);
		foreach ($csv as $row)
		{
			fputcsv($out, $row, $delimiter);
		}

		exit;
	}
}

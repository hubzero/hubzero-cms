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
use User;
use Date;
use Lang;
use App;

// No direct access
defined('_HZEXEC_') or die();

include_once (dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'permissions.php');
include_once (dirname(dirname(__DIR__)) . DS . 'models' . DS . 'profile' . DS . 'field.php');

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

			array_push($keys, $key);
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
			array_push($keys, $attrib->get('name'));
		}


		// Get request vars
		$delimiter = Request::getVar('delimiter', ',');
		//$id = 1010;

		$csv = array();

		// Gather up member information
		foreach ($members->ordered()->rows() as $member)
		{
			$tmp = array();

			foreach ($keys as $key)
			{
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

				$tmp[$key] = $val;
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

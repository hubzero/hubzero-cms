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
 * @author    HUBzero
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Filesystem;
use Request;
use Config;
use Route;
use Lang;
use App;

/**
 * Import PREMIS redistration dump files
 */
class Premis extends AdminController
{
	/**
	 * Display all employer types
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view
			->setLayout('display')
			->display();
	}

	/**
	 * Save records
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('Check the file please.'));
			$this->displayTask();
			return;
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		// Ensure file names fit.
		$ext = Filesystem::extension($file['name']);
		$filename = Filesystem::name($file['name']);

		if ($ext != 'csv')
		{
			$this->setError(Lang::txt('Only .csv files are allowed'));
			$this->view->setError($this->getError());
			$this->view->display();
			return;
		}

		if (strlen($filename) > 230)
		{
			$filename = substr($filename, 0, 230);
		}

		$path = PATH_APP . DS . 'site' . DS . 'protected' . DS . 'premis_uploads';

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				// error
			}
		}

		// Check if file exists
		$counter = '';
		while (file_exists($path . DS . $filename . $counter . '.' . $ext))
		{
			if (empty($counter))
			{
				$counter = 1;
			}
			$counter++;
		}

		$filename = $path . DS . $filename . $counter . '.' . $ext;

		$uploaded = Filesystem::upload($file['tmp_name'], $filename);

		if ($uploaded)
		{
			// parse the file and do the registration
			$skipLines = 1;
			$row = 0;

			$report = array();
			$ok = 0;
			$fail = 0;

			include_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'Premis.php');

			if (($handle = fopen($filename, "r")) !== FALSE)
			{
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
				{
					$line = array();

					$num = count($data);
					$row++;
					if ($row <= $skipLines)
					{
						continue;
					}

					$line['line'] = $row;

					$user['fName'] = $data[0];
					$user['lName'] = $data[1];
					$user['email'] = $data[2];
					$user['casId'] = $data[4];
					$user['premisId'] = $data[3];
					$user['password'] = $data[5];
					$user['premisEnrollmentId'] = $data[9];

					$courses['add'] = $data[6];
					$courses['drop'] = $data[7];

					$return = \MembersHelperPremis::doRegistration($user, $courses);
					if ($return['status'] == 'ok')
					{
						$line['msg'] = $return['message'];
						$ok++;
					}
					else
					{
						$line['msg'] = $return['message'];
						$fail++;
					}
					$line['status'] = $return['status'];

					$report[] = $line;

				}
				fclose($handle);
			}

			$this->view->report = $report;
			$this->view->ok = $ok;
			$this->view->fail = $fail;
		}
		else
		{
			$this->setError(Lang::txt('Error uploading file'));
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Display stats
	 *
	 * @return  void
	 */
	public function statTask()
	{
		echo 'ff';
	}
}

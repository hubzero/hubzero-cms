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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Update\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Update\Helpers\Cli;
use Component;
use Config;

/**
 * Update controller class
 */
class Dashboard extends AdminController
{
	/**
	 * Display the update dashboard
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$source = Component::params('com_update')->get('git_repository_source', null);

		$this->view->repositoryVersion   = json_decode(Cli::version());
		$this->view->repositoryVersion   = $this->view->repositoryVersion[0];
		$this->view->repositoryMechanism = json_decode(Cli::mechanism());
		$this->view->repositoryMechanism = $this->view->repositoryMechanism[0];
		$this->view->databaseMechanism   = Config::get('dbtype');
		$this->view->databaseVersion     = \App::get('db')->getVersion();
		$this->view->status    = json_decode(Cli::status());
		$this->view->upcoming  = json_decode(Cli::update(true, false, $source));
		$this->view->migration = json_decode(Cli::migration());

		if (!isset($this->view->repositoryMechanism))
		{
			$this->view->message = 'Please ensure that the component is properly configured';
			$this->view->setLayout('error');
		}
		elseif ($this->view->repositoryMechanism != 'GIT')
		{
			$this->view->message = 'The CMS update component currently only supports repositories managed via GIT';
			$this->view->setLayout('error');
		}

		// Output the HTML
		$this->view->display();
	}
}
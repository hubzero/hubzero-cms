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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Api\Doc\Generator;
use Request;
use Pathway;
use Config;
use Lang;

/**
 * API Controller
 */
class Api extends SiteController
{
	/**
	 * General intro display
	 * 
	 * @return  void
	 */
	public function displayTask()
	{
		$this->_buildPathway();

		$this->view->display();
	}

	/**
	 * Documentation
	 * 
	 * @return  void
	 */
	public function docsTask()
	{
		// build pathway
		$this->_buildPathway();

		// generate documentation
		$generator = new Generator(!Config::get('debug'));

		// render view
		$this->view
			->set('documentation', $generator->output('array'))
			->display();
	}

	/**
	 * Endpoint Documentation
	 * 
	 * @return  void
	 */
	public function endpointTask()
	{
		// build pathway
		$this->_buildPathway();

		$active = Request::getVar('active', '');

		// generate documentation
		$generator = new Generator(!Config::get('debug'));
		$documentation = $generator->output('array');

		if (!isset($documentation['sections'][$active]))
		{
			throw new \Exception(Lang::txt('Endpoint not found'), 404);
		}

		// render view
		$this->view
			->set('active', $active)
			->set('documentation', $documentation)
			->display();
	}

	/**
	 * Console 
	 * 
	 * @return  void
	 */
	public function consoleTask()
	{
		// build pathway
		$this->_buildPathway();

		// render view
		$this->view->display();
	}

	/**
	 * Status 
	 * 
	 * @return  void
	 */
	public function statusTask()
	{
		// build pathway
		$this->_buildPathway();

		// render view
		$this->view->display();
	}

	/**
	 * Build Pathway
	 * 
	 * @return  void
	 */
	private function _buildPathway()
	{
		// create breadcrumbs
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		Pathway::append(
			Lang::txt(strtoupper($this->_option . '_' . $this->_controller)),
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);

		if (isset($this->_task) && $this->_task != '')
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option . '_' . $this->_controller . '_' . $this->_task)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task
			);
		}
	}
}

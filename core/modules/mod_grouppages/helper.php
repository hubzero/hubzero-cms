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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\GroupPages;

use Hubzero\Module\Module;
use Components\Groups\Models;
use Component;

/**
 * Module class for showing group pages
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return     void
	 */
	public function display()
	{
		if (!\App::isAdmin())
		{
			return;
		}

		// include group page archive model
		require_once Component::path('com_groups') . DS . 'models' . DS . 'page' . DS . 'archive.php';

		// include group module archive model
		require_once Component::path('com_groups') . DS . 'models' . DS . 'module' . DS . 'archive.php';

		// get unapproved pages
		$groupModelPageArchive = new Models\Page\Archive();
		$this->unapprovedPages = $groupModelPageArchive->pages('unapproved', array(
			'state' => array(0, 1)
		), true);

		// get unapproved modules
		$groupModelModuleArchive = new Models\Module\Archive();
		$this->unapprovedModules = $groupModelModuleArchive->modules('unapproved', array(
			'state' => array(0, 1)
		), true);

		// Get the view
		parent::display();
	}
}

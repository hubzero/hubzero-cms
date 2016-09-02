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

namespace Modules\Members;

use Hubzero\Module\Module;
use App;

/**
 * Module class for com_members data
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
		if (!App::isAdmin())
		{
			return;
		}

		$database = App::get('db');

		$database->setQuery("SELECT count(u.id) FROM `#__users` AS u WHERE u.activation < -1");
		$this->unconfirmed = $database->loadResult();

		$database->setQuery("SELECT count(u.id) FROM `#__users` AS u WHERE u.activation >= 1");
		$this->confirmed = $database->loadResult();

		$database->setQuery("SELECT count(*) FROM `#__users` WHERE registerDate >= " . $database->quote(gmdate('Y-m-d', (time() - 24*3600)) . ' 00:00:00'));
		$this->pastDay = $database->loadResult();

		// Get the view
		parent::display();
	}
}

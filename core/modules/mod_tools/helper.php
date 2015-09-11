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

namespace Modules\Tools;

use Hubzero\Module\Module;
use App;

/**
 * Module class for com_tools data
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

		$this->total      = 0;
		$this->registered = 0;
		$this->created    = 0;
		$this->uploaded   = 0;
		$this->installed  = 0;
		$this->updated    = 0;
		$this->approved   = 0;
		$this->published  = 0;
		$this->retired    = 0;
		$this->abandoned  = 0;

		$database = App::get('db');
		$database->setQuery(
			"SELECT f.state FROM `#__tool` as f
				JOIN `#__tool_version` AS v ON f.id=v.toolid AND v.state=3
				WHERE f.id != 0 ORDER BY f.state_changed DESC"
		);
		$this->data = $database->loadObjectList();
		if ($this->data)
		{
			$this->total = count($this->data);

			foreach ($this->data as $data)
			{
				switch ($data->state)
				{
					case 1: $this->registered++; break;
					case 2: $this->created++;    break;
					case 3: $this->uploaded++;   break;
					case 4: $this->installed++;  break;
					case 5: $this->updated++;    break;
					case 6: $this->approved++;   break;
					case 7: $this->published++;  break;
					case 8: $this->retired++;    break;
					case 9: $this->abandoned++;  break;
				}
			}
		}

		// Get the view
		parent::display();
	}
}

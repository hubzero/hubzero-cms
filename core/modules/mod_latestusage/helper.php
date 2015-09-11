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

namespace Modules\LatestUsage;

use Hubzero\Module\Module;

/**
 * Module class for displaying latest usage
 */
class Helper extends Module
{
	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		$database = \App::get('db');

		include_once(\Component::path('com_usage') . DS . 'helpers' . DS . 'helper.php');
		$udb = \Components\Usage\Helpers\Helper::getUDBO();

		$this->cls = trim($this->params->get('moduleclass_sfx'));

		if ($udb)
		{
			$udb->setQuery('SELECT value FROM summary_user_vals WHERE datetime = (SELECT MAX(datetime) FROM summary_user_vals) AND period = "12" AND colid = "1" AND rowid = "1"');
			$this->users = $udb->loadResult();

			$udb->setQuery('SELECT value FROM summary_simusage_vals WHERE datetime  = (SELECT MAX(datetime) FROM summary_simusage_vals) AND period = "12" AND colid = "1" AND rowid = "2"');
			$this->sims = $udb->loadResult();
		}
		else
		{
			$database->setQuery("SELECT COUNT(*) FROM `#__users`");
			$this->users = $database->loadResult();

			$this->sims = 0;
		}

		$database->setQuery("SELECT COUNT(*) FROM `#__resources` WHERE standalone=1 AND published=1 AND access!=1 AND access!=4");
		$this->resources = $database->loadResult();

		$database->setQuery("SELECT COUNT(*) FROM `#__resources` WHERE standalone=1 AND published=1 AND access!=1 AND access!=4 AND type=7");
		$this->tools = $database->loadResult();

		require $this->getLayoutPath();
	}
}


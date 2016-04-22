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

namespace Modules\Featuredmember;

use Hubzero\Module\Module;
use Hubzero\User\Profile;
use Hubzero\Config\Registry;
use MembersProfile;
use Component;
use User;

/**
 * Module class for displaying featured members
 */
class Helper extends Module
{
	/**
	 * Generate module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		include_once(Component::path('com_members') . DS . 'models' . DS . 'member.php');

		$database = \App::get('db');
		$this->row = null;

		// Randomly choose one
		$filters = array(
			'limit'      => 1,
			'show'       => trim($this->params->get('show')),
			'start'      => 0,
			'sortby'     => "RAND()",
			'search'     => '',
			'authorized' => false
		);
		if ($min = $this->params->get('min_contributions'))
		{
			$filters['contributions'] = $min;
		}

		$query = "SELECT id FROM `#__users` WHERE `block`=0 ORDER BY RAND() LIMIT 1";
		$db->setQuery($query);

		// Load their bio
		$this->row = Member::oneOrNew($row->loadResult());

		if (trim(strip_tags($this->row->get('bio'))) == '')
		{
			return '';
		}

		// Did we have a result to display?
		if ($this->row)
		{
			$this->cls = trim($this->params->get('moduleclass_sfx'));
			$this->txt_length = trim($this->params->get('txt_length'));

			$config = Component::params('com_members');

			$rparams = new Registry($this->row->get('params'));
			$this->params = $config;
			$this->params->merge($rparams);

			require $this->getLayoutPath();
		}
	}

	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}

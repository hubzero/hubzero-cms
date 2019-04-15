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

namespace Modules\Featuredblog;

use Hubzero\Module\Module;
use Components\Blog\Models\Entry;

/**
 * Module class for displaying a random, featured blog entry
 */
class Helper extends Module
{
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

	/**
	 * Build module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		include_once \Component::path('com_blog') . DS . 'models' . DS . 'entry.php';

		$this->row = null;

		$filters = array(
			'state'      => 1,
			'access'     => 1,
			'sort'       => "RAND()",
			'sort_Dir'   => '',
			'search'     => '',
			'scope'      => 'member',
			'scope_id'   => 0,
			'authorized' => false
		);

		$row = Entry::all()
			->whereEquals('scope', $filters['scope'])
			->whereEquals('state', $filters['state'])
			->whereEquals('access', $filters['access'])
			->row();

		// Did we have a result to display?
		if ($row->get('id'))
		{
			$this->row = $row;
			$this->cls = trim($this->params->get('moduleclass_sfx'));
			$this->txt_length = trim($this->params->get('txt_length'));

			require $this->getLayoutPath();
		}
	}
}

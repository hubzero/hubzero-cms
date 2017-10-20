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

namespace Modules\MySubmissions;

use Components\Resources\Models\Orm\Resource as Entry;
use Hubzero\Module\Module;
use Component;
use User;
use App;

/**
 * Module class for displaying a user's submissions and their progress
 */
class Helper extends Module
{
	/**
	 * Check if the type selection step is completed
	 *
	 * @param   object   $row  Resource
	 * @return  boolean  True if step completed
	 */
	public function step_type_check($row)
	{
		return ($row->id ? true : false);
	}

	/**
	 * Check if the compose step is completed
	 *
	 * @param   object   $row  Resource
	 * @return  boolean  True if step completed
	 */
	public function step_compose_check($row)
	{
		return ($row->id ? true : false);
	}

	/**
	 * Check if the attach step is completed
	 *
	 * @param   object   $row  Resource
	 * @return  boolean  True if step completed
	 */
	public function step_attach_check($row)
	{
		if ($row->id)
		{
			$total = $row->children()->total();
		}
		else
		{
			$total = 0;
		}
		return ($total ? true : false);
	}

	/**
	 * Check if the authors step is completed
	 *
	 * @param   object   $row  Resource
	 * @return  boolean  True if step completed
	 */
	public function step_authors_check($row)
	{
		if ($row->id)
		{
			$contributors = $row->authors()->total();
		}
		else
		{
			$contributors = 0;
		}

		return ($contributors ? true : false);
	}

	/**
	 * Check if the tags step is completed
	 *
	 * @param   object   $row  Resource
	 * @return  boolean  True if step completed
	 */
	public function step_tags_check($row)
	{
		$tags = $row->tags();

		if (count($tags) > 0)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if the review step is completed
	 *
	 * @param   object   $row  Resource
	 * @return  boolean  True if step completed
	 */
	public function step_review_check($row)
	{
		return false;
	}

	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		if (User::isGuest())
		{
			return false;
		}

		include_once Component::path('com_resources') . DS . 'models' . DS . 'orm' . DS . 'resource.php';

		$this->steps = array(
			'Type',
			'Compose',
			'Attach',
			'Authors',
			'Tags',
			'Review'
		);

		$this->rows = Entry::all()
			->whereEquals('standalone', 1)
			->whereEquals('published', 2)
			->where('type', '!=', 7)
			->whereEquals('created_by', User::get('id'))
			->rows();

		require $this->getLayoutPath();
	}
}


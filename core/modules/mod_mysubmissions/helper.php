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
	 * @param   integer  $id  Resource ID
	 * @return  boolean  True if step completed
	 */
	public function step_type_check($id)
	{
		return ($id ? true : false);
	}

	/**
	 * Check if the compose step is completed
	 *
	 * @param   integer  $id  Resource ID
	 * @return  boolean  True if step completed
	 */
	public function step_compose_check($id)
	{
		return ($id ? true : false);
	}

	/**
	 * Check if the attach step is completed
	 *
	 * @param   integer  $id  Resource ID
	 * @return  boolean  True if step completed
	 */
	public function step_attach_check($id)
	{
		if ($id)
		{
			$database = App::get('db');
			$ra = new \Components\Resources\Tables\Assoc($database);
			$total = $ra->getCount($id);
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
	 * @param   integer  $id  Resource ID
	 * @return  boolean  True if step completed
	 */
	public function step_authors_check($id)
	{
		if ($id)
		{
			$database = App::get('db');
			$rc = new \Components\Resources\Tables\Contributor($database);
			$contributors = $rc->getCount($id, 'resources');
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
	 * @param   integer  $id  Resource ID
	 * @return  boolean  True if step completed
	 */
	public function step_tags_check($id)
	{
		$database = App::get('db');

		$rt = new \Components\Resources\Helpers\Tags($id);
		$tags = $rt->tags('count');

		if ($tags > 0)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if the review step is completed
	 *
	 * @param   integer  $id  Resource ID
	 * @return  boolean  True if step completed
	 */
	public function step_review_check($id)
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

		include_once(Component::path('com_resources') . DS . 'tables' . DS . 'resource.php');
		include_once(Component::path('com_resources') . DS . 'tables' . DS . 'type.php');

		$this->steps = array('Type','Compose','Attach','Authors','Tags','Review');

		$database = App::get('db');

		$rr = new \Components\Resources\Tables\Resource($database);
		$rt = new \Components\Resources\Tables\Type($database);

		$query = "SELECT r.*, t.type AS typetitle
			FROM " . $rr->getTableName() . " AS r
			LEFT JOIN " . $rt->getTableName() . " AS t ON r.type=t.id
			WHERE r.published=2 AND r.standalone=1 AND r.type!=7 AND r.created_by=" . User::get('id');
		$database->setQuery($query);
		$this->rows = $database->loadObjectList();

		if ($this->rows)
		{
			include_once(Component::path('com_resources') . DS . 'tables' . DS . 'assoc.php');
			include_once(Component::path('com_resources') . DS . 'tables' . DS . 'contributor.php');
			include_once(Component::path('com_resources') . DS . 'helpers' . DS . 'tags.php');
		}

		require $this->getLayoutPath();
	}
}


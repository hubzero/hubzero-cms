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

namespace Modules\MyResources;

use Hubzero\Module\Module;
use Request;
use User;

/**
 * Module class for displaying a user's resources
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
		$this->no_html = Request::getInt('no_html', 0);

		$database = \App::get('db');

		$this->limit = intval($this->params->get('limit', 5));

		$this->sort = $this->params->get('sort', 'publish_up');

		// Get "published" contributions
		$query  = "SELECT DISTINCT R.id, R.title, R.type, R.logical_type AS logicaltype,
							AA.subtable, R.created, R.created_by, R.modified, R.published, R.publish_up, R.standalone,
							R.rating, R.times_rated, R.alias, R.ranking, rt.type AS typetitle, R.params ";
		if ($this->sort == 'usage')
		{
			$query .= ", (SELECT rs.users FROM #__resource_stats AS rs WHERE rs.resid=R.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS users ";
		}
		$query .= "FROM #__author_assoc AS AA, #__resource_types AS rt, #__resources AS R ";
		//$query .= "LEFT JOIN #__resource_types AS t ON R.logical_type=t.id ";
		$query .= "WHERE AA.authorid = ". User::get('id') ." ";
		$query .= "AND R.id = AA.subid ";
		$query .= "AND AA.subtable = 'resources' ";
		$query .= "AND R.standalone=1 AND R.type=rt.id AND R.published=1 ";
		$query .= "ORDER BY ";

		switch ($this->sort)
		{
			case 'usage':
				$query .= "users DESC";
			break;
			case 'title':
				$query .= "title ASC, publish_up DESC";
			break;
			case 'publish_up':
			default:
				$query .= "publish_up DESC, title ASC";
			break;
		}
		if ($this->limit > 0 && $this->limit != 'all')
		{
			$query .= " LIMIT " . $this->limit;
		}

		$database->setQuery($query);

		$this->contributions = $database->loadObjectList();

		require $this->getLayoutPath();
	}
}


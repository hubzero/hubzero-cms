<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 Purdue University. All rights reserved.
 * All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2009-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\RecentQuestions;

use Hubzero\Module\Module;
use Components\Answers\Models\Question;
use Components\Answers\Models\Tags;
use Component;
use Request;
use Date;

/**
 * Module class for displaying recent questions
 */
class Helper extends Module
{
	/**
	 * Get module contents
	 * 
	 * @return  void
	 */
	public function run()
	{
		$this->cssId    = $this->params->get('cssId');
		$this->cssClass = $this->params->get('cssClass');

		$this->tag   = Request::getVar('tag', '', 'get');
		$this->style = Request::getVar('style', '', 'get');

		require_once(Component::path('com_answers') . DS . 'models' . DS . 'question.php');

		$records = Question::all();

		switch ($this->params->get('state', 'open'))
		{
			case 'open':   $records->whereEquals('state', 0); break;
			case 'closed': $records->whereEquals('state', 1); break;
			case 'both':
			default:       $records->where('state', '<', 2); break;
		}

		if ($this->tag)
		{
			$cloud = new Tags();
			$tags = $cloud->parse($this->tag);

			$records
				->select('#__answers_questions.*')
				->join('#__tags_object', '#__tags_object.objectid', '#__answers_questions.id')
				->join('#__tags', '#__tags.id', '#__tags_object.tagid')
				->whereEquals('#__tags_object.tbl', 'answers')
				->whereIn('#__tags.tag', $tags);
		}

		$this->rows = $records
			->limit(intval($this->params->get('limit', 5)))
			->ordered()
			->rows();

		require $this->getLayoutPath();
	}

	/**
	 * Display module content
	 * 
	 * @return  void
	 */
	public function display()
	{
		// Push the module CSS to the template
		$this->css();

		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}

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

namespace Modules\MyContributions;

use Hubzero\Module\Module;
use Component;
use User;

/**
 * Module class for displaying contributions in progress
 */
class Helper extends Module
{
	/**
	 * Get a list of contributions
	 *
	 * @return  array
	 */
	private function _getContributions()
	{
		$database = \App::get('db');

		$query  = "SELECT DISTINCT R.id, R.title, R.type, R.logical_type AS logicaltype, R.created, R.created_by, R.published, R.publish_up, R.standalone, R.rating, R.times_rated, R.alias, R.ranking, rt.type AS typetitle ";
		$query .= "FROM `#__resource_types` AS rt, `#__resources` AS R ";
		$query .= "LEFT JOIN `#__author_assoc` AS aa ON aa.subid=R.id AND aa.subtable='resources' ";
		$query .= "WHERE (aa.authorid='" . User::get('id') . "' OR R.created_by=" . User::get('id') . ") ";
		$query .= "AND R.standalone=1 AND R.type=rt.id AND (R.published=2 OR R.published=3) AND R.type!=7 ";
		$query .= "ORDER BY published ASC, title ASC";

		$database->setQuery($query);

		return $database->loadObjectList();
	}

	/**
	 * Get a list of tools
	 *
	 * @param   integer  $show_questions  Show question count for tool
	 * @param   integer  $show_wishes     Show wish count for tool
	 * @param   integer  $show_tickets    Show ticket count for tool
	 * @param   string   $limit_tools     Number of records to pull
	 * @return  mixed    False if error, otherwise array
	 */
	private function _getToollist($show_questions, $show_wishes, $show_tickets, $limit_tools='40')
	{
		$database = \App::get('db');

		// Query filters defaults
		$filters = array();
		$filters['sortby'] = 'f.published DESC';
		$filters['filterby'] = 'all';

		include_once Component::path('com_tools') . DS . 'tables' . DS . 'tool.php';
		require_once Component::path('com_tools') . DS . 'models' . DS . 'tool.php';

		// Create a Tool object
		$rows = \Components\Tools\Models\Tool::getTools($filters, false);
		$limit = 100000;

		if ($rows)
		{
			for ($i=0; $i < count($rows); $i++)
			{
				// What is resource id?
				$rid = \Components\Tools\Models\Tool::getResourceId($rows[$i]->id);
				$rows[$i]->rid = $rid;

				// Get questions, wishes and tickets on published tools
				if ($rows[$i]->published == 1 && $i <= $limit_tools)
				{
					if ($show_questions)
					{
						// Get open questions
						require_once Component::path('com_answers') . DS . 'models' . DS . 'question.php';

						$results = \Components\Answers\Models\Question::all()
							->including(['responses', function ($response)
								{
									$response
										->select('id')
										->select('question_id')
										->where('state', '!=', 2);
								}])
							->whereEquals('state', 0)
							->select('#__answers_questions.*')
							->join('#__tags_object', '#__tags_object.objectid', '#__answers_questions.id')
							->join('#__tags', '#__tags.id', '#__tags_object.tagid')
							->whereEquals('#__tags_object.tbl', 'answers')
							->whereIn('#__tags.tag', ['tool' . $rows[$i]->toolname])
							->limit($limit)
							->ordered()
							->rows();

						$unanswered = 0;

						if ($results)
						{
							foreach ($results as $r)
							{
								if ($r->responses->count() == 0)
								{
									$unanswered++;
								}
							}
						}

						$rows[$i]->q = $results->count();
						$rows[$i]->q_new = $unanswered;
					}

					if ($show_wishes)
					{
						// Get open wishes
						require_once Component::path('com_wishlist') . DS . 'site' . DS . 'controllers' . DS . 'wishlists.php';
						require_once Component::path('com_wishlist') . DS . 'tables' . DS . 'wish.php';
						require_once Component::path('com_wishlist') . DS . 'tables' . DS . 'wishlist.php';

						$objWishlist = new \Components\Wishlist\Tables\Wishlist($database);
						$objWish = new \Components\Wishlist\Tables\Wish($database);
						$listid = $objWishlist->get_wishlistID($rid, 'resource');

						$rows[$i]->w = 0;
						$rows[$i]->w_new = 0;

						if ($listid)
						{
							$controller = new \Components\Wishlist\Site\Controllers\Wishlists();
							$filters = $controller->getFilters(1);
							$wishes = $objWish->get_wishes($listid, $filters, 1, User::getInstance());
							$unranked = 0;
							if ($wishes)
							{
								foreach ($wishes as $w)
								{
									if ($w->ranked == 0)
									{
										$unranked++;
									}
								}
							}

							$rows[$i]->w = count($wishes);
							$rows[$i]->w_new = $unranked;
						}
					}

					if ($show_tickets)
					{
						// Get open tickets
						$group = $rows[$i]->devgroup;
						$g = \Hubzero\User\Group::getInstance($group);
						$group = $g->get('gidNumber');

						// Find support tickets on the user's contributions
						$database->setQuery(
							"SELECT id, summary, category, status, severity, owner, created, login, name,
							(SELECT COUNT(*) FROM `#__support_comments` as sc WHERE sc.ticket=st.id AND sc.access=0) as comments
							FROM `#__support_tickets` as st WHERE (st.status=0 OR st.status=1) AND type=0 AND st.group_id='$group'
							ORDER BY created DESC
							LIMIT $limit"
						);
						$tickets = $database->loadObjectList();
						if ($database->getErrorNum())
						{
							echo $database->stderr();
							return false;
						}
						$unassigned = 0;
						if ($tickets)
						{
							foreach ($tickets as $t)
							{
								if ($t->comments == 0 && $t->status == 0 && !$t->owner)
								{
									$unassigned++;
								}
							}
						}

						$rows[$i]->s = count($tickets);
						$rows[$i]->s_new = $unassigned;
					}
				}
			}
		}

		return $rows;
	}

	/**
	 * Get tool status text
	 *
	 * @param   integer  $int  Tool status
	 * @return  string
	 */
	public function getState($int)
	{
		switch ($int)
		{
			case 1:
				$state = 'registered';
				break;
			case 2:
				$state = 'created';
				break;
			case 3:
				$state = 'uploaded';
				break;
			case 4:
				$state = 'installed';
				break;
			case 5:
				$state = 'updated';
				break;
			case 6:
				$state = 'approved';
				break;
			case 7:
				$state = 'published';
				break;
			case 8:
				$state = 'retired';
				break;
			case 9:
				$state = 'abandoned';
				break;
		}
		return $state;
	}

	/**
	 * Get resource type title
	 *
	 * @param   integer  $int  Resource type
	 * @return  string
	 */
	public function getType($int)
	{
		switch ($int)
		{
			case 1:
				$type = 'Online Presentation';
				break;  // online presentations
			case 3:
				$type = 'Publication';
				break;  // publications
			case 5:
				$type = 'Animation';
				break;  // animations
			case 9:
				$type = 'Download';
				break;  // downloads
			case 39:
				$type = 'Teaching Material';
				break;  // teaching materials
			default:
				$type = 'Other';
				break;
		}
		return $type;
	}

	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		// show tool contributions separately?
		$this->show_tools = intval($this->params->get('show_tools', 1));

		// get questions on resources?
		$this->show_questions = intval($this->params->get('get_questions', 1));

		// get wishes on resources?
		$this->show_wishes = intval($this->params->get('get_wishes', 1));

		// get tickets on resources?
		$this->show_tickets = intval($this->params->get('get_tickets', 1));

		// how many tools to display?
		$this->limit_tools = intval($this->params->get('limit_tools', 10));

		// how many tools to display?
		$this->limit_other = intval($this->params->get('limit_other', 5));

		// Tools in progress
		$this->tools = ($this->show_tools) ? $this->_getToollist($this->show_questions, $this->show_wishes, $this->show_tickets, $this->limit_tools) : array();

		// Other cotnributions
		$this->contributions = $this->_getContributions();

		require $this->getLayoutPath();
	}
}

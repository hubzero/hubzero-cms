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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Members Plugin class for wiki pages
 */
class plgMembersWiki extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return a list of categories
	 *
	 * @return     array
	 */
	public function &onMembersContributionsAreas()
	{
		$areas = array(
			'wiki' => Lang::txt('PLG_MEMBERS_WIKI')
		);
		return $areas;
	}

	/**
	 * Build SQL for returning the count of the number of contributions
	 *
	 * @param      string $user_id  Field to join on user ID
	 * @param      string $username Field to join on username
	 * @return     string
	 */
	public function onMembersContributionsCount($user_id='m.uidNumber', $username='m.username')
	{
		//$query  = "SELECT COUNT(*) FROM #__wiki_page AS w WHERE (w.created_by='".$user_id."' OR w.authors LIKE '%".$username."%')";
		$username = ($username == 'm.username') ? $username : "'" . $username . "'";
		$query = "SELECT COUNT(*) FROM #__wiki_page AS w
					WHERE ((" . $user_id . " > 0 AND (w.created_by = " . $user_id . " OR " . $user_id . " IN (SELECT wpa.user_id FROM #__wiki_page_author AS wpa
						WHERE wpa.page_id=w.id))) OR (" . $user_id . " <= 0 AND w.created_by = " . $user_id . "))";
		//if (!$authorized) {
		//	$query .= " AND w.access!=1";
		//}
		/*$query = "SELECT COUNT(*) FROM (
			SELECT COUNT(DISTINCT v.pageid) FROM #__wiki_page AS w, #__wiki_version AS v
			WHERE w.id=v.pageid
			AND v.approved=1
			AND (w.created_by=m.uidNumber OR w.authors LIKE '%m.username%') ";
		if (!$authorized) {
			$query .= " AND w.access!=1";
		}
		$query .= " GROUP BY pageid
		) AS f";*/
		return $query;
	}

	/**
	 * Return either a count or an array of the member's contributions
	 *
	 * @param      object  $member     Current member
	 * @param      string  $option     Component name
	 * @param      string  $authorized Authorization level
	 * @param      integer $limit      Number of record to return
	 * @param      integer $limitstart Record return start
	 * @param      string  $sort       Field to sort records on
	 * @param      array   $areas      Areas to return data for
	 * @return     array
	 */
	public function onMembersContributions($member, $option, $limit=0, $limitstart=0, $sort, $areas=null)
	{
		$database = App::get('db');

		if (is_array($areas) && $limit)
		{
			if (!isset($areas[$this->_name])
			  && !in_array($this->_name, $areas)
			  && !array_intersect($areas, array_keys($this->onMembersContributionsAreas())))
			{
				return array();
			}
		}

		// Do we have a member ID?
		if ($member instanceof \Hubzero\User\Profile)
		{
			if (!$member->get('uidNumber'))
			{
				return array();
			}
			else
			{
				$uidNumber = $member->get('uidNumber');
				$username = $member->get('username');
			}
		}
		else
		{
			if (!$member->uidNumber)
			{
				return array();
			}
			else
			{
				$uidNumber = $member->uidNumber;
				$username = $member->username;
			}
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');

		// Instantiate some needed objects
		$wp = new \Components\Wiki\Tables\Page($database);

		// Build query
		$filters = array();
		$filters['author'] = $uidNumber;
		$filters['username'] = $username;
		$filters['sortby'] = $sort;
		//if ($authorized) {
		//	$filters['authorized'] = 'admin';
		//}

		if (!$limit)
		{
			$filters['select'] = 'count';

			$database->setQuery($wp->buildPluginQuery($filters));
			return $database->loadResult();
		}
		else
		{
			$filters['select'] = 'records';
			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;

			$database->setQuery($wp->buildPluginQuery($filters));
			$rows = $database->loadObjectList();

			if ($rows)
			{
				foreach ($rows as $key => $row)
				{
					if ($row->area != '' && $row->category != '')
					{
						$rows[$key]->href = Route::url('index.php?option=com_groups&scope=' . $row->category . '&pagename=' . $row->alias);
					}
					else
					{
						$rows[$key]->href = Route::url('index.php?option=com_wiki&scope=' . $row->category . '&pagename=' . $row->alias);
					}
					$rows[$key]->text = $rows[$key]->itext;
				}
			}

			return $rows;
		}
	}

	/**
	 * Static method for formatting results
	 *
	 * @param      object $row Database row
	 * @return     string HTML
	 */
	public static function out($row)
	{
		$database = App::get('db');

		$html  = "\t" . '<li class="resource">' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>' . "\n";
		$html .= "\t\t" . '<p class="details">';
		if (isset($row->area) && isset($row->category))
		{
			$html .= Lang::txt('PLG_MEMBERS_WIKI_GROUP_WIKI') . ': ' . $row->area;
		}
		else
		{
			$html .= Lang::txt('PLG_MEMBERS_WIKI');
		}
		$html .= '</p>' . "\n";
		if ($row->text)
		{
			//if ($row->access == 1) {
			//	$html .= "\t\t".'<p class="warning">' . Lang::txt('PLG_MEMBERS_TOPICS_NOT_AUTHORIZED') . '</p>' ."\n";
			//} else {
				$html .= "\t\t<p>" . \Hubzero\Utility\String::truncate(strip_tags(stripslashes($row->text)), 300) . "</p>\n";
			//}
		}
		$html .= "\t" . '</li>' . "\n";
		return $html;
	}
}

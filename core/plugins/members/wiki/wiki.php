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
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return a list of categories
	 *
	 * @return  array
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
	 * @param   string  $user_id   Field to join on user ID
	 * @param   string  $username  Field to join on username
	 * @return  string
	 */
	public function onMembersContributionsCount($user_id='m.uidNumber', $username='m.username')
	{
		$username = ($username == 'm.username') ? $username : "'" . $username . "'";

		$query = "SELECT COUNT(*) FROM `#__wiki_pages` AS w
					WHERE ((" . $user_id . " > 0 AND (w.created_by = " . $user_id . " OR " . $user_id . " IN (SELECT wpa.user_id FROM `#__wiki_authors` AS wpa
						WHERE wpa.page_id=w.id))) OR (" . $user_id . " <= 0 AND w.created_by = " . $user_id . "))";

		return $query;
	}

	/**
	 * Return either a count or an array of the member's contributions
	 *
	 * @param   object   $member      Current member
	 * @param   string   $option      Component name
	 * @param   string   $authorized  Authorization level
	 * @param   integer  $limit       Number of record to return
	 * @param   integer  $limitstart  Record return start
	 * @param   string   $sort        Field to sort records on
	 * @param   array    $areas       Areas to return data for
	 * @return  array
	 */
	public function onMembersContributions($member, $option, $limit=0, $limitstart=0, $sort, $areas=null)
	{
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
				$username  = $member->get('username');
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
				$username  = $member->username;
			}
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'models' . DS . 'page.php');

		// Build query
		if (!$limit)
		{
			return \Components\Wiki\Models\Version::all()
				->whereEquals('created_by', $uidNumber)
				->whereEquals('approved', 1)
				->group('page_id')
				->count();
		}
		else
		{
			$versions = \Components\Wiki\Models\Version::all()
				->whereEquals('created_by', $uidNumber)
				->whereEquals('approved', 1)
				->group('page_id')
				->rows();

			$ids = array();

			foreach ($versions as $version)
			{
				$ids[] = $version->get('page_id');
			}

			$rows = \Components\Wiki\Models\Page::all()
				->whereEquals('state', \Components\Wiki\Models\Page::STATE_PUBLISHED)
				->whereIn('id', $ids)
				->limit($limit)
				->start($limitstart)
				->rows();

			return $rows;
		}
	}

	/**
	 * Static method for formatting results
	 *
	 * @param   object  $row  Database row
	 * @return  string  HTML
	 */
	public static function out($row)
	{
		$html  = "\t" . '<li class="resource">' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->link() . '">' . stripslashes($row->title) . '</a></p>' . "\n";
		$html .= "\t\t" . '<p class="details">' . $row->get('scope') . '</p>' . "\n";
		$html .= "\t\t" . '<p>' . \Hubzero\Utility\String::truncate(strip_tags(stripslashes($row->version->get('pagehtml'))), 300) . "</p>\n";
		$html .= "\t" . '</li>' . "\n";
		return $html;
	}
}

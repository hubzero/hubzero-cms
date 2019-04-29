<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
		if ($member instanceof \Hubzero\User\User)
		{
			if (!$member->get('id'))
			{
				return array();
			}
			else
			{
				$uidNumber = $member->get('id');
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

		include_once \Component::path('com_wiki') . DS . 'models' . DS . 'page.php';

		$versions = \Components\Wiki\Models\Version::all()
			->whereEquals('created_by', $uidNumber)
			->whereEquals('approved', 1)
			->group('page_id')
			->group('id')
			->rows();

		$ids = array();

		foreach ($versions as $version)
		{
			$ids[] = $version->get('page_id');
		}
		$query = \Components\Wiki\Models\Page::all()
			->whereEquals('state', \Components\Wiki\Models\Page::STATE_PUBLISHED)
			->whereEquals('scope', 'site')
			->whereIn('id', $ids);

		if (!$limit)
		{
			return $query->total();
		}
		else
		{
			if ($limitstart < 0)
			{
				$limitstart = 0;
			}

			$rows = $query
				->limit($limit)
				->start($limitstart);

			return $rows->rows();
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
		$html .= "\t\t" . '<p>' . \Hubzero\Utility\Str::truncate(strip_tags(stripslashes($row->version->get('pagehtml'))), 300) . "</p>\n";
		$html .= "\t" . '</li>' . "\n";
		return $html;
	}
}

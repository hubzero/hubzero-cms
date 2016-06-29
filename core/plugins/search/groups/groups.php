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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Groups\Models\Orm\Group;

require_once Component::path('com_groups') . DS . 'models' . DS . 'orm' . DS . 'group.php';

/**
 * Search groups
 */
class plgSearchGroups extends \Hubzero\Plugin\Plugin
{
	/**
	 * Build search query and add it to the $results
	 *
	 * @param      object $request  \Components\Search\Models\Basic\Request
	 * @param      object &$results \Components\Search\Models\Basic\Result\Set
	 * @param      object $authz    \Components\Search\Models\Basic\Authorization
	 * @return     void
	 */
	public static function onSearch($request, &$results, $authz)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(g.cn, g.description, g.public_desc) AGAINST (\'' . join(' ', $terms['stemmed']) . '\')';

		$from = '';

		if (!User::isGuest() && !User::authorise('core.view', 'com_groups'))
		{
			$from = " JOIN `#__xgroups_members` AS m ON m.gidNumber=g.gidNumber AND m.uidNumber=" . User::get('id');
		}

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(g.cn LIKE '%$mand%' OR g.description LIKE '%$mand%' OR g.public_desc LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(g.cn NOT LIKE '%$forb%' AND g.description NOT LIKE '%$forb%' AND g.public_desc NOT LIKE '%$forb%')";
		}

		$results->add(new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				g.description AS title,
				coalesce(g.public_desc, '') AS description,
				concat('index.php?option=com_groups&cn=', g.cn) AS link,
				$weight AS weight,
				NULL AS date,
				'Groups' AS section
			FROM `#__xgroups` g $from
			WHERE
				(g.type = 1 OR g.type = 3) AND g.published=1 AND g.approved=1 AND g.discoverability = 0 AND $weight > 0" .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		));
	}

	public $hubtype = 'group';

	/**
	 * onGetTypes - Announces the available hubtype
	 *
	 * @param mixed $type
	 * @access public
	 * @return void
	 */
	public function onGetTypes($type = null)
	{
		if (isset($type) && $type == $this->hubtype)
		{
			return $this->hubtype;
		}
		elseif (!isset($type))
		{
			return $this->hubtype;
		}
	}

	/**
	 * onGetModel 
	 * 
	 * @param string $type 
	 * @access public
	 * @return void
	 */
	public function onGetModel($type = '')
	{
		if ($type == $this->hubtype)
		{
			return new Group;
		}
	}

	/**
	 * onProcessFields - Set SearchDocument fields which have conditional processing
	 *
	 * @param mixed $type 
	 * @param mixed $row
	 * @access public
	 * @return void
	 */
	public function onProcessFields($type, $row, &$db)
	{
		if ($type == $this->hubtype)
		{
			// Instantiate new $fields object
			$fields = new stdClass;

			// Non-standard ID number
			$fields->id = 'group-' . $row->get('gidNumber');

			// Format the date for SOLR
			$date = Date::of($row->get('created'))->format('Y-m-d');
			$date .= 'T';
			$date .= Date::of($row->get('created'))->format('h:m:s') . 'Z';
			$fields->date = $date;

			// Title is required
			if ($row->get('description') == '')
			{
				$fields->title = $row->get('cn');
			}
			else
			{
				$fields->title = $row->get('description');
			}

			$fields->description = strip_tags(htmlspecialchars_decode($row->public_desc));


			/**
			 * Each entity should have an owner. 
			 * Owner type can be a user or a group,
			 * where the owner is the ID of the user or group
			 **/
			$fields->owner_type = 'group';
			$fields->owner = $row->gidNumber;

			/**
			 * A document should have an access level.
			 * This value can be:
			 *  public - all users can view
			 *  registered - only registered users can view
			 *  private - only owners (set above) can view
			 **/
			if ($row->discoverability == 0 && $row->published == 1 && $row->approved == 1)
			{
				$fields->access_level = 'public';
			}
			else
			{
				$fields->access_level = 'private';
			}

			// The URL this document is accessible through
			// No need for systems group URL
			if ($row->type != 0)
			{
				$fields->url = '/groups/' . $row->cn;
			}

			return $fields;
		}
	}
}


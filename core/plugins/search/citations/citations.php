<?php
/**
 * @package     hubzero-cms
 * @author      Steve Snyder <snyder13@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Search citation entries
 */
class plgSearchCitations extends \Hubzero\Plugin\Plugin
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
		$weight = 'match(c.title, c.isbn, c.doi, c.abstract, c.author, c.publisher) AGAINST (\'' . join(' ', $terms['stemmed']) . '\')';

		//get com_citations params
		$citationParams = Component::params('com_citations');
		$citationSingleView = $citationParams->get('citation_single_view', 1);

		//are we linking to singe citation view
		if ($citationSingleView)
		{
			$sql = "SELECT
						c.title AS title,
						c.abstract AS description,
					 	concat('/citations/view/', c.id) AS link,
						$weight AS weight
					FROM #__citations c
					WHERE
						c.published=1 AND $weight > 0
					ORDER BY $weight DESC";

			$results->add(new \Components\Search\Models\Basic\Result\Sql($sql));

			$sql2 = "SELECT
						c.id as id,
						c.title as title,
						c.abstract as description,
						concat('/citations/view/', c.id) AS link
					 FROM
						#__citations c,
						#__tags as tag,
						#__tags_object as tago
					WHERE
						tago.objectid=c.id
					AND
						tago.tagid=tag.id
					AND
						tago.tbl='citations'
					AND
						tago.label=''";
			$sql2 .= "AND (tag.tag='" . implode("' OR tag.tag='", $terms['stemmed']) . "')";
		}
		else
		{
			$sql = "SELECT
						c.title AS title,
						c.abstract AS description,
					 	concat('/citations/browse?search=" . join(' ', $terms['optional']) . "&year=', c.year) AS link,
						$weight AS weight
					FROM #__citations c
					WHERE
						c.published=1 AND $weight > 0
					ORDER BY $weight DESC";
			$results->add(new \Components\Search\Models\Basic\Result\Sql($sql));

			$sql2 = "SELECT
						c.id as id,
						c.title as title,
						c.abstract as description,
						concat('/citations/browse?search=" . join(' ', $terms['optional']) . "&year=', c.year) AS link
					 FROM
						#__citations c,
						#__tags as tag,
						#__tags_object as tago
					WHERE
						tago.objectid=c.id
					AND
						tago.tagid=tag.id
					AND
						tago.tbl='citations'
					AND
						tago.label=''";
			$sql2 .= "AND (tag.tag='" . implode("' OR tag.tag='", $terms['stemmed']) . "')";
		}

		//add final query to ysearch
		$sql_result_one = "SELECT c.id as id FROM #__citations c WHERE c.published=1 AND $weight > 0 ORDER BY $weight DESC";
		$sql2 .= " AND c.id NOT IN(" . $sql_result_one . ")";
		$results->add(new \Components\Search\Models\Basic\Result\Sql($sql2));
	}

	/**
	 * onGetTypes - Announces the available hubtype
	 * 
	 * @param mixed $type 
	 * @access public
	 * @return void
	 */
	public function onGetTypes($type = null)
	{
		// The name of the hubtype
		$hubtype = 'citation';

		if (isset($type) && $type == $hubtype)
		{
			return $hubtype;
		}
		elseif (!isset($type))
		{
			return $hubtype;
		}
	}

	/**
	 * onIndex 
	 * 
	 * @param string $type
	 * @param integer $id 
	 * @param boolean $run 
	 * @access public
	 * @return void
	 */
	public function onIndex($type, $id, $run = false)
	{
		if ($type == 'citation')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT * FROM #__citations WHERE id={$id};";
				$row = $db->setQuery($sql)->query()->loadObject();

				// Obtain list of related authors
				$sql1 = "SELECT author FROM #__citations_authors WHERE cid={$id};";
				$authors = $db->setQuery($sql1)->query()->loadColumn();

				// Get any tags
				$sql2 = "SELECT tag 
					FROM #__tags
					LEFT JOIN #__tags_object
					ON #__tags.id=#__tags_object.tagid
					WHERE #__tags_object.objectid = {$id} AND #__tags_object.tbl = 'citations';";
				$tags = $db->setQuery($sql2)->query()->loadColumn();

				// Determine the path
				if ($row->scope == 'member')
				{
					$path = '/members/'. $row->scope_id  . '/citations';
				}
				elseif ($row->scope == 'group')
				{
					$group = \Hubzero\User\Group::getInstance($row->scope_id);

					// Make sure group is valid.
					if (is_object($group))
					{
						$cn = $group->get('cn');
						$path = '/groups/'. $cn . '/citations';
					}
					else
					{
						$path = '';
					}
				}
				else
				{
					$path = '/citations/view/' . $id;
				}

				$access_level = 'public';

				if ($row->scope != 'group')
				{
					$owner_type = 'user';
					$owner = $row->uid;
				}
				else
				{
					$owner_type = 'group';
					$owner = $row->scope_id;
				}

				// Get the title
				$title = $row->title;

				// Build the description, clean up text
				$content = $row->address . ' ' .
					$row->author . ' ' .
					$row->booktitle . ' ' .
					$row->chapter . ' ' .
					$row->cite . ' ' .
					$row->edition . ' ' .
					$row->eprint . ' ' .
					$row->howpublished . ' ' .
					$row->institution . ' ' .
					$row->isbn . ' ' .
					$row->journal . ' ' .
					$row->month . ' ' .
					$row->note . ' ' .
					$row->number . ' ' .
					$row->organization . ' ' .
					$row->pages . ' ' .
					$row->publisher . ' ' .
					$row->series . ' ' .
					$row->school . ' ' .
					$row->title . ' ' .
					$row->url . ' ' .
					$row->volume . ' ' .
					$row->year . ' ' .
					$row->doi . ' ' .
					$row->ref_type . ' ' .
					$row->date_submit . ' ' .
					$row->date_accept . ' ' .
					$row->date_publish . ' ' .
					$row->software_use . ' ' .
					$row->notes . ' ' .
					$row->language . ' ' .
					$row->label . ' ';

				$content = preg_replace('/<[^>]*>/', ' ', $content);
				$content = preg_replace('/ {2,}/', ' ', $content);
				$description = \Hubzero\Utility\Sanitize::stripAll($content);

				// Create a record object
				$record = new \stdClass;
				$record->id = $type . '-' . $id;
				$record->hubtype = $type;
				$record->title = $title;
				$record->description = $description;
				$record->author = $authors;
				$record->tags = $tags;
				$record->path = $path;
				$record->access_level = $access_level;
				$record->owner = $owner;
				$record->owner_type = $owner_type;

				// Return the formatted record
				return $record;
			}
			else
			{
				$db = App::get('db');
				$sql = "SELECT id FROM #__citations;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return $ids;
			}
		}
	}
}


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

namespace Components\Resources\Helpers;

use Hubzero\Base\Object;
use User;
use Lang;

include_once(dirname(__DIR__) . DS . 'tables' . DS . 'screenshot.php');

/**
 * Information retrieval for items/info linked to a resource
 */
class Helper extends Object
{
	/**
	 * Resource ID
	 *
	 * @var mixed
	 */
	private $_id = 0;

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Container for properties
	 *
	 * @var array
	 */
	private $_data = array();

	/**
	 * Constructor
	 *
	 * @param   integer  $id   Resource ID
	 * @param   object   &$db  JDatabase
	 * @return  void
	 */
	public function __construct($id, &$db)
	{
		$this->_id = $id;
		$this->_db =& $db;

		$this->contributors = null;
		$this->children = null;
		$this->firstchild = null;
		$this->parents = null;
	}

	/**
	 * Set a property
	 *
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 *
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property]))
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Get a list of contributors without linking names
	 *
	 * @param      boolean $incSubmitter Include submitters?
	 * @return     void
	 */
	public function getUnlinkedContributors($incSubmitter=false)
	{
		if (!isset($this->_contributors))
		{
			$this->getCons();
		}
		$contributors = $this->_contributors;

		$html = '';
		if ($contributors != '')
		{
			$names = array();
			foreach ($contributors as $contributor)
			{
				if ($incSubmitter == false && $contributor->role == 'submitter')
				{
					continue;
				}
				if ($contributor->lastname || $contributor->firstname)
				{
					$name = stripslashes($contributor->firstname) . ' ';
					if ($contributor->middlename != NULL)
					{
						$name .= stripslashes($contributor->middlename) . ' ';
					}
					$name .= stripslashes($contributor->lastname);
				}
				else
				{
					$name = $contributor->name;
				}
				$name = str_replace('"', '&quot;', $name);
				$names[] = $name;
			}
			if (count($names) > 0)
			{
				$html = implode('; ', $names);
			}
		}
		$this->ul_contributors = $html;
	}

	/**
	 * Get a list of authors for a tool
	 *
	 * @param      string $toolname Parameter description (if any) ...
	 * @param      string $revision Parameter description (if any) ...
	 * @return     void
	 */
	public function getToolAuthors($toolname, $revision)
	{
		if (false) // @FIXME  quick hack to deal with influx of data in #__tool_groups
		{
		$sql = "SELECT n.uidNumber AS id, t.name AS name, n.name AS xname,  n.organization AS xorg, n.givenName AS firstname, n.middleName AS middlename, n.surname AS lastname, t.organization AS org, t.*, NULL as role"
			 . "\n FROM #__tool_authors AS t, #__xprofiles AS n "
			 . "\n WHERE n.uidNumber=t.uid AND t.toolname='" . $toolname . "'"
			 . "\n AND t.revision='" . $revision . "'"
			 . "\n ORDER BY t.ordering";
		}
		else
		{
		$sql = "SELECT n.uidNumber AS id, t.name AS name, n.name AS xname, n.organization AS xorg, n.givenName AS firstname, n.middleName AS middlename, n.surname AS lastname, t.organization AS org, t.*, NULL as role"
			 . "\n FROM #__tool_authors AS t, #__xprofiles AS n, #__tool_version AS v "
			 . "\n WHERE n.uidNumber=t.uid AND t.toolname='" . $toolname . "' AND v.id=t.version_id and v.state<>3"
			 . "\n AND t.revision='" . $revision . "'"
			 . "\n ORDER BY t.ordering";
		}
		$this->_db->setQuery($sql);
		$cons = $this->_db->loadObjectList();
		if ($cons)
		{
			foreach ($cons as $k => $c)
			{
				$cons[$k]->authorid = $cons[$k]->id;

				if (!$cons[$k]->name)
				{
					$cons[$k]->name = $cons[$k]->xname;
				}
				if (trim($cons[$k]->org) == '')
				{
					$cons[$k]->org = $cons[$k]->xorg;
				}
			}
		}
		$this->_contributors = $cons;
	}

	/**
	 * Get contributors
	 *
	 * @return     void
	 */
	public function getCons()
	{
		$sql = "SELECT a.authorid, a.name, a.name AS xname, a.organization AS org, a.role, n.uidNumber AS id, n.givenName AS firstname, n.middleName AS middlename, n.surname AS lastname, n.organization AS xorg
				FROM #__author_assoc AS a
				LEFT JOIN #__xprofiles AS n ON n.uidNumber=a.authorid
				WHERE a.subtable='resources'
				AND a.subid=" . $this->_id . "
				ORDER BY ordering, surname, givenName, middleName";

		$this->_db->setQuery($sql);
		$cons = $this->_db->loadObjectList();

		$this->_contributors = $cons;
	}

	/**
	 * Get a list of contributors
	 *
	 * @param      boolean $showorgs Show organizations?
	 * @param      integer $newstyle Use new style formatting?
	 * @return     void
	 */
	public function getContributors($showorgs=false, $newstyle=0)
	{
		if (!isset($this->_contributors) && !$this->_contributors)
		{
			$this->getCons();
		}
		$contributors = $this->_contributors;

		if ($contributors != '')
		{
			$html = '';
			$names = array();
			$orgs = array();
			$i = 1;
			$k = 0;
			$orgsln = '';
			$names_s = array();
			$orgsln_s = '';

			foreach ($contributors as $contributor)
			{
				if (strtolower($contributor->role) == 'submitter')
				{
					continue;
				}

				// Build the user's name and link to their profile
				if ($contributor->name)
				{
					$name = $contributor->name;
				}
				else if ($contributor->lastname || $contributor->firstname)
				{
					$name = stripslashes($contributor->firstname) . ' ';
					if ($contributor->middlename != NULL)
					{
						$name .= stripslashes($contributor->middlename) . ' ';
					}
					$name .= stripslashes($contributor->lastname);
				}
				else
				{
					$name = $contributor->xname;
				}
				if (!$contributor->org)
				{
					$contributor->org = $contributor->xorg;
				}

				$name = str_replace('"', '&quot;', $name);
				if ($contributor->id)
				{
					$link  = '<a href="' . Route::url('index.php?option=com_members&id=' . $contributor->id) . '" data-rel="contributor" class="resource-contributor" title="View the profile of ' . $name . '">' . $name . '</a>';
				}
				else
				{
					$link  = $name;
				}
				$link .= ($contributor->role) ? ' (' . $contributor->role . ')' : '';

				if ($newstyle)
				{
					if (trim($contributor->org) != '' && !in_array(trim($contributor->org), $orgs))
					{
						$orgs[$i-1] = trim($contributor->org);
						$orgsln   .= $i . '. ' . trim($contributor->org) . ' ';
						$orgsln_s .= trim($contributor->org) . ' ';
						$k = $i;
						$i++;

						$link_s = $link;
						$link .= '<sup>' . $k . '</sup>';
						$names_s[] = $link_s;
					}
					else
					{
						//$k = array_search(trim($contributor->org), $orgs) + 1;
						$link_s = $link;
						$link .= '';
						$names_s[] = $link_s;
					}
				}
				else
				{
					$orgs[trim($contributor->org)][] = $link;
				}

				$names[] = $link;
			}

			if ($showorgs && !$newstyle)
			{
				foreach ($orgs as $org => $links)
				{
					$orgs[$org] = implode(', ', $links) . '<br />' . $org;
				}
				$html .= implode('<br /><br />', $orgs);
			}
			else if ($newstyle)
			{
				if (count($names) > 0)
				{
					$html = '<p>'.ucfirst(Lang::txt('By')).' ';
					//$html .= count($orgs) > 1  ? implode(', ', $names) : implode(', ', $names_s);
					$html .= count($contributors) > 1 ? implode(', ', $names) : implode(', ', $names_s);
					$html .= '</p>';
				}
				if ($showorgs && count($orgs) > 0)
				{
					$html .= '<p class="orgs">';
					//$html .= count($orgs) > 1 ? $orgsln : $orgsln_s;
					$html .= count($contributors) > 1 ? $orgsln : $orgsln_s;
					$html .= '</p>';
				}
			}
			else
			{
				if (count($names) > 0)
				{
					$html = implode(', ', $names);
				}
			}
		}
		else
		{
			$html = '';
		}
		$this->contributors = $html;
	}

	/**
	 * Get a list of submitters
	 *
	 * @param      boolean $showorgs Show organizations?
	 * @param      integer $newstyle Use new style formatting?
	 * @return     void
	 */
	public function getSubmitters($showorgs=false, $newstyle=0, $badges=0)
	{
		if (!isset($this->_contributors) && !$this->_contributors)
		{
			$this->getCons();
		}
		$contributors = $this->_contributors;

		if ($contributors != '')
		{
			$html = '';
			$names = array();
			$orgs = array();
			$i = 1;
			$k = 0;
			$orgsln = '';
			$names_s = array();
			$orgsln_s = '';

			foreach ($contributors as $contributor)
			{
				if (strtolower($contributor->role) != 'submitter')
				{
					continue;
				}

				// Build the user's name and link to their profile
				if ($contributor->name)
				{
					$name = $contributor->name;
				}
				else if ($contributor->lastname || $contributor->firstname)
				{
					$name = stripslashes($contributor->firstname) . ' ';
					if ($contributor->middlename != NULL)
					{
						$name .= stripslashes($contributor->middlename) . ' ';
					}
					$name .= stripslashes($contributor->lastname);
				}
				else
				{
					$name = $contributor->xname;
				}
				if (!$contributor->org)
				{
					$contributor->org = $contributor->xorg;
				}

				$name = str_replace('"', '&quot;', $name);
				if ($contributor->id)
				{
					$link  = '<a href="' . Route::url('index.php?option=com_members&id=' . $contributor->id) . '" data-rel="submitter" class="resource-submitter" title="View the profile of ' . $name . '">' . $name . '</a>';
				}
				else
				{
					$link  = $name;
				}

				if ($newstyle)
				{
					if ($badges)
					{
						$xuser = User::getInstance($contributor->id);
						if (is_object($xuser) && $xuser->get('name'))
						{
							$types = array(23 => 'manager', 24 => 'administrator', 25 => 'super administrator', 21 => 'publisher', 20 => 'editor');
							if (isset($types[$xuser->gid]))
							{
								$link .= ' <ul class="badges"><li>' . str_replace(' ', '-', $types[$xuser->gid]) . '</li></ul>';
							}
						}
					}

					if (trim($contributor->org) != '' && !in_array(trim($contributor->org), $orgs))
					{
						$orgs[$i-1] = trim($contributor->org);
						$orgsln    .= $i . '. ' . trim($contributor->org) . ' ';
						$orgsln_s  .= trim($contributor->org).' ';
						$k = $i;
						$i++;
					}
					else
					{
						$k = array_search(trim($contributor->org), $orgs) + 1;
					}
					$link_s = $link;
					$link .= '<sup>' . $k . '</sup>';
					$names_s[] = $link_s;
				}
				else
				{
					$orgs[trim($contributor->org)][] = $link;
				}

				$names[] = $link;
			}

			if ($showorgs && !$newstyle)
			{
				foreach ($orgs as $org => $links)
				{
					$orgs[$org] = implode(', ', $links) . '<br />' . $org;
				}
				$html .= implode('<br /><br />', $orgs);
			}
			else if ($newstyle)
			{
				if (count($names) > 0)
				{
					$html  = '<p>';
					$html .= count($orgs) > 1  ? implode(', ', $names) : implode(', ', $names_s);
					$html .= '</p>';
				}
				if ($showorgs && count($orgs) > 0)
				{
					$html .= '<p class="orgs">';
					$html .= count($orgs) > 1 ? $orgsln : $orgsln_s;
					$html .= '</p>';
				}
			}
			else
			{
				if (count($names) > 0)
				{
					$html = implode(', ', $names);
				}
			}
		}
		else
		{
			$html = '';
		}
		$this->contributors = $html;
	}

	/**
	 * Get the IDs of all the contributors of a resource
	 *
	 * @return     void
	 */
	public function getContributorIDs()
	{
		$cons = array();

		if (isset($this->_data['_contributors']))
		{
			$contributors = $this->_contributors;
		}
		else
		{
			$sql = "SELECT n.uidNumber AS id"
				 . "\n FROM #__xprofiles AS n"
				 . "\n JOIN #__author_assoc AS a ON n.uidNumber=a.authorid"
				 . "\n WHERE a.subtable = 'resources'"
				 . "\n AND a.subid=" . $this->_id
				 . "\n ORDER BY ordering, surname, givenName, middleName";

			$this->_db->setQuery($sql);
			$contributors = $this->_db->loadObjectList();
		}

		if ($contributors)
		{
			foreach ($contributors as $con)
			{
				$cons[] = $con->id;
			}
		}
		$this->contributorIDs = $cons;
	}

	/**
	 * Get citations on a resource
	 *
	 * @return     boolean False if errors
	 */
	public function getCitations()
	{
		if (!$this->_id)
		{
			return false;
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'author.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'secondary.php');

		$cc = new \Components\Citations\Tables\Citation($this->_db);

		$this->citations = $cc->getCitations('resource', $this->_id);
	}

	/**
	 * Get a count of citations on a resource
	 *
	 * @return     void
	 */
	public function getCitationsCount()
	{
		$citations = $this->citations;
		if (!$citations)
		{
			$citations = $this->getCitations();
		}

		$this->citationsCount = $citations;
	}

	/**
	 * Get the last citation's date
	 *
	 * @return     boolean False if errors
	 */
	public function getLastCitationDate()
	{
		if ($this->_id)
		{
			return false;
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'author.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'secondary.php');

		$cc = new \Components\Citations\Tables\Citation($this->_db);

		$this->lastCitationDate = $cc->getLastCitationDate('resource', $this->_id);
	}

	/**
	 * Get tags on this resource
	 *
	 * @param   integer  $tagger_id  Tagger ID
	 * @param   integer  $strength   Tag strength
	 * @param   integer  $admin      Include admin tags?
	 * @return  mixed    False if errors, array on success
	 */
	public function getTags($tagger_id=0, $strength=0, $admin=0)
	{
		if ($this->_id == 0)
		{
			return false;
		}

		include_once(__DIR__ . DS . 'tags.php');

		$rt = new Tags($this->_id);
		$filters = array();
		if ($tagger_id)
		{
			$filters['tagger_id'] = $tagger_id;
		}
		if ($strength)
		{
			$filters['strength'] = $strength;
		}
		if (!$admin)
		{
			$filters['admin'] = $admin;
		}
		$this->tags = $rt->tags('list', $filters);
		return $this->tags;
	}

	/**
	 * Get a comma-separated list of tags for a resource
	 *
	 * @param      integer $tagger_id Tagger ID
	 * @param      integer $strength  Tag strength
	 * @return     boolean False if errors, string on success
	 */
	public function getTagsForEditing($tagger_id=0, $strength=0)
	{
		if ($this->_id == 0)
		{
			return false;
		}

		include_once(__DIR__ . DS . 'tags.php');

		$rt = new Tags($this->_id);
		$filters = array();
		if ($tagger_id)
		{
			$filters['tagger_id'] = $tagger_id;
		}
		if ($strength)
		{
			$filters['strength'] = $strength;
		}
		$this->tagsForEditing = $rt->render('string', $filters);
		return $this->tagsForEditing;
	}

	/**
	 * Get a tag cloud for this resource
	 *
	 * @param      integer $admin Include admin tags?
	 * @return     boolean False if errors, string on success
	 */
	public function getTagCloud($admin=0)
	{
		if ($this->_id == 0)
		{
			return false;
		}

		include_once(__DIR__ . DS . 'tags.php');

		$rt = new Tags($this->_id);
		$this->tagCloud = $rt->render('cloud');
		return $this->tagCloud;
	}

	/**
	 * Get the children of a resource
	 *
	 * @param      integer $id                Optional resource ID (uses internal ID if none passeD)
	 * @param      integer $limit             Number of results to return
	 * @param      string  $standalone        Include standalone children?
	 * @param      integer $excludeFirstChild Exclude first child from results?
	 * @return     array
	 */
	public function getChildren($id=0, $limit=0, $standalone='all', $excludeFirstChild = 0)
	{
		$children = '';
		if (!$id)
		{
			$id = $this->_id;
		}
		$sql = "SELECT r.id, r.title, r.introtext, r.type, r.logical_type AS logicaltype, r.created, r.created_by,
				r.published, r.publish_up, r.path, r.access, r.standalone, r.rating, r.times_rated, r.attribs, r.params,
				t.type AS logicaltitle, rt.type AS typetitle, a.grouping, a.ordering "
			 . "\n FROM #__resource_types AS rt, #__resources AS r"
			 . "\n JOIN #__resource_assoc AS a ON r.id=a.child_id"
			 . "\n LEFT JOIN #__resource_types AS t ON r.logical_type=t.id"
			 . "\n WHERE r.published=1 AND a.parent_id=" . $id . " AND r.type=rt.id";
		switch ($standalone)
		{
			case 'no': $sql .= " AND r.standalone=0"; break;
			case 'yes': $sql .= " AND r.standalone=1"; break;
			case 'all':
			default: $sql .= ""; break;
		}
		$sql .= "\n ORDER BY a.ordering, a.grouping";
		if ($limit != 0 or $excludeFirstChild)
		{
			$sql .= $excludeFirstChild ? " LIMIT $excludeFirstChild, 100" : " LIMIT  ".$limit;
		}
		$this->_db->setQuery($sql);
		$children = $this->_db->loadObjectList();

		if ($limit != 0)
		{
			return (isset($children[0])) ? $children[0] : NULL;
		}
		else
		{
			$this->children = $children;
		}
	}

	/**
	 * Get a count of standalone children
	 *
	 * @param      array $filters Optional filters to apply
	 * @return     integer
	 */
	public function getStandaloneCount($filters)
	{
		$sql = "SELECT COUNT(*)"
			 . " FROM #__resource_types AS rt, #__resources AS r"
			 . " JOIN #__resource_assoc AS a ON r.id=a.child_id"
			 . " LEFT JOIN #__resource_types AS t ON r.logical_type=t.id"
			 . " WHERE r.published=1 AND a.parent_id=" . $filters['id'] . " AND r.standalone=1 AND r.type=rt.id";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get all standalone children
	 *
	 * @param      array $filters Optional filters to apply
	 * @return     array
	 */
	public function getStandaloneChildren($filters)
	{
		$sql = "SELECT r.id, r.title, r.alias, r.introtext, r.fulltxt, r.type, r.logical_type AS logicaltype, r.created, r.created_by,
						r.published, r.publish_up, r.path, r.access, r.standalone, r.rating, r.times_rated, r.attribs, r.ranking,
						r.params, t.type AS logicaltitle, rt.type AS typetitle, a.grouping,
						(SELECT n.surname FROM #__xprofiles AS n, #__author_assoc AS aa WHERE n.uidNumber=aa.authorid AND aa.subtable='resources' AND aa.subid=r.id ORDER BY ordering LIMIT 1) AS author"
			 . "\n FROM #__resource_types AS rt, #__resources AS r"
			 . "\n JOIN #__resource_assoc AS a ON r.id=a.child_id"
			 . "\n LEFT JOIN #__resource_types AS t ON r.logical_type=t.id"
			 . "\n WHERE r.published=1 AND a.parent_id=" . $filters['id'] . " AND r.standalone=1 AND r.type=rt.id";
		if (isset($filters['year']) && $filters['year'] > 0)
		{
			$sql .= " AND r.publish_up >= '" . $filters['year'] . "-01-01 00:00:00' AND r.publish_up <= '" . $filters['year'] . "-12-31 23:59:59'";
		}
		$sql .= " ORDER BY ";
		switch ($filters['sortby'])
		{
			case 'ordering': $sql .= "a.ordering, a.grouping";            break;
			case 'date':     $sql .= "r.publish_up DESC";                 break;
			case 'title':    $sql .= "r.title ASC, r.publish_up";         break;
			case 'rating':   $sql .= "r.rating DESC, r.times_rated DESC"; break;
			case 'ranking':  $sql .= "r.ranking DESC"; break;
			case 'author':   $sql .= "author"; break;
		}
		if (isset($filters['limit']) && $filters['limit'] != '' && $filters['limit'] != 0)
		{
			$sql .= " LIMIT " . $filters['start'] . "," . $filters['limit'] . " ";
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the first child resource
	 *
	 * @return     void
	 */
	public function getFirstChild()
	{
		if ($this->children)
		{
			$this->firstChild = $this->children[0];
		}
		else
		{
			$this->firstChild = $this->getChildren('', 1);
		}
	}

	/**
	 * Get all parents of a resource
	 *
	 * @return     boolean False if errors, true on success
	 */
	public function getParents()
	{
		if ($this->_id == 0)
		{
			return false;
		}

		$sql = "SELECT DISTINCT r.id, r.title, r.alias, r.introtext, r.footertext, r.type, r.logical_type AS logicaltype,
				r.created, r.published, r.publish_up, r.path, r.standalone, r.hits, r.rating, r.times_rated, r.params, r.ranking,
				t.type AS logicaltitle, rt.type AS typetitle
				FROM #__resource_types AS rt, #__resources AS r
				JOIN #__resource_assoc AS a ON r.id=a.parent_id
				LEFT JOIN #__resource_types AS t ON r.logical_type=t.id
				WHERE r.published=1 AND a.child_id=" . $this->_id . " AND r.type=rt.id AND r.type!=8
				ORDER BY a.ordering, a.grouping";
		$this->_db->setQuery($sql);
		$parents = $this->_db->loadObjectList();

		$this->parents = $parents;

		return true;
	}

	/**
	 * Get the reviews for a resource
	 *
	 * @return     boolean False if errors, true on success
	 */
	public function getReviews()
	{
		if ($this->_id == 0)
		{
			return false;
		}

		include_once(dirname(__DIR__) . DS . 'tables' . DS . 'review.php');

		$rr = new Review($this->_db);

		$this->reviews = $rr->getRatings($this->_id);

		return true;
	}
}

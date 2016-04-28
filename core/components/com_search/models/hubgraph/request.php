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

namespace Components\Search\Models\Hubgraph;

/**
 * Hubgraph request class
 */
class Request
{
	private $form;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct($form)
	{
		$this->form = $form;
	}

	/**
	 * Get terms
	 *
	 * @return  string
	 */
	public function getTerms()
	{
		return isset($this->form['terms']) && !is_array($this->form['terms']) ? stripslashes($this->form['terms']) : '';
	}

	/**
	 * Get tags
	 *
	 * @return  array
	 */
	public function getTags()
	{
		static $rv = NULL;

		if (is_null($rv))
		{
			$rv = array();
			if (isset($this->form['tags']) && is_array($this->form['tags']))
			{
				$order = array_flip($this->form['tags']);
				foreach (Db::query('SELECT raw_tag, id FROM `#__tags` WHERE id IN (' . implode(', ', array_fill(0, count($this->form['tags']), '?')) . ')', $this->form['tags']) as $row)
				{
					$rv[] = array(
						'id'    => $row['id'],
						'title' => $row['raw_tag']
					);
				}
				usort($rv, function($a, $b) use ($order)
				{
					$oa = $order[$a['id']];
					$ob = $order[$b['id']];
					return $oa == $ob ? 0 : ($oa > $ob ? 1 : -1);
				});
			}
		}

		return $rv;
	}

	/**
	 * Get timeframe
	 *
	 * @return  mixed
	 */
	public function getTimeframe()
	{
		if (isset($this->form['timeframe']) && is_array($this->form['timeframe']))
		{
			return array_filter($this->form['timeframe'], function($t)
			{
				return preg_match('/^(?:\d\d\d\d|day|week|month|year)$/', $t);
			});
		}
		return null;
	}

	/**
	 * Get contributors
	 *
	 * @return  array
	 */
	public function getContributors()
	{
		static $rv = NULL;

		if (is_null($rv))
		{
			$rv = array();
			if (isset($this->form['users']) && is_array($this->form['users']))
			{
				$order = array_flip($this->form['users']);
				$idList = implode(', ', array_fill(0, count($this->form['users']), '?'));
				foreach (Db::query(
					'SELECT name, uidNumber FROM `#__xprofiles` WHERE uidNumber IN (' . $idList . ')
					UNION
					SELECT name, authorid AS uidNumber FROM `#__author_assoc` WHERE authorid IN (' . $idList . ')
					LIMIT 1', array_merge($this->form['users'], $this->form['users'])) as $row)
				{
					$rv[] = array(
						'id'    => $row['uidNumber'],
						'title' => $row['name']
					);
				}
				usort($rv, function($a, $b) use($order)
				{
					$oa = $order[$a['id']];
					$ob = $order[$b['id']];
					return $oa == $ob ? 0 : ($oa > $ob ? 1 : -1);
				});
			}
		}

		return $rv;
	}

	/**
	 * Get group name
	 *
	 * @param   mixed  $gid
	 * @return  string
	 */
	public function getGroupName($gid)
	{
		static $map = array();

		if (!isset($map[$gid]))
		{
			$map[$gid] = Db::scalarQuery('SELECT description FROM `#__xgroups` WHERE gidNumber = ? OR cn = ?', array($gid, $gid));
		}

		return $map[$gid];
	}

	/**
	 * Get group
	 *
	 * @return  array
	 */
	public function getGroup()
	{
		static $group = NULL;

		$implicit = FALSE;
		if (!$group)
		{
			if (isset($this->form['groups']))
			{
				$group = $this->form['groups'];
			}
			else if (isset($this->form['groups']))
			{
				$group = $this->form['groups'];
			}
			else
			{
				$url = isset($_SERVER['SCRIPT_URL']) ? $_SERVER['SCRIPT_URL'] : $_SERVER['REDIRECT_SCRIPT_URL'];
				if (preg_match('#^/groups/([-_[:alnum:]]+)#', $url, $ma))
				{
					$group = array($ma[1]);
					$implicit = TRUE;
				}
			}
			if ($group)
			{
				$group = Db::query('SELECT gidNumber AS id, description AS title FROM `#__xgroups` WHERE gidNumber = ? OR cn = ?', array($group[0], $group[0]));
				if ($group)
				{
					$group[0]['isUrlImplicit'] = $implicit;
				}
			}
		}

		return $group;
	}

	/**
	 * Get an ID list
	 *
	 * @param   array   $coll
	 * @return  string
	 */
	private static function idList($coll)
	{
		return implode(',', array_map(function($item)
		{
			return $item['id'];
		}, (array) $coll));
	}

	/**
	 * Get transport criteria
	 *
	 * @param   array   $merge
	 * @return  array
	 */
	public function getTransportCriteria($merge = array())
	{
		static $crit;

		if (is_null($crit))
		{
			$groups = array();
			$super = FALSE;
			if (($uid = User::get('id')))
			{
				$super = User::get('usertype') === 'Super Administrator';
				foreach (Db::query(
					'SELECT DISTINCT g.gidNumber FROM `#__xgroups_members` xm INNER JOIN `#__xgroups` g ON g.gidNumber = xm.gidNumber WHERE uidNumber = ?
					UNION SELECT DISTINCT g.gidNumber FROM `#__xgroups_managers` xm INNER JOIN `#__xgroups` g ON g.gidNumber = xm.gidNumber WHERE uidNumber = ?', array($uid, $uid)) as $group)
				{
					$groups[] = $group['gidNumber'];
				}
			}

			$crit = array(
				'terms'        => $this->getTerms(),
				'tags'         => self::idList($this->getTags()),
				'domain'       => lcfirst(htmlentities($this->getDomain())),
				'users'        => self::idList($this->getContributors()),
				'page'         => $this->getPage(),
				'per'          => $this->getPerPage(),
				'super'        => $super,
				'uid'          => $uid,
				'groups'       => $groups,
				'timeframe'    => $this->getTimeframe(),
				'inGroup'      => self::idList($this->getGroup()),
				'cache'        => isset($_GET['cache']) ? $_GET['cache'] : NULL
			);
		}
		return array_merge($merge, $crit);
	}

	/**
	 * Get number per page
	 *
	 * @return  integer
	 */
	public function getPerPage()
	{
		return isset($_GET['per']) && (int)$_GET['per'] == $_GET['per'] && (int)$_GET['per'] ? (int)$_GET['per'] : 40;
	}

	/**
	 * Get page
	 *
	 * @return  integer
	 */
	public function getPage()
	{
		return isset($_GET['page']) && (int)$_GET['page'] ? (int)$_GET['page'] : 1;
	}

	/**
	 * Get domain
	 *
	 * @return  string
	 */
	public function getDomain()
	{
		if (isset($this->form['domain']))
		{
			return $this->form['domain'];
		}
		if (isset($this->form['option']) && $this->form['option'] == 'com_resources')
		{
			if (isset($this->form['type']) && ($type = Db::scalarQuery('SELECT type FROM `#__resource_types` WHERE alias = ?', array($this->form['type']))))
			{
				return 'resources~' . $type;
			}
			return 'resources';
		}
		return '';
	}

	/**
	 * Get domain map
	 *
	 * @return  array
	 */
	public function getDomainMap()
	{
		$map = array();
		if ($parts = explode('~', $this->getDomain()))
		{
			$lineage = '';
			foreach ($parts as $part)
			{
				$map[($lineage ? $lineage.'~' : '') . $part] = TRUE;
				$lineage .= ($lineage ? '~' . $part : $part);
			}
		}
		return $map;
	}

	/**
	 * Description...
	 *
	 * @return  boolean
	 */
	public function anyCriteria()
	{
		foreach ($this->getTransportCriteria() as $key => $crit)
		{
			switch ($key)
			{
				case 'offset':
				case 'super':
				case 'uid':
				case 'groups':
				case 'page':
				case 'per':
					continue;
				break;

				default:
					if (!is_null($crit) && $crit !== array() && $crit !== '')
					{
						return TRUE;
					}
				break;
			}
		}
		return FALSE;
	}
}

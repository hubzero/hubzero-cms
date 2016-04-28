<?php
defined('_HZEXEC_') or die();

class HubgraphRequest
{
	private $form;

	public function __construct($form)
	{
		$this->form = $form;
	}

	public function getTerms()
	{
		return isset($this->form['terms']) && !is_array($this->form['terms']) ? stripslashes($this->form['terms']) : '';
	}

	public function getTags()
	{
		static $rv = NULL;
		if (is_null($rv)) {
			$rv = array();
			if (isset($this->form['tags']) && is_array($this->form['tags'])) {
				$order = array_flip($this->form['tags']);
				foreach (Db::query('SELECT raw_tag, id FROM jos_tags WHERE id IN ('.implode(', ', array_fill(0, count($this->form['tags']), '?')).')', $this->form['tags']) as $row) {
					$rv[] = array('id' => $row['id'], 'title' => $row['raw_tag']);
				}
				usort($rv, function($a, $b) use($order) {
					$oa = $order[$a['id']];
					$ob = $order[$b['id']];
					return $oa == $ob ? 0 : ($oa > $ob ? 1 : -1);
				});
			}
		}
		return $rv;
	}

	public function getTimeframe()
	{
		if (isset($this->form['timeframe']) && is_array($this->form['timeframe'])) {
			return array_filter($this->form['timeframe'], function($t) {
				return preg_match('/^(?:\d\d\d\d|day|week|month|year)$/', $t);
			});
		}
		return null;
	}

	public function getContributors()
	{
		static $rv = NULL;
		if (is_null($rv)) {
			$rv = array();
			if (isset($this->form['users']) && is_array($this->form['users'])) {
				$order = array_flip($this->form['users']);
				$idList = implode(', ', array_fill(0, count($this->form['users']), '?'));
				foreach (Db::query(
					'SELECT name, uidNumber FROM jos_xprofiles WHERE uidNumber IN ('.$idList.')
					UNION
					SELECT name, authorid AS uidNumber FROM jos_author_assoc WHERE authorid IN ('.$idList.')
					LIMIT 1', array_merge($this->form['users'], $this->form['users'])) as $row) {
					$rv[] = array('id' => $row['uidNumber'], 'title' => $row['name']);
				}
				usort($rv, function($a, $b) use($order) {
					$oa = $order[$a['id']];
					$ob = $order[$b['id']];
					return $oa == $ob ? 0 : ($oa > $ob ? 1 : -1);
				});
			}
		}
		return $rv;
	}

	public function getGroupName($gid)
	{
		static $map = array();
		if (!isset($map[$gid])) {
			$map[$gid] = Db::scalarQuery('SELECT description FROM jos_xgroups WHERE gidNumber = ? OR cn = ?', array($gid, $gid));
		}
		return $map[$gid];
	}

	public function getGroup()
	{
		static $group = NULL;
		$implicit = FALSE;
		if (!$group) {
			if (isset($this->form['groups'])) {
				$group = $this->form['groups'];
			}
			else if (isset($this->form['groups'])) {
				$group = $this->form['groups'];
			}
			else {
				$url = isset($_SERVER['SCRIPT_URL']) ? $_SERVER['SCRIPT_URL'] : $_SERVER['REDIRECT_SCRIPT_URL'];
				if (preg_match('#^/groups/([-_[:alnum:]]+)#', $url, $ma)) {
					$group = array($ma[1]);
					$implicit = TRUE;
				}
			}
			if ($group) {
				$group = Db::query('SELECT gidNumber AS id, description AS title FROM jos_xgroups WHERE gidNumber = ? OR cn = ?', array($group[0], $group[0]));
				if ($group) {
					$group[0]['isUrlImplicit'] = $implicit;
				}
			}
		}
		return $group;
	}

	private static function idList($coll)
	{
		return implode(',', array_map(function($item) { return $item['id']; }, (array)$coll));
	}

	public function getTransportCriteria($merge = array())
	{
		static $crit;
		if (is_null($crit)) {
			$groups = array();
			$super = FALSE;
			if (($uid = User::get('id'))) {
				$super = User::get('usertype') === 'Super Administrator';
				foreach (Db::query(
					'SELECT DISTINCT g.gidNumber FROM jos_xgroups_members xm INNER JOIN jos_xgroups g ON g.gidNumber = xm.gidNumber WHERE uidNumber = ? 
					UNION SELECT DISTINCT g.gidNumber FROM jos_xgroups_managers xm INNER JOIN jos_xgroups g ON g.gidNumber = xm.gidNumber WHERE uidNumber = ?', array($uid, $uid)) as $group) {
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

	public function getPerPage()
	{
		return isset($_GET['per']) && (int)$_GET['per'] == $_GET['per'] && (int)$_GET['per'] ? (int)$_GET['per'] : 40;
	}

	public function getPage()
	{
		return isset($_GET['page']) && (int)$_GET['page'] ? (int)$_GET['page'] : 1;
	}

	public function getDomain()
	{
		if (isset($this->form['domain'])) {
			return $this->form['domain'];
		}
		if (isset($this->form['option']) && $this->form['option'] == 'com_resources') {
			if (isset($this->form['type']) && ($type = Db::scalarQuery('SELECT type FROM jos_resource_types WHERE alias = ?', array($this->form['type'])))) {
				return 'resources~'.$type;
			}
			return 'resources';
		}
		return '';
	}

	public function getDomainMap()
	{
		$map = array();
		if (($parts = explode('~', $this->getDomain()))) {
			$lineage = '';
			foreach ($parts as $part) {
				$map[($lineage ? $lineage.'~' : '').$part] = TRUE;
				$lineage .= ($lineage ? '~'.$part : $part);
			}
		}
		return $map;
	}

	public function anyCriteria()
	{
		foreach ($this->getTransportCriteria() as $key=>$crit) {
			switch ($key) {
				case 'offset': case 'super': case 'uid': case 'groups': case 'page': case 'per':
					continue;
				default:
					if (!is_null($crit) && $crit !== array() && $crit !== '') {
						return TRUE;
					}
			}
		}
		return FALSE;
	}
}

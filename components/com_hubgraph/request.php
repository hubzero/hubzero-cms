<? defined('JPATH_BASE') or die();

class HubgraphRequest
{
	private $form;

	public function __construct($form) {
		$this->form = $form;
	}

	public function getTerms() {
		return isset($this->form['terms']) && !is_array($this->form['terms']) ? stripslashes($this->form['terms']) : '';
	}

	public function getTags() {
		static $rv = NULL;
		if (is_null($rv)) {
			$rv = array();
			if (isset($this->form['tags']) && is_array($this->form['tags'])) {
				$order = array_flip($this->form['tags']);
				foreach (Db::query('SELECT raw_tag, id FROM jos_tags WHERE id IN ('.implode(', ', array_fill(0, count($this->form['tags']), '?')).')', $this->form['tags']) as $row) {
					$rv[] = array('id' => $row['id'], 'tag' => $row['raw_tag']);
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

	public function getTimeframe() {
		if (isset($this->form['timeframe']) && preg_match('/^(?:\d\d\d\d|day|week|month|year)$/', $this->form['timeframe'])) {
			return $this->form['timeframe'];
		}
		return null;
	}

	public function getContributors() {
		static $rv = NULL;
		if (is_null($rv)) {
			$rv = array();
			if (isset($this->form['contributors']) && is_array($this->form['contributors'])) {
				$order = array_flip($this->form['contributors']);
				foreach (Db::query('SELECT name, uidNumber FROM jos_xprofiles WHERE uidNumber IN ('.implode(', ', array_fill(0, count($this->form['contributors']), '?')).')', $this->form['contributors']) as $row) {
					$rv[] = array('id' => $row['uidNumber'], 'name' => $row['name']);
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

	public function getGroupName($gid) {
		static $map = array();
		if (!isset($map[$gid])) {
			$map[$gid] = Db::scalarQuery('SELECT description FROM jos_xgroups WHERE gidNumber = ? OR cn = ?', array($gid, $gid));
		}
		return $map[$gid];
	}

	public function getGroup() {
		static $group = NULL;
		if (!$group) {
			if (isset($this->form['gid'])) {
				$group = $this->form['gid'];
			}
			else if (isset($this->form['group'])) {
				$group = $this->form['group'];
			}
			if ($group) {
				$group = Db::scalarQuery('SELECT gidNumber FROM jos_xgroups WHERE gidNumber = ? OR cn = ?', array($group, $group));
			}
		}
		return $group;
	}

	private static function idList($coll) {
		return implode(',', array_map(function($item) { return $item['id']; }, $coll));
	}

	public function getTransportCriteria($merge = array()) {
		static $crit;
		if (is_null($crit)) {
			$user = JFactory::getUser();
			$groups = array();
			$super = FALSE;
			if (($uid = $user->get('id'))) {
				$super = $user->usertype === 'Super Administrator';
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
				'contributors' => self::idList($this->getContributors()),
				'offset'       => isset($_GET['offset']) ? (int)$_GET['offset'] : 0,
				'super'        => $super,
				'uid'          => $uid,
				'groups'       => $groups,
				'timeframe'    => $this->getTimeframe(),
				'inGroup'      => $this->getGroup()
			);
		}
		return array_merge($merge, $crit);
	}

	public function getDomain() {
		if (isset($this->form['domain'])) {
			return $this->form['domain'];
		}
		if (isset($this->form['option']) && $this->form['option'] == 'com_resources') {
			if (isset($this->form['type']) && ($type = Db::scalarQuery('SELECT type FROM jos_resource_types WHERE alias = ?', array($this->form['type'])))) {
				return 'Resources~'.$type;
			}
			return 'Resources';
		}
		return '';
	}

	public function getDomainMap() {
		$map = array();
		if (($parts = array_map('ucfirst', explode('~', $this->getDomain())))) {
			$lineage = '';
			foreach ($parts as $part) {
				$map[($lineage ? $lineage.'~' : '').$part] = TRUE;
				$lineage .= ($lineage ? '~'.$part : $part);
			}
		}
		return $map;
	}

	public function anyCriteria() {
		foreach ($this->getTransportCriteria() as $key=>$crit) {
			switch ($key) {
				case 'offset': case 'super': case 'uid': case 'groups':
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

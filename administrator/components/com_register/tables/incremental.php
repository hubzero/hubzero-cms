<?php

class ModIncrementalRegistrationGroups
{
	private static $possibleCols = array(), $dbh = NULL;

	public function __construct() {
		if (!self::$dbh) {
			self::$dbh = JFactory::getDBO();
		}
		if (!self::$possibleCols) {
			self::$dbh->setQuery('SELECT field, label FROM #__incremental_registration_labels ORDER BY label');
			self::$possibleCols = array_map(function($v) { return $v['label']; }, self::$dbh->loadAssocList('field'));
		}
	}

	public function getAllGroups() {
		$groups = array();
		self::$dbh->setQuery(
			'SELECT group_id, hours, irl.field
			FROM #__incremental_registration_groups irg
			LEFT JOIN #__incremental_registration_group_label_rel irglr ON irglr.group_id = irg.id
			INNER JOIN #__incremental_registration_labels irl ON irl.id = irglr.label_id
			ORDER BY hours'
		);
		foreach (self::$dbh->loadAssocList() as $row) {
			if (!isset($groups[$row['group_id']])) {
				$groups[$row['group_id']] = array(
					'hours' => $row['hours'],
					'cols'  => array()
				);
			}
			$groups[$row['group_id']]['cols'][] = $row['field'];
		}
		return array_values($groups);
	}

	public function getPossibleColumns() {
		return self::$possibleCols;
	}

	public function getActiveColumns($uid) {
		$uid = (int)$uid;
		self::$dbh->setQuery('SELECT irl.* FROM #__incremental_registration_groups irg LEFT JOIN #__incremental_registration_group_label_rel irglr ON irglr.group_id = irg.id INNER JOIN #__incremental_registration_labels irl ON irl.id = irglr.label_id WHERE irg.hours <= (SELECT (unix_timestamp(current_timestamp) - unix_timestamp(registerDate))/60/60 AS hours FROM jos_users WHERE id = '.$uid.')');
		$cols = self::$dbh->loadAssocList();
		// no data we want, don't bother
		if (!$cols) {
			return array();
		}
		self::$dbh->setQuery('SELECT coalesce((SELECT bothered_times FROM #__profile_completion_awards WHERE user_id = '.$uid.'), 0)');
		$times = self::$dbh->loadResult();
		if ($times > 0) {
			self::$dbh->setQuery('SELECT unix_timestamp(last_bothered)/60/60 > (SELECT coalesce((SELECT hours FROM #__incremental_registration_popover_recurrence WHERE idx = '.($times - 1).'), (SELECT hours FROM #__incremental_registration_popover_recurrence ORDER BY idx DESC LIMIT 1))) FROM #__profile_completion_awards WHERE user_id = '.$uid);
			// opt-out period is in effect
			if (!self::$dbh->loadResult()) {
				return array();
			}
		}
		$colNames = array();
		foreach ($cols as $col) {
			$colNames[] = $col['field'];
		}
		self::$dbh->setQuery('SELECT '.implode(', ', $colNames).' FROM #__xprofiles WHERE uidNumber = '.$uid);
		$profile = self::$dbh->loadAssoc();
		$neededCols = array();
		foreach ($cols as $col) {
			if (!trim($profile[$col['field']])) {
				$neededCols[$col['field']] = $col['label'];
			}
		}
		return $neededCols;
	}
}

class ModIncrementalRegistrationOptions
{
	private static $current = NULL;

	public function getAwardPerField() {
		$cur = self::getCurrent();
		return $cur['award_per'];
	}
	
	public function isEnabled($uid = NULL) {
		if (!$uid) {
			$uid = (int)JFactory::getUser()->get('id');
		}
		if (!$uid || !JModuleHelper::isEnabled('incremental_registration')) {
			return false;
		}
		$cur = self::getCurrent();
		if (!$cur['test_group']) {
			return true;
		}
		$dbh = JFactory::getDBO();
		$dbh->setQuery('SELECT 1 FROM #__xgroups_members xme WHERE xme.gidNumber = '.$cur['test_group'].' AND xme.uidNumber = '.$uid.' UNION SELECT 1 FROM #__xgroups_managers xma WHERE xma.gidNumber = '.$cur['test_group'].' AND xma.uidNumber = '.$uid.' LIMIT 1');
		return (bool)$dbh->loadResult();
	}

	public function isCurlEnabled($uid = NULL) {
		if (!$this->isEnabled($uid)) {
			return false;
		}
		if (!$uid) {
			$uid = (int)JFactory::getUser()->get('id');
		}
		$dbh = JFactory::getDBO();
		$dbh->setQuery('SELECT edited_profile FROM #__profile_completion_awards WHERE user_id = '.$uid);
		return !$dbh->loadResult();
	}

	private static function getCurrent() {
		if (!self::$current) {
			$dbh = JFactory::getDBO();
			$dbh->setQuery('SELECT * FROM #__incremental_registration_options ORDER BY added DESC LIMIT 1');
			self::$current = $dbh->loadAssoc();
		}
		return self::$current;
	}
}

class ModIncrementalRegistrationAwards
{
	private static $dbh;
	private $awards, $uid;

	private static function getDbh() {
		if (!self::$dbh) {
			self::$dbh = JFactory::getDBO();
		}
		return self::$dbh;
	}

	public function __construct($profile) {
		$this->profile = $profile;
		$this->uid = is_integer($profile) ? $profile : (int)$this->profile->get('uidNumber');
		self::getDbh();
		do {
			self::$dbh->setQuery('SELECT opted_out, name, orgtype, organization, countryresident, countryorigin, gender, url, reason, race, phone, picture FROM #__profile_completion_awards WHERE user_id = '.$this->uid);
			if (!($this->awards = self::$dbh->loadAssoc())) {
				self::$dbh->execute('INSERT INTO #__profile_completion_awards(user_id) VALUES ('.$this->uid.')');
			}
		} while (!$this->awards);
	}

	public function optOut() {
		self::$dbh->execute('UPDATE #__profile_completion_awards SET opted_out = opted_out + 1, last_bothered = CURRENT_TIMESTAMP WHERE user_id = '.$this->uid);
	}
	
	public function award() {
		if (!$this->uid) {
			return NULL;
		}
		$opts = new ModIncrementalRegistrationOptions;
		$awardPer = $opts->getAwardPerField();
		
		$fieldMap = array(
			'name'            => 'Fullname',
			'orgtype'         => 'Employment',
			'organization'    => 'Organization',
			'countryorigin'   => 'Citizenship',
			'countryresident' => 'Residency',
			'gender'          => 'Sex',
			'url'             => 'URL',
			'reason'          => 'Reason',
			'race'            => 'Race',
			'phone'           => 'Phone'
		);
		$alreadyComplete = 0;
		$eligible = array();
		$newAmount = 0;
		$completeSql = 'UPDATE #__profile_completion_awards SET edited_profile = 1';
		$optedOut = NULL;
		foreach ($this->awards as $k=>$complete) {
			if ($k === 'opted_out') {
				$optedOut = $complete;
				continue;
			}
			if ($complete) {
				continue;
			}
			if ($k === 'picture') {
				self::$dbh->setQuery('SELECT picture FROM #__xprofiles WHERE uidNumber = '.$this->uid);
				if (self::$dbh->loadResult()) {
					$completeSql .= ', '.$k.' = 1'; 
					$alreadyComplete += $awardPer;
				}
				else {
					$eligible['picture'] = 1;
				}
				continue;
			}
			$regField = $fieldMap[$k];
			if ((bool)$this->profile->get($k)) {
				$completeSql .= ', '.$k.' = 1';
				$alreadyComplete += $awardPer;
			}
			else {
				$eligible[$k == 'url' ? 'web' : $k] = 1;
			}
		}
	        self::$dbh->setQuery('SELECT SUM(amount) AS amount FROM #__users_transactions WHERE type = \'deposit\' AND category = \'registration\' AND uid = '.$this->uid);		
		$prior = self::$dbh->loadResult();
		self::$dbh->execute($completeSql.' WHERE user_id = '.$this->uid);

		if ($alreadyComplete) {
			self::$dbh->setQuery('SELECT COALESCE((SELECT balance FROM #__users_transactions WHERE uid = '.$this->uid.' AND id = (SELECT MAX(id) FROM #__users_transactions WHERE uid = '.$this->uid.')), 0)');
			$newAmount = self::$dbh->loadResult() + $alreadyComplete;

			self::$dbh->execute('INSERT INTO #__users_transactions(uid, type, description, category, amount, balance) VALUES('.$this->uid.', \'deposit\', \'Profile completion award\', \'registration\', '.$alreadyComplete.', '.$newAmount.')');

		}
		return array(
			'prior'     => $prior,
			'new'       => $alreadyComplete,
			'eligible'  => $eligible,
			'opted_out' => $optedOut
		);
	}
}

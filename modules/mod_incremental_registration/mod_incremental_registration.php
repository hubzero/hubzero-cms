<?php

class ModIncrementalRegistrationMediaPath
{
	private $tpl_path, $mod_path = '/modules/mod_incremental_registration';
	public function __construct()
	{
		$this->tpl_path = '/templates/'.JFactory::getApplication()->getTemplate().'/html/mod_incremental_registration';
	}

	public function get($path)
	{
		return file_exists($this->tpl_path.$path) ? $this->tpl_path.$path : $this->mod_path.$path;
	}
}

class ModIncrementalRegistrationController
{
	public function __construct()
	{
		$user = JFactory::getUser();
		if ($user->guest) {
			return;
		}
		$uid = (int)$user->get('id');
		$dbg = isset($_GET['incrdebug']);

		$dbh = JFactory::getDBO();
		if ($dbg) {
			require_once JPATH_BASE.'/administrator/components/com_register/tables/incremental.php';
			$opts = new ModIncrementalRegistrationOptions;
			echo $opts->isEnabledFor($uid) ? 'yes' : 'no';
			$groups = new ModIncrementalRegistrationGroups;
			print_r($groups->getActiveColumns($uid));
		}
		return;

		if (!isset($_SESSION['marked-login']))
		{
			$dbh->execute('INSERT INTO #__profile_completion_awards(user_id) VALUES('.$uid.') ON DUPLICATE KEY update logins = logins + 1');
			$_SESSION['marked-login'] = true;
		}

		if (isset($_POST['incremental-registration']) && isset($_POST['submit']) && $_POST['submit'] === 'opt-out')
		{
			$dbh->execute('UPDATE #__profile_completion_awards SET last_bothered = CURRENT_TIMESTAMP, opted_out = opted_out + 1 WHERE user_id = '.$uid);
			$_SESSION['incremental-temp-opt-out'] = true;
			return;
		}
		$interval = '2 day';
		$dbh->setQuery('SELECT opted_out, date_sub(current_timestamp, interval '.$interval.') > last_bothered AS botherable FROM #__profile_completion_awards WHERE user_id = '.$uid);

		$prefs = $dbh->loadAssoc();
		if ($prefs['opted_out'] && !$prefs['botherable'])
			return;

		$media = new ModIncrementalRegistrationMediaPath;
		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['REDIRECT_REQUEST_URI'];
		if (!isset($_SESSION['return']) && !preg_match('/[.]/', $uri))
			$_SESSION['return'] = $uri;

		$show_test = isset($_GET['popup_please']);		
		if ($show_test || ($prefs['botherable'] && !preg_match('%/session/%', $uri)))
		{
			$dbh->setQuery('SELECT orgtype = \'\' AS orgtype, organization = \'\' AS organization, reason = \'\' AS reason FROM #__xprofiles WHERE uidNumber = '.$uid);
			$row = $dbh->loadAssoc();
			if ($show_test || $row['orgtype'] || $row['organization'] || $row['reason'])
			{
				$doc = JFactory::getDocument();
				$doc->addStylesheet($media->get('/incremental_registration.css'));
				$errors = array();
				if (isset($_POST['incremental-registration']))
				{
					$orgtype = null;
					$organization = null;
					$reason = null;
					if (isset($_POST['orgtype']) && trim($_POST['orgtype']))
						$orgtype = trim($_POST['orgtype']);
						
					if (isset($_POST['org-other']) && trim($_POST['org-other']))
						$organization = trim($_POST['org-other']);
					elseif (isset($_POST['org']) && trim($_POST['org']))
						$organization = trim($_POST['org']);

					if (isset($_POST['reason-other']) && trim($_POST['reason-other']))
						$reason = trim($_POST['reason-other']);
					elseif (isset($_POST['reason']) && trim($_POST['reason']))
						$reason = trim($_POST['reason']);

					if ($row['orgtype'] && !$orgtype)
						$errors['orgtype'] = true;
					if ($row['organization'] && !$organization)
						$errors['organization'] = true;
					if ($row['reason'] && !$reason)
						$errors['reason'] = true;

					if (!$errors)
					{
						$base_award = ($row['orgtype'] ? 15 : 0) + ($row['organization'] ? 15 : 0) + ($row['reason'] ? 15 : 0);
						$dbh->setQuery('SELECT organization, orgtype, reason FROM #__profile_completion_awards WHERE user_id = '.$uid);
						$awarded = $dbh->loadAssoc();
						$award = $base_award + (!$row['orgtype'] && !$awarded['orgtype'] ? 15 : 0) + (!$row['organization'] && !$awarded['organization'] ? 15 : 0) + (!$row['reason'] && !$awarded['reason'] ? 15 : 0);

						$dbh->setQuery('SELECT COALESCE((SELECT balance FROM #__users_transactions WHERE uid = '.$uid.' AND id = (SELECT MAX(id) FROM #__users_transactions WHERE uid = '.$uid.')), 0)');
						$new_amount = $dbh->loadResult() + $award;

						$dbh->execute('INSERT INTO #__users_transactions(uid, type, description, category, amount, balance) VALUES 
							('.$uid.', \'deposit\', \'Profile completion award\', \'registration\', '.$award.', '.$new_amount.')');

						$xp_update = 'UPDATE #__xprofiles SET ';
						$aw_update = 'UPDATE #__profile_completion_awards SET ';
						$first = true;
						foreach (array('orgtype', 'organization', 'reason') as $k)
							if ($row[$k])
							{
								$xp_update .= ($first ? '' : ', ').$k.' = '.$dbh->quote($$k);
								$aw_update .= ($first ? '' : ', ').$k.' = 1';
								$first = false;
							}
						$dbh->execute($xp_update.' WHERE uidNumber = '.$uid);
						$dbh->execute($aw_update.' WHERE user_id = '.$uid);
						
						require JPATH_BASE.$media->get('/views/thanks.php');
						return;
					}
				}
				require JPATH_BASE.$media->get('/views/group2.php');
			}
			return;
		}
		if ((!$prefs['opted_out'] || $prefs['botherable']) && !preg_match('%^/members/\d+/edit%', $uri))
		{
			$dbh->setQuery('SELECT opted_out, logins, orgtype = \'\' or organization = \'\' or reason = \'\' AS available FROM #__profile_completion_awards WHERE user_id = '.$uid);
			$row = $dbh->loadAssoc();
			if ($row['available'])
			{
				$doc = JFactory::getDocument();
				$doc->addStylesheet($media->get('/incremental_registration.css'));
				$doc->addScript($media->get('/incremental_registration.js'));
				require JPATH_BASE.$media->get('/views/curl.php');
			}
		}
	}
}

new ModIncrementalRegistrationController;

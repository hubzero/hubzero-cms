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

		$dbh = JFactory::getDBO();
		
		require_once JPATH_BASE.'/administrator/components/com_register/tables/incremental.php';

		$opts = new ModIncrementalRegistrationOptions;
		if (!$opts->isEnabled($uid)) {
			return;
		}

		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['REDIRECT_REQUEST_URI'];
		if (preg_match('%^/(?:session|legal/privacy)%', $uri)) {
			return;
		}
	
		if (isset($_POST['incremental-registration']) && isset($_POST['submit']) && $_POST['submit'] === 'opt-out') {
			$awards = new ModIncrementalRegistrationAwards($uid);
			$awards->optOut();
			return;
		}

		$media = new ModIncrementalRegistrationMediaPath;
		$groups = new ModIncrementalRegistrationGroups;
		if (($row = $groups->getActiveColumns($uid)) || $opts->isCurlEnabled()) {
			if (!isset($_SESSION['return']) && !preg_match('/[.]/', $uri)) {
				$_SESSION['return'] = $uri;
			}
			$doc = JFactory::getDocument();
			$doc->addStylesheet($media->get('/incremental_registration.css'));
			$doc->addScript($media->get('/incremental_registration.js'));

			if ($row) {
				if ($_SERVER['REQUEST_METHOD'] == 'GET') {
					require JPATH_BASE.$media->get('/views/popover.php');
				}
				elseif (isset($_POST['incremental-registration']) && $_POST['incremental-registration'] == 'update') {
					$errors = array();
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

					if (isset($row['orgtype']) && !$orgtype)
						$errors['orgtype'] = true;
					if (isset($row['organization']) && !$organization)
						$errors['organization'] = true;
					if (isset($row['reason']) && !$reason)
						$errors['reason'] = true;

					if ($errors) {
						require JPATH_BASE.$media->get('/views/popover.php');
					}
					else {
						$base_award = (isset($row['orgtype']) ? 15 : 0) + (isset($row['organization']) ? 15 : 0) + (isset($row['reason']) ? 15 : 0);
						$dbh->setQuery('SELECT organization, orgtype, reason FROM #__profile_completion_awards WHERE user_id = '.$uid);
						$awarded = $dbh->loadAssoc();
						$award = $base_award + (!isset($row['orgtype']) && !$awarded['orgtype'] ? 15 : 0) + (!isset($row['organization']) && !$awarded['organization'] ? 15 : 0) + (!isset($row['reason']) && !$awarded['reason'] ? 15 : 0);

						$dbh->setQuery('SELECT COALESCE((SELECT balance FROM #__users_transactions WHERE uid = '.$uid.' AND id = (SELECT MAX(id) FROM #__users_transactions WHERE uid = '.$uid.')), 0)');
						$new_amount = $dbh->loadResult() + $award;

						$dbh->execute('INSERT INTO #__users_transactions(uid, type, description, category, amount, balance) VALUES 
							('.$uid.', \'deposit\', \'Profile completion award\', \'registration\', '.$award.', '.$new_amount.')');

						$xp_update = 'UPDATE #__xprofiles SET ';
						$aw_update = 'UPDATE #__profile_completion_awards SET edited_profile = 1, ';
						$first = true;
						foreach (array('orgtype', 'organization', 'reason') as $k)
							if (isset($row[$k]))
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
			}
			else if (!preg_match('%^/members/'.$uid.'/profile%', $uri)) {
				require JPATH_BASE.$media->get('/views/curl.php');
			}
		}
	}
}

new ModIncrementalRegistrationController;

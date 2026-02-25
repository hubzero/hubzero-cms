<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\IncrementalRegistration;

use Hubzero\Module\Module;
use Components\Members\Models\Incremental\Options;
use Components\Members\Models\Incremental\Groups;
use Components\Members\Models\Incremental\Awards;
use Hubzero\Facades\Component;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Document;

/**
 * Incremental Registration Module controller class
 */
class IncrementalRegistration extends Module
{
    /**
     * Path to template overrides
     *
     * @var array
     */
    private $tpl_path = '/core/templates/system/html/mod_incremental_registration';

    /**
     * Path to module directory
     *
     * @var array
     */
    private $mod_path = '/core/modules/mod_incremental_registration/assets';

    /**
     * Determines if the file ($path) passed to it exists
     *
     * @param   string  $path  File path
     * @return  string
     */
    public function media($path)
    {
        $this->tpl_path = App::get('template')->path . '/html/mod_incremental_registration';

        $b = rtrim(Request::base(true), '/');
        if (file_exists(PATH_APP . $this->tpl_path . $path)) {
            return $b . $this->tpl_path . $path;
        }
        return $b . $this->mod_path . $path;
    }

    /**
     * Display module contents
     *
     * @return  void
     */
    public function display()
    {
        if (User::isGuest()) {
            return;
        }

        $dbg = isset($_GET['dbg']);
        $uid = (int) User::get('id');
        $dbh = App::get('db');

        $opts = new Options();
        if (!$opts->isEnabled($uid)) {
            return;
        }

        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['REDIRECT_REQUEST_URI'];
        if (preg_match('%(?:members|invoke|session|privacy)%', $uri)) {
            return;
        }

        // looks like an error page, don't show
        if (Document::getType() === 'error') {
            return;
        }

        if (isset($_POST['incremental-registration']) && isset($_POST['submit']) && $_POST['submit'] === 'opt-out') {
            $awards = new Awards($uid);
            $awards->optOut();
            return;
        }

        $groups = new Groups();

        $hasCurl = file_exists(__DIR__ . '/assets/img/bigcurl.png');
        if (($row = $groups->getActiveColumns($uid)) || $hasCurl) {
            if (!isset($_SESSION['return']) && !preg_match('/[.]/', $uri)) {
                $_SESSION['return'] = $uri;
            }

            $this->css();
            $this->js();

            if ($row) {
                $dbh->setQuery(
                    'SELECT popover_text, award_per FROM `#__incremental_registration_options` ' .
                    'ORDER BY added DESC LIMIT 1'
                );
                list($introText, $awardPer) = $dbh->loadRow();

                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    require $this->getLayoutPath('popover');
                } elseif (isset($_POST['incremental-registration']) && $_POST['incremental-registration'] == 'update') {
                    $errors       = array();
                    $orgtype      = null;
                    $organization = null;
                    $reason       = null;
                    $location     = null;
                    $name         = null;
                    $mailPreferenceOption = -1;

                    if (isset($_POST['mailPreferenceOption'])) {
                        $mailPreferenceOption = (int)$_POST['mailPreferenceOption'];
                    }
                    if (isset($_POST['orgtype']) && trim($_POST['orgtype'])) {
                        $orgtype = trim($_POST['orgtype']);
                    }
                    if (isset($_POST['org-other']) && trim($_POST['org-other'])) {
                        $organization = trim($_POST['org-other']);
                    } elseif (isset($_POST['org']) && trim($_POST['org'])) {
                        $organization = trim($_POST['org']);
                    }

                    if (isset($_POST['reason-other']) && trim($_POST['reason-other'])) {
                        $reason = trim($_POST['reason-other']);
                    } elseif (isset($_POST['reason']) && trim($_POST['reason'])) {
                        $reason = trim($_POST['reason']);
                    }

                    $nameInput = Request::getArray('name', array());
                    if (!empty($nameInput)) {
                        if (empty($nameInput['first']) || empty($nameInput['last'])) {
                            $errors['name'] = true;
                        }
                        $name = preg_replace(
                            '/\s+/',
                            ' ',
                            trim(implode(' ', array(
                                $nameInput['first'] ?? '',
                                $nameInput['middle'] ?? '',
                                $nameInput['last'] ?? ''
                            )))
                        );
                    }

                    if (isset($row['gender'])) {
                        $validGender = in_array(
                            $_POST['gender'] ?? '',
                            ['male', 'female', 'refused']
                        );
                        if (!isset($_POST['gender']) || !$validGender) {
                            $errors['gender'] = true;
                        } else {
                            $gender = $_POST['gender'];
                        }
                    }

                    if (isset($_POST['url'])) {
                        if (!trim($_POST['url'])) {
                            $errors['url'] = true;
                        }
                        $url = trim($_POST['url']);
                    }

                    if (isset($_POST['phone'])) {
                        if (!trim($_POST['phone'])) {
                            $errors['phone'] = true;
                        }
                        $phone = trim($_POST['phone']);
                    }

                    if (isset($row['race'])) {
                        if (empty($_POST['race']) || !is_array($_POST['race'])) {
                            $errors['race'] = true;
                        } else {
                            $race = array_map('trim', $_POST['race']);
                        }
                    }

                    if (isset($row['countryorigin'])) {
                        if (isset($_POST['countryorigin_us']) && $_POST['countryorigin_us'] == 'yes') {
                            $countryorigin = 'us';
                        } elseif (
                            !isset($_POST['countryorigin'])
                            || !preg_match('/[A-Za-z]{2}/', $_POST['countryorigin'])
                        ) {
                            $errors['countryorigin'] = true;
                        } else {
                            $countryorigin = $_POST['countryorigin'];
                        }
                        // race does not apply to non-us
                        if (isset($countryorigin) && strtolower($countryorigin) != 'us' && isset($errors['race'])) {
                            unset($errors['race']);
                        }
                    }
                    if (isset($row['countryresident'])) {
                        if (isset($_POST['countryresident_us']) && $_POST['countryresident_us'] == 'yes') {
                            $countryresident = 'us';
                        } elseif (
                            !isset($_POST['countryresident'])
                            || !preg_match('/[A-Za-z]{2}/', $_POST['countryresident'])
                        ) {
                            $errors['countryresident'] = true;
                        } else {
                            $countryresident = $_POST['countryresident'];
                        }
                    }

                    if (isset($row['disability'])) {
                        $noSpecific = !isset($_POST['specificDisability'])
                            || !$_POST['specificDisability'];
                        $noOther = !isset($_POST['otherDisability'])
                            || !trim($_POST['otherDisability']);
                        if (
                            !isset($_POST['disability'])
                            || ($_POST['disability'] == 'yes' && ($noSpecific && $noOther))
                        ) {
                            $errors['disability'] = true;
                        }
                    }

                    if (isset($row['orgtype']) && !$orgtype) {
                        $errors['orgtype'] = true;
                    }
                    if (isset($row['organization']) && !$organization) {
                        $errors['organization'] = true;
                    }
                    if (isset($row['reason']) && !$reason) {
                        $errors['reason'] = true;
                    }
                    if (isset($row['mailPreferenceOption']) && $mailPreferenceOption == -1) {
                        $errors['mailPreferenceOption'] = true;
                    }
                    if (isset($row['location']) && !$location) {
                        if (isset($_POST['location'])) {
                            $location = trim($_POST['location']);
                        } else {
                            $errors['location'] = true;
                        }
                    }

                    if ($errors) {
                        require $this->getLayoutPath('popover');
                    } else {
                        $dbh->setQuery(
                            'SELECT ' . implode(', ', array_keys($row)) .
                            ' FROM #__profile_completion_awards WHERE user_id = ' . $uid
                        );
                        $award = 0;
                        $awarded = $dbh->loadAssoc();

                        if (!empty($awarded)) {
                            foreach ($awarded as $v) {
                                if (!$v) {
                                    $award += $awardPer;
                                }
                            }
                        }

                        // Old query:
                        // $dbh->setQuery('SELECT COALESCE((SELECT balance FROM
                        // `#__users_transactions` WHERE uid = ' . $uid . ' AND id =
                        // (SELECT MAX(id) FROM `#__users_transactions` WHERE uid = '
                        // . $uid . ')), 0)');
                        $dbh->setQuery('SELECT balance FROM `#__users_points` WHERE uid = ' . $uid);
                        $new_amount = intval($dbh->loadResult()) + $award;

                        if ($award) {
                            $BTL = new \Hubzero\Bank\Teller($uid);
                            $BTL->deposit(
                                $award,
                                Lang::txt('MOD_INCREMENTAL_REGISTRATION_PROFILE_COMPLETION_AWARD'),
                                'registration',
                                0
                            );
                        }

                        $aw_update = 'UPDATE `#__profile_completion_awards` SET edited_profile = 1, ' .
                            'last_bothered = CURRENT_TIMESTAMP, bothered_times = bothered_times + 1,  ';
                        $first = true;
                        foreach (array_keys($row) as $k) {
                            if ($k == 'race') {
                                if (isset($race)) {
                                    $profiles = \Components\Members\Models\Profile::all()
                                        ->whereEquals('profile_key', $k)
                                        ->whereEquals('user_id', $uid)
                                        ->rows();

                                    $found = array();
                                    foreach ($profiles as $profile) {
                                        if (in_array($profile->get('profile_value'), $race)) {
                                            $found[] = $profile->get('profile_value');
                                            continue;
                                        }

                                        $profile->destroy();
                                    }

                                    foreach ($race as $r) {
                                        if (in_array($r, $found)) {
                                            continue;
                                        }

                                        $profile = \Components\Members\Models\Profile::blank();
                                        $profile->set('profile_key', $k);
                                        $profile->set('profile_value', $r);
                                        $profile->set('user_id', $uid);
                                        $profile->set('access', 5);
                                        $profile->save();
                                    }
                                }
                                continue;
                            }
                            if ($k == 'disability') {
                                $disabilities = array();
                                switch ($_POST['disability']) {
                                    case 'yes':
                                        $disabilities = array();
                                        $isSpecificArray = isset($_POST['specificDisability'])
                                            && is_array($_POST['specificDisability']);
                                        if ($isSpecificArray) {
                                            $disabilities = $_POST['specificDisability'];
                                        }
                                        $hasOther = isset($_POST['otherDisability'])
                                            ? trim($_POST['otherDisability'])
                                            : null;
                                        if ($hasOther) {
                                            $disabilities[] = $hasOther;
                                        }
                                        break;
                                    case 'no':
                                        $disabilities[] = 'none';
                                        break;
                                    case 'refused':
                                        $disabilities[] = 'refused';
                                        break;
                                }
                                $profiles = \Components\Members\Models\Profile::all()
                                    ->whereEquals('profile_key', $k)
                                    ->whereEquals('user_id', $uid)
                                    ->rows();

                                $found = array();
                                foreach ($profiles as $profile) {
                                    if (in_array($profile->get('profile_value'), $disabilities)) {
                                        $found[] = $profile->get('profile_value');
                                        continue;
                                    }

                                    $profile->destroy();
                                }

                                foreach ($disabilities as $r) {
                                    if (in_array($r, $found)) {
                                        continue;
                                    }

                                    $profile = \Components\Members\Models\Profile::blank();
                                    $profile->set('profile_key', $k);
                                    $profile->set('profile_value', $r);
                                    $profile->set('user_id', $uid);
                                    $profile->set('access', 5);
                                    $profile->save();
                                }
                                continue;
                            }
                            if ($k == 'name') {
                                $dbh->setQuery(
                                    'UPDATE `#__users` SET givenName = ' .
                                    $dbh->quote($_POST['name']['first']) . ', middleName = ' .
                                    $dbh->quote($_POST['name']['middle']) . ', surname = ' .
                                    $dbh->quote($_POST['name']['last']) . ' WHERE id = ' . $uid
                                );
                                $dbh->execute();
                            }
                            if ($k == 'mailPreferenceOption') {
                                $dbh->setQuery(
                                    'UPDATE `#__users` SET sendEmail = ' . $dbh->quote($$k) .
                                    ' WHERE id = ' . $uid
                                );
                                $dbh->execute();
                            }
                            if ($k == 'countryorigin' || $k == 'countryresident') {
                                $$k = strtoupper($$k);
                            }
                            if (isset($row[$k])) {
                                $aw_update .= ($first ? '' : ', ') . $k . ' = 1';
                                $first = false;
                            }
                            $profile = \Components\Members\Models\Profile::oneByKeyAndUser($k, $uid);
                            $profile->set('profile_key', $k);
                            $profile->set('profile_value', $$k);
                            $profile->set('user_id', $uid);
                            $profile->set('access', $profile->get('access', 5));
                            $profile->save();
                        }
                        if (!$first) {
                            $dbh->setQuery($aw_update . ' WHERE user_id = ' . $uid);
                            $dbh->execute();
                        }

                        require $this->getLayoutPath('thanks');
                        return;
                    }
                }
            } elseif (!preg_match('%^/members/' . $uid . '/profile%', $uri) && $hasCurl) {
                // check if there's anything we might ask about in the future. if so, show
                // a curl tempting the user to be proactive and fill in more of their profile
                list($allGroups) = $groups->getAllGroups();
                $dbh->setQuery(
                    'SELECT ' . implode(', ', $allGroups['cols']) .
                    ' FROM #__profile_completion_awards WHERE user_id = ' . $uid
                );
                $row = $dbh->loadRow();
                // never entered anything so far, or entered partial information w/r/t requested profile groups
                if (
                    $row === null || array_filter($row, function ($col) {
                        return $col === 0;
                    })
                ) {
                    require $this->getLayoutPath('curl');
                }
            }
        }
    }
}

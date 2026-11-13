<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\Domainrestriction;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;
use Hubzero\Utility\Network;

/**
 * User plugin for blocking registration by domains or IPs
 */
class Domainrestriction extends Plugin
{
    private $tlds;
    private $domains;
    private $emails;
    private $badtlds;
    private $baddomains;
    private $bademails;
    /** @var string|null */
    private $email;
    private $domain;
    private $tld;
    private $allowed;

    /**
     * Utility method to act on a user before it has been saved.
     *
     * @param   array    $user     Holds the new user data.
     * @param   boolean  $isNew    True if a new user is stored.
     * @param   array  $new  True if user was succesfully stored in the database.
     * @return  bool
     */
    public function onUserBeforeSave($user, $isNew, $new)
    {
        if (App::isAdmin()) {
            return true;
        }

        if ($isNew) {
            $listtest = $this->blackwhite($this->getIP());

            if ($listtest === true) {
                return true;
            }

            if ($listtest !== false) {
                Notify::warning(Lang::txt('PLG_USER_DOMAINRESTRICTION_DENY'));
                return false;
            }
        } else {
            if ($this->params->get('ignorechange', 1)) {
                return true;
            }
        }

        $result = true;

        // retrieve and clean up domain and email params
        $this->sortParams();
        $this->parseEmail($new['email']);

        //var_dump($this->domains); die;

        // Here is the logic: allowed takes precedence over disallowed: if the domain/email is both
        // allowed and disallowed it will be allowed. If the domain/email both neither allowed,
        // nor disallowed it will be allowed.
        // I'm open to suggestions

        // Presumption of innocence: allowed, unless disallowed
        $this->allowed = true;

        // Check if top level domain is disallowed
        if (in_array($this->tld, $this->badtlds)) {
            $this->allowed = false;
        }

        // Check if domain is disallowed
        if (in_array($this->domain, $this->baddomains)) {
            $this->allowed = false;
        }

        // Check if domain is allowed (may reverse two previous steps, and that is ok)
        if (in_array($this->domain, $this->domains)) {
            $this->allowed = true;
        }

        // Check if email is disallowed
        if (in_array($this->email, $this->bademails)) {
            $this->allowed = false;
        }

        // Check if email is allowed
        if (in_array($this->email, $this->emails)) {
            $this->allowed = true;
        }

        // Check for affiliated registration and let it go
        if ($this->domain == 'invalid') {
            $this->allowed = true;
        }

        if (!$this->allowed) {
            Lang::load('plg_user_domainrestriction', __DIR__);

            $message = $isNew ? 'PLG_USER_DOMAINRESTRICTION_DENY' : 'PLG_USER_DOMAINRESTRICTION_DENYCHANGE';
            Notify::error(Lang::txt($message));

            $result = false;
        }

        return $result;
    }

    /**
     * Utility method to act on a user after it has been saved.
     *
     * This method assigns the user to access groups based on plugin settings
     *
     * @param   array    $user     Holds the new user data.
     * @param   boolean  $isnew    True if a new user is stored.
     * @param   boolean  $success  True if user was succesfully stored in the database.
     * @param   string   $msg      Message.
     * @return  bool
     */
    public function onUserAfterSave($user, $isnew, $success, $msg)
    {
        if ($isnew) {
            $this->updateGroups($user);
        }

        return true;
    }

    /**
     * This method should handle any login logic and report back to the subject
     *
     * @param   array    $user     holds the user data
     * @param   array    $options  array holding options (remember, autoregister, group)
     * @return  boolean  True on success
     */
    public function onUserLogin($user, $options)
    {
        $this->updateGroups($user);
        return true;
    }

    /**
     * Get the user's IP
     *
     * @return  string
     */
    private function getIP()
    {
        return Request::ip();
    }

    /**
     * Check user's IP against whitelist/blacklist
     *
     * @param   string  $ip
     * @return  mixed
     */
    private function blackwhite($ip)
    {
        $lists = [
            'whitelist'    => [],
            'blacklist'    => [],
            'whitelistnet' => [],
            'blacklistnet' => [],
        ];

        foreach (['whitelist', 'blacklist'] as $name) {
            $default = ($name == 'blacklist')
                ? Lang::txt('PLG_USER_DOMAINRESTRICTION_DEFAULT_BLACKLIST')
                : Lang::txt('PLG_USER_DOMAINRESTRICTION_DEFAULT_WHITELIST');
            $items = explode("\n", trim(str_replace(
                ["\r", "\t", " "],
                ['', '', ''],
                $this->params->get($name, $default),
            )));

            foreach ($items as $item) {
                $item = trim($item);
                if (strpos($item, '/') !== false) {
                    $lists[$name . 'net'][] = $item;
                } else {
                    $lists[$name][] = $item;
                }
            }
        }

        // Single addresses compare as addresses, not strings, like the
        // networks below: 2001:DB8::1 or ::ffff:192.0.2.1 matches its entry
        if ($this->inList($ip, $lists['whitelist'])) {
            return true;
        }

        if ($this->inList($ip, $lists['blacklist'])) {
            return $ip;
        }

        $nets = array_merge($lists['whitelistnet'], $lists['blacklistnet']);
        if (count($nets)) {
            foreach ($lists['whitelistnet'] as $net) {
                $ipnet = $this->bwnet($net);

                if ($ipnet && $ipnet->contains($ip)) {
                    return true;
                }
            }

            foreach ($lists['blacklistnet'] as $net) {
                $ipnet = $this->bwnet($net);

                if ($ipnet && $ipnet->contains($ip)) {
                    return $net;
                }
            }
        }
        return false;
    }

    /**
     * Whether an address is one of a list of single addresses
     *
     * @param   string  $ip
     * @param   array   $addresses
     * @return  bool
     */
    private function inList($ip, $addresses)
    {
        foreach ($addresses as $address) {
            $network = $this->bwnet($address);

            if ($network && $network->contains($ip)) {
                return true;
            }
        }

        return false;
    }

    /**
     * A network from the white or black list
     *
     * An entry that is not a network is null, and matches nobody, rather
     * than stopping every sign-in.
     *
     * @param   string  $net
     * @return  Network|null
     */
    private function bwnet($net)
    {
        return Network::parse($net);
    }

    /**
     * Match email
     *
     * @param   bool  $allowed
     * @return  bool
     */
    private function decision($allowed = false)
    {
        $ret = $allowed
                ? $this->mailmatch(['tlds', 'domains', 'emails'])
                : !$this->mailmatch(['badtlds', 'baddomains', 'bademails']);
        return $ret;
    }

    /**
     * Match email address
     *
     * @param   array  $keys
     * @return  bool
     */
    private function mailmatch($keys = [])
    {
        $ret = false;

        //print_r($keys); die;

        if ($this->{$keys[0]} || $this->{$keys[1]} || $this->{$keys[2]}) {
            if (
                in_array($this->tld, $this->{$keys[0]})
                || in_array($this->domain, $this->{$keys[1]})
                || in_array($this->email, $this->{$keys[2]})
            ) {
                $ret = true;
            }
        }

        //echo '-->'; var_dump($ret); die('oops');

        return $ret;
    }

    /**
     * Sort params
     *
     * @return  void
     */
    private function sortParams()
    {
        foreach (['tld', 'domain', 'email', 'badtld', 'baddomain', 'bademail'] as $param) {
            $paramvalue = (string) $this->params->get($param, '');
            $arrayvar = $param . 's';

            $this->{$arrayvar} = [];
            $this->{$arrayvar} = array_map([$this, 'clean'], explode(',', $paramvalue));

            foreach ($this->{$arrayvar} as $key => $d) {
                if (!strlen(trim($d))) {
                    unset($this->{$arrayvar}[$key]);
                }
            }

            $this->{$arrayvar} = array_values($this->{$arrayvar});
        }
    }

    private static function clean($val)
    {
        return trim($val, " \t\n\r\0\x0B.");
    }

    /**
     * Parse email address
     *
     * @param   string  $email
     * @return  string
     */
    private function parseEmail($email)
    {
        $this->email = strtolower($email);

        $email = explode('@', $this->email);

        $this->domain = $email[1];
        $this->tld = $this->getTldFromUrl($email[1]);

        return $email[0];
    }

    /**
     * Update a user's groups
     *
     * @param   array  $user
     * @return  bool
     */
    private function updateGroups($user)
    {
        if (App::isAdmin()) {
            return true;
        }

        // Are there any auto-group assignments?
        $assignments = $this->getAssignments();
        if (!$assignments) {
            return true;
        }

        $user = $this->getUser($user['username']);

        $excludegroups = $this->params->get('excludegroup', []);
        foreach ($user->groups as $group) {
            if (in_array($group, (array) $excludegroups)) {
                return true;
            }
        }

        $emailuser = $this->parseEmail($user->email);
        $excluded = json_decode(str_replace('*', $emailuser, base64_decode($this->params->get('excludeauto', 'W10K'))));

        if (count($excluded) && in_array(strtolower($user->email), $excluded)) {
            return true;
        }

        $akey = $this->getAssignmentsKey($assignments);

        if ($akey) {
            $groupchange = false;
            foreach ($assignments[$akey] as $groupid) {
                if (!in_array($groupid, $user->groups)) {
                    \Hubzero\Access\Map::addUserToGroup($user->id, $groupid);
                    $groupchange = true;
                }
            }
            foreach ($user->groups as $groupid) {
                if (!in_array($groupid, $assignments[$akey])) {
                    \Hubzero\Access\Map::removeUserFromGroup($user->id, $groupid);
                    $groupchange = true;
                }
            }

            if ($groupchange) {
                $user->set('groups', \Hubzero\Access\Access::getGroupsByUser($user->id));
                $user->set('authlevels', \Hubzero\Access\Access::getAuthorisedViewLevels($user->id));
            }
        }
        return true;
    }

    /**
     * Get user by username
     *
     * @param   string  $username
     * @return  object
     */
    private function getUser($username)
    {
        return User::getInstance($username);
    }

    /**
     * Get assignments
     *
     * @return  mixed
     */
    private function getAssignments()
    {
        $assignments = json_decode(base64_decode($this->params->get('autogroups', 'W10K')));
        if (count($assignments)) {
            foreach ($assignments as $key => $assignment) {
                if (is_array($assignment)) {
                    $assignments[strtolower($assignment[0])] = $assignment[1];
                } else {
                    $assignments[strtolower($assignment->domain)] = $assignment->groups;
                }
                unset($assignments[$key]);
            }
        } else {
            $assignments = false;
        }
        return $assignments;
    }

    /**
     * Get assignment key
     *
     * @param   array  $assignments
     * @return  mixed
     */
    private function getAssignmentsKey($assignments)
    {
        return array_key_exists($this->email, $assignments) ? $this->email : (
            array_key_exists($this->domain, $assignments) ? $this->domain : (
                array_key_exists($this->tld, $assignments) ? $this->tld : false
            )
        );
    }

    /**
     * Get tld from URL
     *
     * @param   string  $url
     * @return  string
     */
    private function getTldFromUrl($url)
    {
        $url = strpos($url, '://') ? $url : 'http://' . $url;

        $host = parse_url($url);
        $domain = str_replace('__', '', $host['host']);
        $tail = strlen($domain) >= 7 ? substr($domain, -7) : $domain;
        $tld = strstr($tail, '.');

        return preg_replace('/^\./', '', $tld);
    }
}

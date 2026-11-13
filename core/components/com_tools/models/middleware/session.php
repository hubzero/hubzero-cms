<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models\Middleware;

use Components\Tools\Helpers\Utils;
use Hubzero\Base\ItemList;
use Hubzero\User\Group;
use Hubzero\Facades\User;
use Hubzero\Facades\Lang;

/**
 * Middleware model for a tool session
 */
class Session extends Base
{
    /**
     * Table class name
     *
     * @var  string
     */
    protected $tableClass = '\\Components\\Tools\\Tables\\Session';

    /**
     * \Hubzero\ItemList
     *
     * @var  array
     */
    private $cache = [
        'shared.count' => null,
        'shared.list'  => null,
    ];

    /**
     * Constructor
     *
     * @param   mixed   $oid         Integer (ID), string (alias), object or array
     * @param   string  $authorized  Authorization level
     * @return  void
     */
    public function __construct($oid = null, $authorized = null)
    {
        $this->db = Utils::getMWDBO();

        if ($this->tableClass) {
            $cls = $this->tableClass;
            $this->table = new $cls($this->db);

            // It is asked for loadSession() below, which the sessions table has
            if (!($this->table instanceof \Components\Tools\Tables\Session)) {
                $this->logError(
                    __CLASS__ . '::' . __FUNCTION__ . '(); '
                    . Lang::txt('Table class must be an instance of \\Components\\Tools\\Tables\\Session.'),
                );
                throw new \LogicException(
                    Lang::txt('Table class must be an instance of \\Components\\Tools\\Tables\\Session.'),
                );
            }

            if (is_numeric($oid) || is_string($oid)) {
                // Make sure $oid isn't empty
                // This saves a database call
                if ($oid) {
                    $obj = $this->table->loadSession($oid, $authorized);
                    if ($obj) {
                        $this->bind($obj);
                    }
                }
            } elseif (is_object($oid) || is_array($oid)) {
                $this->bind($oid);
            }
        }
    }

    /**
     * Returns a reference to an session model
     *
     * @param   mixed   $oid         Session ID or object
     * @param   string  $authorized  Authorization level
     * @return  object
     */
    public static function &getInstance($oid = null, $authorized = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        if (is_object($oid)) {
            $key = $oid->sessnum;
        } elseif (is_array($oid)) {
            $key = $oid['sessnum'];
        } else {
            $key = $oid;
        }

        if (!isset($instances[$key])) {
            $instances[$key] = new self($key, $authorized);
        }

        return $instances[$key];
    }

    /**
     * Check if the entry exists (i.e., has a database record)
     *
     * @return  boolean  True if record exists, False if not
     */
    public function exists()
    {
        if ($this->get('sessnum') && (int) $this->get('sessnum') > 0) {
            return true;
        }
        return false;
    }

    /**
     * Get a list of shared views
     *
     * @param   string   $rtrn     Data type to return [count, list]
     * @param   array    $filters  Filters to apply to query
     * @param   boolean  $clear    Clear cached data?
     * @return  mixed    Returns an integer or array depending upon format chosen
     */
    public function shared($rtrn = 'list', $filters = [], $clear = false)
    {
        $tbl = new \Components\Tools\Tables\Viewperm($this->db);

        if (!isset($filters['sessnum'])) {
            $filters['sessnum'] = $this->get('sessnum');
        }

        switch (strtolower($rtrn)) {
            case 'count':
                if (!isset($this->cache['shared.count']) || $clear) {
                    $this->cache['shared.count'] = count($this->shared('list', $filters));
                }
                return $this->cache['shared.count'];

            case 'list':
            case 'results':
            default:
                if (!($this->cache['shared.list'] instanceof ItemList) || $clear) {
                    if ($results = $tbl->loadViewperm($filters['sessnum'])) {
                        foreach ($results as $key => $result) {
                            $results[$key] = new Location($result);
                        }
                    } else {
                        $results = [];
                    }
                    $this->cache['shared.list'] = new ItemList($results);
                }
                return $this->cache['shared.list'];
        }
    }

    /**
     * Share a session
     *
     * @param   string  $with      List of users
     * @param   string  $group     Group to share with
     * @param   string  $readonly  More to share with
     * @return  boolean
     */
    public function share($with = null, $group = null, $readonly = 'Yes')
    {
        $users = [];

        if (strstr($with, ',')) {
            $users = explode(',', $with);
            $users = array_map('trim', $users);
        } elseif (strstr($with, ' ')) {
            $users = explode(' ', $with);
            $users = array_map('trim', $users);
        } else {
            $users[] = $with;
        }

        if ($group) {
            $hg = Group::getInstance($group);
            $members = $hg->get('members');

            // merge group members with any passed in username field
            $users = array_values(array_unique(array_merge($users, $members)));

            // remove this user
            $isUserInArray = array_search(User::get('id'), $users);
            if ($isUserInArray !== false) {
                unset($users[$isUserInArray]);
            }

            // fix array keys
            $users = array_values(array_filter($users));
        }

        if ($readonly != 'Yes') {
            $readonly = 'No';
        }

        // The owner's own view entry carries the connection details a share copies
        $sess = $this->get('sessnum');
        $mwViewperm = new \Components\Tools\Tables\Viewperm($this->db);
        $rows = $mwViewperm->loadViewperm($sess, $this->get('username'));
        if (!$rows) {
            $this->setError(Lang::txt('COM_TOOLS_ERROR_SESSION_NOT_FOUND'));
            return false;
        }

        foreach ($users as $user) {
            // Check for invalid characters
            if (!preg_match("/^[0-9a-zA-Z]+[_0-9a-zA-Z]*$/i", $user)) {
                $this->setError(Lang::txt('MW_ERROR_INVALID_USERNAME') . ': ' . $user);
                continue;
            }

            // Check that the user exist
            $zuser = User::getInstance($user);
            if (!$zuser || !is_object($zuser) || !$zuser->get('id')) {
                $this->setError(Lang::txt('MW_ERROR_INVALID_USERNAME') . ': ' . $user);
                continue;
            }

            //load current view perm
            $mwViewperm = new \Components\Tools\Tables\Viewperm($this->db);
            $currentViewPerm = $mwViewperm->loadViewperm($this->get('sessnum'), $zuser->get('username'));

            // If there are no matching entries in viewperm, add a new entry,
            // Otherwise, update the existing entry (e.g. readonly).
            if (count($currentViewPerm) == 0) {
                $mwViewperm->sessnum   = $this->get('sessnum');
                $mwViewperm->viewuser  = $zuser->get('username');
                $mwViewperm->viewtoken = bin2hex(random_bytes(16));
                $mwViewperm->geometry  = $rows[0]->geometry;
                $mwViewperm->fwhost    = $rows[0]->fwhost;
                $mwViewperm->fwport    = $rows[0]->fwport;
                $mwViewperm->vncpass   = $rows[0]->vncpass;
                $mwViewperm->readonly  = $readonly;
                $mwViewperm->insert();
            } else {
                $mwViewperm->sessnum   = $currentViewPerm[0]->sessnum;
                $mwViewperm->viewuser  = $currentViewPerm[0]->viewuser;
                $mwViewperm->viewtoken = $currentViewPerm[0]->viewtoken;
                $mwViewperm->geometry  = $currentViewPerm[0]->geometry;
                $mwViewperm->fwhost    = $currentViewPerm[0]->fwhost;
                $mwViewperm->fwport    = $currentViewPerm[0]->fwport;
                $mwViewperm->vncpass   = $currentViewPerm[0]->vncpass;
                $mwViewperm->readonly  = $readonly;
                $mwViewperm->updateViewPerm();
            }

            if ($mwViewperm->getError()) {
                $this->setError($mwViewperm->getError());
                return false;
            }
        }

        return true;
    }

    /**
     * Stop sharing a session with a specified user
     *
     * @param   string   $with  Username
     * @return  boolean
     */
    public function unshare($with = null)
    {
        $mv = new \Components\Tools\Tables\Viewperm($this->db);
        if (!$mv->deleteViewperm($this->get('sessnum'), $with)) {
            $this->setError($mv->getError());
            return false;
        }
        return true;
    }

    /**
     * Get associated tool information
     *
     * @param   string   $what  What data to return
     * @return  boolean
     */
    public function app($what = null)
    {
        $app = $this->get('appname');

        switch (strtolower($what)) {
            case 'tool':
            case 'name':
                return strstr($app, '_', true);

            case 'version':
                $r = substr(strrchr($app, '_'), 1);
                if (substr($r, 0, 1) == 'r') {
                    return substr($r, 1);
                } elseif (substr($r, 0, 3) == 'dev') {
                    return 'dev';
                } else {
                    return '';
                }

            default:
                return $app;
        }
    }

    /**
     * Delete the record and all associated data
     *
     * @return  boolean  False if error, True on success
     */
    public function delete()
    {
        // Can't delete what doesn't exist
        if (!$this->exists()) {
            return true;
        }

        // Remove comments
        foreach ($this->shared('list') as $shared) {
            if (!$shared->delete()) {
                $this->setError($shared->getError());
                return false;
            }
        }

        // Attempt to delete the record
        return parent::delete();
    }
}

<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models;

use Hubzero\Base\Model;
use Components\Projects\Tables;
use Hubzero\Base\ItemList;
use Hubzero\Facades\Component;
use Hubzero\Facades\Route;
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;
use Hubzero\Facades\App;
use Hubzero\Facades\Event;
use Hubzero\Facades\Request;

/**
 * Project model
 */
class Project extends Model
{
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $base;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $creator;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $groupOwner;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $member;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $newCount;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $objPub;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $owner;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $project;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $publication;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $repo;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $tblActivity;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $tblBlog;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $tblComment;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    // Read and written from outside the class: the projects controller
    // reconciles groups, matches invitations and loads the team through it.
    public $tblOwner;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $tblTodo;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $tblType;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $team;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $type;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $userGroups;

    // Read from outside the class throughout the setup and project views,
    // as $this->model->params->get(...).
    public $params;

    /**
     * Table class name
     *
     * @var  string
     */
    protected $tableClass = '\\Components\\Projects\\Tables\\Project';

    /**
     * Model context
     *
     * @var  string
     */
    protected $context = 'com_projects.project.about';

    /**
     * Registry
     *
     * @var  object
     */
    protected $config = null;

    /**
     * Authorized
     *
     * @var  mixed
     */
    private $authorized = false;

    /**
     * Constructor
     *
     * @param   mixed  $oid  ID (int) or alias (string)
     * @return  void
     */
    public function __construct($oid = null)
    {
        $this->db = App::get('db');

        $this->table = new Tables\Project($this->db);

        if ($oid) {
            if (is_object($oid) || is_array($oid)) {
                $this->bind($oid);
            } else {
                $this->table->loadProject($oid);
            }
        }

        $paramsRegistry = new \Hubzero\Config\Registry($this->table->get('params'));

        if (empty($paramsRegistry->toArray())) {
            $parsedParams = $this->getParsedParams();
            $paramsRegistry = new \Hubzero\Config\Registry($parsedParams);
        }

        $this->params = $paramsRegistry;
    }

    /**
     * Get params as an array
     *
     * @return array
     */
    protected function getParsedParams()
    {
        $parsedParams = [];
        $splitParams = explode("\n", $this->table->get('params', ''));

        foreach ($splitParams as $param) {
            if (empty($param)) {
                continue;
            }
            $pair = explode('=', $param);
            $key = $pair[0];
            $value = $pair[1];
            $parsedParams[$key] = $value;
        }

        return $parsedParams;
    }

    /**
     * Returns a reference to an article model
     *
     * @param   mixed  $oid  ID or alias
     * @return  object
     */
    public static function &getInstance($oid = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        if (is_object($oid)) {
            $key = $oid->id;
        } elseif (is_array($oid)) {
            $key = $oid['id'];
        } else {
            $key = $oid;
        }

        if (!isset($instances[$key])) {
            $instances[$key] = new self($oid);
        }

        return $instances[$key];
    }

    /**
     * Reload project
     *
     * @return  void
     */
    public function reloadProject()
    {
        $this->table->loadProject($this->get('id'));
    }

    /**
     * Get project object
     *
     * @param   boolean  $reload
     * @return  object
     */
    public function project($reload = false)
    {
        if (!isset($this->project) || $reload == true) {
            $this->project = $this->table->getProject($this->get('id'), User::get('id'));
        }

        return $this->project;
    }

    /**
     * Get project local repo
     *
     * @return  object
     */
    public function repo()
    {

        if (!isset($this->repo)) {
            $this->repo = new Repo($this, 'local');
        }

        return $this->repo;
    }

    /**
     * Return a formatted created timestamp
     *
     * @param   string  $as  What data to return
     * @return  string
     */
    public function created($as = '')
    {
        return $this->date('created', $as);
    }

    /**
     * Return a formatted modified timestamp
     *
     * @param   string  $as  What data to return
     * @return  string
     */
    public function modified($as = '')
    {
        return $this->date('modified', $as);
    }

    /**
     * Return a formatted timestamp
     *
     * @param   string  $key  Field to return
     * @param   string  $as   What data to return
     * @return  string
     */
    protected function date($key, $as = '')
    {
        if ($this->get($key) == $this->db->getNullDate()) {
            return null;
        }
        switch (strtolower($as)) {
            case 'date':
                return Date::of($this->get($key))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                break;

            case 'time':
                return Date::of($this->get($key))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
                break;

            case 'datetime':
                return $this->date($key, 'date') . ' &#64; ' . $this->date($key, 'time');
                break;

            case 'timeago':
                return \Components\Projects\Helpers\Html::timeAgo($this->get($key));
                break;

            default:
                return $this->get($key);
                break;
        }
    }

    /**
     * Get project member
     *
     * @param   boolean  $refresh
     * @return  object
     */
    public function member($reload = false)
    {
        if (!$this->exists()) {
            return false;
        }
        if (!isset($this->tblOwner)) {
            $this->tblOwner = new Tables\Owner($this->db);
        }
        if (!isset($this->member) || $reload == true) {
            $this->tblOwner->loadOwner($this->get('id'), User::get('id'));
            $this->member = $this->tblOwner->id ? $this->tblOwner : false;
        }

        return $this->member;
    }

    /**
     * Check if the member is confirmed
     *
     * @return  boolean
     */
    public function isMemberConfirmed()
    {
        $member = $this->member();

        if ($member && $member->status == 1) {
            return true;
        }
        return false;
    }

    /**
     * Check if the project is private
     *
     * @return  boolean
     */
    public function isPrivate()
    {
        //return ($this->get('private') == 1);
        return ($this->get('access') == 5);
    }

    /**
     * Check if the project is public
     *
     * @return  boolean
     */
    public function isPublic()
    {
        if (!$this->exists()) {
            return false;
        }

        if ($this->isPrivate()) {
            return false;
        }

        return true;
    }

    /**
     * Check if the project is open
     *
     * @return  boolean
     */
    public function isOpen()
    {
        if (!$this->exists()) {
            return false;
        }

        if ($this->get('private') != -1) {
            return false;
        }

        return true;
    }

    /**
     * Allow membership requests?
     *
     * @return  boolean
     */
    public function allowMembershipRequest()
    {
        /*if ($this->isPublic())
        {
            if ($this->params->get('allow_membershiprequest') == 1)
            {
                return true;
            }
        }*/
        return ($this->params->get('allow_membershiprequest') == 1);
    }

    /**
     * Check if the project is active
     *
     * @return  boolean
     */
    public function isActive()
    {
        if (!$this->exists()) {
            return false;
        }

        if ($this->get('state') == 1 && !$this->inSetup()) {
            return true;
        }

        return false;
    }

    /**
     * Check if the project is rejected
     *
     * @return  boolean
     */
    public function isRejected()
    {
        if (!$this->exists()) {
            return false;
        }

        if ($this->get('state') == 4 && !$this->inSetup()) {
            return true;
        }

        return false;
    }

    /**
     * Is project archived?
     *
     * @return  boolean
     */
    public function isArchived()
    {
        if ($this->get('state') == 3) {
            return true;
        }
        return false;
    }

    /**
     * Is project deleted?
     *
     * @return  boolean
     */
    public function isDeleted()
    {
        if ($this->get('state') == 2) {
            return true;
        }
        return false;
    }

    /**
     * Is project provisioned?
     *
     * @return  boolean
     */
    public function isProvisioned()
    {
        if ($this->get('provisioned') == 1) {
            return true;
        }
        return false;
    }

    /**
     * Get publication of a provisioned project
     *
     * @return  boolean
     */
    public function getPublication()
    {
        if (!$this->exists() || !$this->isProvisioned()) {
            return false;
        }
        if (!isset($this->publication)) {
            $this->objPub = new \Components\Publications\Tables\Publication($this->db);
            $this->publication = $this->objPub->getProvPublication($this->get('id'));
        }
        return $this->publication;
    }

    /**
     * Get provisioned project
     *
     * @return  boolean
     */
    public function loadProvisioned($pid = null)
    {
        if (!intval($pid)) {
            return false;
        }

        // Load by publication ID
        $this->table->loadProvisionedProject($pid);
        $this->params = new \Hubzero\Config\Registry($this->table->get('params'));
        return false;
    }

    /**
     * Is project pending approval?
     *
     * @return  boolean
     */
    public function isPending()
    {
        if ($this->get('state') == 5) {
            return true;
        }
        return false;
    }

    /**
     * Is project suspended?
     *
     * @return  boolean
     */
    public function isInactive()
    {
        if ($this->get('state') == 0 && !$this->inSetup()) {
            return true;
        }
        return false;
    }

    /**
     * Is project in setup?
     *
     * @return  boolean
     */
    public function inSetup()
    {
        $setupComplete = $this->config()->get('confirm_step') ? 3 : 2;

        if ($this->get('setup_stage') < $setupComplete) {
            return true;
        }
        return false;
    }

    /**
     * Authorize current user
     *
     * @param   boolean  $reviewer
     * @return  void
     */
    private function authorize($reviewer = false)
    {
        $this->authorized = true;

        if (
            in_array($this->get('access'), User::getAuthorisedViewLevels())
            && ($this->isActive() || $this->isArchived())
        ) {
            $this->params->set('access-view-project', true);
        }

        // NOT logged in
        /*if (User::isGuest())
        {
            if (in_array($this->get('access'), User::getAuthorisedViewLevels())
             && ($this->isActive() || $this->isArchived()))
            {
                $this->params->set('access-view-project', true);
                $this->params->set('access-readonly-project', true);

                if ($this->isArchived())
                {
                    $this->params->set('access-member-project', true);
                }
            }

            // If the project is active and public
            if ($this->isPublic() && $this->isActive())
            {
                // Allow public view access
                $this->params->set('access-view-project', true);
            }
            // If an open project
            //if ($this->get('private') < 0 && ($this->isActive() || $this->isArchived()))
            if ($this->get('access') == 1 && ($this->isActive() || $this->isArchived()))
            {
                // Allow read-only mode for everything
                $this->params->set('access-member-project', true);
                $this->params->set('access-readonly-project', true);
            }
            return;
        }*/

        // Check reviewer access?
        if ($reviewer) {
            // Get user groups
            if (!isset($this->userGroups)) {
                $ugs = \Hubzero\User\Helper::getGroups(User::get('id'));
                $this->userGroups = $this->getGroupProperty($ugs);
            }

            switch (strtolower($reviewer)) {
                case 'general':
                case 'admin':
                default:
                    $reviewer = 'admin';
                    $group = \Hubzero\User\Group::getInstance($this->config()->get('admingroup'));
                    break;

                case 'sensitive':
                    $group = \Hubzero\User\Group::getInstance($this->config()->get('sdata_group'));
                    break;

                case 'sponsored':
                    $group = \Hubzero\User\Group::getInstance($this->config()->get('ginfo_group'));
                    break;

                case 'reports':
                    $group = \Hubzero\User\Group::getInstance($this->config()->get('reportgroup'));
                    break;
            }

            $authorized = false;
            if ($this->userGroups && count($this->userGroups) > 0) {
                foreach ($this->userGroups as $cn) {
                    if ($group && $cn == $group->get('cn')) {
                        $authorized = true;

                        $this->params->set('access-view-project', true);
                        $this->params->set('access-member-project', true);
                        $this->params->set('access-readonly-project', true);
                        break;
                    }
                }
            }

            $this->params->set('access-reviewer-' . strtolower($reviewer) . '-project', $authorized);
            return;
        }

        // Allowed to create a project
        if (!$this->exists()) {
            $cg = $this->config()->get('creatorgroup', '');
            $cg = explode(',', $cg);
            $cg = array_map('trim', $cg);

            if (!empty($cg) && !empty($cg[0])) {
                foreach ($cg as $c) {
                    $group = \Hubzero\User\Group::getInstance($c);

                    if ($group) {
                        if (
                            $group->isMemberOf('members', User::get('id'))
                            || $group->isMemberOf('managers', User::get('id'))
                        ) {
                            $this->params->set('access-create-project', true);
                        }
                    }
                }
            } else {
                $this->params->set('access-create-project', true);
            }
        }

        // Is the user a manager of the component? (set in component permissions)
        if (User::authorise('core.manage', 'com_projects')) {
            $this->params->set('access-view-project', true);
            $this->params->set('access-member-project', true);

            if ($this->isArchived()) {
                $this->params->set('access-readonly-project', true);
            } else {
                $this->params->set('access-manager-project', true); // May edit project properties
                $this->params->set('access-content-project', true); // May add/edit/delete all content
                $this->params->set('access-owner-project', true);
                $this->params->set('access-componentmanager-project', true);
            }
            return;
        }

        // Is user project member?
        $member = $this->member();
        if (empty($member) || $member->get('status') != 1) {
            // If an open project
            if ($this->isOpen()) {
                // Allow read-only mode for everything
                $this->params->set('access-member-project', true);
                $this->params->set('access-readonly-project', true);
            }
        } else {
            $this->params->set('access-view-project', true);
            $this->params->set('access-member-project', true); // internal project view

            //if ($this->isArchived() || $this->get('private') < 0)
            if ($this->isArchived()) {// || $this->get('access') == 1)
                // Read-only
                $this->params->set('access-readonly-project', true);
                return;
            }

            // Project roles
            switch ($member->role) {
                case 1:
                    // Manager
                    $this->params->set('access-manager-project', true); // May edit project properties
                    $this->params->set('access-content-project', true); // May add/edit/delete all content

                    // Owner (principal user/creator)
                    if ($this->owner('id') == $member->userid) {
                        $this->params->set('access-owner-project', true);
                    }
                    break;

                case 5:
                    // Read-only
                    $this->params->set('access-readonly-project', true);
                    break;

                case 2:
                case 3:
                default:
                    // Collaborator/author
                    $this->params->set('access-content-project', true);
                    break;
            }
        }
    }

    /**
     * Check a user's authorization
     *
     * @param   string   $action  Action to check
     * @return  boolean  True if authorized, false if not
     */
    public function access($action = 'view')
    {
        if (!$this->authorized) {
            $this->authorize();
        }
        return $this->params->get('access-' . strtolower($action) . '-project');
    }

    /**
     * Check a reviewer's authorization
     *
     * @param   string   $action  Action to check
     * @return  boolean  True if authorized, false if not
     */
    public function reviewerAccess($reviewer = false)
    {
        if (!$reviewer) {
            return false;
        }

        $this->authorize($reviewer);
        return $this->params->get('access-reviewer-' . strtolower($reviewer) . '-project');
    }

    /**
     * Get the owner of this entry
     *
     * Accepts an optional property name. If provided
     * it will return that property value. Otherwise,
     * it returns the entire User object
     *
     * @param   string  $property
     * @return  mixed
     */
    public function owner($property = null)
    {
        if (!isset($this->owner) || !($this->owner instanceof \Hubzero\User\User)) {
            $this->owner = User::getInstance($this->get('owned_by_user'));
        }
        if ($property) {
            return $this->owner->get($property);
        }
        return $this->owner;
    }

    /**
     * Get the creator of this entry
     *
     * Accepts an optional property name. If provided
     * it will return that property value. Otherwise,
     * it returns the entire User object
     *
     * @param   string  $property
     * @return  mixed
     */
    public function creator($property = null)
    {
        if (!isset($this->creator) || !($this->creator instanceof \Hubzero\User\User)) {
            $this->creator = User::getInstance($this->get('created_by_user'));
        }
        if ($property) {
            return $this->creator->get($property);
        }
        return $this->creator;
    }

    /**
     * Get the group owner of this entry
     *
     * Accepts an optional property name. If provided
     * it will return that property value. Otherwise,
     * it returns the entire Group object
     *
     * @param   string  $property
     * @return  mixed
     */
    public function groupOwner($property = null)
    {
        if (!$this->get('owned_by_group')) {
            return false;
        }
        if (!isset($this->groupOwner) || !($this->groupOwner instanceof \Hubzero\User\Group)) {
            $this->groupOwner = \Hubzero\User\Group::getInstance($this->get('owned_by_group'));
        }
        if ($property) {
            $property = ($property == 'id' ? 'gidNumber' : $property);
            return $this->groupOwner ? $this->groupOwner->get($property) : null;
        }
        return $this->groupOwner;
    }

    /**
     * Get the content of the record.
     * Optional argument to determine how content should be handled
     *
     * parsed - performs parsing on content (i.e., converting wiki markup to HTML)
     * clean  - parses content and then strips tags
     * raw    - as is, no parsing
     *
     * @param   string   $as       Format to return content in [parsed, clean, raw]
     * @param   integer  $shorten  Number of characters to shorten text to
     * @return  mixed    String or Integer
     */
    public function about($as = 'parsed', $shorten = 0)
    {
        $as = strtolower($as);
        $options = [];

        switch ($as) {
            case 'parsed':
                $content = $this->get('about.parsed', null);

                if ($content === null) {
                    $config = [
                        'option'   => Request::getCmd('option', 'com_projects'),
                        'scope'    => $this->get('alias') . DS . 'notes',
                        'pagename' => 'projects',
                        'pageid'   => $this->get('id'),
                        'filepath' => $this->config('webpath'),
                        'domain'   => $this->get('alias'),
                    ];

                    $content = (string) stripslashes($this->get('about', ''));
                    $this->importPlugin('content')->trigger('onContentPrepare', [
                        'com_projects.project.about',
                        &$this,
                        &$config,
                    ]);

                    $this->set('about.parsed', (string) $this->get('about', ''));
                    $this->set('about', $content);

                    return $this->about($as, $shorten);
                }

                $options['html'] = true;
                break;

            case 'clean':
                $content = strip_tags($this->about('parsed'));
                break;

            case 'raw':
            default:
                $content = stripslashes($this->get('about'));
                $content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
                break;
        }

        if ($shorten) {
            $content = \Hubzero\Utility\Str::truncate($content, $shorten, $options);
        }
        return $content;
    }

    /**
     * Get a configuration value
     * If no key is passed, it returns the configuration object
     *
     * @param   string  $key  Config property to retrieve
     * @return  mixed
     */
    public function config($key = null)
    {
        if (!isset($this->config)) {
            $this->config = Component::params('com_projects');
        }
        if ($key) {
            return $this->config->get($key);
        }
        return $this->config;
    }

    /**
     * Store changes to this database entry
     *
     * @param   boolean  $check  Perform data validation check?
     * @return  boolean  False if error, True on success
     */
    public function store($check = true)
    {
        $this->table->store();
        if (!$this->table->getError()) {
            return true;
        }
        $this->setError($this->table->getError());
        return false;
    }

    /**
     * Check alias name
     *
     * @param   string   $name  Alias name
     * @return  boolean  False if error, True on success
     */
    public function check($name = '', $pid = 0, $ajax = 0)
    {
        // Load config
        $this->config();

        // Set name length
        $minLength = $this->config('min_name_length', 3);
        $maxLength = $this->config('max_name_length', 30);

        // Array of reserved names (task names and default dirs)
        $reserved = explode(',', $this->config('reserved_names'));
        $tasks    = ['start', 'setup', 'browse',
            'intro', 'features', 'deleteimg',
            'reports', 'stats', 'view', 'edit',
            'suspend', 'reinstate', 'fixownership',
            'delete', 'intro', 'activate', 'process',
            'upload', 'img', 'verify', 'autocomplete',
            'showcount', 'preview', 'auth', 'public',
            'get', 'media',
        ];

        if ($name) {
            $name = preg_replace('/ /', '', $name);
            $name = strtolower($name);
        }

        // Perform checks
        if (!$name) {
            // Cannot be empty
            $this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_EMPTY'));
        } elseif (strlen($name) < intval($minLength)) {
            // Check for length
            $this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_TOO_SHORT'));
        } elseif (strlen($name) > intval($maxLength)) {
            $this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_TOO_LONG'));
        } elseif (preg_match('/[^a-z0-9]/', $name)) {
            // Check for illegal characters
            $this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID'));
        } elseif (is_numeric($name)) {
            // Check for all numeric (not allowed)
            $this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID_NUMERIC'));
        } else {
            // Verify name uniqueness
            if (
                !$this->table->checkUniqueName($name, $pid)
                || in_array($name, $reserved)
                || in_array($name, $tasks)
            ) {
                $this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_NOT_UNIQUE'));
            }
        }
        if ($this->getError()) {
            return false;
        }

        return true;
    }

    /**
     * Get group property
     *
     * @param   object  $groups
     * @param   string  $get
     * @return  array
     */
    public function getGroupProperty($groups, $get = 'cn')
    {
        $arr = [];
        if (!empty($groups)) {
            foreach ($groups as $group) {
                if ($group->regconfirmed) {
                    $arr[] = $get == 'cn' ? $group->cn : $group->gidNumber;
                }
            }
        }
        return $arr;
    }

    /**
     * Save param
     *
     * @param   string  $param
     * @param   string  $value
     * @return  void
     */
    public function saveParam($param = '', $value = '')
    {
        $this->table->saveParam($this->get('id'), trim($param), htmlentities($value));
    }

    /**
     * Get params
     *
     * @param   boolean  $refresh
     * @return  object
     */
    public function getParams($refresh = true)
    {
        return $this->params;
    }

    /**
     * Get a count of new activity
     *
     * @param   boolean  $refresh
     * @return  integer
     */
    public function newCount($refresh = false)
    {
        if (!isset($this->newCount) || $refresh == true) {
            $this->newCount = \Hubzero\Activity\Recipient::all()
                ->whereEquals('scope', 'project')
                ->whereEquals('scope_id', $this->get('id'))
                ->where('viewed', 'IS', null, 'and', 1)
                    ->orWhereEquals('viewed', '0000-00-00 00:00:00', 1)
                    ->resetDepth()
                ->whereEquals('state', 1)
                ->total();
        }

        return $this->newCount;
    }

    /**
     * Get project team
     *
     * @param   array    $filters
     * @param   boolean  $refresh
     * @return  object
     */
    public function team($filters = [], $refresh = false)
    {
        if (!isset($this->tblOwner)) {
            $this->tblOwner = new Tables\Owner($this->db);
        }
        if (!isset($this->team) || $refresh == true) {
            $this->team = $this->tblOwner->getOwners($this->get('id'), $filters);
        }

        return $this->team;
    }

    /**
     * Get project table
     *
     * @param   string  $name
     * @return  object
     */
    public function table($name = null)
    {
        if ($name == 'Activity') {
            if (!isset($this->tblActivity)) {
                $this->tblActivity = new Tables\Activity($this->db);
            }
            return $this->tblActivity;
        }
        if ($name == 'Owner') {
            if (!isset($this->tblOwner)) {
                $this->tblOwner = new Tables\Owner($this->db);
            }
            return $this->tblOwner;
        }
        if ($name == 'Type') {
            if (!isset($this->tblType)) {
                $this->tblType = new Tables\Type($this->db);
            }
            return $this->tblType;
        }
        if ($name == 'Blog') {
            if (!isset($this->tblBlog)) {
                $this->tblBlog = new Tables\Blog($this->db);
            }
            return $this->tblBlog;
        }
        if ($name == 'Comment') {
            if (!isset($this->tblComment)) {
                $this->tblComment = new Tables\Comment($this->db);
            }
            return $this->tblComment;
        }
        if ($name == 'Todo') {
            if (!isset($this->tblTodo)) {
                $this->tblTodo = new Tables\Todo($this->db);
            }
            return $this->tblTodo;
        }

        return $this->table;
    }

    /**
     * Get a count of, model for, or list of entries
     *
     * @param   string   $rtrn     Data to return
     * @param   array    $filters  Filters to apply to data retrieval
     * @param   boolean  $admin    Admin?
     * @return  mixed
     */
    public function entries($rtrn = 'list', $filters = [], $admin = false)
    {
        $showDeleted = $admin ? true : false;
        $setupComplete = $this->config()->get('confirm_step') ? 3 : 2;

        $filters['uid'] = isset($filters['uid']) ? $filters['uid'] : User::get('id');

        switch (strtolower($rtrn)) {
            case 'count':
                return (int) $this->table->getCount($filters, $admin, $filters['uid'], $showDeleted, $setupComplete);
                break;

            case 'group':
                $group = isset($filters['group']) ? $filters['group'] : null;
                $results = $this->table->getGroupProjects(
                    $group,
                    User::get('id'),
                    $filters,
                    $setupComplete,
                );
                break;

            default:
                $results = $this->table->getRecords($filters, $admin, $filters['uid'], $showDeleted, $setupComplete);
                break;
        }

        if ($results) {
            foreach ($results as $key => $result) {
                $results[$key] = new self($result);
            }
        }

        return new ItemList($results);
    }

    /**
     * Record activity
     *
     * @param   string   $activity
     * @param   integer  $refid
     * @param   string   $underline
     * @param   string   $url
     * @param   string   $class
     * @param   integer  $commentable
     * @param   integer  $admin
     * @param   integer  $managers_only
     * @return  mixed
     */
    public function recordActivity($activity = '', $refid = '', $underline = '', $url = '', $class = 'project', $commentable = 0, $admin = 0, $managers_only = 0)
    {
        if ($activity) {
            $refid = $refid ? $refid : $this->get('id');

            $recipients = [
                ['user', User::get('id')],
            ];

            if ($managers_only) {
                $recipients[] = ['project_managers', $this->get('id')];
            } else {
                $recipients[] = ['project', $this->get('id')];

                if ($gid = $this->get('owned_by_group')) {
                    $recipients[] = ['group', $gid];
                }
            }

            // Legacy support
            // Translate project activity to the global activity schema
            $action   = 'created';
            $scope    = 'project';
            $scope_id = $refid;
            switch ($activity) {
                case 'started the project':
                    break;

                case 'deleted project':
                    $action = 'deleted';
                    break;

                case 'joined the project':
                    $action = 'joined';
                    break;

                case 'left the project':
                    $action = 'cancelled';
                    break;

                case 'posted a to do item':
                case 'posted a to-do item':
                case 'checked off a to do item':
                case 'checked off a to-do item':
                    $scope = 'project.todo';
                    break;

                case 'said':
                    $scope = 'project.comment';
                    break;

                case 'commented on a to do item':
                case 'commented on a to-do item':
                    $scope = 'project.todo.comment';
                    break;

                case 'commented on a blog post':
                case 'commented on an activity':
                    $scope = 'project.comment';
                    break;

                case 'added a new page in project notes':
                    $scope = 'project.note';
                    break;

                case 'changed the project settings':
                case 'edited project information':
                case 'replaced project picture':
                    $action = 'updated';
                    break;

                default:
                    if (substr($activity, 0, strlen('uploaded')) == 'uploaded') {
                        $action = 'uploaded';
                        $scope = 'project.file';
                        $scope_id = $this->get('id');
                    }
                    if (substr($activity, 0, strlen('updated file')) == 'updated file') {
                        $action = 'updated';
                        $scope = 'project.file';
                        $scope_id = $this->get('id');
                    }
                    if (substr($activity, 0, strlen('restored deleted file')) == 'restored deleted file') {
                        $action = 'updated';
                        $scope = 'project.file';
                        $scope_id = $this->get('id');
                    }
                    if (substr($activity, 0, strlen('created database')) == 'created database') {
                        $action = 'created';
                        $scope = 'project.database';
                    }
                    if (substr($activity, 0, strlen('removed database')) == 'removed database') {
                        $action = 'deleted';
                        $scope = 'project.database';
                        $scope_id = $refid;
                    }
                    if (substr($activity, 0, strlen('updated database')) == 'updated database') {
                        $action = 'updated';
                        $scope = 'project.database';
                        $scope_id = $refid;
                    }
                    // Publications
                    if (
                        substr($activity, 0, strlen('started a new publication')) == 'started a new publication'
                        || substr($activity, 0, strlen('started draft')) == 'started draft'
                        || substr($activity, 0, strlen('started a new')) == 'started a new'
                    ) {
                        $action = 'created';
                        $scope = 'publication';
                    }
                    if (substr($activity, 0, strlen('started new version')) == 'started new version') {
                        $action = 'created';
                        $scope = 'publication';
                    }
                    if (
                        substr($activity, 0, strlen('published version')) == 'published version'
                        || substr($activity, 0, strlen('re-published version')) == 're-published version'
                    ) {
                        $action = 'published';
                        $scope = 'publication';
                    }
                    if (
                        substr($activity, 0, strlen('submitted draft')) == 'submitted draft'
                        || substr($activity, 0, strlen('re-submitted draft')) == 're-submitted draft'
                    ) {
                        $action = 'submitted';
                        $scope = 'publication';
                    }
                    if (substr($activity, 0, strlen('deleted draft')) == 'deleted draft') {
                        $action = 'deleted';
                        $scope = 'publication';
                    }
                    if (substr($activity, 0, strlen('reviewed')) == 'reviewed') {
                        $action = 'reviewed';
                        $scope = 'publication';
                    }
                    if (substr($activity, 0, strlen('approved')) == 'approved') {
                        $action = 'approved';
                        $scope = 'publication';
                    }
                    if (substr($activity, 0, strlen('reverted to draft')) == 'reverted to draft') {
                        $action = 'reverted';
                        $scope = 'publication';
                    }
                    if (substr($activity, 0, strlen('unpublished')) == 'unpublished') {
                        $action = 'unpublished';
                        $scope = 'publication';
                    }
                    if (substr($activity, 0, strlen('posted version')) == 'posted version') {
                        $action = 'published';
                        $scope = 'publication';
                    }
                    if (substr($activity, 0, strlen('has been updated by administrator')) == 'has been updated by administrator') {
                        $action = 'updated';
                        $scope = 'publication';
                    }
                    // Notes
                    if (substr($activity, 0, strlen('edited page')) == 'edited page') {
                        $action = 'updated';
                        $scope = 'project.note';
                    }
                    break;
            }

            /*Event::trigger('system.logActivity', [
                'activity' => [
                    'action'      => $action,
                    'scope'       => $scope,
                    'scope_id'    => $scope_id,
                    'description' => $activity,
                    'details'     => array(
                        'title' => $this->get('title'),
                        'url'   => ($url ? $url : Route::url($this->link())),
                        'class' => $class,
                        'underline' => $underline
                    )
                ],
                'recipients' => $recipients
            ]);*/
            $act = \Hubzero\Activity\Log::blank()->set([
                'action'      => $action,
                'scope'       => $scope,
                'scope_id'    => $scope_id,
                'description' => $activity,
                'details'     => [
                    'title'       => $this->get('title'),
                    'url'         => ($url ? $url : Route::url($this->link())),
                    'class'       => $class,
                    'underline'   => $underline,
                    'commentable' => $commentable,
                    'admin'       => $admin,
                    'referenceid' => $refid,
                    'projectid'   => $this->get('id'),
                ],
            ]);

            if (!$act->save()) {
                return false;
            }

            $aid  = $act->get('id');
            $sent = [];

            // Do we have any recipients?
            foreach ($recipients as $receiver) {
                $key = implode('.', $receiver);

                // No duplicate sendings
                if (in_array($key, $sent)) {
                    continue;
                }

                // Create a recipient object that ties a user to an activity
                $recipient = \Hubzero\Activity\Recipient::blank()->set([
                    'scope'    => $receiver[0],
                    'scope_id' => $receiver[1],
                    'log_id'   => $aid,
                    'state'    => 1,
                ]);

                $recipient->save();

                $sent[] = $key;
            }

            // Notify subscribers
            if ($aid && !User::isGuest() && !$this->isProvisioned()) {
                Event::trigger('projects.onWatch', [$this, $class, [$aid], User::get('id')]);
            }
            return $aid;
        }

        return false;
    }

    /**
     * Record project page visit
     *
     * @return  void
     */
    public function recordVisit()
    {
        $member = $this->member();

        if ($member && $this->isActive() && $this->isMemberConfirmed() && !$this->isProvisioned()) {
            $timecheck = Date::of(time() - (6 * 60 * 60))->toSql(); // visit in last 6 hours
            if ($member->num_visits == 0 or $member->lastvisit < $timecheck) {
                $member->num_visits = $member->num_visits + 1; // record visit in a day
                $member->prev_visit = $member->lastvisit;
            }
            $member->lastvisit = Date::toSql();
            $member->store();
        }
    }

    /**
     * Checks if an activity has been recorded
     *
     * @param   string  $activity
     * @return  integer
     */
    public function checkActivity($activity = null)
    {
        $log = \Hubzero\Activity\Log::all();

        $l = $log->getTableName();
        $r = \Hubzero\Activity\Recipient::blank()->getTableName();

        return $log
            ->join($r, $r . '.log_id', $l . '.id', 'inner')
            ->whereEquals($r . '.scope', 'project')
            ->whereEquals($r . '.scope_id', $this->get('id'))
            ->whereEquals($l . '.description', $activity)
            ->order($l . '.created', 'desc')
            ->row()
            ->get('id');
    }

    /**
     * Record first join activity
     *
     * @return  void
     */
    public function recordFirstJoinActivity()
    {
        if ($this->isMemberConfirmed() && !$this->isProvisioned() && $this->isActive()) {
            if (!$this->member()->lastvisit) {
                $aid = $this->recordActivity(Lang::txt('COM_PROJECTS_ACTIVITY_JOINED_THE_PROJECT'), $this->get('id'), '', '', 'team', 1);
                if ($aid) {
                    $this->member()->saveParam(
                        $this->get('id'),
                        User::get('id'),
                        'join_activityid',
                        $aid,
                    );
                }

                // If newly created - remove join activity of project creator
                $timecheck = Date::of(time() - (10 * 60)); // last second

                if ($this->access('owner') && $timecheck <= $this->get('created')) {
                    $activity = \Hubzero\Activity\Log::oneOrFail($aid);
                    $activity->destroy();
                }
            }
        }
    }

    /**
     * Get the project type
     *
     * @return  object
     */
    public function type()
    {
        if (empty($this->type)) {
            $this->type = new Tables\Type($this->db);
            $this->type->load($this->get('type'));
            $this->type->_params = new \Hubzero\Html\Parameter($this->type->params);
        }

        return $this->type;
    }

    /**
     * Generate and return various links to the entry
     * Link will vary depending upon action desired, such as edit, delete, etc.
     *
     * @param   string  $type  The type of link to return
     * @return  string
     */
    public function link($type = '')
    {
        if (!isset($this->base)) {
            $this->base  = 'index.php?option=com_projects';
            $this->base .= '&alias=' . $this->get('alias');
        }

        // If it doesn't exist or isn't published
        switch (strtolower($type)) {
            case 'publications':
            case 'files':
            case 'team':
            case 'feed':
            case 'links':
            case 'databases':
            case 'notes':
            case 'todo':
                $link = $this->base . '&active=' . strtolower($type);
                break;

            case 'setup':
                $link = $this->base . '&task=setup';
                break;

            case 'edit':
                $link = $this->base . '&task=edit';
                break;

            case 'thumb':
                $link = $this->picture();
                break;

            case 'stamp':
                $link = 'index.php?option=com_projects&task=get';
                break;

            case 'permalink':
            default:
                $link = $this->base;
                break;
        }

        return $link;
    }

    /**
     * Generate and return path to a picture for the project
     *
     * @param   string   $size      Thumbnail (thumb) or full size (master)?
     * @param   boolean  $realpath  Return the actual file path? When FALSE, it returns a link to /files/{hash}
     * @return  string
     */
    public function picture($size = 'thumb', $realpath = false)
    {
        $src  = '';
        $path = PATH_APP . DS . trim($this->config()->get('imagepath', '/site/projects'), DS) . DS . $this->get('alias') . DS . 'images';

        if ($size == 'thumb') {
            // Does a thumb exist?
            if (file_exists($path . DS . 'thumb.png')) {
                $src = $path . DS . 'thumb.png';
            }

            // No thumb. Try to create it...
            if (!$src && $this->get('picture')) {
                $thumb = \Components\Projects\Helpers\Html::createThumbName($this->get('picture'));

                if ($thumb && file_exists($path . DS . $thumb)) {
                    $src = $path . DS . $thumb;
                }
            }
        } elseif (is_file($path . DS . 'master.png') && $size != 'original') {
            $src = $path . DS . 'master.png';
        } else {
            // Get the picture if set
            if ($this->get('picture') && is_file($path . DS . $this->get('picture'))) {
                $src = $path . DS . $this->get('picture');
            }
        }

        // Still no file? Let's use the default
        if (!$src) {
            $deprecated = [
                'components/com_projects/site/assets/img/project.png',
                'components/com_projects/assets/img/project.png',
                'components/com_projects/site/assets/img/projects-large.gif',
                'components/com_projects/assets/img/projects-large.gif',
            ];

            $path = trim($this->config()->get('defaultpic', 'components/com_projects/site/assets/img/project.png'), DS);

            if (in_array($path, $deprecated)) {
                $path = 'components/com_projects/site/assets/img/project.svg';
                $rootPath = PATH_CORE;
            } else {
                $rootPath = PATH_APP;
            }

            $src = $rootPath . DS . $path;
        }

        // Gnerate a file link
        if (!$realpath) {
            $src = with(new \Hubzero\Content\Moderator($src, 'public'))->getUrl();
        }

        return $src;
    }
}

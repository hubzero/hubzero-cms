<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User;

use Hubzero\Base\Obj;
use Hubzero\User\Profile\Helper as ProfileHelper;
use Hubzero\Config\Registry;
use Hubzero\Utility\Str;
use Hubzero\Facades\Event;
use Hubzero\Facades\App;

/**
 * Extended user profile
 */
class Profile extends Obj
{
    /**
     * Properties the profile keeps for itself, not fields
     *
     * @var  array<int, string>
     */
    private const INTERNAL = ['plainPassword', 'paramsRegistry'];

    /**
     * ID (primary key)
     *
     * @var  integer
     */
    private $uidNumber;

    /**
     * Name
     *
     * @var  string|null
     */
    private $name = null;

    /**
     * Username
     *
     * @var  string
     */
    private $username;

    /**
     * Email address
     *
     * @var  string
     */
    private $email;

    /**
     * Timestamp for registration date
     *
     * @var  string
     */
    private $registerDate;

    /**
     * Description for 'gidNumber'
     *
     * @var  integer
     */
    private $gidNumber;

    /**
     * System home directory for the user
     *
     * @var  string
     */
    private $homeDirectory;

    /**
     * Description for 'loginShell'
     *
     * @var  string
     */
    private $loginShell;

    /**
     * Description for 'ftpShell'
     *
     * @var  string
     */
    private $ftpShell;

    /**
     * Description for 'userPassword'
     *
     * @var  string
     */
    private $userPassword;

    /**
     * Description for 'shadowExpire'
     *
     * @var  integer
     */
    private $shadowExpire;

    /**
     * Description for 'gid'
     *
     * @var  string
     */
    private $gid;

    /**
     * Organization type
     *
     * @var  string
     */
    private $orgtype;

    /**
     * Organization
     *
     * @var  string
     */
    private $organization;

    /**
     * Two-letter code for 'Country of residence'
     *
     * @var  string
     */
    private $countryresident;

    /**
     * Two-letter code for 'Country of origin'
     *
     * @var  string
     */
    private $countryorigin;

    /**
     * Gender
     *
     * @var  string
     */
    private $gender;

    /**
     * URL
     *
     * @var  string
     */
    private $url;

    /**
     * Reason (for getting an account)
     *
     * @var  string
     */
    private $reason;

    /**
     * Receive email updates form the site?
     *
     * @var  integer
     */
    private $mailPreferenceOption = -1;

    /**
     * Agreed to Terms of Service?
     *
     * @var  integer
     */
    private $usageAgreement;

    /**
     * Timestamp for last time profile was modified
     *
     * @var  string
     */
    private $modifiedDate;

    /**
     * Email address confirmed?
     *
     * @var  integer
     */
    private $emailConfirmed;

    /**
     * Registration IP
     *
     * @var  string
     */
    private $regIP;

    /**
     * Registration host
     *
     * @var  string
     */
    private $regHost;

    /**
     * Native tribe
     *
     * @var  string
     */
    private $nativeTribe;

    /**
     * Phone number
     *
     * @var  string
     */
    private $phone;

    /**
     * Password from proxy creation
     *
     * @var  string
     */
    private $proxyPassword;

    /**
     * User ID from proxy creation
     *
     * @var  string
     */
    private $proxyUidNumber;

    /**
     * Given name (first name)
     *
     * @var  string
     */
    private $givenName;

    /**
     * Middle name
     *
     * @var  string
     */
    private $middleName;

    /**
     * Surname (last/family name)
     *
     * @var  string
     */
    private $surname;

    /**
     * Picture
     *
     * @var  string
     */
    private $picture;

    /**
     * VIP status?
     *
     * @var  integer
     */
    private $vip;

    /**
     * Public profile?
     *
     * @var  integer
     */
    private $public;

    /**
     * Parameters
     *
     * @var  string
     */
    private $params;

    /**
     * Notes
     *
     * @var  string
     */
    private $note;

    /**
     * ORCID
     * http://orcid.org
     *
     * @var  string
     */
    private $orcid;

    // Multi-value properties stored in auxilliary tables

    /**
     * Profile bio
     *
     * @var  string
     */
    private $auxsBio;

    /**
     * List of diabilities
     *
     * @var  array<mixed>
     */
    private $auxvDisability = [];

    /**
     * List of hispanic options
     *
     * @var  array<mixed>
     */
    private $auxvHispanic = [];

    /**
     * List of races
     *
     * @var  array<mixed>
     */
    private $auxvRace = [];

    /**
     * Description for 'auxvAdmin'
     *
     * @var  array<mixed>
     */
    private $auxvAdmin = [];

    /**
     * Description for 'auxvHost'
     *
     * @var  array<mixed>
     */
    private $auxvHost = [];

    /**
     * List of Edu levels
     *
     * @var  array<mixed>
     */
    private $auxvEdulevel = [];

    /**
     * List of roles
     *
     * @var  array<mixed>
     */
    private $auxvRole = [];

    /**
     * Description for 'plainPassword'
     *
     * @var  string
     */
    private $plainPassword;

    /**
     * Registry
     *
     * @var  \Hubzero\Config\Registry|null
     */
    private $paramsRegistry = null;

    /**
     * Add an error message.
     *
     * @param   string|\Exception|false|null  $error  Error message. False, null or empty (what getError() gives
     *                                                when there is none, often passed on) is not recorded.
     * @param   string|null  $key    Specific key to set the value to
     * @return  $this
     */
    public function setError($error, $key = null)
    {
        if ($error === false || $error === null || $error === '') {
            return $this;
        }

        $bt = debug_backtrace();

        $error = __CLASS__ . '::' . $bt[1]['function'] . '():' . $error;

        return parent::setError($error, $key);
    }

    /**
     * Reset all properties, making an empty profile object
     *
     * @return  boolean  True
     */
    public function clear()
    {
        $classvars = get_class_vars(__CLASS__);

        foreach ($classvars as $property => $value) {
            if ('_s_' == substr($property, 0, 3)) { // Don't touch static variables
                continue;
            }

            unset($this->$property);
            $this->$property = $value;
        }

        $objvars = get_object_vars($this);

        foreach ($objvars as $property => $value) {
            if (!array_key_exists($property, $classvars)) {
                unset($this->$property);
            }
        }

        return true;
    }

    /**
     * Whether a name is one the profile keeps for itself rather than a field
     *
     * @param   string|mixed  $property
     * @return  bool
     */
    private static function isOwn($property)
    {
        return !is_string($property) || $property === '' || $property[0] === '_'
            || in_array($property, self::INTERNAL, true)
            || self::isAux($property);
    }

    /**
     * The property holding a field kept in a table of its own
     *
     * @param   string  $field  Field name, e.g. "race"
     * @param   string  $kind   "s" for a single value, "v" for a list
     * @return  string
     */
    private static function auxProperty($field, $kind)
    {
        return 'aux' . $kind . ucfirst((string) $field);
    }

    /**
     * Whether a property holds a field kept in a table of its own
     *
     * @param   string|mixed  $property
     * @param   string  $kinds  Which kinds to accept: "s", "v" or "sv"
     * @return  bool
     */
    private static function isAux($property, $kinds = 'sv')
    {
        foreach (str_split($kinds) as $kind) {
            if (
                is_string($property) && strlen($property) > 4
                && str_starts_with($property, 'aux' . $kind) && property_exists(__CLASS__, $property)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * The field a property kept in a table of its own holds
     *
     * @param   string  $property  e.g. "auxvRace"
     * @return  string  e.g. "race", also the name of its table, xprofiles_race
     */
    private static function auxField($property)
    {
        return lcfirst(substr($property, 4));
    }

    /**
     * Load a record from the MySQL database
     *
     * @param   mixed    $user  Integer (ID) or string (username)
     * @return  boolean  True on success, False on error
     */
    private function sqlLoad($user)
    {
        $db = App::get('db');

        if (empty($user)) {
            $this->setError('No user specified');
            return false;
        }

        // zooley: Removed check for >= 0 because profiles without acounts have negative IDs
        //if (is_numeric($user) && $user >= 0)
        if (is_numeric($user)) {
            $query = "SELECT * FROM `#__xprofiles` WHERE uidNumber = " . $db->quote(intval($user)) . ";";
        } else {
            $query = "SELECT * FROM `#__xprofiles` WHERE username = " . $db->quote($user) . " AND uidNumber>0;";
        }

        $db->setQuery($query);

        $result = $db->loadAssoc();

        if (empty($result)) {
            $this->setError('No such user [' . $user . ']');
            return false;
        }

        $this->clear();

        $this->paramsRegistry = new Registry($result['params']);

        foreach ($result as $property => $value) {
            $this->set($property, $value);
        }

        $classvars = get_class_vars(__CLASS__);

        foreach ($classvars as $property => $value) {
            if (self::isAux($property)) {
                $this->$property = false; // This property is loaded on demand
            }
        }

        $this->paramsRegistryOrFail()->merge($this->params);

        return true;
    }

    /**
     * Load an author record into this profile
     *
     * @param   integer  $authorid  Author ID
     * @return  boolean  True on success, False on error
     */
    private function sqlAuthorLoad($authorid)
    {
        static $_propertyauthormap = [
            'uidNumber'    => 'id',
            'givenName'    => 'firstname',
            'middleName'   => 'middlename',
            'surname'      => 'lastname',
            'organization' => 'org',
            'bio'          => 'bio',
            'url'          => 'url',
            'picture'      => 'picture',
            'vip'          => 'principal_investigator',
        ];

        $db =  App::get('db');

        $query = "SELECT * FROM `#__author` WHERE id=" . $db->quote($authorid);

        $db->setQuery($query);

        $result = $db->loadAssoc();

        if (empty($result)) {
            $this->setError('No such author [' . $authorid . ']');
            return false;
        }

        $this->clear();

        foreach ($_propertyauthormap as $property => $aproperty) {
            if (!empty($result[$aproperty])) {
                $this->set($property, $result[$aproperty]);
            }
        }

        return true;
    }

    /**
     * Bind registration data
     *
     * @param   mixed  $registration  A \Components\Members\Models\Registration
     * @return  boolean
     */
    private function xregistrationLoad($registration)
    {
        static $_propertyregmap = [
            'username' => 'login',
            'name' => 'name',
            'email' => 'email',
            'orcid' => 'orcid',
            'orgtype' => 'orgtype',
            'organization' => 'org',
            'countryresident' => 'countryresident',
            'countryorigin' => 'countryorigin',
            'gender' => 'sex',
            'url' => 'web',
            'reason' => 'reason',
            'mailPreferenceOption' => 'mailPreferenceOption',
            'usageAgreement' => 'usageAgreement',
            'nativeTribe' => 'nativeTribe',
            'phone' => 'phone',
            'disability' => 'disability',
            'hispanic' => 'hispanic',
            'race' => 'race',
            'admin' => 'admin',
            'host' => 'host',
            'edulevel' => 'edulevel',
            'role' => 'role',
            'givenName' => 'givenName',
            'middleName' => 'middleName',
            'surname' => 'surname',
        ];

        if (!($registration instanceof \Components\Members\Models\Registration)) {
            $this->setError("Invalid XRegistration object");
            return false;
        }

        foreach ($_propertyregmap as $property => $rproperty) {
            if ($registration->get($rproperty) !== null) {
                $this->set($property, $registration->get($rproperty));
            }
        }

        $this->set(
            'mailPreferenceOption',
            $this->get('mailPreferenceOption') ? $this->get('mailPreferenceOption') : '-1',
        );
        $this->set('usageAgreement', $this->get('usageAgreement') ? '1' : '0');

        return true;
    }

    /**
     * Load registration data
     *
     * @param   mixed  &$registration  A \Components\Members\Models\Registration
     * @return  boolean
     */
    public function loadRegistration(&$registration)
    {
        if (!($registration instanceof \Components\Members\Models\Registration)) {
            return false;
        }

        $keys = [
            'email',
            'name',
            'orgtype',
            'countryresident',
            'countryorigin',
            'disability',
            'hispanic',
            'race',
            'phone',
            'reason',
            'edulevel',
            'role',
            'surname',
            'givenName',
            'middleName',
            'orcid',
        ];

        foreach ($keys as $key) {
            if ($registration->get($key) !== null) {
                $this->set($key, $registration->get($key));
            }
        }

        if ($registration->get('login') !== null) {
            $this->set('username', $registration->get('login'));
        }

        if ($registration->get('password') !== null) {
            $this->set('password', $registration->get('password'));
        }

        if ($registration->get('org') !== null || $registration->get('orgtext') !== null) {
            $this->set('organization', $registration->get('org'));

            if ($registration->get('orgtext')) {
                $this->set('organization', $registration->get('orgtext'));
            }
        }

        if ($registration->get('sex') !== null) {
            $this->set('gender', $registration->get('sex'));
        }

        if ($registration->get('nativetribe') !== null) {
            $this->set('nativeTribe', $registration->get('nativetribe'));
        }

        if ($registration->get('web') !== null) {
            $this->set('url', $registration->get('web'));
        }

        if ($registration->get('mailPreferenceOption') !== null) {
            $this->set(
                'mailPreferenceOption',
                $registration->get('mailPreferenceOption') ? $registration->get('mailPreferenceOption') : '-1',
            );
        }

        if ($registration->get('usageAgreement') !== null) {
            $this->set('usageAgreement', $registration->get('usageAgreement') ? true : false);
        }

        return true;
    }

    /**
     * Load a record
     *
     * @param   mixed    $user     User data
     * @param   string   $storage  Storage type
     * @return  boolean  True on success, False on error
     */
    public function load($user, $storage = 'mysql')
    {
        if (!empty($storage) && !in_array($storage, ['mysql', 'author', 'xregistration'])) {
            $this->setError('Invalid storage option requested [' . $storage . ']');
            return false;
        }

        if ($storage == 'mysql') {
            return $this->sqlLoad($user);
        }

        if ($storage == 'author') {
            return $this->sqlAuthorLoad($user);
        }

        if ($storage == 'xregistration') {
            return $this->xregistrationLoad($user);
        }

        return true;
    }

    /**
     * Constructor
     *
     * @param   mixed    $user  User data
     * @return  bool|null  True on success, False on error
     */
    public function __construct($user = null)
    {
        if (!empty($user)) {
            $this->load($user);
        }

        return;
    }

    /**
     * Returns a reference to the global User object, only creating it if it doesn't already exist.
     *
     * @param   mixed  $id  The user to load - Can be an integer or string
     * @return  static|false  The profile, or false when there is no such user
     */
    public static function getInstance($id = null)
    {
        static $instances;
        static $usernames;

        if (!isset($instances)) {
            $instances = [];
        }

        if (!isset($usernames)) {
            $usernames = [];
        }

        // Is this a username?
        if (!is_numeric($id)) {
            // Normalize and check if we have data for this username
            $id = strtolower(trim($id));
            if (!isset($usernames[$id])) {
                $user = new self($id);

                // Save
                $usernames[$id] = $user->get('uidNumber');
                $instances[$usernames[$id]] = $user;
            }
            // Change the $id from username to numeric ID
            $id = $usernames[$id];
        }

        // Check for existing record
        if (empty($instances[$id]) || $instances[$id]->get('uidNumber') != $id) {
            $user = new self($id);
            $instances[$id] = $user;
        }

        // Ensure record has data
        if (!$instances[$id]->get('uidNumber')) {
            return false;
        }

        return $instances[$id];
    }

    /**
     * Create a new entry in the profiles table
     *
     * @return  boolean  True on success, False on error
     */
    public function create()
    {
        $db = App::get('db');

        $modifiedDate = gmdate('Y-m-d H:i:s');

        if (is_numeric($this->get('uidNumber'))) {
            $query = "INSERT INTO `#__xprofiles` (uidNumber,username,modifiedDate) VALUE ("
                . $db->quote($this->get('uidNumber'))
                . ','
                . $db->quote($this->get('username'))
                . ','
                . $db->quote($modifiedDate)
                . ");";

            $db->setQuery($query);

            try {
                $db->query();
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                if (!$db->isDuplicateKeyException($e)) {
                    throw $e;
                }

                $this->setError(
                    'uidNumber (' . $this->get('uidNumber') . ') already exists' . ' in xprofiles table',
                );

                return false;
            }
        } else {
            $token = uniqid();

            $query = "INSERT INTO `#__xprofiles` (uidNumber,username,modifiedDate) SELECT "
                . "IF(MIN(uidNumber)>0,-1,MIN(uidNumber)-1),"
                . $db->quote($token)
                . ','
                . $db->quote($modifiedDate)
                . " FROM #__xprofiles;";

            $db->setQuery($query);

            $db->query();

            $query = "SELECT uidNumber FROM `#__xprofiles` WHERE username="
                . $db->quote($token)
                . " AND modifiedDate="
                . $db->quote($modifiedDate);

            $db->setQuery($query);

            $result = $db->loadColumn() ?? [];

            if (count($result) > 1) {
                // The driver has no error to give here: say what went wrong
                $this->setError('Error adding data to xprofiles table: more than one new row matched');

                return false;
            }

            $this->set('uidNumber', $result[0]);
        }

        $this->set('modifiedDate', $modifiedDate);

        if ($this->update() === false) {
            return false;
        }

        return true;
    }

    /**
     * Alias for the load() method
     *
     * @param   boolean|null  $instance  The user to load - Can be an integer or string
     * @return  boolean  True on success, False on error
     */
    public function read($instance = null)
    {
        return $this->load($instance);
    }

    /**
     * Store data to the database record.
     * Creates a record if doesn't exist, otherwise updates record.
     *
     * @return  boolean  True on success, False on error
     */
    public function store()
    {
        if (!is_numeric($this->get('uidNumber'))) {
            return $this->create();
        }
        return $this->update();
    }

    /**
     * Update an existing record
     *
     * @return  boolean  True on success, False on error
     */
    public function update()
    {
        if (!is_numeric($this->get('uidNumber'))) {
            return false;
        }

        $db =  App::get('db');

        $modifiedDate = gmdate('Y-m-d H:i:s');

        $this->set('modifiedDate', $modifiedDate);

        $query = "UPDATE `#__xprofiles` SET ";

        $classvars = get_class_vars(__CLASS__);

        $first = true;
        $affected = 0;

        foreach ($classvars as $property => $value) {
            if (self::isOwn($property)) {
                continue;
            }

            if (!$first) {
                $query .= ',';
            } else {
                $first = false;
            }

            if ($property == 'params') {
                if (is_object($this->paramsRegistry)) {
                    $query .= "params=" . $db->quote($this->paramsRegistry->toString());
                } else {
                    $query .= "params=''";
                }
                continue;
            }

            if ($this->get($property) === null) {
                // DEFAULT, not NULL: most #__xprofiles columns are NOT NULL
                // with a '' / 0 default, so writing NULL for an unset property
                // failed on strict-SQL servers (e.g. userPassword while
                // create() fills in a brand-new registration). Nullable
                // columns default to NULL, so they are unchanged.
                $query .= "$property=DEFAULT";
            } else {
                $query .= "$property=" . $db->quote($this->get($property));
            }
        }

        $query .= " WHERE uidNumber=" . $db->quote($this->get('uidNumber')) . ";";

        $db->setQuery($query);

        $db->query();

        $affected = $db->getAffectedRows();

        foreach ($classvars as $property => $value) {
            if (!self::isAux($property)) {
                continue;
            }

            $property = self::auxField($property);

            $first = true;

            $query = "REPLACE INTO #__xprofiles_" . $property . " (uidNumber, " . $property . ") VALUES ";
            $query_values = "";

            $list = $this->get($property);

            if (!is_array($list)) {
                $list = [$list];
            }

            foreach ($list as $value) {
                if (!$first) {
                    $query_values .= ',';
                }

                $first = false;

                $query_values .= '(' . $db->quote($this->get('uidNumber')) . ',' . $db->quote($value) . ')';
            }

            if ($query_values != '') {
                $db->setQuery($query . $query_values);

                $db->query();
                $affected += $db->getAffectedRows();
            }

            if (property_exists(__CLASS__, self::auxProperty($property, 'v'))) {
                foreach ($list as $key => $value) {
                    $list[$key] = $db->quote($value);
                }

                $valuelist = implode(",", $list);

                if (empty($valuelist)) {
                    $valuelist = "''";
                }

                $query = "DELETE FROM #__xprofiles_"
                    . $property
                    . " WHERE uidNumber="
                    . $this->get('uidNumber')
                    . " AND $property NOT IN ($valuelist);";

                $db->setQuery($query);

                $db->query();
                $affected += $db->getAffectedRows();
            }
        }

        if ($affected > 0) {
            Event::trigger('user.onAfterStoreProfile', [$this]);
        }

        return true;
    }

    /**
     * Delete a record
     *
     * @return  boolean  True on success, False on error
     */
    public function delete()
    {
        $db = App::get('db');

        if (!is_numeric($this->get('uidNumber'))) {
            $this->setError("missing required field 'uidNumber'");
            return false;
        }

        $classvars = get_class_vars(__CLASS__);

        $affected = 0;

        foreach ($classvars as $property => $value) {
            if (!self::isAux($property)) {
                continue;
            }

            $property = self::auxField($property);

            $query = "DELETE FROM `#__xprofiles_$property` WHERE uidNumber = " . $db->quote($this->get('uidNumber'));
            $db->setQuery($query);

            $db->query();
            $affected += $db->getAffectedRows();
        }

        $query = "DELETE FROM `#__xprofiles` WHERE uidNumber = " . $db->quote($this->get('uidNumber'));
        $db->setQuery($query);

        $db->query();
        $affected += $db->getAffectedRows();

        if ($affected > 0) {
            Event::trigger('user.onAfterDeleteProfile', [$this]);
        }

        $this->clear();

        return true;
    }

    /**
     * Get a property's value
     *
     * @param   string  $property  Name of the property to retrieve
     * @param   mixed   $default     Default value
     * @return  mixed
     */
    public function get($property, $default = null)
    {
        if ($property == 'password') {
            return $this->plainPassword;
        }

        if (self::isOwn($property)) {
            $this->setError("Can't access private properties");
            return false;
        }

        if (!property_exists(__CLASS__, $property)) {
            if (property_exists(__CLASS__, self::auxProperty($property, 's'))) {
                $property = self::auxProperty($property, 's');
            } elseif (property_exists(__CLASS__, self::auxProperty($property, 'v'))) {
                $property = self::auxProperty($property, 'v');
            } else {
                $this->setError("Unknown property: $property");
                return false;
            }
        }

        if ($this->$property === false) {
            $db = App::get('db');

            $property_name = self::auxField($property);

            $query = "SELECT $property_name FROM `#__xprofiles` AS x, `#__xprofiles_$property_name` AS xp WHERE "
                . "x.uidNumber=xp.uidNumber AND xp.uidNumber="
                . $db->quote($this->get('uidNumber'))
                . " ORDER BY $property_name ASC;";

            $db->setQuery($query);

            $result = $db->loadColumn();

            if (self::isAux($property, 's')) {
                if (isset($result[0])) {
                    $this->set($property_name, $result[0]);
                } else {
                    $this->set($property_name, '');
                }
            } else {
                if (is_array($result) && count($result) <= 1 && empty($result[0])) {
                    $this->set($property_name, []);
                } else {
                    $this->set($property_name, $result);
                }
            }
        }

        return $this->$property;
    }

    /**
     * Set a property's value
     *
     * @param   string   $property  Property name
     * @param   mixed    $value     Property value
     * @return  $this  An error is recorded, not returned, when the property cannot be set
     */
    public function set($property, $value = null)
    {
        if ($property == 'password') {
            if ($value != '') {
                $this->userPassword = \Hubzero\User\Password::getPasshash($value);
            } else {
                $this->userPassword = '';
            }

            $this->plainPassword = $value;

            return $this;
        }

        if (self::isOwn($property)) {
            $this->setError("Can't access private properties");
            return $this;
        }

        if (!property_exists(__CLASS__, $property)) {
            if (property_exists(__CLASS__, self::auxProperty($property, 's'))) {
                $property = self::auxProperty($property, 's');
            } elseif (property_exists(__CLASS__, self::auxProperty($property, 'v'))) {
                $property = self::auxProperty($property, 'v');
            } else {
                $this->setError("Unknown property: $property");
                return $this;
            }
        }

        if (self::isAux($property, 'v')) {
            if (empty($value)) {
                $value = [];
            } else {
                if (!is_array($value)) {
                    $value = [$value];
                }

                $list = array_unique($value);
                sort($list);
                $value = [];

                foreach ($list as $v) {
                    $value[] = strval($v);
                }
            }
        } elseif (is_string($value)) {
            $value = strval($value);
        }

        $this->$property = $value;

        if ($property == 'userPassword') {
            $this->plainPassword = '';
        }

        if (in_array($property, ['givenName','middleName','surname','name'])) {
            $value = \Hubzero\Utility\Sanitize::cleanProperName($value);
        }

        return $this;
    }

    /**
     * Add to a list of values for multi-value properties
     *
     * @param   string   $property  Property name
     * @param   array|mixed    $value     Property values
     * @return  boolean  True on success, False on error
     */
    public function add($property, $value)
    {
        if (self::isOwn($property)) {
            $this->setError("Can't access private properties");
            return false;
        }

        if (property_exists(__CLASS__, $property) || property_exists(__CLASS__, self::auxProperty($property, 's'))) {
            $this->setError("Can't add value(s) to non-array property.");
            return false;
        }

        if (!property_exists(__CLASS__, self::auxProperty($property, 'v'))) {
            $this->setError("Unknown property: $property");
            return false;
        }

        if (empty($value)) {
            return true;
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $property = self::auxProperty($property, 'v');

        foreach ($value as $v) {
            $v = strval($v);

            if (!in_array($v, $this->$property)) {
                array_push($this->$property, $v);
            }
        }

        sort($this->$property);

        return true;
    }

    /**
     * Remove from a list of values for multi-value properties
     *
     * @param   string             $property  Property name
     * @param   array<mixed>|string|null  $value     Property values, a single value, or null to remove none
     * @return  boolean            True on success, False on error
     */
    public function remove($property, $value)
    {
        if (self::isOwn($property)) {
            $this->setError("Can't access private properties");
            return false;
        }

        if (property_exists(__CLASS__, $property) || property_exists(__CLASS__, self::auxProperty($property, 's'))) {
            $this->setError("Can't remove value(s) from non-array property.");
            return false;
        }

        if (!property_exists(__CLASS__, self::auxProperty($property, 'v'))) {
            $this->setError("Unknown property: $property");
            return false;
        }

        if (!isset($value)) {
            return true;
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $property = self::auxProperty($property, 'v');

        foreach ($value as $v) {
            $v = strval($v);

            if (in_array($v, $this->$property)) {
                $this->$property = array_diff($this->$property, [$v]);
            }
        }

        return true;
    }

    /**
     * Returns a property of the Params object or
     * the default value if the property is not set.
     *
     * @param   string   $key      The name of the property.
     * @param   mixed    $default  The default value.
     * @return  boolean
     */
    public function getParam($key, $default = null)
    {
        return $this->paramsRegistryOrFail()->get($key, $default);
    }

    /**
     * Modifies a property of the Params object.
     *
     * @param   string   $key    The name of the property.
     * @param   mixed    $value  The value of the property to set.
     * @return  \Hubzero\Config\Registry  The parameters (chainable)
     */
    public function setParam($key, $value)
    {
        return $this->paramsRegistryOrFail()->set($key, $value);
    }

    /**
     * Sets a default value on the Params object
     * if not alreay assigned.
     *
     * @param   string   $key    The name of the property.
     * @param   mixed    $value  The default value.
     * @return  string  The value set, or the default if it was not set
     */
    public function defParam($key, $value)
    {
        return $this->paramsRegistryOrFail()->def($key, $value);
    }

    /**
     * Get parameters object
     *
     * @param   boolean  $loadsetupfile  Load the XML set up file?
     * @param   string|null   $path           Path to parameters XML file
     * @return  \Hubzero\Config\Registry|null   Registry
     */
    public function &getParameters($loadsetupfile = false, $path = null)
    {
        static $parampath;

        /*
        // Set a custom parampath if defined
        if (isset($path))
        {
            $parampath = $path;
        }

        // Set the default parampath if not set already
        if (!isset($parampath))
        {
            $parampath = PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'admin' . DS . 'models';
        }

        if ($loadsetupfile)
        {
            $type = str_replace(' ', '_', strtolower($this->usertype));

            $file = $parampath . DS . $type . '.xml';
            if (!file_exists($file))
            {
                $file = $parampath . DS . 'user.xml';
            }

            $this->paramsRegistry->loadSetupFile($file);
        }
        */

        return $this->paramsRegistry;
    }

    /**
     * Set parameters
     *
     * @param   \Hubzero\Config\Registry  $params  Parameters object to set
     * @return  void
     */
    public function setParameters($params)
    {
        $this->paramsRegistry = $params;
    }

    /**
     * Get group roles for a specific member/group pair
     *
     * @param   string  $uid  User ID
     * @param   string  $gid  Group ID
     * @return  array<int, array<string, mixed>>
     */
    public static function getGroupMemberRoles($uid, $gid)
    {
        $db = App::get('db');
        $sql = "SELECT r.id, r.name, r.permissions FROM `#__xgroups_roles` as r, `#__xgroups_member_roles` as m WHERE "
            . "r.id=m.roleid AND m.uidNumber="
            . $db->quote($uid)
            . " AND r.gidNumber="
            . $db->quote($gid);
        $db->setQuery($sql);

        return $db->loadAssocList() ?? [];
    }

    /**
     * Check to see if user has permission to perform task
     *
     * @param   \Hubzero\User\Group   $group   \Hubzero\User\Group
     * @param   string   $action  Group Action to perform
     * @return  boolean
     */
    public static function userHasPermissionForGroupAction($group, $action)
    {
        // Get user roles
        $roles = self::getGroupMemberRoles(
            \User::get('id'),
            $group->get('gidNumber'),
        );

        // Check to see if any of our roles for user has permission for action
        foreach ($roles as $role) {
            $permissions = json_decode($role['permissions']);
            $permissions = (is_object($permissions)) ? $permissions : new \stdClass();
            if (property_exists($permissions, $action) && $permissions->$action == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the groups for a user
     *
     * @param   string  $role  The group set to return. Returns all groups if not set
     * @return  array<mixed>   Array of groups
     */
    public function getGroups($role = 'all')
    {
        // One set per user: a function's static is shared by every instance
        static $cache = [];

        $key = (int) $this->get('uidNumber');
        if (!isset($cache[$key])) {
            $groups = [
                'applicants' => [],
                'invitees'   => [],
                'members'    => [],
                'managers'   => [],
                'all'        => [],
            ];
            $groups['all'] = Helper::getGroups($this->get('uidNumber'), 'all', 1);

            if ($groups['all']) {
                foreach ($groups['all'] as $item) {
                    if ($item->registered) {
                        if (!$item->regconfirmed) {
                            $groups['applicants'][] = $item;
                        } else {
                            if ($item->manager) {
                                $groups['managers'][] = $item;
                            } else {
                                $groups['members'][] = $item;
                            }
                        }
                    } else {
                        $groups['invitees'][] = $item;
                    }
                }
            }
            $cache[$key] = $groups;
        }

        $groups = $cache[$key];

        if ($role) {
            return (isset($groups[$role])) ? $groups[$role] : false;
        }

        return $groups;
    }

    /**
     * Get the content of the entry
     *
     * @param   string   $as       Format to return state in [text, number]
     * @param   integer  $shorten  Number of characters to shorten text to
     * @return  string
     */
    public function getBio($as = 'parsed', $shorten = 0)
    {
        $options = [];

        switch (strtolower($as)) {
            case 'parsed':
                $config = [
                    'option'   => 'com_members',
                    'scope'    => 'profile',
                    'pagename' => 'member',
                    'pageid'   => 0,
                    'filepath' => '',
                    'domain'   => '',
                    'camelcase' => 0,
                ];

                Event::trigger('content.onContentPrepare', [
                    'com_members.profile.bio',
                    &$this,
                    &$config,
                ]);
                $content = $this->get('bio');

                $options = ['html' => true];
                break;

            case 'clean':
                $content = strip_tags($this->getBio('parsed'));
                break;

            case 'raw':
            default:
                $content = stripslashes($this->get('bio') ?? '');
                $content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
                break;
        }

        if ($shorten) {
            $content = Str::truncate($content, $shorten, $options);
        }

        return $content;
    }

    /**
     * Get a user's picture
     *
     * @param   integer  $anonymous  Is user anonymous?
     * @param   boolean  $thumbit    Show thumbnail or full picture?
     * @param   bool  $serveFile
     * @return  string
     */
    public function getPicture($anonymous = 0, $thumbit = true, $serveFile = true)
    {
        return ProfileHelper::getMemberPhoto($this, $anonymous, $thumbit, $serveFile);
    }

    /**
     * Generate and return various links to the entry
     * Link will vary depending upon action desired such as edit, delete, etc.
     *
     * @param   string  $type  The type of link to return
     * @return  string
     */
    public function getLink($type = '')
    {
        if (!$id = $this->get('uidNumber')) {
            return '';
        }

        $link = 'index.php?option=com_members&id=' . $id;

        // If it doesn't exist or isn't published
        $type = strtolower($type);
        switch ($type) {
            case 'edit':
            case 'changepassword':
                $link .= '&task=' . $type;
                break;

            default:
                break;
        }

        return $link;
    }

    /**
     * The paramsRegistry, which the calls using it need set
     *
     * @return  \Hubzero\Config\Registry
     * @throws  \LogicException  When it is not
     */
    protected function paramsRegistryOrFail()
    {
        if ($this->paramsRegistry === null) {
            throw new \LogicException(static::class . '::$paramsRegistry is not set');
        }

        return $this->paramsRegistry;
    }
}

<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models;

use Components\Members\Models\Import\Record;
use Components\Members\Models\Profile\Field;
use Hubzero\Content\Import\Model\Import as Base;
use Hubzero\Content\Importer;
use Hubzero\Utility\Sanitize;
use stdClass;

/**
 * Member importer
 */
class Import extends Base
{
    /**
     * Import column to field mapping
     *
     * @var  array|null
     */
    private $fields = null;

    /**
     * Whether fieldMap() has added the custom profile fields
     *
     * @var  bool
     */
    private $mapped = false;

    /**
     * Mapping key
     *
     * @var  array
     */
    private $fieldMap = [
        'surname' => [
            'lname',
            'lastname',
            'surname',
        ],
        'givenName' => [
            'fname',
            'firstname',
            'givenname',
        ],
        'middleName' => [
            'mname',
            'middlename',
            'midname',
        ],
        'name' => [
            'name',
            'fullname',
        ],
        'id' => [
            'id',
            'uid',
            'userid',
            'uidnumber',
            'mid',
            'midnumber',
            'memberid',
            'profileid',
        ],
        'email' => [
            'mail',
            'email',
            'electronicmail',
            'emailaddress',
        ],
        'username' => [
            'username',
            'uname',
            'login',
            'userlogin',
        ],
        'access' => [
            'public',
            'publicprofile',
            'access',
            'visibility',
            'permissions',
        ],
        'block' => [
            'block',
            'blocked',
        ],
        'approved' => [
            'approve',
            'approved',
        ],
        'interests' => [
            'tags',
            'interests',
            'tag',
            'interest',
            'likes',
            'keyword',
            'keywords',
        ],
        'note' => [
            'note',
            'notes',
            'adminnote',
            'adminnotes',
            'accountnote',
            'accountnotes',
            'administratornote',
            'administratornotes',
            'comment',
            'comments',
            'admincomment',
            'admincomments',
            'accountcomment',
            'accountcomments',
            'administratorcomment',
            'administratorcomment',
        ],
        'jobsAllowed' => [
            'jobsallowed',
            'jobquota',
            'joballowance',
            'jobs',
            'quota',
        ],
        'homeDirectory' => [
            'home',
            'homedirectory',
            'homepath',
            'homefolder',
            'homedir',
        ],
        'loginShell' => [
            'loginshell',
        ],
        'ftpShell' => [
            'ftpshell',
        ],
        'usageAgreement' => [
            'usageagreement',
            'termsofservice',
            'tos',
            'agreement',
        ],
        'password' => [
            'userpassword',
            'password',
            'pass',
            'passcode',
            'passwd',
            'passwrd',
            'memberpassword',
        ],
        'sendEmail' => [
            'mailpreferenceoption',
            'mailpreference',
            'recievemail',
            'recieveemail',
            'getmail',
            'getemail',
            'mailupdates',
            'emailupdates',
            'sendemail',
        ],
        'activation' => [
            'emailconfirmed',
            'confirmedemail',
            'validemail',
            'emailconfirm',
            'activation',
        ],
        /* @deprecated
        'countryresident' => array(
            'countryresident',
            'resident',
            'residence',
            'residency',
            'country',
        ),
        'countryorigin' => array(
            'countryorigin',
            'origin',
            'birthplace',
            'birthcountry',
            'citizenship',
            'citizen',
        ),
        'bio' => array(
            'bio',
            'biography',
            'about',
        ),
        'race' => array(
            'race',
            'racial',
            'ethnicity',
            'ethnic',
        ),
        'hispanic' => array(
            'hispanic',
            'latin',
            'latino',
        ),
        'nativeTribe' => array(
            'nativetribe',
            'tribe',
            'nativeamericantribe',
            'indiantribe',
        ),
        'disability' => array(
            'disability',
            'disabled',
            'handicap',
        ),
        'organization' => array(
            'org',
            'organization',
            'organisation',
            'company',
        ),
        'orgtype' => array(
            'orgtype',
            'organizationtype',
            'organisationtype',
            'companytype',
            'employertype',
            'employmenttype',
        ),
        'phone' => array(
            'phone',
            'cell',
            'cellphone',
            'telephone',
            'workphone',
        ),
        'gender' => array(
            'gender',
            'sex',
        ),
        'orcid' => array(
            'orcid',
        ),
        'url' => array(
            'url',
            'website',
            'webpage',
            'site',
            'homepage',
        ),
        'reason' => array(
            'reason',
            'reasonforaccount',
            'reasonforjoining',
            'reasonformembership',
            'whyjoin',
            'whybecomeamember',
        ),
        'picture' => array(
            'picture',
            'pic',
            'image',
            'img',
            'photo',
        ),
        'vip' => array(
            'vip',
            'veryimportantperson',
            'veryimportant',
        ),*/
        'locked' => [
            'locked',
            'lock',
            'padlock',
            'restrict',
            'restricted',
        ],
        'registerIP' => [
            'regip',
            'registrationip',
            'registeredip',
            'registerip',
        ],
        'registerHost' => [
            'reghost',
            'registrationhost',
            'registeredhost',
            'registerhost',
        ],
        'registerDate' => [
            'registerdate',
            'registrationdate',
            'registereddate',
            'registered',
            'signedup',
            'created',
            'createddate',
            'createdate',
        ],
        'modifiedDate' => [
            'modifieddate',
            'modified',
            'changed',
            'changedate',
            'changeddate',
            'updated',
            'updatedate',
            'updateddate',
            'lastmodified',
            'lastchanged',
            'lastupdate',
            'lastupdated',
        ],
        'groups' => [
            'group',
            'groups',
            'gid',
            'gids',
            'gidNumber',
            'gidNumbers',
            'cn',
            'group_alias',
            'group_aliases',
            'group_cn',
            'group_membership',
        ],
        'projects' => [
            'project',
            'projects',
            'pid',
            'pids',
            'pidNumber',
            'pidNumbers',
            'project_alias',
            'project_aliases',
            'project_membership',
        ],
    ];

    /**
     * Store changes to this database entry
     *
     * @return  boolean  False if error, True on success
     */
    public function save()
    {
        $this->set('type', 'members');

        return parent::save();
    }

    /**
     * Get the generated record from the raw data
     *
     * @param   mixed   $raw      Raw data
     * @param   array   $options  Import options
     * @param   string  $mode     Operation mode (update|patch)
     * @return  object
     */
    public function getRecord($raw, $options = [], $mode = 'UPDATE')
    {
        $record = new stdClass();
        $record->_unused = [];

        if (is_array($raw)) {
            foreach ($raw as $key => $val) {
                $val = trim($val);
                $val = Sanitize::cleanMsChar($val);
                if (function_exists('mb_convert_encoding')) {
                    $val = mb_convert_encoding($val, 'UTF-8');
                }

                if (!$field = $this->fields($key)) {
                    $record->_unused[$key] = $val;
                    continue;
                }

                $record->$field = $val;
            }
        } elseif (is_object($raw)) {
            foreach (get_object_vars($raw) as $key => $val) {
                $val = trim($val);
                $val = Sanitize::cleanMsChar($val);
                if (function_exists('mb_convert_encoding')) {
                    $val = mb_convert_encoding($val, 'UTF-8');
                }

                if (!$field = $this->fields($key)) {
                    $record->_unused[$key] = $val;
                    continue;
                }

                $record->$field = $val;
            }
        }

        return new Record($record, $options, $mode);
    }

    /**
     * Accepts a column name (from import file) and tries
     * to find an associated record field
     *
     * @param   string  $name
     * @return  string
     */
    public function fields($name = null)
    {
        if (!$this->fields) {
            $this->fields = [];

            if ($fields = $this->get('fields')) {
                $this->fields = json_decode($fields, true);
            }

            if ($this->get('file')) {
                $headers = with(new Importer())->headers($this);

                $this->fields = $this->autoDetectFields($headers);
            }
        }

        if ($name) {
            $name  = $this->normalize($name);
            $field = $name;

            if (isset($this->fields[$name])) {
                $field = $this->fields[$name]['field'];
            }

            foreach ($this->fieldMap as $column => $aliases) {
                if (in_array($name, $aliases)) {
                    $field = $column;
                    break;
                }
            }

            return $field;
        }

        return $this->fields;
    }

    /**
     * Strip spaces, punctuation, and make lower case
     *
     * @param   string  $txt
     * @return  string
     */
    private function normalize($txt)
    {
        return strtolower(preg_replace("/[^a-zA-Z0-9]/", '', $txt));
    }

    /**
     * Map headers to fields
     *
     * @param   array   $headers  List of columns from import file
     * @return  array
     */
    public function autoDetectFields(array $headers)
    {
        $mapping = [];

        foreach ($headers as $header) {
            $norm = $this->normalize($header);

            $map = [
                'label' => $header,
                'name'  => $norm,
                'field' => '',
            ];

            foreach ($this->fieldMap() as $column => $aliases) {
                if (in_array($norm, $aliases)) {
                    $map['field'] = $column;
                    break;
                }
            }

            $mapping[$norm] = $map;
        }

        return $mapping;
    }

    /**
     * Map custom fields
     *
     * @return  array
     */
    public function fieldMap()
    {
        if (!$this->mapped) {
            $fields = Field::all()
                ->ordered()
                ->rows();

            foreach ($fields as $field) {
                if (isset($this->fieldMap[$field->get('name')])) {
                    continue;
                }

                $this->fieldMap[$field->get('name')] = [
                    $field->get('name'),
                    strtolower($field->get('name')),
                    preg_replace('/[^a-zA-Z0-9]/', '', $field->get('name')),
                ];
            }

            $this->mapped = true;
        }

        return $this->fieldMap;
    }
}

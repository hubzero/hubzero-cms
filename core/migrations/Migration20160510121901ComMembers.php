<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding tables and data for profile schema
  *
**/
class Migration20160510121901ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__user_profile_fields')) {
            $schema->createTable('#__user_profile_fields')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('type', 255)
                ->string('name', 255)->default('')
                ->string('label', 255)->default('')
                ->string('placeholder', 255)->nullable()
                ->mediumText('description')->nullable()
                ->integer('ordering')->default(0)
                ->integer('access')->default(0)
                ->tinyInteger('option_other')->default(0)
                ->tinyInteger('option_blank')->default(0)
                ->tinyInteger('action_create')->default(1)
                ->tinyInteger('action_update')->default(1)
                ->tinyInteger('action_edit')->default(1)
                ->primaryKey('id')
                ->index('idx_type', 'type')
                ->index('idx_access', 'access')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $params = \Component::params('com_members');

            // Build field value arrays for cleaner INSERT query
            $orgCreate = self::state($params->get('registrationOrganization'), 'HHHH', 'create');
            $orgUpdate = self::state($params->get('registrationOrganization'), 'HHHH', 'update');
            $orgEdit = self::state($params->get('registrationOrganization'), 'HHHH', 'edit');

            $empCreate = self::state($params->get('registrationEmployment'), 'HHHH', 'create');
            $empUpdate = self::state($params->get('registrationEmployment'), 'HHHH', 'update');
            $empEdit = self::state($params->get('registrationEmployment'), 'HHHH', 'edit');

            $resCreate = self::state($params->get('registrationResidency'), 'HHHH', 'create');
            $resUpdate = self::state($params->get('registrationResidency'), 'HHHH', 'update');
            $resEdit = self::state($params->get('registrationResidency'), 'HHHH', 'edit');

            $citCreate = self::state($params->get('registrationCitizenship'), 'HHHH', 'create');
            $citUpdate = self::state($params->get('registrationCitizenship'), 'HHHH', 'update');
            $citEdit = self::state($params->get('registrationCitizenship'), 'HHHH', 'edit');

            $urlCreate = self::state($params->get('registrationURL'), 'HHHH', 'create');
            $urlUpdate = self::state($params->get('registrationURL'), 'HHHH', 'update');
            $urlEdit = self::state($params->get('registrationURL'), 'HHHH', 'edit');

            $phoneCreate = self::state($params->get('registrationPhone'), 'HHHH', 'create');
            $phoneUpdate = self::state($params->get('registrationPhone'), 'HHHH', 'update');
            $phoneEdit = self::state($params->get('registrationPhone'), 'HHHH', 'edit');

            $orcidCreate = self::state($params->get('registrationORCID'), 'HHHH', 'create');
            $orcidUpdate = self::state($params->get('registrationORCID'), 'HHHH', 'update');
            $orcidEdit = self::state($params->get('registrationORCID'), 'HHHH', 'edit');

            $sexCreate = self::state($params->get('registrationSex'), 'HHHH', 'create');
            $sexUpdate = self::state($params->get('registrationSex'), 'HHHH', 'update');
            $sexEdit = self::state($params->get('registrationSex'), 'HHHH', 'edit');

            $raceCreate = self::state($params->get('registrationRace'), 'HHHH', 'create');
            $raceUpdate = self::state($params->get('registrationRace'), 'HHHH', 'update');
            $raceEdit = self::state($params->get('registrationRace'), 'HHHH', 'edit');

            $disCreate = self::state($params->get('registrationDisability'), 'HHHH', 'create');
            $disUpdate = self::state($params->get('registrationDisability'), 'HHHH', 'update');
            $disEdit = self::state($params->get('registrationDisability'), 'HHHH', 'edit');

            $reasonCreate = self::state($params->get('registrationReason'), 'HHHH', 'create');
            $reasonUpdate = self::state($params->get('registrationReason'), 'HHHH', 'update');
            $reasonEdit = self::state($params->get('registrationReason'), 'HHHH', 'edit');

            $intCreate = self::state($params->get('registrationInterests'), 'HHHH', 'create');
            $intUpdate = self::state($params->get('registrationInterests'), 'HHHH', 'update');
            $intEdit = self::state($params->get('registrationInterests'), 'HHHH', 'edit');

            $addrCreate = self::state($params->get('registrationAddress'), 'HHHH', 'create');
            $addrUpdate = self::state($params->get('registrationAddress'), 'HHHH', 'update');
            $addrEdit = self::state($params->get('registrationAddress'), 'HHHH', 'edit');

            $hispCreate = self::state($params->get('registrationHispanic'), 'HHHH', 'create');
            $hispUpdate = self::state($params->get('registrationHispanic'), 'HHHH', 'update');
            $hispEdit = self::state($params->get('registrationHispanic'), 'HHHH', 'edit');

            $orcidDesc = 'Open Researcher and Contributor ID (ORCID) provides a persistent '
                . 'digital identifier that distinguishes you from every other researcher '
                . 'and supports automated linkages between you and your professional '
                . 'activities ensuring that your work is recognized.';

            $this->db->getQuery(true)
                ->insert('#__user_profile_fields')
                ->columns([
                    'id',
                    'type',
                    'name',
                    'label',
                    'placeholder',
                    'description',
                    'ordering',
                    'access',
                    'option_other',
                    'option_blank',
                    'action_create',
                    'action_update',
                    'action_edit',
                ])
                ->values([
                    [
                        1,
                        'select',
                        'organization',
                        'Organization',
                        null,
                        null,
                        1,
                        1,
                        1,
                        1,
                        $orgCreate,
                        $orgUpdate,
                        $orgEdit,
                    ],
                    [
                        2,
                        'select',
                        'orgtype',
                        'Employment Status',
                        null,
                        null,
                        2,
                        5,
                        0,
                        1,
                        $empCreate,
                        $empUpdate,
                        $empEdit,
                    ],
                    [
                        3,
                        'country',
                        'countryresident',
                        'Residency',
                        null,
                        'Select your Country of Residency.',
                        3,
                        5,
                        0,
                        1,
                        $resCreate,
                        $resUpdate,
                        $resEdit,
                    ],
                    [
                        4,
                        'country',
                        'countryorigin',
                        'Citizenship',
                        null,
                        'Select your Country of Citizenship.',
                        4,
                        5,
                        0,
                        1,
                        $citCreate,
                        $citUpdate,
                        $citEdit,
                    ],
                    [5, 'url', 'url', 'Website', null, null, 5, 1, 0, 0, $urlCreate, $urlUpdate, $urlEdit],
                    [6, 'text', 'phone', 'Telephone', null, null, 6, 5, 0, 0, $phoneCreate, $phoneUpdate, $phoneEdit],
                    [
                        7,
                        'orcid',
                        'orcid',
                        'ORCID',
                        null,
                        $orcidDesc,
                        7,
                        1,
                        0,
                        0,
                        $orcidCreate,
                        $orcidUpdate,
                        $orcidEdit,
                    ],
                    [8, 'radio', 'gender', 'Gender', null, null, 8, 5, 0, 0, $sexCreate, $sexUpdate, $sexEdit],
                    [
                        9,
                        'checkboxes',
                        'race',
                        'Racial Background',
                        null,
                        null,
                        9,
                        5,
                        0,
                        0,
                        $raceCreate,
                        $raceUpdate,
                        $raceEdit,
                    ],
                    [
                        10,
                        'checkboxes',
                        'disability',
                        'Disability',
                        null,
                        null,
                        10,
                        5,
                        0,
                        0,
                        $disCreate,
                        $disUpdate,
                        $disEdit,
                    ],
                    [
                        11,
                        'select',
                        'reason',
                        'Reason',
                        null,
                        null,
                        11,
                        5,
                        0,
                        1,
                        $reasonCreate,
                        $reasonUpdate,
                        $reasonEdit,
                    ],
                    [12, 'tags', 'tags', 'Interests', null, null, 12, 1, 0, 0, $intCreate, $intUpdate, $intEdit],
                    [13, 'address', 'address', 'Address', null, null, 13, 5, 0, 0, $addrCreate, $addrUpdate, $addrEdit],
                    [
                        14,
                        'radio',
                        'hispanic',
                        'Hispanic Heritage',
                        null,
                        null,
                        14,
                        5,
                        0,
                        0,
                        $hispCreate,
                        $hispUpdate,
                        $hispEdit,
                    ],
                    [15, 'editor', 'bio', 'Biography', null, null, 15, 1, 0, 0, 0, 0, 1]
                ])
                ->execute();
        }

        if (!$schema->tableExists('#__user_profile_options')) {
            $schema->createTable('#__user_profile_options')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('field_id')->default(0)
                ->string('value', 255)->default('')
                ->string('label', 255)->default('')
                ->integer('ordering')->default(0)
                ->tinyInteger('checked')->default(0)
                ->primaryKey('id')
                ->index('idx_field_id', 'field_id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            // Gender
            $this->db->getQuery(true)
                ->insert('#__user_profile_options')
                ->columns(['id', 'field_id', 'value', 'label', 'ordering', 'checked'])
                ->values([
                    [null, 8, 'male', 'Male', 1, 0],
                    [null, 8, 'female', 'Female', 2, 0],
                    [null, 8, 'refused', 'Do not with to reveal', 3, 0]
                ])
                ->execute();

            // Race
            $this->db->getQuery(true)
                ->insert('#__user_profile_options')
                ->columns(['id', 'field_id', 'value', 'label', 'ordering', 'checked'])
                ->values([
                    [null, 9, 'nativeamerican', 'American Indian or Alaska Native', 1, 0],
                    [null, 9, 'asian', 'Asian', 2, 0],
                    [null, 9, 'black', 'Black or African American', 3, 0],
                    [null, 9, 'hawaiian', 'Native Hawaiian or Other Pacific Islander', 4, 0],
                    [null, 9, 'white', 'White', 5, 0],
                    [null, 9, 'refused', 'Do not wish to reveal', 6, 0]
                ])
                ->execute();

            // Hispanic
            $this->db->getQuery(true)
                ->insert('#__user_profile_options')
                ->columns(['id', 'field_id', 'value', 'label', 'ordering', 'checked'])
                ->values([
                    [null, 14, 'cuban', 'Cuban', 1, 0],
                    [null, 14, 'mexican', 'Mexican American or Chicano', 2, 0],
                    [null, 14, 'puertorican', 'Puerto Rican', 3, 0],
                    [null, 14, 'no', 'No (not Hispanic or Latino)', 4, 0],
                    [null, 14, 'refused', 'Do not wish to reveal', 5, 0]
                ])
                ->execute();

            // Disability
            $this->db->getQuery(true)
                ->insert('#__user_profile_options')
                ->columns(['id', 'field_id', 'value', 'label', 'ordering', 'checked'])
                ->values([
                    [null, 10, 'blind', 'Blind / Visually Impaired', 1, 0],
                    [null, 10, 'deaf', 'Deaf / Hard of Hearing', 2, 0],
                    [null, 10, 'physical', 'Physical / Orthopedic Disability', 3, 0],
                    [null, 10, 'learning', 'Learning / Cognitive Disability', 4, 0],
                    [null, 10, 'vocal', 'Vocal / Speech Disability', 5, 0],
                    [null, 10, 'no', 'No (none)', 6, 0],
                    [null, 10, 'refused', 'Do not wish to reveal', 7, 0]
                ])
                ->execute();

            // Organizations
            if ($schema->tableExists('#__xorganizations')) {
                $organizations = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__xorganizations')
                    ->loadObjectList();

                if (count($organizations) > 0) {
                    $columns = ['id', 'field_id', 'value', 'label', 'ordering', 'checked'];
                    $values  = [];

                    foreach ($organizations as $i => $organization) {
                        $values[] = [
                            null,
                            1,
                            $organization->organization,
                            $organization->organization,
                            ($i + 1),
                            0
                        ];
                    }

                    $this->db->getQuery(true)
                        ->insert('#__user_profile_options')
                        ->columns($columns)
                        ->values($values)
                        ->execute();
                }

                $this->db->schema()->dropTable('#__xorganizations');
            }

            // Organization types
            if ($schema->tableExists('#__xorganization_types')) {
                $types = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__xorganization_types')
                    ->loadObjectList();

                if (count($types) > 0) {
                    $columns = ['id', 'field_id', 'value', 'label', 'ordering', 'checked'];
                    $values  = [];

                    foreach ($types as $i => $type) {
                        $values[] = [
                            null,
                            2,
                            $type->type,
                            $type->title,
                            ($i + 1),
                            0
                        ];
                    }

                    $this->db->getQuery(true)
                        ->insert('#__user_profile_options')
                        ->columns($columns)
                        ->values($values)
                        ->execute();
                }

                $this->db->schema()->dropTable('#__xorganization_types');
            }

            // Reasons
            if ($schema->tableExists('#__xprofiles_reasons')) {
                $reasons = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__xprofiles_reasons')
                    ->loadObjectList();

                if (count($reasons) > 0) {
                    $columns = ['id', 'field_id', 'value', 'label', 'ordering', 'checked'];
                    $values  = [];

                    foreach ($reasons as $i => $reason) {
                        $values[] = [
                            null,
                            11,
                            $reason->reason,
                            $reason->reason,
                            ($i + 1),
                            0
                        ];
                    }

                    $this->db->getQuery(true)
                        ->insert('#__user_profile_options')
                        ->columns($columns)
                        ->values($values)
                        ->execute();
                }

                $this->db->schema()->dropTable('#__xprofiles_reasons');
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__user_profile_fields');

        // Organization types
        if (!$schema->tableExists('#__xorganizations')) {
            $schema->createTable('#__xorganizations')
                ->integer('id', ['autoIncrement' => true])
                ->string('organization', 255)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            if ($schema->tableExists('#__user_profile_options')) {
                $organizations = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__user_profile_options')
                    ->where('field_id', '=', 1)
                    ->order('ordering', 'asc')
                    ->loadObjectList();

                if (count($organizations) > 0) {
                    $columns = ['id', 'organization'];
                    $values  = [];

                    foreach ($organizations as $i => $organization) {
                        $values[] = [null, $organization->label];
                    }

                    $this->db->getQuery(true)
                        ->insert('#__xorganizations')
                        ->columns($columns)
                        ->values($values)
                        ->execute();
                }
            }
        }

        // Organization types
        if (!$schema->tableExists('#__xorganization_types')) {
            $schema->createTable('#__xorganization_types')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('type', 150)->nullable()
                ->string('title', 255)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            if ($schema->tableExists('#__user_profile_options')) {
                $types = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__user_profile_options')
                    ->where('field_id', '=', 2)
                    ->order('ordering', 'asc')
                    ->loadObjectList();

                if (count($types) > 0) {
                    $columns = ['id', 'type', 'title'];
                    $values  = [];

                    foreach ($types as $i => $type) {
                        $values[] = [
                            null,
                            $type->name,
                            $type->label
                        ];
                    }

                    $this->db->getQuery(true)
                        ->insert('#__xorganization_types')
                        ->columns($columns)
                        ->values($values)
                        ->execute();
                }
            }
        }

        // Reasons
        if (!$schema->tableExists('#__xprofiles_reasons')) {
            $schema->createTable('#__xprofiles_reasons')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('reason', 255)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            if ($schema->tableExists('#__user_profile_options')) {
                $reasons = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__user_profile_options')
                    ->where('field_id', '=', 11)
                    ->order('ordering', 'asc')
                    ->loadObjectList();

                if (count($reasons) > 0) {
                    $columns = ['id', 'reason'];
                    $values  = [];

                    foreach ($reasons as $i => $reason) {
                        $values[] = [null, $reason->label];
                    }

                    $this->db->getQuery(true)
                        ->insert('#__xprofiles_reasons')
                        ->columns($columns)
                        ->values($values)
                        ->execute();
                }
            }
        }

        $schema->dropTable('#__user_profile_options');
    }

    /**
     * Return if a field is required, option, read only, or hidden
     *
     * @param   string  $name     Property name
     * @param   string  $default  Default property value
     * @param   string  $task     Task to look up value for
     * @return  string
     */
    public static function state($configured, $default = 'OOOO', $task = 'create')
    {
        switch ($task) {
            case 'register':
            case 'create':
                $index = 0;
                break;
            case 'proxy':
                $index = 1;
                break;
            case 'update':
                $index = 2;
                break;
            case 'edit':
                $index = 3;
                break;
            default:
                $index = 0;
                break;
        }

        $default = str_pad($default, 4, '-');

        if (empty($configured)) {
            $configured = $default;
        }

        $length = strlen($configured);

        if ($length <= $index) {
            $configured = $default;
        }

        $key = substr($configured, $index, 1);

        switch ($key) {
            case 'R':
                $val = 2;
                break;
            case 'O':
                $val = 1;
                break;
            case 'U':
                $val = 4;
                break;
            case 'H':
            case '-':
            default:
                $val = 0;
                break;
        }

        return $val;
    }
}

<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models;

use Components\Groups\Models\Import\Record;
use Components\Groups\Models\Orm\Field;
use Hubzero\Content\Import\Model\Import as Base;
use Hubzero\Content\Importer;
use Hubzero\Utility\Sanitize;
use stdClass;

/**
 * Member importer
 *
 */
class Import extends Base
{
    /**
     * Import column to field mapping
     *
     * @var  object
     */
    private $fields = null;

    /**
     * Whether fieldMap() has added the custom fields yet
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
        'cn' => [
            'cn',
            'name',
            'alias',
            'group',
            'group_alias',
            'group_aliases',
        ],
        'gidNumber' => [
            'id',
            'gid',
            'gids',
            'gidnumber',
            'groupid',
        ],
        'description' => [
            'description',
            'title',
        ],
        'published' => [
            'published',
            'state',
            'status',
            'unpublished',
        ],
        'approved' => [
            'approved',
            'approval',
            'approve',
            'allowed',
        ],
        /*'public_desc' => array(
            'public_desc',
            'publicdesc',
            'publicdescription',
            'publictext',
            'publicinfo',
            'public',
            'about',
        ),
        'private_desc' => array(
            'private_desc',
            'privatedesc',
            'privatedescription',
            'privatetext',
            'privateinfo',
            'private',
        ),*/
        'restrict_msg' => [
            'restrict_msg',
            'restrictmsg',
            'restrict',
            'join_msg',
            'restricted',
            'restrict_message',
            'restrictmessage',
        ],
        'join_policy' => [
            'join_policy',
            'joinpolicy',
            'policy',
            'membership',
            'membershippolicy',
        ],
        'discoverability' => [
            'discoverability',
            'access',
            'visibility',
            'visible',
            'discoverable',
            'hidden',
            'hide',
            'show',
        ],
        'discussion_email_autosubscribe' => [
            'discussion_email_autosubscribe',
            'discussionemailautosubscribe',
            'discussionemail',
            'discussionautosubscribe',
            'emailautosubscribe',
            'discussion',
            'email',
            'autosubscribe',
        ],
        'plugins' => [
            'plugins',
            'plugin',
            'extensions',
            'tabs',
        ],
        'created' => [
            'created',
            'createdtime',
            'createddate',
            'date',
            'time',
            'datetime',
            'made',
        ],
        'created_by' => [
            'createdby',
            'creator',
            'maker',
        ],
        'members' => [
            'members',
            'membership',
            'users',
        ],
        'managers' => [
            'managers',
            'manager',
        ],
        /*'projects' => array(
            'project',
            'projects',
        )*/
    ];

    /**
     * Store changes to this database entry
     *
     * @return  boolean  False if error, True on success
     */
    public function save()
    {
        $this->set('type', 'groups');

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
     * @return  object
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

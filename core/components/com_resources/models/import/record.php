<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Import;

use Components\Resources\Models\Entry;
use Components\Resources\Models\Type;
use Components\Resources\Models\Author;
use Components\Resources\Models\Association;
use Components\Resources\Helpers\Tags;
use Hubzero\Base\Obj;
use Exception;
use stdClass;

// include elements model
include_once dirname(__DIR__) . DS . 'elements.php';

/**
 * Resource Import Record Model
 */
class Record extends Obj
{
    public const TITLE_MATCH = 10;

    public $raw;
    public $record;
    private $mode;
    private $options;
    private $database;
    private $user;

    /**
     * Resource Import Record Constructor
     *
     * @param   mixes  $raw      Raw Resource data
     * @param   array  $options  Import options
     * @return  void
     */
    public function __construct($raw, $options = array(), $mode = 'UPDATE')
    {
        static $fields = null;

        // store our incoming data
        $this->raw      = $raw;
        $this->options = $options;
        $this->mode    = $mode;

        // create core objects
        $this->database = \App::get('db');
        $this->user     = \User::getInstance();

        // create resource objects
        $this->record               = new stdClass();
        $this->record->resource     = Entry::blank();
        $this->record->type         = Type::blank();
        $this->record->children     = array();
        $this->record->tags         = array();
        $this->record->contributors = array();
        $this->record->custom       = new stdClass();

        // message bags for user
        $this->record->errors       = array();
        $this->record->notices      = array();

        // bind data
        $this->bind();
    }

    /**
     * Get the columns from database table.
     *
     * @return  mixed  An array of the field names, or false if an error occurs.
     */
    public function getFields()
    {
        static $cache = null;

        if ($cache === null) {
            // Lookup the fields for this table only once.
            $fields = $this->database->getTableColumns($this->record->resource->getTableName(), false);

            $cache = $fields;
        }

        return $cache;
    }

    /**
     * Bind all raw data
     *
     * @return  object  $this  Current object
     */
    public function bind()
    {
        // wrap type mapping in separate try catch to allow resource
        // data to still be mapped even if there is no id.
        try {
            $this->mapTypeData();
        } catch (Exception $e) {
            array_push($this->record->errors, $e->getMessage());
        }

        // wrap in try catch to avoid breaking in middle of import
        try {
            // map resource data
            $this->mapResourceData();

            // map child resource data
            $this->mapChildData();

            // map contributors
            $this->mapContributorData();

            // map tags
            $this->mapTagsData();
        } catch (Exception $e) {
            array_push($this->record->errors, $e->getMessage());
        }

        // chainability
        return $this;
    }

    /**
     * Check Data integrity
     *
     * @return  object  $this  Current object
     */
    public function check()
    {
        // run save check method
        if (!$this->record->resource->validate()) {
            array_push($this->record->errors, $this->record->resource->getError());
        }

        // check custom field if we have that on
        if ($this->options['requiredfields'] && isset($this->record->type->id)) {
            $resourcesElements = new \Components\Resources\Models\Elements(
                (array) $this->record->custom,
                $this->record->type->customFields
            );
            foreach ($resourcesElements->getSchema()->fields as $field) {
                $value = $resourcesElements->get($field->label);
                if ($field->required && (!isset($value) || $value == '')) {
                    $errorMsg = Lang::txt(
                        'COM_RESOURCES_IMPORT_RECORD_MODEL_MISSING_REQUIREDCUSTOMFIELDS',
                        $field->label
                    );
                    array_push($this->record->errors, $errorMsg);
                }
            }
        }

        // chainability
        return $this;
    }

    /**
     * Store Resource Data
     *
     * @param   integer  $dryRun  Dry Run mode
     * @return  object   $this    Current object
     */
    public function store($dryRun = 1)
    {
        // are we running in dry run mode?
        if ($dryRun || count($this->record->errors) > 0) {
            return $this;
        }

        // attempt to save all data
        // wrap in try catch to avoid break mid import
        try {
            // save resource
            $this->saveResourceData();

            // save child resource data
            $this->saveChildData();

            // save contributors
            $this->saveContributorData();

            // save tags
            $this->saveTagsData();
        } catch (Exception $e) {
            array_push($this->record->errors, $e->getMessage());
        }

        // chainability
        return $this;
    }

    /**
     * Map Resource Type
     *
     * @return  void
     */
    private function mapTypeData()
    {
        // make sure we have a type
        if (!isset($this->raw->type)) {
            throw new Exception(Lang::txt('COM_RESOURCES_IMPORT_RECORD_MODEL_MUSTHAVETYPE'));
        }

        // load type
        $this->record->type = Type::oneOrNew($this->raw->type);

        // make sure we have a valid type
        if (!$this->record->type->id) {
            throw new Exception(Lang::txt('COM_RESOURCES_IMPORT_RECORD_MODEL_UNABLE_LOADTYPE', $this->raw->type));
        }
    }

    /**
     * Map Resource Data
     *
     * @return  void
     */
    private function mapResourceData()
    {
        // do we want to do a title match?
        if ($this->options['titlematch'] == 1 && isset($this->record->type->id)) {
            $quotedTitle = $this->database->quote($this->raw->title);
            $sql = 'SELECT id, title, LEVENSHTEIN( title, ' . $quotedTitle . ' ) as titleDiff
                    FROM `#__resources`
                    WHERE `type`=' . $this->record->type->id
                    . ' HAVING titleDiff < ' . self::TITLE_MATCH;
            $this->database->setQuery($sql);
            $results = $this->database->loadObjectList('id');

            // did we get more then one result?
            if (count($results) > 1) {
                $ids = implode(", ", array_keys($results));
                throw new Exception(Lang::txt('COM_RESOURCES_IMPORT_RECORD_MODEL_UNABLE_DETECTDUPLICATE', $ids));
            }

            // if we only have one were all good
            if (count($results) == 1) {
                // set our id to the matched resource
                $resource = reset($results);
                $this->raw->id = $resource->id;

                // add a notice with link to resource matched
                $baseUrl = rtrim(str_replace('administrator', '', \Request::base()), DS);
                $resourceLink = $baseUrl . DS . 'resources' . DS . $resource->id;
                $link = '<a rel="noopener" target="_blank" href="' . $resourceLink . '">'
                    . $resourceLink . '</a>';
                $matchNotice = Lang::txt(
                    'COM_RESOURCES_IMPORT_RECORD_MODEL_MATCHEDBYTITLE',
                    $link
                );
                array_push($this->record->notices, $matchNotice);
            }
        }

        // do we have a resource id
        // either passed in the raw data or gotten from the title match
        if (isset($this->raw->id) && $this->raw->id > 1) {
            $this->record->resource = Entry::oneOrNew($this->raw->id);
        } else {
            $this->raw->standalone = 1;
            $this->raw->created    = \Date::toSql();
            $this->raw->created_by = $this->user->get('id');

            // publish up/down
            if (!isset($this->raw->publish_up)) {
                $this->raw->publish_up = \Date::toSql();
            }
            if (!isset($this->raw->publish_down)) {
                $this->raw->publish_down = '0000-00-00 00:00:00';
            }
        }

        // set modified date/user
        $this->raw->modified    = \Date::toSql();
        $this->raw->modified_by = $this->user->get('id');

        // set status
        if (isset($this->options['status'])) {
            $this->raw->published = (int) $this->options['status'];
        }

        // set group
        if (isset($this->options['group'])) {
            $this->raw->group_owner = $this->options['group'];
        }

        // set access
        if (isset($this->options['access'])) {
            $this->raw->access = (int) $this->options['access'];
        }

        $raw = (array)$this->raw;
        $props = $this->getFields();

        foreach ($raw as $key => $val) {
            if (isset($props[$key])) {
                // bind resource data
                $this->record->resource->set($key, $val);
            }
        }

        // resource params
        $params = new \Hubzero\Config\Registry($this->record->resource->get('params'));
        $this->record->resource->set('params', $params->toString());

        // resource attributes
        $attribs = new \Hubzero\Config\Registry($this->record->resource->get('attribs'));
        $this->record->resource->set('attribs', $attribs->toString());

        // full text pieces - to add paragraph tags
        $fullTextPieces = array_map("trim", explode("\n", $this->record->resource->introtext));
        $fullTextPieces = array_values(array_filter($fullTextPieces));

        // set the full text
        $this->record->resource->set('fulltxt', "<p>" . implode("</p>\n<p>", $fullTextPieces) . "</p>");

        if (!isset($this->raw->custom_fields)) {
            $this->raw->custom_fields = array();
        }

        $this->record->type = $this->record->resource->type;

        // bind custom fields to types custom fields
        if ($this->record->type->id) {
            $resourcesElements = new \Components\Resources\Models\Elements(
                (array) $this->raw->custom_fields,
                $this->record->type->customFields
            );
            $customFieldsHtml = $resourcesElements->toDatabaseHtml();

            // add all custom fields to custom object
            foreach ($resourcesElements->getSchema()->fields as $field) {
                $fieldLabel = $field->label;
                $fieldName = $field->name;
                $customFields = $this->raw->custom_fields;
                $value = isset($customFields->$fieldName) ? $customFields->$fieldName : null;

                if ($field->type == 'hidden') {
                    $value = (isset($field->options[0])) ? $field->options[0]->value : $value;
                }

                $this->record->custom->$fieldLabel = $value;
            }
        } else {
            $customFieldsHtml = '';
        }

        // add custom fields to fulltxt
        $this->record->resource->set('fulltxt', $this->record->resource->get('fulltxt') . "\n\n" . $customFieldsHtml);
    }

    /**
     * Save Parent Resource Data
     *
     * @return  void
     */
    private function saveResourceData()
    {
        // save main resource
        if (!$this->record->resource->save()) {
            throw new Exception(Lang::txt('COM_RESOURCES_IMPORT_RECORD_MODEL_UNABLE_SAVERESOURCE'));
        }
    }

    /**
     * Map Child Resources
     *
     * @return  void
     */
    private function mapChildData()
    {
        // do we have an array of child resources?
        if (isset($this->raw->children) && is_array($this->raw->children)) {
            // loop through each child resource and bind as a resource object
            foreach ($this->raw->children as $child) {
                $childResource = Entry::blank();
                $childResource->set($child);

                // add this child to
                array_push($this->record->children, $childResource);
            }
        }
    }

    /**
     * Save Child Resources
     *
     * @return  void
     */
    private function saveChildData()
    {
        // if we updating we want to completely replace
        if ($this->mode == 'UPDATE' && $this->record->resource->id) {
            // remove any existing files
            $children = $this->record->resource->children()->rows();

            foreach ($children as $child) {
                $rconfig = \Component::params('com_resources');
                $base = PATH_APP . DS . trim($rconfig->get('uploadpath', '/site/resources'), DS);
                $file = $base . DS . $child->path;

                //get file info
                $info = pathinfo($file);
                $directory = $info['dirname'];

                if ($child->get('type') == 13 && file_exists($file)) {
                    \Filesystem::delete($file);
                }

                if (is_dir($directory)) {
                    // get iterator on directory
                    $iterator = new \FilesystemIterator($directory);
                    $isDirEmpty = !$iterator->valid();

                    // remove directory if empty
                    if ($isDirEmpty) {
                        \Filesystem::deleteDirectory($directory);
                    }
                }
            }

            // delete all child resources
            $quotedId = $this->database->quote($this->record->resource->id);
            $sql = "DELETE FROM `#__resources` WHERE `id` IN (
                    SELECT child_id FROM `#__resource_assoc` WHERE `parent_id`=" . $quotedId
                    . ")";
            $this->database->setQuery($sql);
            $this->database->query();

            // delete all child resource associations
            $sql = "DELETE FROM `#__resource_assoc` WHERE `parent_id`=" . $quotedId;
            $this->database->setQuery($sql);
            $this->database->query();
        }

        // loop through each child
        foreach ($this->record->children as $child) {
            // save child
            if (!$child->save()) {
                throw new Exception(Lang::txt('COM_RESOURCES_IMPORT_RECORD_MODEL_UNABLE_SAVECHILD'));
            }

            // create parent - child association
            $assoc = Association::blank();
            $assoc->set('parent_id', $this->record->resource->id);
            $assoc->set('child_id', $child->id);
            $assoc->set('grouping', 0);

            if (!$assoc->save()) {
                throw new Exception(Lang::txt('COM_RESOURCES_IMPORT_RECORD_MODEL_UNABLE_SAVECHILDASSOC'));
            }
        }
    }

    /**
     * Map Resource Contributors
     *
     * @return  void
     */
    private function mapContributorData()
    {
        // get any contributors
        $contributors = (isset($this->raw->contributors)) ? $this->raw->contributors : new stdClass();

        // get roles for resource type
        $rolesForType = $this->record->resource->type->roles->toArray();
        $rolesForType = (is_array($rolesForType)) ? $rolesForType : array();

        // get valid role aliases
        $existingRoles = array_map(function ($role) {
            return $role['alias'];
        }, $rolesForType);

        // handle contributors as string
        if (is_string($contributors)) {
            $contributors = array_map("trim", explode(';', $contributors));
            $contributors = array_values(array_filter($contributors));

            $contributors = array_map(function ($c) {
                $cc = new stdClass();
                $cc->name = $c;
                return $cc;
            }, $contributors);
        }

        // loop through each contributor
        foreach ($contributors as $contributor) {
            // create resource contributor object
            $resourceContributor = null;

            if ($this->record->resource->id) {
                if (isset($contributor->authorid)) {
                    // Do we already have a relationship by authorid?
                    $resourceContributor = Author::oneByRelationship(
                        $this->record->resource->id,
                        $contributor->authorid
                    );
                }

                if (!$resourceContributor && isset($contributor->name)) {
                    // Check for a relationship by author's name
                    $resourceContributor = Author::oneByName($this->record->resource->id, $contributor->name);
                }
            }

            if (!$resourceContributor) {
                $resourceContributor = Author::blank();
            }

            // check to see if we have an author id
            $authorid = (isset($contributor->authorid)) ? $contributor->authorid : null;

            // load name
            if ($authorid != null) {
                if ($profile = \Hubzero\User\User::oneOrNew($authorid)) {
                    $resourceContributor->authorid = $profile->get('id');
                }
            }

            $contribName = isset($contributor->name) ? $contributor->name : '';
            $contribOrg = isset($contributor->organization) ? $contributor->organization : '';
            $hasValidRole = isset($contributor->role)
                && in_array($contributor->role, $existingRoles);
            $contribRole = $hasValidRole ? $contributor->role : '';
            $resourceContributor->set(array(
                'name'         => $contribName,
                'organization' => $contribOrg,
                'role'         => $contribRole,
                'subtable'     => 'resources'
            ));

            array_push($this->record->contributors, $resourceContributor);
        }
    }

    /**
     * Save Resource Contributors
     *
     * @return  void
     */
    private function saveContributorData()
    {
        // if we updating we want to completely replace
        if ($this->mode == 'UPDATE' && $this->record->resource->id) {
            // delete all child resource associations
            $quotedId = $this->database->quote($this->record->resource->id);
            $sql = "DELETE FROM `#__author_assoc` "
                . "WHERE `subtable`='resources' AND `subid`=" . $quotedId;
            $this->database->setQuery($sql);
            $this->database->query();
        }

        // create new author assoc for resource
        foreach ($this->record->contributors as $contributor) {
            $contributor->set('subid', $this->record->resource->id);
            $authorId = ($contributor->authorid == '')
                ? $contributor->getUserId($contributor->name)
                : $contributor->authorid;
            $contributor->set('authorid', $authorId);
            $contributor->save();
        }
    }

    /**
     * Map Resource Tags
     *
     * @return  void
     */
    private function mapTagsData()
    {
        if (isset($this->raw->tags)) {
            $tags = $this->raw->tags;

            // handle tags as string (comma separated)
            if (is_string($tags)) {
                $tags = array_map("trim", explode(',', $tags));
                $tags = array_values(array_filter($tags));
            }

            $this->record->tags = $tags;
        }
    }

    /**
     * Save Resource Tags
     *
     * @return  void
     */
    private function saveTagsData()
    {
        $ntags = array();
        $admintags = array();

        // save tags
        $resourcesTags = new Tags($this->record->resource->id);

        foreach ($this->record->tags as $tag) {
            if (substr($tag, 0, 6) == 'admin:') {
                $admintags[] = substr($tag, 6);
            } elseif ($tag[0] == ':') {
                $ntags[] = substr($tag, 1);
            } else {
                $admintags[] = $tag;
            }
        }

        $resourcesTags->setTags($admintags, $this->user->get('id'), 1, 1);
        $resourcesTags->setTags($ntags, $this->user->get('id'), 0, 1);
    }

    /**
     * Output object of string
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * To String object
     *
     * Removes private properties before returning
     *
     * @return  string
     */
    public function toString()
    {
        // reflect on class to get private or protected props
        $reflectionClass   = new \ReflectionClass($this);
        $privateProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE);

        // remove each private or protected prop
        foreach ($privateProperties as $prop) {
            $name = (string) $prop->name;
            unset($this->$name);
        }

        // output as json
        return json_encode($this);
    }
}

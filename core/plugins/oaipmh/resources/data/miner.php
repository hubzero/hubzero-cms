<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Oaipmh\Resources\Data;

use Hubzero\Base\Obj;
use Components\Oaipmh\Models\Provider;
use Hubzero\Facades\Component;

/**
 * Data miner for resources to be used by OAI-PMH
 */
class Miner extends Obj implements Provider
{
    /**
     * Base URL
     *
     * @var  string
     */
    protected static $base = null;

    /**
     * Database connection
     *
     * @var  object
     */
    protected $database = null;

    /**
     * Data source name
     *
     * @var  string
     */
    protected $name = 'resources';

    /**
     * Data source aliases
     *
     * @var  object
     */
    protected $provides = array(
        'resources',
        'resource'
    );

    /**
     * Constructor
     *
     * @param   object  $db
     * @return  void
     * @throws  Exception
     */
    public function __construct($db = null)
    {
        if (!$db) {
            $db = \Hubzero\Facades\App::get('db');
        }

        if (!($db instanceof \Hubzero\Database\Driver)) {
            throw new \Exception(\Hubzero\Facades\Lang::txt('Database must be of type \Hubzero\\Database\\Driver'), 500);
        }

        $this->database = $db;

        if (is_null(self::$base)) {
            self::$base = rtrim(\Hubzero\Facades\Request::getSchemeAndHttpHost(), '/');
        }
    }

    /**
     * Get Data source name
     *
     * @return  string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Does this provider handle the specified type?
     *
     * @param   string   $type
     * @return  boolean
     */
    public function provides($type)
    {
        return in_array($type, $this->provides);
    }

    /**
     * Get query for returning sets
     *
     * @return  string
     */
    public function sets()
    {
        $query = "SELECT alias, type, description, " . $this->database->quote($this->name()) . " as base
				FROM `#__resource_types`";
        if ($type = $this->get('type')) {
            $query .= " WHERE id=" . $this->database->quote($type);
        } else {
            $query .= " WHERE category=" . $this->database->quote(27);
        }

        return $query;
    }

    /**
     * Build query for retrieving records
     *
     * @param   array   $filters
     * @return  string
     */
    public function records($filters = array())
    {
        if (isset($filters['set']) && $filters['set']) {
            if (!preg_match('/^resources\:(.+)/i', $filters['set'], $matches)) {
                return '';
            }

            $set = trim($matches[1]);
            $sql = "SELECT t.id FROM `#__resource_types` AS t "
                . "WHERE t.alias=" . $this->database->quote($set);
            $this->database->setQuery($sql);
            $this->set('type', $this->database->loadResult());
            if (!$this->get('type')) {
                return '';
            }
        }

        $baseName = $this->database->quote($this->name());
        $query = "SELECT CASE WHEN v.revision THEN CONCAT(r.id, ':', v.revision) "
            . "ELSE r.id END AS id, " . $baseName . " AS `base`
            FROM `#__resources` AS r
            LEFT JOIN `#__tool_version` AS v ON v.`toolname`=r.`alias`
            WHERE r.`standalone`=1 AND r.`published`=1";
        if ($type = $this->get('type')) {
            $query .= " AND r.`type`=" . $this->database->quote($type);
        }

        if (isset($filters['from']) && $filters['from']) {
            $d = explode('-', $filters['from']);
            $filters['from'] = $d[0];
            $filters['from'] .= '-' . (isset($d[1]) ? $d[1] : '00');
            $filters['from'] .= '-' . (isset($d[2]) ? $d[2] : '00');

            $dateTimePattern = '/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/';
            if (!preg_match($dateTimePattern, $filters['from'])) {
                $filters['from'] .= ' 00:00:00';
            }
            $fromQuoted = $this->database->quote($filters['from']);
            $query .= " AND (
                (r.`publish_up` IS NOT NULL AND r.`publish_up` != '0000-00-00 00:00:00' "
                . "AND r.`publish_up` >= " . $fromQuoted . ")
                OR
                ((r.`publish_up` IS NULL OR r.`publish_up` = '0000-00-00 00:00:00') "
                . "AND r.`created` >= " . $fromQuoted . ")
            )";
        }
        if (isset($filters['until']) && $filters['until']) {
            $d = explode('-', $filters['until']);
            $filters['until'] = $d[0];
            $filters['until'] .= '-' . (isset($d[1]) ? $d[1] : '00');
            $filters['until'] .= '-' . (isset($d[2]) ? $d[2] : '00');

            $dateTimePattern = '/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/';
            if (!preg_match($dateTimePattern, $filters['until'])) {
                $filters['until'] .= ' 00:00:00';
            }
            $untilQuoted = $this->database->quote($filters['until']);
            $query .= " AND (
                (r.`publish_up` IS NOT NULL AND r.`publish_up` != '0000-00-00 00:00:00' "
                . "AND r.`publish_up` < " . $untilQuoted . ")
                OR
                ((r.`publish_up` IS NULL OR r.`publish_up` = '0000-00-00 00:00:00') "
                . "AND r.`created` < " . $untilQuoted . ")
            )";
        }

        return $query;
    }

    /**
     * Process list of records
     *
     * @param   array  $records
     * @return  array
     */
    public function postRecords($records)
    {
        foreach ($records as $i => $record) {
            if (!$this->provides($record->base)) {
                continue;
            }

            $records[$i] = $this->record($record->id);
        }

        return $records;
    }

    /**
     * Try to match a given ID as being an
     * ID of the data type.
     *
     * @param   string   $identifier
     * @return  integer
     */
    public function match($identifier)
    {
        if (preg_match('/(.*?)\/resources\/(\d+)(?:\?rev=(\d+))?/i', $identifier, $matches)) {
            return $matches[2] . (isset($matches[3]) && is_numeric($matches[3]) ? ':' . $matches[3] : '');
        }

        $resolver = $this->doiResolver();
        if (substr($identifier, 0, strlen($resolver)) == $resolver) {
            $identifier = substr($identifier, strlen($resolver));
        } else {
            if ($shoulder = Component::params('com_tools')->get('doi_shoulder')) {
                if (substr($identifier, 0, strlen($shoulder)) == $shoulder) {
                    $identifier = substr($identifier, strlen($shoulder));
                }
            }
        }
        $identifier = trim($identifier, '/');

        $this->database->setQuery(
            "SELECT a.*
			FROM `#__doi_mapping` AS a
			WHERE a.`doi`=" . $this->database->quote($identifier) . "
			LIMIT 1"
        );
        $doi = $this->database->loadObject();
        if ($doi && $doi->rid) {
            return $doi->rid . ($doi->local_revision ? ':' . $doi->local_revision : '');
        }

        return 0;
    }

    /**
     * Process a single record
     *
     * @param   integer  $id
     * @return  object
     */
    public function record($id)
    {
        if (strstr($id, ':')) {
            list($id, $revision) = explode(':', $id);
        }
        $id = intval($id);
        if (!isset($revision)) {
            $revision = 0;
        }

        $this->database->setQuery(
            "SELECT r.id, r.id AS identifier, r.title, r.introtext AS description, "
            . "r.fulltxt, r.created, r.publish_up, r.alias, rt.alias AS type
            FROM `#__resources` AS r
            INNER JOIN `#__resource_types` AS rt ON r.type = rt.id
            WHERE r.id = " . $this->database->quote($id)
        );
        $record = $this->database->loadObject();

        $record->base = $this->name();
        $record->type = $record->base . ':' . $record->type;

        if ($revision) {
            $aliasQuoted = $this->database->quote($record->alias);
            $revQuoted = $this->database->quote($revision);
            $this->database->setQuery(
                "SELECT *
                FROM `#__tool_version`
                WHERE toolname=" . $aliasQuoted . " AND revision=" . $revQuoted . "
                LIMIT 1"
            );
            $tool = $this->database->loadObject();
            if ($tool->id) {
                $record->title .= ' [version ' . $tool->version . ']';
                $record->fulltxt = $tool->fulltxt;
                $record->publish_up = $tool->released;
            }
        }

        $record->date = $record->created;
        if ($record->publish_up && $record->publish_up != $this->database->getNullDate()) {
            $record->date = $record->publish_up;
        }

        if (!$record->description) {
            $record->description = $record->fulltxt;
            $record->description = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $record->description);
        }
        $record->description = strip_tags($record->description);
        $record->description = trim($record->description);

        unset($record->publish_up);
        unset($record->created);
        unset($record->fulltxt);

        $isTool = 0;

        if ($record->alias) {
            $this->database->setQuery(
                "SELECT id
				FROM `#__tool`
				WHERE toolname=" . $this->database->quote($record->alias) . "
				LIMIT 1"
            );
            $isTool = $this->database->loadResult();
        }

        if ($revision) {
            /*"SELECT a.`doi`
                FROM `#__doi_mapping` AS a
                LEFT JOIN `#__tool_version` AS v ON v.id=a.versionid
                WHERE a.rid=" . $this->database->quote($id) . " AND v.revision=" . $this->database->quote($revision) . "
                LIMIT 1"*/
            $idQuoted = $this->database->quote($id);
            $revQuoted = $this->database->quote($revision);
            $this->database->setQuery(
                "SELECT a.*
                FROM `#__doi_mapping` AS a
                WHERE a.rid=" . $idQuoted . " AND a.local_revision=" . $revQuoted . "
                LIMIT 1"
            );
        } else {
            $idQuoted = $this->database->quote($id);
            $this->database->setQuery(
                "SELECT a.*
                FROM `#__doi_mapping` AS a
                WHERE a.rid=" . $idQuoted . "
                ORDER BY `versionid` DESC LIMIT 1"
            );
        }
        $record->identifier = $this->identifier($id, $this->database->loadObject(), $revision);

        $idQuoted = $this->database->quote($id);
        $this->database->setQuery(
            "SELECT DISTINCT t.raw_tag
            FROM `#__tags` t, `#__tags_object` tos
            WHERE t.id = tos.tagid AND tos.objectid=" . $idQuoted . " "
            . "AND tos.tbl='resources' AND t.admin=0
            ORDER BY t.raw_tag"
        );
        $record->subject = $this->database->loadColumn();

        $record->relation = array();

        $this->database->setQuery(
            "SELECT r.id, r.title, r.type, r.logical_type AS logicaltype, r.created, r.created_by,
			r.published, r.publish_up, r.path, r.access, t.type AS logicaltitle, rt.type AS typetitle, r.standalone
			FROM `#__resources` AS r
			INNER JOIN `#__resource_types` AS rt ON r.type=rt.id
			INNER JOIN `#__resource_assoc` AS a ON r.id=a.child_id
			LEFT JOIN `#__resource_types` AS t ON r.logical_type=t.id
			WHERE r.published=1 AND a.parent_id=" . $this->database->quote($id) . "
			ORDER BY a.ordering, a.grouping"
        );
        if ($children = $this->database->loadObjectList()) {
            foreach ($children as $child) {
                $child->type = \Components\Resources\Models\Type::oneOrNew($child->type);

                $uri = \Components\Resources\Helpers\Html::processPath('com_resources', $child, $id, 3);
                if (substr($uri, 0, 4) != 'http') {
                    $uri = self::$base . '/' . ltrim($uri, '/');
                }

                $record->relation[] = array(
                    'type'  => 'hasPart',
                    'value' => $uri
                );
            }
        }

        $this->database->setQuery(
            "SELECT DISTINCT a.parent_id
			FROM `#__resources` AS r
			INNER JOIN `#__resource_assoc` AS a ON r.id=a.parent_id
			WHERE r.published=1 AND a.child_id=" . $this->database->quote($id) . "
			ORDER BY a.parent_id"
        );
        if ($parents = $this->database->loadObjectList()) {
            foreach ($parents as $parent) {
                $record->relation[] = array(
                    'type'  => 'isPartOf',
                    'value' => $this->identifier($parent->parent_id, 0)
                );
            }
        }

        if ($isTool) {
            if ($revision) {
                $aliasQuoted = $this->database->quote($record->alias);
                $revQuoted = $this->database->quote($revision);
                $this->database->setQuery(
                    "SELECT
                        CASE WHEN t.name!='' AND t.name IS NOT NULL THEN t.name
                        ELSE n.name
                        END AS `name`
                    FROM `#__tool_authors` AS t, `#__xprofiles` AS n, `#__tool_version` AS v
                    WHERE n.uidNumber=t.uid AND t.toolname=" . $aliasQuoted . " "
                    . "AND v.id=t.version_id and v.state<>3
                    AND t.revision=" . $revQuoted . "
                    ORDER BY t.ordering"
                );
                $record->creator = $this->database->loadColumn();

                /*$record->relation[] = array(
                    'type'  => 'isVersionOf',
                    'value' => $this->identifier($id, '', 0)
                );*/
            }

            $this->database->setQuery(
                "SELECT v.id, v.revision, d.*
				FROM `#__tool_version` as v
				LEFT JOIN `#__doi_mapping` as d
				ON d.alias = v.toolname
				AND d.local_revision=v.revision
				WHERE v.toolname = " . $this->database->quote($record->alias) . "
				AND v.state!=3
				ORDER BY v.state DESC, v.revision DESC"
            );
            $versions = $this->database->loadObjectList();
            foreach ($versions as $i => $v) {
                if (!$v->revision || $v->revision == $revision) {
                    continue;
                }

                $record->relation[] = array(
                    'type'  => 'hasVersion',
                    'value' => $this->identifier($id, $v, $v->revision)
                );
            }
        }

        if (!isset($record->creator)) {
            $this->database->setQuery(
                "SELECT 
					CASE WHEN a.name!='' AND a.name IS NOT NULL THEN a.name
					ELSE n.name
					END AS `name`
				FROM `#__author_assoc` AS a
				LEFT JOIN `#__xprofiles` AS n ON n.uidNumber=a.authorid
				WHERE a.subtable='resources' AND a.subid=" . $this->database->quote($id) . " AND a.role!='submitter'
				ORDER BY a.ordering, a.name"
            );
            $record->creator = $this->database->loadColumn();
        }

        if ($this->get('citations', 1)) {
            $formatterPath = Component::path('com_citations') . DS . 'helpers' . DS . 'format.php';

            $idQuoted = $this->database->quote($id);
            $this->database->setQuery(
                "SELECT *
                FROM `#__citations` AS a
                INNER JOIN `#__citations_assoc` AS n ON n.`cid`=a.`id`
                WHERE n.`tbl`='resource' AND n.`oid`=" . $idQuoted . " "
                . "AND n.`type`!='references' AND a.`published`=1
                ORDER BY `year` DESC"
            );
            $references = $this->database->loadObjectList();
            if (count($references) && file_exists($formatterPath)) {
                $formatter = new \Components\Citations\Helpers\Format();
                $formatter->setTemplate('apa');

                foreach ($references as $reference) {
                    // <dcterms:isReferencedBy>uytruytry</dcterms:isReferencedBy>
                    $formatted = $reference->formatted
                        ? $reference->formatted
                        : \Components\Citations\Helpers\Format::formatReference($reference, '');
                    $cite = strip_tags(html_entity_decode($formatted));
                    $cite = str_replace('&quot;', '"', $cite);

                    $record->relation[] = array(
                        'type'  => 'isReferencedBy',
                        'value' => trim($cite)
                    );
                }
            }

            $this->database->setQuery(
                "SELECT *
                FROM `#__citations` AS a
                INNER JOIN `#__citations_assoc` AS n ON n.`cid`=a.`id`
                WHERE n.`tbl`='resource' AND n.`oid`=" . $idQuoted . " "
                . "AND n.`type`='references' AND a.`published`=1
                ORDER BY `year` DESC"
            );
            $references = $this->database->loadObjectList();
            if (count($references) && file_exists($formatterPath)) {
                $formatter = new \Components\Citations\Helpers\Format();
                $formatter->setTemplate('apa');

                foreach ($references as $reference) {
                    // <dcterms:references>uytruytry</dcterms:references>
                    $formatted = $reference->formatted
                        ? $reference->formatted
                        : \Components\Citations\Helpers\Format::formatReference($reference, '');
                    $cite = strip_tags(html_entity_decode($formatted));
                    $cite = str_replace('&quot;', '"', $cite);

                    $record->relation[] = array(
                        'type'  => 'references',
                        'value' => trim($cite)
                    );
                }
            }
        }

        return $record;
    }

    /**
     * Build the identifier URI for a resource
     *
     * @param   integer  $id
     * @param   string   $doi
     * @param   integer  $rev
     * @return  string
     */
    protected function identifier($id, $doi, $rev = 0)
    {
        if ($doi) {
            if (is_object($doi)) {
                $doiResolve = Component::params('com_tools')->get('doi_resolve', 'https://doi.org/');
                $identifier = rtrim($doiResolve, '/') . '/';
                $shoulder = $doi->doi_shoulder
                    ? $doi->doi_shoulder
                    : Component::params('com_tools')->get('doi_shoulder');
                $identifier .= $shoulder . '/';
                $identifier .= $doi->doi;
            } else {
                $identifier = $this->doiResolver() . $doi;
            }
        } else {
            $revPart = $rev ? '&rev=' . $rev : '';
            $route = \Hubzero\Facades\Route::url('index.php?option=com_resources&id=' . $id . $revPart);
            $identifier = self::$base . '/' . ltrim($route, '/');
        }

        return $identifier;
    }

    /**
     * Get the DOI resolver
     *
     * @return  string
     */
    protected function doiResolver()
    {
        static $resolver;

        if (!$resolver) {
            $resolver = Component::params('com_tools')->get('doi_resolve', 'https://doi.org/');
            $resolver = rtrim($resolver, '/') . '/';
            if ($shoulder = Component::params('com_tools')->get('doi_shoulder')) {
                $resolver .= $shoulder . '/';
            }
        }

        return $resolver;
    }
}

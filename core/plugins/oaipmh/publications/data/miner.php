<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Oaipmh\Publications\Data;

use Hubzero\Base\Obj;
use Components\Oaipmh\Models\Provider;

/**
 * Data miner for publications to be used by OAI-PMH
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
    protected $name = 'publications';

    /**
     * Data source aliases
     *
     * @var  array
     */
    protected $provides = array(
        'publications',
        'publication'
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
            throw new \Exception(\Hubzero\Facades\Lang::txt('Database must be of type \Hubzero\Database\Driver'), 500);
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
        $query = "SELECT alias, name AS type, description, " . $this->database->quote($this->name()) . " as base
				FROM `#__publication_categories`";
        if ($type = $this->get('type')) {
            $query .= " WHERE id=" . $this->database->quote($type);
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
            if (!preg_match('/^publications\:(.+)/i', $filters['set'], $matches)) {
                return '';
            }

            $set = trim($matches[1]);
            $setQuoted = $this->database->quote($set);
            $this->database->setQuery(
                "SELECT t.id FROM `#__publication_categories` AS t WHERE t.alias=" . $setQuoted
            );
            $this->set('type', $this->database->loadResult());
            if (!$this->get('type')) {
                return '';
            }
        }

        $nameQuoted = $this->database->quote($this->name());
        $query = "SELECT CONCAT(p.id, ':', pv.version_number) AS id, " . $nameQuoted . " AS `base`
				FROM `#__publications` AS p
				INNER JOIN `#__publication_versions` AS pv ON pv.publication_id = p.id
				WHERE pv.state=1";

        if ($type = $this->get('type')) {
            $query .= " AND p.category=" . $this->database->quote($type);
        }

        $datePattern = "/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/";
        $dateCase = "CASE WHEN pv.`accepted` IS NOT NULL AND pv.`accepted` != '0000-00-00 00:00:00' "
            . "THEN pv.`accepted` ELSE pv.`published_up` END";

        if (isset($filters['from']) && $filters['from']) {
            $d = explode('-', $filters['from']);
            $filters['from'] = $d[0];
            $filters['from'] .= '-' . (isset($d[1]) ? $d[1] : '00');
            $filters['from'] .= '-' . (isset($d[2]) ? $d[2] : '00');

            if (!preg_match($datePattern, $filters['from'])) {
                $filters['from'] .= ' 00:00:00';
            }

            $fromQuoted = $this->database->quote($filters['from']);
            $query .= " AND ((" . $dateCase . ") >= " . $fromQuoted . ")";
        }
        if (isset($filters['until']) && $filters['until']) {
            $d = explode('-', $filters['until']);
            $filters['until'] = $d[0];
            $filters['until'] .= '-' . (isset($d[1]) ? $d[1] : '00');
            $filters['until'] .= '-' . (isset($d[2]) ? $d[2] : '00');

            if (!preg_match($datePattern, $filters['until'])) {
                $filters['until'] .= ' 00:00:00';
            }

            $untilQuoted = $this->database->quote($filters['until']);
            $query .= " AND ((" . $dateCase . ") < " . $untilQuoted . ")";
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
        if (preg_match('/(.*?)\/publications\/(\d+)(?:\/(\d+))?/i', $identifier, $matches)) {
            return $matches[2] . (isset($matches[3]) && is_numeric($matches[3]) ? ':' . $matches[3] : '');
        }

        $resolver = $this->doiResolver();
        if (substr($identifier, 0, strlen($resolver)) == $resolver) {
            $identifier = substr($identifier, strlen($resolver));
        }

        $this->database->setQuery(
            "SELECT pv.`publication_id`, pv.`version_number`
			FROM `#__publication_versions` AS pv
			WHERE pv.`doi`=" . $this->database->quote($identifier) . "
			LIMIT 1"
        );
        $doi = $this->database->loadObject();
        if ($doi && $doi->publication_id) {
            return $doi->publication_id . ($doi->version_number ? ':' . $doi->version_number : '');
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

        $idQuoted = $this->database->quote($id);
        $revisionWhere = $revision
            ? " AND pv.version_number=" . $this->database->quote($revision)
            : "";
        $this->database->setQuery(
            "SELECT pv.*, pv.doi AS identifier, rt.alias AS type
			FROM `#__publication_versions` AS pv
			INNER JOIN `#__publications` AS p ON p.id = pv.publication_id
			INNER JOIN `#__publication_categories` AS rt ON rt.id = p.category
			WHERE p.id = " . $idQuoted . $revisionWhere
        );
        $record = $this->database->loadObject();
        $record->version_id = $record->id;
        $record->id = $id;

        $record->base = $this->name();
        $record->type = $record->base . ':' . $record->type;

        $record->title .= ' [version ' . $record->version_label . ']';
        $abstract = trim(strip_tags($record->abstract));
        $record->description = $abstract;

        // Size and MIME type
        $record->format = [];

        $pubHelper = \Hubzero\Facades\Component::path('com_publications') . DS . 'helpers' . DS . 'html.php';
        if (file_exists($pubHelper)) {
            $pubIdQuoted = $this->database->quote($record->publication_id);
            $this->database->setQuery(
                "SELECT master_type FROM `#__publications` WHERE id = " . $pubIdQuoted
            );
            $masterType = $this->database->loadResult();

            $versionIdQuoted = $this->database->quote($record->version_id);
            $this->database->setQuery(
                "SELECT * FROM `#__publication_attachments` WHERE publication_version_id = " . $versionIdQuoted
            );
            $results = $this->database->loadObjectList();

            $path = \Components\Publications\Helpers\Html::buildPubPath(
                $record->publication_id,
                $record->version_id,
                '',
                '',
                1
            );
            $path .= DIRECTORY_SEPARATOR . str_replace(['.', '/'], '_', $record->identifier) . ".zip";

            if ($masterType == 1 && file_exists($path)) {
                $size = filesize($path);
                if ($size) {
                    $record->extent = \Hubzero\Utility\Number::formatBytes($size);
                }
            }

            if ($results) {
                $attachments = array(
                    '1'        => array(),
                    '2'        => array(),
                    '3'        => array(),
                );

                foreach ($results as $result) {
                    if ($result->role == 1) {
                        $attachments['1'][] = $result;
                    } elseif ($result->role == 2 || $result->role == 0) {
                        $attachments['2'][] = $result;
                    } else {
                        $attachments['3'][] = $result;
                    }
                }
            }

            if ($masterType == 1) {
                $mimeTypes = \Components\Publications\Helpers\Html::getMimeTypes(
                    $masterType,
                    $record->publication_id,
                    $record->version_id,
                    $record->secret,
                    $attachments,
                    true
                );
            } elseif ($masterType == 7) {
                $files = \Components\Publications\Helpers\Html::getdatabaseFiles(
                    $record->publication_id,
                    $record->version_id
                );

                $mimeTypes = \Components\Publications\Helpers\Html::getMimeTypes(
                    $masterType,
                    $record->publication_id,
                    $record->version_id,
                    $record->secret,
                    $attachments,
                    true
                );

                if ($files != false && !empty($files)) {
                    foreach ($files as $file) {
                        //$mimeType = mime_content_type($file);
                        $mimer = new  \Hubzero\Content\Mimetypes();
                        $mimeType = $mimer->getMimeType($file, true);
                        if ($mimeType && !in_array($mimeType, $mimeTypes)) {
                            $mimeTypes[] = $mimeType;
                        }
                    }
                }
            } else {
                $mimeTypes = [];
            }

            if (!empty($mimeTypes)) {
                $record->format = array_merge($record->format, $mimeTypes);
            }
        }

        $record->identifier  = $this->identifier($id, $record->identifier, $revision);

        $this->database->setQuery(
            "SELECT pv.created, pv.submitted, pv.published_up, pv.accepted
			FROM `#__publication_versions` AS pv
			WHERE pv.id = " . $this->database->quote($record->version_id) . "
			ORDER BY pv.submitted DESC LIMIT 1"
        );
        $dates = $this->database->loadObject();
        $record->date = $dates->created;
        if ($dates->submitted && $dates->submitted != '0000-00-00 00:00:00') {
            $record->date = $dates->submitted;
        }
        if ($dates->accepted && $dates->accepted != '0000-00-00 00:00:00') {
            $record->date = $dates->accepted;
        }
        $nullDate = '0000-00-00 00:00:00';
        if ($dates->published_up && $dates->published_up != $nullDate && $dates->published_up > $record->date) {
            $record->date = $dates->published_up;
        }

        $versionIdQuoted = $this->database->quote($record->version_id);
        $this->database->setQuery(
            "SELECT pa.name, pa.user_id
			FROM `#__publication_authors` AS pa
			WHERE (pa.role IS NULL OR pa.role != 'submitter') AND pa.status=1
			AND pa.publication_version_id=" . $versionIdQuoted . "
			ORDER BY pa.name"
        );
        $creators = $this->database->loadObjectList();
        foreach ($creators as $creator) {
            $userIdQuoted = $this->database->quote($creator->user_id);
            $this->database->setQuery(
                "SELECT profile_value FROM `#__user_profiles` "
                . "WHERE user_id = " . $userIdQuoted . " AND profile_key='orcid'"
            );
            $orcid = $this->database->loadResult();
            $record->creator[] = $creator->name . (!empty($orcid) ? ", " . $orcid : "");
        }

        $this->database->setQuery(
            "SELECT DISTINCT t.raw_tag
			FROM `#__tags` t, `#__tags_object` tos
			WHERE t.id = tos.tagid AND tos.objectid=" . $versionIdQuoted . "
			AND tos.tbl='publications' AND t.admin=0
			ORDER BY t.raw_tag"
        );
        $record->subject = $this->database->loadColumn();

        // Relations
        $record->relation = array();

        $this->database->setQuery(
            "SELECT v.id, v.publication_id, v.version_number, v.doi
			FROM `#__publication_versions` as v
			WHERE v.state=1 AND v.publication_id = " . $this->database->quote($record->id) . "
			ORDER BY v.version_number DESC"
        );
        $versions = $this->database->loadObjectList();
        foreach ($versions as $i => $v) {
            if (!$v->version_number || $v->version_number == $revision) {
                continue;
            }

            $record->relation[] = array(
                'type'  => 'hasVersion',
                'value' => $this->identifier($id, $v->doi, $v->version_number)
            );
        }

        $idQuoted = $this->database->quote($id);
        $this->database->setQuery(
            "SELECT *
			FROM `#__citations` AS a
			LEFT JOIN `#__citations_assoc` AS n ON n.`cid`=a.`id`
			WHERE n.`tbl`='publication' AND n.`oid`=" . $idQuoted . "
			AND n.`type`='owner' AND a.`published`=1
			ORDER BY `year` DESC"
        );
        $references = $this->database->loadObjectList();
        $citationHelper = \Hubzero\Facades\Component::path('com_citations') . DS . 'helpers' . DS . 'format.php';
        if (count($references) && file_exists($citationHelper)) {
            $formatter = new \Components\Citations\Helpers\Format();
            $formatter->setTemplate('apa');

            foreach ($references as $reference) {
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
			LEFT JOIN `#__citations_assoc` AS n ON n.`cid`=a.`id`
			WHERE n.`tbl`='publication' AND n.`oid`=" . $idQuoted . "
			AND n.`type`!='owner' AND a.`published`=1
			ORDER BY `year` DESC"
        );
        $references = $this->database->loadObjectList();
        if (count($references) && file_exists($citationHelper)) {
            $formatter = new \Components\Citations\Helpers\Format();
            $formatter->setTemplate('apa');

            foreach ($references as $reference) {
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
            $identifier = $this->doiResolver() . $doi;
        } else {
            $revPart = $rev ? '&v=' . $rev : '';
            $route = \Hubzero\Facades\Route::url('index.php?option=com_publications&pid=' . $id . $revPart);
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
            $resolver = \Hubzero\Facades\Component::params('com_publications')->get('doi_resolve', 'https://doi.org/');
            $resolver = rtrim($resolver, '/') . '/';
        }

        return $resolver;
    }
}

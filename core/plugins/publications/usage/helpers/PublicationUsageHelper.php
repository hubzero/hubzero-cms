<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Publications\Usage\Helpers;

use Hubzero\Facades\App;

class PublicationUsageHelper
{
    /**
     * Database connection
     *
     * @var \Hubzero\Database\Driver
     */
    protected $db;

    /**
     * Publication
     *
     * @var \Components\Publications\Models\Publication
     */
    protected $publication;

    /**
     * Log table
     *
     * @var string
     */
    protected $logsTable = '#__publication_logs';

    /**
     * Version ID
     *
     * @var string|null  The id, quoted for SQL
     */
    protected $versionId = null;

    /**
     * Publication ID
     *
     * @var string|null  The id, quoted for SQL
     */
    protected $publicationId = null;

    /**
     * Instantiates PublicationUsageHelper
     *
     * @param    array<mixed>   $args   Instantiation data
     * @return   void
     */
    public function __construct($args = [])
    {
        $this->db = App::get('db');
        $this->publication = $args['publication'];
    }

    /**
     * Calculates publications total views
     *
     * @return   int
     */
    public function totalViews()
    {
        $totalViewsQuery = $this->generateColumnSumQuery('page_views');

        return (int) $this->runQuery($totalViewsQuery);
    }

    /**
     * Calculates publications total downloads
     *
     * @return   int
     */
    public function totalDownloads()
    {
        $totalDownloadsQuery = $this->generateColumnSumQuery('primary_accesses');

        return (int) $this->runQuery($totalDownloadsQuery);
    }

    /**
     * Generates query to sum given column
     *
     * @param    string   $column   Name of column to sum
     * @return   string
     */
    protected function generateColumnSumQuery($column)
    {
        $publicationId = $this->getPublicationId();
        $versionId = $this->getVersionId();

        $sumQuery = "SELECT SUM($column)
			FROM `$this->logsTable`
			WHERE `publication_id`=$publicationId AND `publication_version_id`=$versionId
			ORDER BY `year` ASC, `month` ASC";

        return $sumQuery;
    }

    /**
     * Executes given query
     *
     * @param    string   $query   Query to execute
     * @return   mixed
     */
    protected function runQuery($query)
    {
        $this->db->setQuery($query);

        return $this->db->loadResult();
    }

    /**
     * Gets escaped publication's ID
     *
     * @return   string  The id, quoted for SQL
     */
    protected function getPublicationId()
    {
        if (!isset($this->publicationId)) {
            $publicationId = $this->publication->id;
            $this->publicationId = $this->db->quote($publicationId);
        }

        return $this->publicationId;
    }

    /**
     * Gets escaped publication's version's ID
     *
     * @return   string  The id, quoted for SQL
     */
    protected function getVersionId()
    {
        if (!isset($this->versionId)) {
            $versionId = $this->publication->version->id;
            $this->versionId = $this->db->quote($versionId);
        }

        return $this->versionId;
    }
}

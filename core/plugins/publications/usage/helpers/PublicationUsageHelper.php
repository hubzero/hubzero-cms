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
     * @var object
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $db;

    /**
     * Publication
     *
     * @var object
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $publication;

    /**
     * Log table
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $logsTable = '#__publication_logs';

    /**
     * Version ID
     *
     * @var int
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $versionId;

    /**
     * Publication ID
     *
     * @var int
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $publicationId;

    /**
     * Instantiates PublicationUsageHelper
     *
     * @param    array   $args   Instantiation data
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
        $totalViewsQuery = $this->_generateColumnSumQuery('page_views');

        return (int) $this->_runQuery($totalViewsQuery);
    }

    /**
     * Calculates publications total downloads
     *
     * @return   int
     */
    public function totalDownloads()
    {
        $totalDownloadsQuery = $this->_generateColumnSumQuery('primary_accesses');

        return (int) $this->_runQuery($totalDownloadsQuery);
    }

    /**
     * Generates query to sum given column
     *
     * @param    string   $column   Name of column to sum
     * @return   string
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _generateColumnSumQuery($column)
    {
        $publicationId = $this->_getPublicationId();
        $versionId = $this->_getVersionId();

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
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _runQuery($query)
    {
        $this->db->setQuery($query);

        return $this->db->loadResult();
    }

    /**
     * Gets escaped publication's ID
     *
     * @return   int
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getPublicationId()
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
     * @return   int
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getVersionId()
    {
        if (!isset($this->versionId)) {
            $versionId = $this->publication->version->id;
            $this->versionId = $this->db->quote($versionId);
        }

        return $this->versionId;
    }
}

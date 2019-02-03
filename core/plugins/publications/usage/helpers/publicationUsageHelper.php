<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

class PublicationUsageHelper
{

	protected $_logsTable = '#__publication_logs';

	/**
	 * Instantiates PublicationUsageHelper
	 *
	 * @param    array   $args   Instantiation data
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_db = App::get('db');
		$this->_publication = $args['publication'];
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
	protected function _generateColumnSumQuery($column)
	{
		$publicationId = $this->_getPublicationId();
		$versionId = $this->_getVersionId();

		$sumQuery = "SELECT SUM($column)
			FROM `$this->_logsTable`
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
	protected function _runQuery($query)
	{
		$this->_db->setQuery($query);

		return $this->_db->loadResult();
	}

	/**
	 * Gets escaped publication's ID
	 *
	 * @return   int
	 */
	protected function _getPublicationId()
	{
		if (!isset($this->_publicationId))
		{
			$publicationId = $this->_publication->id;
			$this->_publicationId = $this->_db->quote($publicationId);
		}

		return $this->_publicationId;
	}

	/**
	 * Gets escaped publication's version's ID
	 *
	 * @return   int
	 */
	protected function _getVersionId()
	{
		if (!isset($this->_versionId))
		{
			$versionId = $this->_publication->version->id;
			$this->_versionId = $this->_db->quote($versionId);
		}

		return $this->_versionId;
	}

}

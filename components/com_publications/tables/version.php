<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Publications\Tables;

/**
 * Table class for publication version
 */
class Version extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	function __construct( &$db )
	{
		parent::__construct( '#__publication_versions', 'id', $db );
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      integer  $pid 		Pub ID
	 * @param      string   $version 	Version number or name
	 * @return     mixed False if error, Object on success
	 */
	public function loadVersion ( $pid = NULL, $version = 'dev' )
	{
		if ($pid === NULL)
		{
			$pid = $this->publication_id;
		}
		if ($pid === NULL )
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE publication_id=" . $this->_db->Quote($pid);
		if ($version == 'default' or $version == 'current' && $version == 'main')
		{
			$query.= " AND main=1 ";
		}
		elseif ($version == 'dev')
		{
			$query.= " AND state=3 ";
		}
		elseif (is_numeric($version))
		{
			$query.= " AND version_number=" . $this->_db->Quote($version);
		}
		else
		{
			// Error in supplied version value
			$query.= " AND 1=2 ";
		}
		$query .= " LIMIT 1";

		$this->_db->setQuery( $query );

		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind( $result );
		}
		else
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Update record
	 *
	 * @param      integer  $pid 		Pub ID
	 * @param      string   $update		Column name
	 * @param      string   $new 		Update to
	 * @param      string   $where 		Where
	 * @param      string   $version 	Version number or name
	 * @return     void
	 */
	public function updateVersion ($pid = NULL, $update = '', $new = '', $where = '', $version = 'dev')
	{
		if ($pid === NULL)
		{
			$pid = $this->publication_id;
		}
		if ($pid === NULL )
		{
			return false;
		}
		if (!$update or !$new )
		{
			return false;
		}

		$query  = "UPDATE $this->_tbl SET $update = '".$new."' WHERE publication_id=" . $this->_db->Quote($pid);
		$query .= $where ? " AND ".$where : "";
		if ($version == 'default' or $version == 'current' && $version == 'main')
		{
			$query.= " AND main=1 ";
		}
		elseif ($version == 'dev')
		{
			$query.= " AND state=3 ";
		}
		elseif (is_numeric($version))
		{
			$query.= " AND version_number=" . $this->_db->Quote($version);
		}
		else
		{
			// Error in supplied version value
			$query.= " AND 1=2 ";
		}
		$query .= "LIMIT 1";
		$this->_db->setQuery( $query );
		$this->_db->query();
	}

	/**
	 * Get used labels
	 *
	 * @param      integer  $pid 		Pub ID
	 * @param      string   $exclude 	Version number or name
	 * @return     array
	 */
	public function getUsedLabels ($pid = NULL, $exclude = 'dev' )
	{
		if ($pid === NULL)
		{
			$pid = $this->publication_id;
		}
		if ($pid === NULL )
		{
			return false;
		}
		$labels = array();

		$query  = "SELECT version_label FROM $this->_tbl WHERE publication_id=" . $this->_db->Quote($pid);

		if ($exclude == 'default' or $exclude == 'current' && $exclude == 'main')
		{
			$query.= " AND main!=1 ";
		}
		elseif ($exclude == 'dev')
		{
			$query.= " AND state!=3 ";
		}
		elseif (is_numeric($exclude))
		{
			$query.= " AND version_number!=" . $this->_db->Quote($exclude);
		}

		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();

		if ($result)
		{
			foreach ($result as $r)
			{
				$labels[] = $r->version_label;
			}
		}

		return $labels;
	}

	/**
	 * Get attribute
	 *
	 * @param      integer  $pid 		Pub ID
	 * @param      string   $version 	Version number or name
	 * @param      string   $select		Select query
	 * @return     string
	 */
	public function getAttribute ($pid = NULL, $version = 'dev', $select = '' )
	{
		if ($pid === NULL)
		{
			$pid = $this->publication_id;
		}
		if ($pid === NULL )
		{
			return false;
		}
		if (!$select)
		{
			return false;
		}

		$query  = "SELECT $select FROM $this->_tbl WHERE publication_id=" . $this->_db->Quote($pid);

		if ($version == 'default' or $version == 'current' && $version == 'main')
		{
			$query.= " AND main=1 ";
		}
		elseif ($version == 'dev')
		{
			$query.= " AND state=3 ";
		}
		elseif (is_numeric($version))
		{
			$query.= " AND version_number=" . $this->_db->Quote($version);
		}
		else
		{
			// Error in supplied version value
			$query.= " AND 1=2 ";
		}
		$query .= " LIMIT 1";

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param      integer  $pid 		Pub ID
	 * @param      array   $filters 	Query filters
	 * @return     object
	 */
	public function getVersions ( $pid, $filters = array() )
	{
		if ($pid === NULL)
		{
			$pid = $this->publication_id;
		}
		if ($pid === NULL )
		{
			return false;
		}

		$withdev = isset($filters['withdev']) && $filters['withdev'] == 1 ? 1 : 0;
		$sortby  = isset($filters['sortby']) && $filters['sortby'] != '' ? $filters['sortby'] : 'v.version_number DESC';
		$public  = isset($filters['public']) && $filters['public'] == 1 ? 1 : 0;

		$query = "SELECT v.*, p.alias, p.ranking as parent_ranking, p.rating as parent_rating, ";
		$query.= " p.checked_out, p.checked_out_time, p.access as parent_access, p.project_id ";
		$query.= " FROM $this->_tbl AS v ";
		$query.= " JOIN #__publications AS p ON p.id=v.publication_id ";
		$query.= " WHERE publication_id=" . $this->_db->Quote($pid);
		$query.= $withdev ? "" : " AND v.state!=3 ";
		$query.= $public ? " AND (v.state = 1 OR v.state = 0) AND v.access <= 1 " : "";
		$query.= " ORDER BY " . $sortby;

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Check version
	 *
	 * @param      integer  $pid 		Pub ID
	 * @param      string   $version 	Version number or name
	 * @return     boolean
	 */
	public function checkVersion( $pid = NULL, $version = NULL )
	{
		if ($pid === NULL )
		{
			return false;
		}
		if ($version == 'default')
		{
			return true;
		}
		if ($version == 'dev')
		{
			$query  = "SELECT id FROM $this->_tbl WHERE publication_id=" . $this->_db->Quote($pid) . " AND state = 3 LIMIT 1 ";
			$this->_db->setQuery( $query );
			$result = $this->_db->loadResult();
			return $result ? true : false;
		}
		if (is_numeric($version))
		{
			$query  = "SELECT id FROM $this->_tbl WHERE publication_id=" . $this->_db->Quote($pid) . " AND version_number=" . $this->_db->Quote($version) . " LIMIT 1 ";
			$this->_db->setQuery( $query );
			$result = $this->_db->loadResult();
			return $result ? true : false;
		}

		return false;
	}

	/**
	 * Get pub id
	 *
	 * @param      integer  $vid 		Pub version ID
	 * @return     integer
	 */
	public function getPubId( $vid = NULL )
	{
		if ($vid === NULL )
		{
			return false;
		}
		$query  = "SELECT publication_id FROM $this->_tbl WHERE id=" . $this->_db->Quote($vid) . " LIMIT 1 ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Get ID of main version
	 *
	 * @param      integer  $pid 		Pub ID
	 * @return     integer
	 */
	public function getMainVersionId( $pid = NULL )
	{
		if ($pid === NULL )
		{
			return false;
		}
		$query  = "SELECT id FROM $this->_tbl WHERE publication_id=" . $this->_db->Quote($pid) . " AND main = 1 LIMIT 1 ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Get published version number
	 *
	 * @param      integer  $pid 		Pub ID
	 * @return     integer
	 */
	public function getPublishedVersionNumber( $pid = NULL )
	{
		if ($pid === NULL )
		{
			return false;
		}
		$query  = "SELECT version_number FROM $this->_tbl WHERE publication_id=" . $this->_db->Quote($pid) . " AND state = 1 ORDER BY version_number DESC LIMIT 1 ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Get published version count
	 *
	 * @param      integer  $pid 		Pub ID
	 * @return     integer
	 */
	public function getPublishedCount( $pid = NULL )
	{
		if ($pid === NULL )
		{
			return false;
		}
		$query  = "SELECT COUNT(*) FROM $this->_tbl WHERE publication_id=" . $this->_db->Quote($pid) . " AND state = 1 ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Get version count
	 *
	 * @param      integer  $pid 		Pub ID
	 * @return     integer
	 */
	public function getCount( $pid = NULL )
	{
		if ($pid === NULL )
		{
			return false;
		}
		$query  = "SELECT COUNT(*) FROM $this->_tbl WHERE publication_id=" . $this->_db->Quote($pid);
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Get last public release
	 *
	 * @param      integer  $pid 		Pub ID
	 * @return     object
	 */
	public function getLastPubRelease( $pid = NULL )
	{
		if ($pid === NULL )
		{
			return false;
		}
		$query  = "SELECT * FROM $this->_tbl WHERE publication_id=" . $this->_db->Quote($pid) . " AND state = 1 ORDER BY version_number DESC LIMIT 1 ";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}

	/**
	 * Remove main flag
	 *
	 * @param      integer   $vid 		Pub version ID
	 * @param      boolean   $unpublish
	 * @return     void
	 */
	public function removeMainFlag( $vid = NULL, $unpublish = 0 )
	{
		if ($vid === NULL || !is_numeric($vid) || intval($vid) == 0)
		{
			return false;
		}
		$query  = "UPDATE $this->_tbl SET main = 0 ";
		$query .= $unpublish ? ", state = 0 " : "";
		$query .= "WHERE id=" . $this->_db->Quote($vid) . " AND main = 1 LIMIT 1 ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Get title for new draft
	 *
	 * @param      integer   $projectid  Project ID
	 * @param      string    $title
	 * @return     void
	 */
	public function getDefaultTitle( $projectid = 0, $title = '' )
	{
		if (!is_numeric($projectid) || intval($projectid) == 0)
		{
			return false;
		}
		$query  = "SELECT COUNT(*)";
		$query.= " FROM $this->_tbl AS v ";
		$query.= " JOIN #__publications AS p ON p.id=v.publication_id ";
		$query.= " WHERE v.main=1 AND v.title LIKE '" . $title . "%' AND p.project_id = " . $this->_db->Quote($projectid). " AND v.state != 2";

		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();
		$result = $result ? ($result + 1) : 1;

		$title .= ' (' . $result . ')';

		return $title;
	}

	/**
	 * Create new version
	 *
	 * @param      object   $dev 				Version to duplicate
	 * @param      integer  $state				New version status
	 * @param      string   $secret 			New version secret
	 * @param      integer  $version_number 	New version number
	 * @param      integer   $main				Default?
	 * @return     integer or False
	 */
	public function createNewVersion( $dev, $state = 1, $secret = '', $version_number = 1, $main = 1 )
	{
		$new =  new self( $this->_db );
		$new = $dev;
		$new->id = 0;
		$new->rating = '0.0';
		$new->state = $state;
		$new->secret = $secret ? $secret : strtolower(\Components\Projects\Helpers\Html::generateCode(10, 10, 0, 1, 1));
		$new->version_number = $version_number;
		$new->main = $main;

		if ($new->store())
		{
			return $new->id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get record
	 *
	 * @param      integer  $pid 		Pub ID
	 * @param      string   $version 	Version number or name
	 * @return     object or False
	 */
	public function getVersion ( $pid, $version = 'dev' )
	{
		if ($pid === NULL)
		{
			$pid = $this->publication_id;
		}
		if ($pid === NULL )
		{
			return false;
		}

		$query = "SELECT v.*, p.alias, p.ranking as parent_ranking, p.rating as parent_rating, ";
		$query.= " p.checked_out, p.checked_out_time, p.access as parent_access, p.project_id ";
		$query.= " FROM $this->_tbl AS v ";
		$query.= " JOIN #__publications AS p ON p.id=v.publication_id ";
		if ($version == 'default' or $version == 'current' && $version == 'main')
		{
			$query.= " AND v.main=1 ";
		}
		elseif ($version == 'dev')
		{
			$query.= " AND v.state=3 ";
		}
		elseif (is_numeric($version))
		{
			$query.= " AND v.version_number=" . $this->_db->Quote($version);
		}
		else
		{
			// Error in supplied version value
			$query.= " AND 1=2 ";
		}
		$query .= " LIMIT 1";

		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}

	/**
	 * Save version parameter
	 *
	 * @param      integer $id
	 * @param      string  $param
	 * @param      string  $value
	 * @return     void
	 */
	public function saveParam ( $id = NULL, $param = '', $value = 0 )
	{
		if ($id === NULL)
		{
			$id = $this->id;
		}

		if (!$id)
		{
			return false;
		}

		// Clean up value
		$value = preg_replace('/=/', '', $value);

		if ($this->load($id))
		{
			if ($this->params)
			{
				$params = explode("\n", $this->params);
				$in = '';
				$found = 0;

				// Change param
				if (!empty($params))
				{
					foreach ($params as $p)
					{
						if (trim($p) != '' && trim($p) != '=')
						{
							$extracted = explode('=', $p);
							if (!empty($extracted))
							{
								$in .= $extracted[0] . '=';
								$default = isset($extracted[1]) ? $extracted[1] : 0;
								$in .= $extracted[0] == $param ? $value : $default;
								$in	.= "\n";
								if ($extracted[0] == $param)
								{
									$found = 1;
								}
							}
						}
					}
				}
				if (!$found)
				{
					$in .= "\n" . $param . '=' . $value;
				}
			}
			else
			{
				$in = $param . '=' . $value;
			}
			$this->params = $in;
			$this->store();
		}

		return true;
	}

	/**
	 * Get top-level publication version stats
	 *
	 * @param      array 	$validProjects
	 * @param      string 	$get
	 * @return     mixed
	 */
	public function getPubStats ( $validProjects = array(), $get = 'total')
	{
		if (empty($validProjects))
		{
			return NULL;
		}

		$query  = " SELECT COUNT(v.id) as versions ";
		$query .= " FROM $this->_tbl as v ";
		$query .= " JOIN #__publications as p ON p.id = v.publication_id ";
		$query .= " WHERE v.state != 2 ";

		if (!empty($validProjects))
		{
			$query .= " AND p.project_id IN ( ";

			$tquery = '';
			foreach ($validProjects as $v)
			{
				$tquery .= "'".$v."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$query .= $tquery.") ";
		}

		$this->_db->setQuery( $query );

		if ($get == 'total')
		{
			return $this->_db->loadResult();
		}
	}
}
?>
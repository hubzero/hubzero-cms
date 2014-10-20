<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Content\Import\Model;

use Hubzero\Content\Import\Table;
use Hubzero\Base\Model;
use Hubzero\Base\ItemList;
use Exception;

/**
 * Content Import Model
 */
class Import extends Model
{
	/**
	 * Table name
	 *
	 * @var  string
	 */
	protected $_tbl_name = '\Hubzero\Content\Import\Table\Import';

	/**
	 * List of import runs
	 *
	 * @var  object
	 */
	protected $_runs;

	/**
	 * Count  of import runs
	 *
	 * @var  integer
	 */
	protected $_runs_total;

	/**
	 * Return raw import data
	 *
	 * @return  string
	 */
	public function getData()
	{
		return file_get_contents($this->getDataPath());
	}

	/**
	 * Return path to imports data file
	 *
	 * @return  string
	 */
	public function getDataPath()
	{
		// make sure we have file
		if (!$file = $this->get('file'))
		{
			throw new Exception(__METHOD__ . '(); ' . \JText::_('Missing required data file.'));
		}

		// build path to file
		$filePath = $this->fileSpacePath() . DS . $file;

		// make sure file exists
		if (!file_exists($filePath))
		{
			throw new Exception(__METHOD__ . '(); ' . \JText::sprintf('Data file does not exist at path: %s.', $filePath));
		}

		// make sure we can read the file
		if (!is_readable($filePath))
		{
			throw new Exception(__METHOD__ . '(); ' . \JText::_('Data file not readable.'));
		}

		return $filePath;
	}

	/**
	 * Return imports filespace path
	 *
	 * @return string
	 */
	public function fileSpacePath()
	{
		// build upload path
		$uploadPath = JPATH_ROOT . DS . 'site' . DS . 'import' . DS . $this->get('id');

		// return path
		return $uploadPath;
	}

	/**
	 * Return import runs
	 *
	 * @return  string
	 */
	public function runs($rtrn = 'list', $filters = array(), $clear = false)
	{
		switch (strtolower($rtrn))
		{
			case 'count':
				if (is_null($this->_runs_total) || $clear)
				{
					$tbl = new Table\Run($this->_db);

					$this->_runs_total = $tbl->find('count', $filters);
				}
				return $this->_runs_total;
			break;

			case 'current':
				if (!($this->_runs instanceof ItemList) || $clear)
				{
					$this->_runs = $this->runs('list', $filters, $clear);
				}
				return $this->_runs->first();
			break;

			case 'list':
			default:
				if (!($this->_runs instanceof ItemList) || $clear)
				{
					$tbl = new Table\Run($this->_db);
					if ($results = $tbl->find('list', $filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Run($result);
						}
					}
					$this->_runs = new ItemList($results);
				}
				return $this->_runs;
		}
	}

	/**
	 * Mark Import Run
	 *
	 * @param   integer  $dryRun  Dry run mode
	 * @return  void
	 */
	public function markRun($dryRun = 1)
	{
		$importRun = new Run();
		$importRun->set('import_id', $this->get('id'))
				->set('count', $this->get('count'))
				->set('ran_by', \JFactory::getUser()->get('id'))
				->set('ran_at', \JFactory::getDate()->toSql())
				->set('dry_run', $dryRun)
				->store();
	}
}
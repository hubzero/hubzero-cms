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
 * @package   framework
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
			throw new Exception(__METHOD__ . '(); ' . \Lang::txt('Missing required data file.'));
		}

		// build path to file
		$filePath = $this->fileSpacePath() . DS . $file;

		// make sure file exists
		if (!file_exists($filePath))
		{
			throw new Exception(__METHOD__ . '(); ' . \Lang::txt('Data file does not exist at path: %s.', $filePath));
		}

		// make sure we can read the file
		if (!is_readable($filePath))
		{
			throw new Exception(__METHOD__ . '(); ' . \Lang::txt('Data file not readable.'));
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
		$uploadPath = PATH_APP . DS . 'site' . DS . 'import' . DS . $this->get('id');

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
				->set('ran_by', \User::get('id'))
				->set('ran_at', \Date::toSql())
				->set('dry_run', $dryRun)
				->store();
	}
}
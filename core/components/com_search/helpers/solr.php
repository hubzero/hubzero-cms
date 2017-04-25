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
 * @since     2.1.1
 */

namespace Components\Search\Helpers;

use Components\Search\Models\Hubtype;
use stdClass;
use Solarium;

/**
 * Solr helper class
 */
class SolrHelper
{
	/**
	 * enqueueDB
	 * 
	 * @param   string  $type 
	 * @param   array   $ids
	 * @param   string  $action
	 * @static
	 * @access  public
	 * @return  void
	 * @throws  Hubzero\Exception\Exception
	 */
	public static function enqueueDB($type = '', $ids = array(), $action='index')
	{
		if ($type != '' && !empty($ids))
		{
			$db = App::get('db');
			$userID = User::get('id');
			$timestamp = Date::of()->toSql();

			if ($db->tableExists('#__search_queue') && count($ids) > 0)
			{
				$sql = "INSERT INTO `#__search_queue` (type, type_id, status, action, created_by, created) VALUES ";

				foreach ($ids as $key => $id)
				{
					if (!is_array($id))
					{
						$sql .= "('" . $type . "'," . $id . ", 0, '". $action ."', " . $userID . ", '$timestamp}'),";
					}
				}

				$sql = rtrim($sql, ',');
				$sql .= ';';

				try
				{
					$db->setQuery($sql);
					$db->query();
					return true;
				}
				catch (\Exception $e)
				{
					//@FIXME: properly handle this error
					ddie($e->getMessage());
				}
			}
			else
			{
				throw new \Hubzero\Exception\Exception('Queue table does not exist.');
			}
		}
		return false;
	}

	public static function getQueueDB($limit = 100)
	{
		$db = App::get('db');
		$userID = User::get('id');
		$timestamp = Date::of()->toSql();

		if ($db->tableExists('#__search_queue') && count($ids) > 0)
		{
			$sql = "SELECT id, type, type_id FROM `#__search_queue` WHERE status";
		}
	}

	/**
	 * queueStatus - a rudimentary report to check up on the queue
	 * 
	 * @static
	 * @access  public
	 * @return  void
	 */
	public static function queueStatus()
	{
		$sql = "SELECT 
		MAX(modified) as modified,
		MAX(created) as created,
		count(*) as total,
		FLOOR(AVG(modified - created) / 60) as serviceTime,
		(SELECT count(*) FROM `#__search_queue` WHERE status = 0) as notstarted,
		(SELECT count(*) FROM `#__search_queue` WHERE status = 1) as indexed,
		(SELECT count(*) FROM `#__search_queue` WHERE status = 2) as failed
		FROM `#__search_queue`;";

		$db = App::get('db');
		$db->setQuery($sql);
		$report = $db->query()->loadAssoc();

		return $report;
	}

	/**
	 * parseDocumentID - returns a friendly way to access the type and id from a solr ID 
	 * 
	 * @param   string  $id 
	 * @static
	 * @access  public
	 * @return  mixed
	 */
	public static function parseDocumentID($id = '')
	{
		if ($id != '')
		{
			$parts = explode('-', $id);

			if (count($parts) == 3)
			{
				$type = $parts[0] . '-' . $parts[1];
				$id   = $parts[2];
			}
			elseif (count($parts) == 2)
			{
				$type = $parts[0];
				$id   = $parts[1];
			}

			return array('type' => $type, 'id' => $id);
		}
		return false;
	}
}

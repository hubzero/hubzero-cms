<?php
namespace Components\Search\Helpers;

use Components\Search\Models\Hubtype;
use stdClass;
use \Solarium;


class SolrHelper
{
	/**
	 * enqueueDB
	 * 
	 * @param string $type 
	 * @param int $id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function enqueueDB($type = '', $ids = array(), $action='index')
	{
		if ($type != '' && !empty($ids))
		{
			$db = App::get('db');
			$userID = User::getInstance()->get('uidNumber');
			$timestamp = Date::of()->toSql();

			if ($db->tableExists('#__search_queue') && count($ids) > 0)
			{
				$sql = "INSERT INTO #__search_queue (type, type_id, status, action, created_by, created) VALUES ";

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
	}

	/**
	 * queueStatus - a rudimentary report to check up on the queue
	 * 
	 * @static
	 * @access public
	 * @return void
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
		FROM #__search_queue;";

		$db = App::get('db');
		$db->setQuery($sql);
		$report = $db->query()->loadAssoc();

		return $report;
	}

	/**
	 * parseDocumentID - returns a friendly way to access the type and id from a solr ID 
	 * 
	 * @param string $id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function parseDocumentID($id = '')
	{
		if ($id != '')
		{
			$parts = explode('-', $id);

			if (count($parts) == 3)
			{
				$type = $parts[0] . '-' . $parts[1];
				$id = $parts[2];
			}
			elseif (count($parts) == 2)
			{
				$type = $parts[0];
				$id = $parts[1];
			}

			return array('type' => $type, 'id' => $id);
		}
		return false;
	}
}

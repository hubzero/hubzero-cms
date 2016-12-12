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

<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Models;

use Hubzero\Database\Relational;
use User;
use App;

/**
 * Support query model
 */
class Query extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'support';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'ordering';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title'      => 'notempty',
		'conditions' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $always = array(
		'sort',
		'sort_dir'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by',
		'ordering'
	);

	/**
	 * Generates automatic ordering field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticOrdering($data)
	{
		if (!isset($data['ordering']))
		{
			$last = self::all()
				->select('ordering')
				->whereEquals('user_id', $data['user_id'])
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = $last->ordering + 1;
		}

		return $data['ordering'];
	}

	/**
	 * Defines a belongs to one relationship between comment and user
	 *
	 * @param   array   $data
	 * @return  string
	 */
	public function automaticSort($data)
	{
		if (!isset($data['sort']) || !$data['sort'])
		{
			$data['sort'] = 'created';
		}
		return $data['sort'];
	}

	/**
	 * Defines a belongs to one relationship between comment and user
	 *
	 * @param   array   $data
	 * @return  string
	 */
	public function automaticSortDir($data)
	{
		if (!isset($data['sort_dir']) || !$data['sort_dir'])
		{
			$data['sort_dir'] = 'desc';
		}
		if (!in_array($data['sort_dir'], array('desc', 'asc')))
		{
			$data['sort_dir'] = 'desc';
		}
		return $data['sort_dir'];
	}

	/**
	 * Defines a belongs to one relationship between comment and user
	 *
	 * @return  object
	 */
	public function folder()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\QueryFolder', 'folder_id');
	}

	/**
	 * Defines a belongs to one relationship between comment and user
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Defines a belongs to one relationship between comment and user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}

	/**
	 * Get common queries
	 *
	 * @return  object
	 */
	public static function allCommonNotInACL()
	{
		return self::all()
			->whereEquals('iscore', 4);
	}

	/**
	 * Get common queries
	 *
	 * @return  object
	 */
	public static function allCommon()
	{
		return self::all()
			->whereEquals('iscore', 2);
	}

	/**
	 * Get my queries
	 *
	 * @return  object
	 */
	public static function allMine()
	{
		return self::all()
			->whereEquals('iscore', 1);
	}

	/**
	 * Get custom queries
	 *
	 * @param   integer  $user_id
	 * @return  object
	 */
	public static function allCustom($user_id)
	{
		return self::all()
			->whereEquals('iscore', 0)
			->whereEquals('user_id', $user_id);
	}

	/**
	 * Remove entries by folder ID
	 *
	 * @param   integer  $user_id
	 * @return  bool
	 */
	public static function destroyByFolder($folder_id)
	{
		$queries = self::all()
			->whereEquals('folder_id', $folder_id)
			->rows();

		foreach ($queries as $query)
		{
			if (!$query->destroy())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Recursive method to iterate over the condition tree and generate the query
	 *
	 * @param   mixed  $condition  Accepts either a JSON string or object
	 * @return  string
	 */
	public function toSql($condition = null)
	{
		if (!$condition)
		{
			$condition = $this->get('conditions'); //, '{"operator":"AND","expressions":[]}');
		}

		if (is_string($condition))
		{
			$condition = json_decode($condition);
		}

		if (empty($condition))
		{
			return '';
		}

		$db = App::get('db');
		$user = User::getInstance();

		$op = ' ' . strtoupper($condition->operator) . ' ';

		$having = '';
		$e = array();

		$tags = array();
		$nottags = array();
		for ($i = 0; $i < count($condition->expressions); $i++)
		{
			$expr = $condition->expressions[$i];
			if (strtolower($expr->fldval) == 'tag')
			{
				$tags[] = $expr->val;

				if ($expr->opval == '!=')
				{
					$nottags[] = $expr->val;
				}
			}
			/*if (strtolower($expr->fldval) == 'status' && $expr->val == '-1')
			{
				$condition->expressions[$i]->val = '0';

				$exp = new stdClass;
				$exp->fldval = 'open';
				$exp->opval  = '=';
				$exp->opdisp = 'is';
				$exp->val    = '0';
				array_push($condition->expressions, $exp);
			}*/
		}

		for ($i = 0; $i < count($condition->expressions); $i++)
		{
			$uid = 'username';
			if (strtolower($expr->fldval) == 'owner')
			{
				$uid = 'id';
			}

			$expr = $condition->expressions[$i];
			switch ($expr->opval)
			{
				case 'lt':
					$expr->opval = '<';
					break;
				case 'lt=':
					$expr->opval = '<=';
					break;
				case 'gt':
					$expr->opval = '>';
					break;
				case 'gt=':
					$expr->opval = '>=';
					break;
				default:
				break;
			}

			if ($expr->val == 'trivial')
			{
				$expr->val = 'minor';
			}

			//$prfx = (strtolower($expr->fldval) == 'tag') ? 't' : 'f';
			$prfx = 'f';
			if (strtolower($expr->fldval) == 'tag')
			{
				continue;

				$prfx = 't';

				if (count($tags) > 1 && strtoupper($condition->operator) == 'AND')
				{
					// Skip adding multiple tags for AND conditions
					// We need to do an IN () later
					continue;
				}
			}

			if ($expr->fldval == 'group')
			{
				$expr->fldval = 'group_id';

				if (!is_numeric($expr->val))
				{
					if ($group = \Hubzero\User\Group::getInstance($expr->val))
					{
						$expr->val = $group->get('gidNumber');
					}
					else
					{
						$expr->val = 0;
					}
				}
			}

			if (strtoupper($expr->val) == 'NULL' || strtoupper($expr->val) == 'NULL')
			{
				$expr->opval = ($expr->opval == '=') ? 'IS $1' : 'IS NOT $1';
			}
			else if ($expr->val == '*')
			{
				$expr->opval = 'IN ($1)';

				if ($expr->fldval == 'group_id')
				{
					$xgroups = \Hubzero\User\Helper::getGroups($user->get('id'), 'members');
					$expr->val = '';
					if ($xgroups)
					{
						$g = array();
						foreach ($xgroups as $xgroup)
						{
							$g[] = $xgroup->gidNumber;
						}
						$expr->val = "'" . implode("','", $g) . "'";
					}
					else
					{
						continue;
					}
				}
			}

			if (strstr($expr->opval, '$1'))
			{
				if (strtolower($expr->fldval) == 'tag')
				{
					//$e[] = '(' . $prfx . '.' . $db->quoteName($expr->fldval) . ' ' . str_replace('$1', $expr->val, $expr->opval) . ' OR ' . $prfx . '.' . $db->quoteName('raw_' . $expr->fldval) . ' ' . str_replace('$1', $expr->val, $expr->opval) . ')';
				}
				else
				{
					$e[] = $prfx . '.' . $db->quoteName($expr->fldval) . ' ' . str_replace('$1', $expr->val, $expr->opval);
				}
			}
			else
			{
				$uid = 'username';
				if (strtolower($expr->fldval) == 'owner')
				{
					$uid = 'id';
				}

				if ($expr->val == '$me')
				{
					$expr->val = $user->get($uid);
				}
				else if (strtolower($expr->fldval) == 'owner')
				{
					$vuser = User::getInstance($expr->val);

					if ($vuser)
					{
						$expr->val = $vuser->get('id');
					}
				}

				if (strtolower($expr->fldval) == 'status' && $expr->val == '-1')
				{
					$condition->expressions[$i]->val = '0';

					$e[] = '(' . $prfx . '.' . $db->quoteName($expr->fldval) . ' ' . $expr->opval . ' ' . $db->quote($expr->val) . ' AND ' . $prfx . '.' . $db->quoteName('open') . ' = ' . $db->quote('0') . ')';
				}
				else
				{
					$e[] = $prfx . '.' . $db->quoteName($expr->fldval) . ' ' . $expr->opval . ' ' . $db->quote($expr->val);
				}
			}
		}

		if (count($tags) > 0)
		{
			if (implode("','", $tags) == implode("','", $nottags))
			{
				$e[] = 'f.' . $db->quoteName('id') . ' NOT IN (
							SELECT st.' . $db->quoteName('objectid') . ' FROM #__tags_object AS st
							LEFT JOIN #__tags AS t ON st.' . $db->quoteName('tagid') . '=t.' . $db->quoteName('id') . '
							WHERE st.' . $db->quoteName('tbl') . '=\'support\'
							AND (t.' . $db->quoteName('tag') . str_replace('$1', "'" . implode("','", $tags) . "'", 'IN ($1)') . ' OR t.' . $db->quoteName('raw_tag') . ' ' . str_replace('$1', "'" . implode("','", $tags) . "'", 'IN ($1)') . '))';
				$having = " GROUP BY f.id ";
			}
			else if (count($tags) && count($nottags))
			{
				$e[] = '(t.' . $db->quoteName('tag') . ' ' . str_replace('$1', "'" . implode("','", $tags) . "'", 'IN ($1)') . ' OR t.' . $db->quoteName('raw_tag') . ' ' . str_replace('$1', "'" . implode("','", $tags) . "'", 'IN ($1)') . ')';
				$e[] = 'f.' . $db->quoteName('id') . ' NOT IN (
							SELECT jto.' . $db->quoteName('objectid') . ' FROM #__tags_object AS jto
							JOIN #__tags AS jt ON jto.' . $db->quoteName('tagid') . '=jt.' . $db->quoteName('id') . '
							WHERE jto.' . $db->quoteName('tbl') . '=\'support\'
							AND (jt.' . $db->quoteName('tag') . str_replace('$1', "'" . implode("','", $nottags) . "'", 'IN ($1)') . ' OR jt.' . $db->quoteName('raw_tag') . ' ' . str_replace('$1', "'" . implode("','", $nottags) . "'", 'IN ($1)') . '))';
				$having = " GROUP BY f.id ";
			}
			else
			{
				$e[] = '(t.' . $db->quoteName('tag') . ' ' . str_replace('$1', "'" . implode("','", $tags) . "'", 'IN ($1)') . ' OR t.' . $db->quoteName('raw_tag') . ' ' . str_replace('$1', "'" . implode("','", $tags) . "'", 'IN ($1)') . ')';

				$having  = " GROUP BY f.id ";
				if (strtoupper($condition->operator) == 'OR')
				{
					$h = 1;
				}
				else
				{
					$h = (count($tags) - count($nottags));
					$having .= "HAVING uniques='" . $h . "'";
				}
			}
		}

		$n = array();
		$nlen = count($condition->nestedexpressions);
		for ($k = 0; $k < $nlen; $k++)
		{
			$nestexpr = $condition->nestedexpressions[$k];
			$n[] = $this->toSql($nestexpr);
		}

		$q = array();
		if (count($e) > 0)
		{
			$q[] = implode($op, $e);
		}
		if (count($n) > 0)
		{
			$q[] = implode($op, $n);
		}

		return (count($q) ? '(' . implode($op, $q) . ')' : '') . $having;
	}

	/**
	 * Populate the database with default values
	 *
	 * @param   string   $type       Type of query to populate [common, mine]
	 * @param   integer  $folder_id  Folder to add queries to
	 * @return  boolean  False if errors, True on success
	 */
	public static function populateDefaults($type='common', $folder_id=0)
	{
		$me = '$me';

		switch (strtolower(trim($type)))
		{
			case 'common':
				$method = 'allCommon';
				$data = array(
					array(
						'title'      => 'Open tickets',
						'conditions' => '{"operator":"AND","expressions":[{"fldval":"open","flddisp":"Open/Closed","opval":"=","opdisp":"is","val":"1"},{"fldval":"type","flddisp":"Type","opval":"=","opdisp":"is","val":"0"}],"nestedexpressions":[]}',
						'query'      => '',
						'user_id'    => 0,
						'sort'       => 'created',
						'sort_dir'   => 'desc',
						'iscore'     => 2,
						'folder_id'  => $folder_id
					),
					array(
						'title'      => 'New tickets',
						'conditions' => '{"operator":"AND","expressions":[{"fldval":"open","flddisp":"Open/Closed","opval":"=","opdisp":"is","val":"1"},{"fldval":"type","flddisp":"Type","opval":"=","opdisp":"is","val":"0"},{"fldval":"status","flddisp":"Status","opval":"=","opdisp":"is","val":"0"}],"nestedexpressions":[]}',
						'query'      => '',
						'user_id'    => 0,
						'sort'       => 'created',
						'sort_dir'   => 'desc',
						'iscore'     => 2,
						'folder_id'  => $folder_id
					),
					array(
						'title'      => 'Unassigned',
						'conditions' => '{"operator":"AND","expressions":[{"fldval":"open","flddisp":"Open/Closed","opval":"=","opdisp":"is","val":"1"},{"fldval":"type","flddisp":"Type","opval":"=","opdisp":"is","val":"0"}],"nestedexpressions":[{"operator":"OR","expressions":[{"fldval":"owner","flddisp":"Owner","opval":"=","opdisp":"is","val":""},{"fldval":"owner","flddisp":"Owner","opval":"=","opdisp":"is","val":"NULL"}],"nestedexpressions":[]}]}',
						'query'      => '',
						'user_id'    => 0,
						'sort'       => 'created',
						'sort_dir'   => 'desc',
						'iscore'     => 2,
						'folder_id'  => $folder_id
					),
					array(
						'title'      => 'Closed tickets',
						'conditions' => '{"operator":"AND","expressions":[{"fldval":"open","flddisp":"Open/Closed","opval":"=","opdisp":"is","val":"0"},{"fldval":"type","flddisp":"Type","opval":"=","opdisp":"is","val":"0"}],"nestedexpressions":[]}',
						'query'      => '',
						'user_id'    => 0,
						'sort'       => 'created',
						'sort_dir'   => 'desc',
						'iscore'     => 2,
						'folder_id'  => $folder_id
					),
					array(
						'title'      => 'All tickets',
						'conditions' => '{"operator":"AND","expressions":[{"fldval":"type","flddisp":"Type","opval":"=","opdisp":"is","val":"0"}],"nestedexpressions":[]}',
						'query'      => '',
						'user_id'    => 0,
						'sort'       => 'created',
						'sort_dir'   => 'desc',
						'iscore'     => 2,
						'folder_id'  => $folder_id
					)
				);
			break;

			case 'commonnotacl':
				$method = 'allCommonNotInACL';
				$data = array(
					array(
						'title'      => 'Open tickets',
						'conditions' => '{"operator":"AND","expressions":[{"fldval":"open","flddisp":"Open/Closed","opval":"=","opdisp":"is","val":"1"},{"fldval":"type","flddisp":"Type","opval":"=","opdisp":"is","val":"0"}],"nestedexpressions":[{"operator":"OR","expressions":[{"fldval":"group","flddisp":"Group","opval":"=","opdisp":"is","val":"*"},{"fldval":"owner","flddisp":"Owner","opval":"=","opdisp":"is","val":"$me"},{"fldval":"login","flddisp":"Submitter","opval":"=","opdisp":"is","val":"$me"}],"nestedexpressions":[]}]}',
						'query'      => '',
						'user_id'    => 0,
						'sort'       => 'created',
						'sort_dir'   => 'desc',
						'iscore'     => 4,
						'folder_id'  => $folder_id
					),
					array(
						'title'      => 'New tickets',
						'conditions' => '{"operator":"AND","expressions":[{"fldval":"open","flddisp":"Open/Closed","opval":"=","opdisp":"is","val":"1"},{"fldval":"type","flddisp":"Type","opval":"=","opdisp":"is","val":"0"},{"fldval":"status","flddisp":"Status","opval":"=","opdisp":"is","val":"0"}],"nestedexpressions":[{"operator":"OR","expressions":[{"fldval":"group","flddisp":"Group","opval":"=","opdisp":"is","val":"*"},{"fldval":"owner","flddisp":"Owner","opval":"=","opdisp":"is","val":"$me"},{"fldval":"login","flddisp":"Submitter","opval":"=","opdisp":"is","val":"$me"}],"nestedexpressions":[]}]}',
						'query'      => '',
						'user_id'    => 0,
						'sort'       => 'created',
						'sort_dir'   => 'desc',
						'iscore'     => 4,
						'folder_id'  => $folder_id
					),
					array(
						'title'      => 'Unassigned',
						'conditions' => '{"operator":"AND","expressions":[{"fldval":"open","flddisp":"Open/Closed","opval":"=","opdisp":"is","val":"1"},{"fldval":"type","flddisp":"Type","opval":"=","opdisp":"is","val":"0"}],"nestedexpressions":[{"operator":"OR","expressions":[{"fldval":"group","flddisp":"Group","opval":"=","opdisp":"is","val":"*"},{"fldval":"login","flddisp":"Submitter","opval":"=","opdisp":"is","val":"$me"}],"nestedexpressions":[]},{"operator":"OR","expressions":[{"fldval":"owner","flddisp":"Owner","opval":"=","opdisp":"is","val":""},{"fldval":"owner","flddisp":"Owner","opval":"=","opdisp":"is","val":"NULL"}],"nestedexpressions":[]}]}',
						'query'      => '',
						'user_id'    => 0,
						'sort'       => 'created',
						'sort_dir'   => 'desc',
						'iscore'     => 4,
						'folder_id'  => $folder_id
					),
					array(
						'title'      => 'Closed tickets',
						'conditions' => '{"operator":"AND","expressions":[{"fldval":"open","flddisp":"Open/Closed","opval":"=","opdisp":"is","val":"0"},{"fldval":"type","flddisp":"Type","opval":"=","opdisp":"is","val":"0"}],"nestedexpressions":[{"operator":"OR","expressions":[{"fldval":"group","flddisp":"Group","opval":"=","opdisp":"is","val":"*"},{"fldval":"owner","flddisp":"Owner","opval":"=","opdisp":"is","val":"$me"},{"fldval":"login","flddisp":"Submitter","opval":"=","opdisp":"is","val":"$me"}],"nestedexpressions":[]}]}',
						'query'      => '',
						'user_id'    => 0,
						'sort'       => 'created',
						'sort_dir'   => 'desc',
						'iscore'     => 4,
						'folder_id'  => $folder_id
					)
				);
			break;

			case 'mine':
				$method = 'allMine';
				$data = array(
					array(
						'title'      => 'Reported by me',
						'conditions' => '{"operator":"AND","expressions":[{"fldval":"open","flddisp":"Open/Closed","opval":"=","opdisp":"is","val":"1"},{"fldval":"type","flddisp":"Type","opval":"=","opdisp":"is","val":"0"}],"nestedexpressions":[{"operator":"OR","expressions":[{"fldval":"login","flddisp":"Submitter","opval":"=","opdisp":"is","val":"$me"}],"nestedexpressions":[]}]}',
						'query'      => '',
						'user_id'    => 0,
						'sort'       => 'created',
						'sort_dir'   => 'desc',
						'iscore'     => 1,
						'folder_id'  => $folder_id
					),
					array(
						'title'      => 'Assigned to me',
						'conditions' => '{"operator":"AND","expressions":[{"fldval":"open","flddisp":"Open/Closed","opval":"=","opdisp":"is","val":"1"},{"fldval":"type","flddisp":"Type","opval":"=","opdisp":"is","val":"0"}],"nestedexpressions":[{"operator":"OR","expressions":[{"fldval":"owner","flddisp":"Owner","opval":"=","opdisp":"is","val":"$me"}],"nestedexpressions":[]}]}',
						'query'      => '',
						'user_id'    => 0,
						'sort'       => 'created',
						'sort_dir'   => 'desc',
						'iscore'     => 1,
						'folder_id'  => $folder_id
					),
					array(
						'title'      => 'Assigned to me (closed)',
						'conditions' => '{"operator":"AND","expressions":[{"fldval":"open","flddisp":"Open/Closed","opval":"=","opdisp":"is","val":"0"},{"fldval":"type","flddisp":"Type","opval":"=","opdisp":"is","val":"0"}],"nestedexpressions":[{"operator":"OR","expressions":[{"fldval":"owner","flddisp":"Owner","opval":"=","opdisp":"is","val":"$me"},{"fldval":"login","flddisp":"Submitter","opval":"=","opdisp":"is","val":"$me"}],"nestedexpressions":[]}]}',
						'query'      => '',
						'user_id'    => 0,
						'sort'       => 'created',
						'sort_dir'   => 'desc',
						'iscore'     => 1,
						'folder_id'  => $folder_id
					)
				);
			break;

			default:
				return false;
			break;
		}

		foreach ($data as $datum)
		{
			$row = self::blank()->set($datum);

			if (!$row->save())
			{
				return false;
			}
		}

		return self::$method()->rows();
	}
}

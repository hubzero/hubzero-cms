<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Models;

use Hubzero\Database\Relational;
use User;
use Date;

include_once __DIR__ . '/query.php';

/**
 * Support ticket message model
 */
class QueryFolder extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'support_query';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__support_query_folders';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

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
		'title'   => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $always = array(
		'alias',
		'modified',
		'modified_by'
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
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : $data['title']);
		$alias = str_replace(' ', '-', $alias);
		return preg_replace("/[^a-zA-Z0-9]/", '', strtolower($alias));
	}

	/**
	 * Generates automatic created field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return (isset($data['id']) && $data['id'] ? Date::of('now')->toSql() : null);
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @return  int
	 */
	public function automaticModifiedBy($data)
	{
		return (isset($data['id']) && $data['id'] ? User::get('id') : 0);
	}

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
	 * Defines relationship between folder and query
	 *
	 * @return  object
	 */
	public function queries()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Query', 'folder_id');
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove data
		foreach ($this->queries()->rows() as $query)
		{
			if (!$query->destroy())
			{
				$this->addError($query->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Clone core folders and queries and assign
	 * them to a given user ID
	 *
	 * @param   integer  $user_id  User ID
	 * @return  array
	 */
	public static function cloneCore($user_id=0)
	{
		// Get all the default folders
		$folders = self::all()
			->whereEquals('user_id', 0)
			->whereEquals('iscore', 1)
			->order('ordering', 'asc')
			->rows();

		if (count($folders) <= 0)
		{
			$folders = array();

			$defaults = array(
				1 => array('Common', 'Mine', 'Custom'),
				2 => array('Common', 'Mine'),
			);

			foreach ($defaults as $iscore => $fldrs)
			{
				$i = 1;

				foreach ($fldrs as $fldr)
				{
					$f = self::blank();
					$f->set('iscore', $iscore);
					$f->set('title', $fldr);
					$f->set('ordering', $i);
					$f->set('user_id', 0);
					$f->save();

					switch ($f->get('alias'))
					{
						case 'common':
							$j = ($iscore == 1 ? Query::populateDefaults('common', $f->id) : Query::populateDefaults('commonnotacl', $f->id));
						break;

						case 'mine':
							Query::populateDefaults('mine', $f->id);
						break;

						default:
							// Nothing for custom folder
						break;
					}

					$i++;

					if ($iscore == 1)
					{
						$folders[] = $f;
					}
				}
			}
		}

		$user_id = $user_id ?: User::get('id');
		$fid = 0;
		$fldrs = array();

		// Loop through each folder
		foreach ($folders as $k => $folder)
		{
			// Copy the folder for the user
			$stqf = self::blank();
			$stqf->set($folder->toArray());
			$stqf->set('created_by', $user_id);
			$stqf->set('created', Date::toSql());
			$stqf->set('id', 0);
			$stqf->set('user_id', $user_id);
			$stqf->set('iscore', 0);
			$stqf->save();

			$queries = Query::all()
				->whereEquals('folder_id', $folder->get('id'))
				->rows();

			// Copy all the queries from the folder to the user
			foreach ($queries as $query)
			{
				$stq = Query::blank();
				$stq->set($query->toArray());
				$stq->set('created_by', $user_id);
				$stq->set('created', Date::toSql());
				$stq->set('id', 0);
				$stq->set('user_id', $user_id);
				$stq->set('folder_id', $stqf->get('id'));
				$stq->set('iscore', 0);
				$stq->save();
			}

			// If the folder is "custom", get its ID
			if ($folder->get('alias') == 'custom')
			{
				$fid = $stqf->get('id');
			}

			$fldrs[] = $stqf;
		}

		if ($fid)
		{
			$folds = Query::all()
				->whereEquals('user_id', $user_id)
				->whereEquals('iscore', 0)
				->whereEquals('folder_id', 0)
				->rows();

			foreach ($folds as $folder)
			{
				$folder->set('folder_id', $fid);
				$folder->save();
			}
		}

		return $fldrs;
	}
}

<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Import\Model;

use Hubzero\Database\Relational;
use Exception;
use User;
use Date;
use Lang;

/**
 * Class for an import
 */
class Import extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'import';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__imports';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'name';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Current run object
	 *
	 * @var  object
	 */
	protected $_currentRun = null;

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'name' => 'notempty',
		'type' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 **/
	public $initiate = array(
		'created_at',
		'created_by'
	);

	/**
	 * Generates automatic created field value
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function automaticCreatedAt()
	{
		return Date::toSql();
	}

	/**
	 * Get a list of runs
	 *
	 * @return  object
	 */
	public function runs()
	{
		return $this->oneToMany('Hubzero\Content\Import\Model\Run', 'import_id');
	}

	/**
	 * Get most current run
	 *
	 * @return  object
	 */
	public function currentRun()
	{
		if (!$this->_currentRun)
		{
			$this->_currentRun = $this->runs()
				->whereEquals('import_id', $this->get('id'))
				->ordered()
				->row();
		}

		return $this->_currentRun;
	}

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
			throw new Exception(__METHOD__ . '(); ' . Lang::txt('Missing required data file.'));
		}

		// build path to file
		$filePath = $this->fileSpacePath() . DS . $file;

		// make sure file exists
		if (!file_exists($filePath))
		{
			throw new Exception(__METHOD__ . '(); ' . Lang::txt('Data file does not exist at path: %s.', $filePath));
		}

		// make sure we can read the file
		if (!is_readable($filePath))
		{
			throw new Exception(__METHOD__ . '(); ' . Lang::txt('Data file not readable.'));
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
	 * Mark Import Run
	 *
	 * @param   integer  $dryRun  Dry run mode
	 * @return  void
	 */
	public function markRun($dryRun = 1)
	{
		$run = Run::blank();
		$run->set('import_id', $this->get('id'))
			->set('count', $this->get('count'))
			->set('ran_by', User::get('id'))
			->set('ran_at', Date::toSql())
			->set('dry_run', $dryRun)
			->save();
	}
}

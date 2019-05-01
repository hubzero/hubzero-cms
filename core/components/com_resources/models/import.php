<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models;

use Hubzero\Database\Relational;
use Components\Resources\Models\Import\Run;
use Exception;
use Date;
use Lang;
use User;

include_once __DIR__ . DS . 'import' . DS . 'hook.php';
include_once __DIR__ . DS . 'import' . DS . 'run.php';

/**
 * Resource import model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Import extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'resource';

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
		'name' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created_at',
		'created_by'
	);

	/**
	 * Generates automatic added field value
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 * @since   2.0.0
	 **/
	public function automaticCreatedAt($data)
	{
		return (isset($data['created_at']) && $data['created_at'] ? $data['created_at'] : Date::toSql());
	}

	/**
	 * Return a formatted timestamp for created date
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		if (strtolower($as) == 'date')
		{
			$as = Lang::txt('DATE_FORMAT_HZ1');
		}

		if (strtolower($as) == 'time')
		{
			$as = Lang::txt('TIME_FORMAT_HZ1');
		}

		if ($as)
		{
			return Date::of($this->get('created_at'))->toLocal($as);
		}

		return $this->get('created_at');
	}

	/**
	 * Defines a belongs to one relationship between audience and user
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Defines a one to many relationship between import and runs
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function runs()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Import\\Run', 'import_id');
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
	 * @throws  Exception
	 */
	public function getDataPath()
	{
		// make sure we have file
		if (!$file = $this->get('file'))
		{
			throw new Exception(Lang::txt('COM_RESOURCES_IMPORT_MODEL_REQUIRED_FILE'));
		}

		// build path to file
		$filePath = $this->fileSpacePath() . DS . $file;

		// make sure file exists
		if (!file_exists($filePath))
		{
			throw new Exception(Lang::txt('COM_RESOURCES_IMPORT_MODEL_FILE_MISSING', $filePath));
		}

		// make sure we can read the file
		if (!is_readable($filePath))
		{
			throw new Exception(Lang::txt('COM_RESOURCES_IMPORT_MODEL_FILE_NOTREADABLE'));
		}

		return $filePath;
	}

	/**
	 * Return imports filespace path
	 *
	 * @return  string
	 */
	public function fileSpacePath()
	{
		// get com resources params
		$params = \Component::params('com_resources');

		// build upload path
		$uploadPath = $params->get('import_uploadpath', '/site/resources/import');
		$uploadPath = PATH_APP . DS . trim($uploadPath, DS) . DS . $this->get('id');

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
		$importRun = Run::blank()
			->set(array(
				'import_id' => $this->get('id'),
				'count'     => $this->get('count'),
				'ran_by'    => User::get('id'),
				'ran_at'    => Date::toSql(),
				'dry_run'   => $dryRun
			));
		$importRun->save();
	}
}

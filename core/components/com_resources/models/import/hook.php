<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Import;

use Hubzero\Database\Relational;
use Date;
use Lang;

/**
 * Resource import hook model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Hook extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'resource_import';

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
		'created',
		'created_by'
	);

	/**
	 * Return a formatted timestamp for created date
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			$as = Lang::txt('DATE_FORMAT_HZ1');
		}

		if ($as == 'time')
		{
			$as = Lang::txt('TIME_FORMAT_HZ1');
		}

		if ($as)
		{
			return Date::of($this->get('created'))->toLocal($as);
		}

		return $this->get('created');
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
	 * Return imports filespace path
	 *
	 * @return  string
	 */
	public function fileSpacePath()
	{
		// get com resources params
		$params = \Component::params('com_resources');

		// build upload path
		$uploadPath = $params->get('import_hooks_uploadpath', '/site/resources/import/hooks');
		// (int) because the id decides the directory: every importer controller
		// builds an upload target from this path, and they bind hook[id]/import[id]
		// straight off the request through set(), where Request::getArray() filters
		// nothing. A traversing id walked move_uploaded_file() clean out of the
		// import filespace, and save() did not stand in the way -- a non-numeric
		// primary key leaves isNew() false, so the UPDATE matched no rows and still
		// returned success. Casting here holds the containment for every caller
		// rather than relying on four controllers each to remember.
		$uploadPath = PATH_APP . DS . trim($uploadPath, DS) . DS . (int) $this->get('id');

		// return path
		return $uploadPath;
	}
}

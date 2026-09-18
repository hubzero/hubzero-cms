<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Import\Model;

use Hubzero\Database\Relational;

/**
 * Class for an import hook
 */
class Hook extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'import';

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
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'type' => 'notempty',
		'name' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 **/
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Return imports filespace path
	 *
	 * @return  string
	 */
	public function fileSpacePath()
	{
		// build upload path
		// (int) because the id decides the directory: every importer controller
		// builds an upload target from this path, and they bind hook[id]/import[id]
		// straight off the request through set(), where Request::getArray() filters
		// nothing. A traversing id walked move_uploaded_file() clean out of the
		// import filespace, and save() did not stand in the way -- a non-numeric
		// primary key leaves isNew() false, so the UPDATE matched no rows and still
		// returned success. Casting here holds the containment for every caller
		// rather than relying on four controllers each to remember.
		$uploadPath = PATH_APP . DS . 'site' . DS . 'import' . DS . 'hooks' . DS . (int) $this->get('id');

		// return path
		return $uploadPath;
	}
}

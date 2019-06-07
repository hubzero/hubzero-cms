<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Orm;

use Hubzero\Database\Relational;
use Date;
use User;
use Lang;

/**
 * Model class for publication attachment
 */
class Attachment extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'publication';

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
		'type' => 'notempty',
		'path' => 'notempty',
		'publication_id' => 'positive|nonzero',
		'publication_version_id' => 'positive|nonzero'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
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
		'created_by'
	);

	/**
	 * Filespace path
	 *
	 * @var  string
	 */
	public $filespace = null;

	/**
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		if (!isset($data['modified']) || !$data['modified'] || $data['modified'] == '0000-00-00 00:00:00')
		{
			$data['modified'] = Date::of('now')->toSql();
		}
		return $data['modified'];
	}

	/**
	 * Generates automatic modified by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  int
	 */
	public function automaticModifiedBy($data)
	{
		if (!isset($data['modified_by']) || !$data['modified_by'])
		{
			$data['modified_by'] = User::get('id');
		}
		return $data['modified_by'];
	}

	/**
	 * Establish relationship to parent publication
	 *
	 * @return  object
	 */
	public function publication()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Publication', 'publication_id');
	}

	/**
	 * Establish relationship to parent publication version
	 *
	 * @return  object
	 */
	public function version()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Version', 'publication_version_id');
	}

	/**
	 * Get the filespace path
	 *
	 * @return  string
	 */
	public function filespace()
	{
		if (!isset($this->filespace))
		{
			$version = $this->version;

			$this->filespace = $version->filespace();
		}

		return $this->filespace;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		if ($this->get('type') == 'file')
		{
			$path = $this->filespace() . '/' . $this->get('path');

			// Remove the file
			if (\Filesystem::exists($path))
			{
				if (!\Filesystem::delete($path))
				{
					$this->addError(Lang::txt('Failed to remove file from filesystem.'));
					return false;
				}
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}
}

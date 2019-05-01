<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Feedback\Models;

use Components\Members\Models\Member;
use Hubzero\Database\Relational;
use Filesystem;
use Lang;
use Date;

/**
 * Feedback model for a quote
 */
class Quote extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'feedback';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__feedback';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'date';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'quote' => 'notempty'
	);

	/**
	 * Return user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}

	/**
	 * Defines a belongs to one relationship
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get('date'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('date'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			case 'relative':
				return Date::of($this->get('date'))->relative();
			break;

			default:
				if ($as)
				{
					return Date::of($this->get('date'))->toLocal($as);
				}
				return $this->get('date');
			break;
		}
	}

	/**
	 * Return the path for uploads
	 *
	 * @param   bool    $root
	 * @return  string
	 */
	public function filespace($root = true)
	{
		$config = \Component::params('com_feedback');

		return ($root ? PATH_APP : substr(PATH_APP, strlen(PATH_ROOT))) . DS . trim($config->get('uploadpath', '/site/quotes'), DS);
	}

	/**
	 * Deletes the existing/current model
	 *
	 * @return  bool
	 */
	public function destroy()
	{
		$path = $this->filespace() . DS . $this->get('id');

		if (is_dir($path))
		{
			Filesystem::deleteDirectory($path, true);
		}

		return parent::destroy();
	}

	/**
	 * Get a list of files for this entry
	 *
	 * @return  array
	 */
	public function files()
	{
		$files = array();

		if (!$this->get('id'))
		{
			return $files;
		}

		$path = $this->filespace() . DS . $this->get('id');

		if (is_dir($path))
		{
			$pictures = Filesystem::files($path);

			foreach ($pictures as $picture)
			{
				$files[] = new \SplFileInfo($path . DS . ltrim($picture, DS));
			}
		}

		return $files;
	}
}

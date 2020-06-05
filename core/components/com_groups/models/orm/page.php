<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Orm;

use Hubzero\Database\Relational;

require_once __DIR__ . DS . 'page' . DS . 'category.php';
require_once __DIR__ . DS . 'page' . DS . 'version.php';
require_once __DIR__ . DS . 'page' . DS . 'hit.php';

/**
 * Group page model
 */
class Page extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'xgroups';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'lft';

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
		'gidNumber' => 'positive|nonzero',
		'title'     => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'alias'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'lft',
		'rgt'
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
		return preg_replace("/[^a-zA-Z0-9\-_]/", '', strtolower($alias));
	}

	/**
	 * Generates automatic lft value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticLft($data)
	{
		if (!$data['parent'])
		{
			$data['lft'] = 0;
		}
		return $data['lft'];
	}

	/**
	 * Generates automatic lft value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticRgt($data)
	{
		if (!isset($data['rgt']))
		{
			if (!isset($data['lft']))
			{
				$data['lft'] = $this->automaticLft($data);
			}
			$data['rgt'] = $data['lft'] + 1;
		}
		return $data['rgt'];
	}

	/**
	 * Get parent entry
	 *
	 * @return  object
	 */
	public function parent()
	{
		return self::one($this->get('parent'));
	}

	/**
	 * Get versions
	 *
	 * @return  object
	 */
	public function versions()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Page\\Version', 'pageid');
	}

	/**
	 * Get parent category
	 *
	 * @return  object
	 */
	public function category()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Page\\Category', 'category');
	}

	/**
	 * Get page hits
	 *
	 * @return  object
	 */
	public function hits()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Page\\Hit', 'pageid');
	}

	/**
	 * Create a page hit
	 *
	 * @return  object
	 */
	public function hit()
	{
		$hit = Page\Hit::blank()
			->set(array(
				'gidNumber' => $this->get('gidNumber'),
				'pageid' => $this->get('id'),
				'userid' => \User::get('id'),
				'ip' => \Request::ip()
			));

		return $hit->save();
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove versions
		foreach ($this->versions()->rows() as $version)
		{
			if (!$version->destroy())
			{
				$this->addError($version->getError());
				return false;
			}
		}

		// Remove hits
		foreach ($this->hits()->rows() as $hit)
		{
			if (!$hit->destroy())
			{
				$this->addError($hit->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}
}

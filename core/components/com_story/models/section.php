<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;

/**
 * A front page and its siblings
 */
class Section extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'story';

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
		'title' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'alias'
	);

	/**
	 * Published state
	 */
	const STATE_UNPUBLISHED = 0;
	const STATE_PUBLISHED   = 1;

	/**
	 * Generates automatic alias value
	 *
	 * @param   array   $data
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && trim($data['alias']) != '') ? $data['alias'] : (isset($data['title']) ? $data['title'] : '');
		$alias = str_replace(' ', '-', strtolower(trim($alias)));

		return preg_replace('/[^a-z0-9\-]/', '', $alias);
	}

	/**
	 * Load a section by its alias within a scope
	 *
	 * @param   string   $alias
	 * @param   string   $scope
	 * @param   integer  $scopeId
	 * @return  object
	 */
	public static function oneByAlias($alias, $scope = 'site', $scopeId = 0)
	{
		return self::all()
			->whereEquals('alias', (string) $alias)
			->whereEquals('scope', (string) $scope)
			->whereEquals('scope_id', (int) $scopeId)
			->row();
	}

	/**
	 * Transform params into a Registry
	 *
	 * @return  object
	 */
	public function transformParams()
	{
		if (!is_object($this->params))
		{
			$this->params = new Registry($this->get('params'));
		}

		return $this->params;
	}

	/**
	 * Defines a one to many relationship with stories
	 *
	 * @return  object
	 */
	public function stories()
	{
		return $this->oneToMany('Components\Story\Models\Story', 'section_id');
	}

	/**
	 * Is this section published?
	 *
	 * @return  bool
	 */
	public function isPublished()
	{
		return ((int) $this->get('state') === self::STATE_PUBLISHED);
	}
}

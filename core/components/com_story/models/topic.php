<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Models;

use Hubzero\Database\Relational;

/**
 * What a story is about
 *
 * A tree, and deliberately only a tree. The system this is modelled on used a
 * weighted graph with topics that were also section front pages; that
 * flexibility bought it very little and cost it a great deal of explaining.
 */
class Topic extends Relational
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
	 * Load a topic by alias
	 *
	 * @param   string  $alias
	 * @return  object
	 */
	public static function oneByAlias($alias)
	{
		return self::all()->whereEquals('alias', (string) $alias)->row();
	}

	/**
	 * Defines a belongs to one relationship with a parent topic
	 *
	 * @return  object
	 */
	public function parent()
	{
		return $this->belongsToOne('Components\Story\Models\Topic', 'parent_id');
	}

	/**
	 * Defines a one to many relationship with child topics
	 *
	 * @return  object
	 */
	public function children()
	{
		return $this->oneToMany('Components\Story\Models\Topic', 'parent_id');
	}

	/**
	 * Is this topic published?
	 *
	 * @return  bool
	 */
	public function isPublished()
	{
		return ((int) $this->get('state') === self::STATE_PUBLISHED);
	}
}

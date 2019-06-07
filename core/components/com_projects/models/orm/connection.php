<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;
use Hubzero\Filesystem\Manager;
use User;

include_once __DIR__ . '/provider.php';

/**
 * Connections model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Connection extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'projects';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'project_id'  => 'positive|nonzero',
		'provider_id' => 'positive|nonzero'
	);

	/**
	 * Defines a belongs to one relationship between connections and connection providers
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 **/
	public function provider()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Provider', 'provider_id');
	}

	/**
	 * Defines a belongs to one relationship between connections and projects
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 **/
	public function project()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Project', 'project_id');
	}

	/**
	 * Get owner
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function owner()
	{
		return $this->belongsToOne('Hubzero\\User\\User', 'owner_id');
	}

	/**
	 * Generates the filesystem adapter for the given provider
	 *
	 * @param   array   $options  extra params to include with defaults
	 * @return  object
	 **/
	public function adapter($options=[])
	{
		$params = (array)json_decode($this->params);
		$params = array_merge($params, $options);

		return Manager::adapter($this->provider->alias, $params);
	}

	/**
	 * Gets the connection name, defaulting to the provider name if not set
	 *
	 * @return  string
	 **/
	public function transformName()
	{
		if ($this->hasAttribute('name'))
		{
			return $this->get('name');
		}

		return $this->provider->name;
	}

	/**
	 * Gets the connections that are mine or are public to my project
	 *
	 * @return  $this
	 **/
	public function thatICanView()
	{
		return $this->whereEquals('owner_id', User::get('id'), 1)
			->orWhereEquals('owner_id', 0, 1)
			->orWhereRaw('owner_id IS NULL', [], 1);
	}

	/**
	 * Checks to see if a given connection is shared or private
	 *
	 * @return  bool
	 **/
	public function isShared()
	{
		return !$this->owner_id ? true : false;
	}
}

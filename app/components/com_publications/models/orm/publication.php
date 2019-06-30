<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Orm;

use Hubzero\Database\Relational;
use stdClass;

require_once __DIR__ . DS . 'version.php';
require_once __DIR__ . DS . 'rating.php';
require_once __DIR__ . DS . 'type.php';
require_once __DIR__ . DS . 'category.php';

/**
 * Model class for publication
 */
class Publication extends Relational
{
	/**
	 * Database state constants
	 *
	 * These are defiend int he base Relational class:
	 *     STATE_UNPUBLISHED = 0;
	 *     STATE_PUBLISHED   = 1;
	 *     STATE_DELETED     = 2;
	 **/
	const STATE_DRAFT       = 3;
	const STATE_POSTED      = 4;  // Posted for review
	const STATE_PENDING     = 5;  // Pending publishing
	const STATE_ARCHIVED    = 6;
	const STATE_WORKED      = 7;  // Pending author changes

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title' => 'notempty'
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
	 * Active version
	 *
	 * @var  string
	 */
	public $activeVersion = null;

	/**
	 * Component configuration
	 *
	 * @var  object
	 */
	protected $config = null;

	/**
	 * Establish relationship to type
	 *
	 * @return  object
	 */
	public function type()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Type', 'id', 'master_type');
	}

	/**
	 * Establish relationship to category
	 *
	 * @return  object
	 */
	public function category()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Category', 'id', 'category');
	}

	/**
	 * Establish relationship to versions
	 *
	 * @return  object
	 */
	public function versions()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Version', 'publication_id');
	}

	/**
	 * Establish relationship to ratings
	 *
	 * @return  object
	 */
	public function ratings()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Rating', 'publication_id');
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Establish relationship to group
	 *
	 * @return  object
	 */
	public function group()
	{
		return $this->belongsToOne('Hubzero\\User\\Group', 'gidNumber', 'group_owner');
	}

	/**
	 * Establish relationship to project
	 *
	 * @return  object
	 */
	public function project()
	{
		require_once \Component::path('com_projects') . '/models/orm/project.php';

		return $this->belongsToOne('Components\Projects\Models\Orm\Project', 'project_id');
	}

	/**
	 * Get the ancestor this was forked from
	 *
	 * @return  object
	 */
	public function ancestor()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Publication', 'forked_from');
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove ratings
		foreach ($this->ratings as $rating)
		{
			if (!$rating->destroy())
			{
				$this->addError($rating->getError());
				return false;
			}
		}

		// Remove versions
		foreach ($this->versions as $version)
		{
			if (!$version->destroy())
			{
				$this->addError($version->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 *
	 * @param   string  $key      Config property to retrieve
	 * @param   mixed   $default
	 * @return  mixed
	 */
	public function config($key=null, $default=null)
	{
		if (!isset($this->config))
		{
			$this->config = \Component::params('com_publications');
		}
		if ($key)
		{
			return $this->config->get($key, $default);
		}
		return $this->config;
	}

	/**
	 * Get tag cloud
	 *
	 * @return  array
	 */
	public function tags()
	{
		include_once \Component::path('com_tags') . '/models/cloud.php';

		$cloud = new \Components\Tags\Models\Cloud();

		$filters = array(
			'scope'    => 'publications',
			'scope_id' => $this->getActiveVersion()->id
		);

		return $cloud->tags('list', $filters);
	}

	/**
	 * Get most recent version that is still marked as active
	 * 
	 * @return  object  Components\Publications\Models\Orm\Version
	 */
	public function getActiveVersion()
	{
		if (empty($this->activeVersion))
		{
			$versions = $this->versions->sort('id', false);

			foreach ($versions as $version)
			{
				if ($version->state == 1)
				{
					$this->activeVersion = $version;
					break;
				}
			}

			if (empty($this->activeVersion))
			{
				$this->activeVersion = $versions->first();
			}
		}

		return $this->activeVersion;
	}

	/**
	 * Generate link to current active version
	 *
	 * @return  string
	 */
	public function link()
	{
		return $this->getActiveVersion()->link();
	}
}

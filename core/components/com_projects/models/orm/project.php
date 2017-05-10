<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;
use User;

include_once __DIR__ . '/owner.php';
include_once __DIR__ . '/description.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/activity.php';

/**
 * Projects database model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Project extends Relational
{
	/**
	 * State constants
	 *
	 * @var  integer
	 **/
	const STATE_ARCHIVED = 3;
	const STATE_PENDING  = 5;

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'title';

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
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by_user'
	);

	/**
	 * Hubzero\Config\Registry
	 *
	 * @var  object
	 */
	public $config = null;

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('alias', function($data)
		{
			$alias = $this->automaticAlias($data);

			// Set name length
			$minLength = (int)$this->config('min_name_length', 3);
			$maxLength = (int)$this->config('max_name_length', 30);

			if (strlen($alias) < $minLength)
			{
				return Lang::txt('COM_PROJECTS_ERROR_NAME_TOO_SHORT');
			}

			if (strlen($alias) > $maxLength)
			{
				return Lang::txt('COM_PROJECTS_ERROR_NAME_TOO_LONG');
			}

			if (preg_match('/[^a-z0-9]/', $alias))
			{
				// Check for illegal characters
				return Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID');
			}

			if (is_numeric($alias))
			{
				// Check for all numeric (not allowed)
				return Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID_NUMERIC');
			}

			return false;
		});
	}

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : $data['title']);
		$alias = strip_tags($alias);

		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 */
	public function automaticCreatedByUser($data)
	{
		return (isset($data['created_by_user']) && $data['created_by_user'] ? (int)$data['created_by_user'] : (int)User::get('id'));
	}

	/**
	 * Defines a one to many relationship between projects and connections
	 *
	 * @return  \Hubzero\Database\Relationship\OneToMany
	 **/
	public function connections()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Connection');
	}

	/**
	 * Defines a one to many relationship between projects and description fields
	 *
	 * @return  \Hubzero\Database\Relationship\OneToMany
	 **/
	public function descriptions()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Description', 'project_id');
	}

	/**
	 * Defines a one to many relationship between projects and team
	 *
	 * @return  \Hubzero\Database\Relationship\OneToMany
	 **/
	public function team()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Owner', 'projectid');
	}

	/**
	 * Check if the project is public
	 *
	 * @return  boolean
	 */
	public function isPublic()
	{
		if (!$this->get('id'))
		{
			return false;
		}

		if ($this->get('private') == 1)
		{
			return false;
		}

		return true;
	}

	/**
	 * Is project archived?
	 *
	 * @return  boolean
	 */
	public function isArchived()
	{
		return ($this->get('state') == self::STATE_ARCHIVED);
	}

	/**
	 * Is project deleted?
	 *
	 * @return  boolean
	 */
	public function isDeleted()
	{
		return ($this->get('state') == self::STATE_DELETED);
	}

	/**
	 * Is project pending approval?
	 *
	 * @return  boolean
	 */
	public function isPending()
	{
		return ($this->get('state') == self::STATE_PENDING);
	}

	/**
	 * Is project suspended?
	 *
	 * @return     boolean
	 */
	public function isInactive()
	{
		return ($this->get('state') == 0 && !$this->inSetup());
	}

	/**
	 * Is project provisioned?
	 *
	 * @return  boolean
	 */
	public function isProvisioned()
	{
		return ($this->get('provisioned') == 1);
	}

	/**
	 * Is project in setup?
	 *
	 * @return  boolean
	 */
	public function inSetup()
	{
		$setupComplete = $this->config()->get('confirm_step') ? 3 : 2;

		return ($this->get('setup_stage') < $setupComplete);
	}

	/**
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 *
	 * @param   string  $key  Config property to retrieve
	 * @return  mixed
	 */
	public function config($key=null)
	{
		if (!isset($this->config))
		{
			$this->config = \Component::params('com_projects');
		}
		if ($key)
		{
			return $this->config->get($key);
		}
		return $this->config;
	}

	/**
	 * Deletes the existing/current model
	 *
	 * @return  bool
	 */
	public function destroy()
	{
		foreach ($this->connections as $connection)
		{
			if (!$connection->destroy())
			{
				$this->addError($connection->getError());
				return false;
			}
		}

		foreach ($this->descriptions as $description)
		{
			if (!$description->destroy())
			{
				$this->addError($description->getError());
				return false;
			}
		}

		foreach ($this->team as $team)
		{
			if (!$team->destroy())
			{
				$this->addError($team->getError());
				return false;
			}
		}

		return parent::destroy();
	}
}

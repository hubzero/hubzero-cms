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
use Hubzero\Config\Registry;

/**
 * Projects owner model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Owner extends Relational
{
	/**
	 * Role values
	 *
	 * @var  int
	 **/
	const ROLE_INVITEE      = 0;
	const ROLE_MANAGER      = 1;
	const ROLE_COLLABORATOR = 2;
	const ROLE_AUTHOR       = 3;
	const ROLE_REVIEWER     = 5;

	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'project';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'projectid' => 'positive|nonzero'
	);

	/**
	 * Params
	 *
	 * @var  object
	 */
	protected $params = null;

	/**
	 * Defines a belongs to one relationship between owner and project
	 *
	 * @return  object
	 */
	public function project()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Project', 'projectid');
	}

	/**
	 * Defines a belongs to one relationship between owner and user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'userid');
	}

	/**
	 * Defines a belongs to one relationship between owner and group
	 *
	 * @return  object
	 */
	public function group()
	{
		$group = \Hubzero\User\Group::getInstance($this->get('groupid'));
		if (!$group)
		{
			$group = new \Hubzero\User\Group();
		}
		return $group;
		//return $this->belongsToOne('Hubzero\User\Group', 'groupid');
	}

	/**
	 * Load a single record by project ID and user ID
	 *
	 * @param   integer  $projectid
	 * @param   integer  $userid
	 * @return  object
	 */
	public static function oneByProjectAndUser($projectid, $userid)
	{
		return self::all()
			->whereEquals('projectid', $projectid)
			->whereEquals('userid', $userid)
			->row();
	}

	/**
	 * Is the user a manager of the project?
	 *
	 * @return  bool
	 */
	public function isManager()
	{
		return ($this->get('role') == self::ROLE_MANAGER);
	}

	/**
	 * Is the user a collaborator of the project?
	 *
	 * @return  bool
	 */
	public function isCollaborator()
	{
		return ($this->get('role') == self::ROLE_COLLABORATOR);
	}

	/**
	 * Is the user a reviewer of the project?
	 *
	 * @return  bool
	 */
	public function isReviewer()
	{
		return ($this->get('role') == self::ROLE_REVIEWER);
	}

	/**
	 * Is the user invited to the project?
	 *
	 * @return  bool
	 */
	public function isInvited()
	{
		return ($this->get('role') == self::ROLE_INVITEE);
	}

	/**
	 * Get a param value
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
}

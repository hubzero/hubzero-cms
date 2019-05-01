<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Models\Application;

use Hubzero\Database\Relational;
use Hubzero\User\User;

/**
 * Model class for a team member
 */
class Member extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'developer_application_team';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'application_id';

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
		'application_id' => 'positive|nonzero',
		'uidNumber'      => 'positive|nonzero'
	);

	/**
	 * Get Profile Object from user id
	 * 
	 * @return  object  Profile object
	 */
	public function getProfile()
	{
		return User::oneOrNew($this->get('uidNumber'));
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
	 */
	public function link($type='')
	{
		static $base;

		if (!isset($base))
		{
			$base = 'index.php?option=com_developer&controller=applications&id=' . $this->get('application_id');
		}

		$link = $base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'remove':
			default:
				$link .= '&task=removemember&uidNumber=' . $this->get('uidNumber');
			break;
		}

		return $link;
	}
}

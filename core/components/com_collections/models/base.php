<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models;

use Components\Members\Models\Member;
use Hubzero\Base\Model;
use Component;
use Date;
use User;
use Lang;

require_once Component::path('com_members') . DS . 'models' . DS . 'member.php';

/**
 * Abstract model for collections
 */
class Base extends Model
{
	/**
	 * \Hubzero\User\User
	 *
	 * @var  object
	 */
	protected $_creator = null;

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('created');
			break;
		}
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire object
	 *
	 * @param   string  $property  Property to retrieve
	 * @param   mixed   $default   Default value if property not set
	 * @return  mixed
	 */
	public function creator($property=null, $default=null)
	{
		if (!($this->_creator instanceof Member))
		{
			$this->_creator = Member::oneOrNew($this->get('created_by'));

			if (!trim($this->_creator->get('name')))
			{
				$this->_creator->set('name', Lang::txt('(unknown)'));
			}
		}
		if ($property)
		{
			$property = ($property == 'uidNumber' ? 'id' : $property);
			return $this->_creator->get($property, $default);
		}
		return $this->_creator;
	}

	/**
	 * Get the path to the file space
	 *
	 * @return  string
	 */
	public function filespace()
	{
		static $path;

		if (!$path)
		{
			$path = PATH_APP . DS . trim(Component::params('com_collections')->get('filepath', '/site/collections'), DS);
		}

		return $path;
	}
}

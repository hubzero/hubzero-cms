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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Models;

use Hubzero\Base\Object;
use Hubzero\Utility\Validate;
use InvalidArgumentException;
use stdClass;
use User;
use Lang;

/**
 * Support mdoel for a ticket changelog
 */
class Changelog extends Object
{
	/**
	 * ItemList
	 *
	 * @var object
	 */
	private $_log = null;

	/**
	 * Log format
	 *
	 * @var string
	 */
	private $_format = 'json';

	/**
	 * Is the question open?
	 *
	 * @param   string  $data
	 * @return  boolean
	 */
	public function __construct($data=null)
	{
		if ($data)
		{
			$this->_raw = $data;

			if (substr($data, 0, 1) == '{')
			{
				$this->_log = json_decode($data, true);
			}
			else
			{
				$this->_format = 'html';

				$log = array(
					'changes'       => array(),
					'notifications' => array(),
					'cc'            => array()
				);

				$data = preg_replace("/\n\t\r/i", '', $data);
				$data = str_replace(array('<ul class="changes">', '</ul>'), '', $data);
				$data = str_replace(array('<ul class="changelog">', '</ul>'), '', $data);
				$data = str_replace(array('<ul class=email-in-log>', '</ul>'), '', $data);
				$data = explode('</li>', $data);
				$data = array_map('trim', $data);

				/*<ul class=email-in-log><li>Comment submitted via email from zooley@purdue.edu</li></ul><ul class=email-out-log><li>E-mailed ticket creator zooley@purdue.edu </li></ul><ul class=email-out-log><li>E-mailed ticket owner zooley@purdue.edu </li></ul>*/

				foreach ($data as $key => $item)
				{
					$item = trim($item);
					if (!$item)
					{
						unset($data[$key]);
						continue;
					}
					$item = str_replace('<li>', '', $item);

					$obj = array(
						'field'  => '',
						'before' => '',
						'after'  => ''
					);
					if (preg_match('/Comment submitted via email from (.+)/i', $item, $matches))
					{
						$obj = array(
							'role'    => 'commenter',
							'name'    => Lang::txt('COM_SUPPORT_NONE'),
							'address' => trim($matches[1])
						);
					}
					if (preg_match('/E\-mailed ticket ([^ ]+) (.+)/i', $item, $matches))
					{
						$obj = array(
							'role'    => trim($matches[1]),
							'name'    => Lang::txt('COM_SUPPORT_NONE'),
							'address' => trim($matches[2])
						);
					}
					if (preg_match('/<strong>(.*?)<\/strong>/i', $item, $matches))
					{
						$matches[1] = trim($matches[1]);
						if ($matches[1] == 'cc')
						{
							$obj = array(
								'role'    => $matches[1],
								'name'    => '',
								'address' => ''
							);
						}
						else
						{
							$obj['field'] = $matches[1];
						}
					}
					if (preg_match('/<em>(.*?)<\/em>/i', $item, $matches))
					{
						if (isset($matches[2]))
						{
							if (isset($obj['field']))
							{
								$obj['before'] = trim($matches[1]);
								$obj['after']  = trim($matches[2]);
							}
							else
							{
								$obj['name']    = trim($matches[1]);
								$obj['address'] = trim($matches[2]);
							}
						}
						else
						{
							if (isset($obj['field']))
							{
								$obj['after'] = trim($matches[1]);
							}
							else
							{
								$obj['name']    = Lang::txt('COM_SUPPORT_NONE');
								$obj['address'] = trim($matches[2]);
							}
						}
					}
					if (isset($obj['role']))
					{
						$log['notifications'][] = $obj;
						$log['cc'][] = $obj['address'];
					}
					else
					{
						$log['changes'][] = $obj;
					}
				}
				$this->_log = $log;
			}
		}

		if (!isset($this->_log['changes']))
		{
			$this->_log['changes'] = array();
		}
		if (!isset($this->_log['notifications']))
		{
			$this->_log['notifications'] = array();
		}
		if (!isset($this->_log['cc']))
		{
			$this->_log['cc'] = array();
		}
	}

	/**
	 * Get the format
	 *
	 * @return  string
	 */
	public function format()
	{
		return $this->_format;
	}

	/**
	 * Get the whole log
	 *
	 * @return  array
	 */
	public function lists()
	{
		return $this->_log;
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $default   The default value.
	 * @return  mixed   The value of the property.
	 */
	public function get($property, $default = null)
	{
		if (isset($this->_log[$property]))
		{
			return $this->_log[$property];
		}
		return $default;
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set.
	 * @return  mixed   Previous value of the property.
	 */
	public function set($property, $value = null)
	{
		$this->_log[$property] = $value;
		return $this;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  boolean
	 */
	public function render()
	{
		$clog = array();
		foreach ($this->_log as $type => $log)
		{
			if (is_array($log) && count($log) > 0)
			{
				if ($type == 'cc')
				{
					$cc = $log;
					continue;
				}
				$clog[] = '<ul class="' . $type . '">';
				foreach ($log as $items)
				{
					if ($type == 'changes' && $items['before'] != $items['after'])
					{
						$clog[] = '<li>' . Lang::txt('COM_SUPPORT_CHANGELOG_BEFORE_AFTER', $items['field'], $items['before'], $items['after']) . '</li>';
					}
					else if ($type == 'notifications')
					{
						$clog[] = '<li>' . Lang::txt('COM_SUPPORT_CHANGELOG_NOTIFIED', $items['role'], $items['name'], $items['address']) . '</li>';
					}
				}
				$clog[] = '</ul>';
			}
		}
		if (!count($clog))
		{
			$clog[] = '<ul class="changes"><li>' . Lang::txt('COM_SUPPORT_CHANGELOG_NONE_MADE') . '</li></ul>';
		}
		return implode("\n", $clog);
	}

	/**
	 * Add an entry to the change log
	 *
	 * @param   string  $field   Field name
	 * @param   string  $before  Old value (if any)
	 * @param   string  $after   New value (if any)
	 * @return  object
	 */
	public function changed($field, $before='', $after='')
	{
		$obj = new stdClass();
		$obj->field  = (string) $field;
		$obj->before = (string) $before;
		$obj->after  = (string) $after;

		$this->_log['changes'][] = $obj;

		return $this;
	}

	/**
	 * Add CC info to the log
	 *
	 * @param   string  $val  Value to log
	 * @return  object
	 */
	public function cced($val)
	{
		$val = trim($val);
		if (!$val)
		{
			return $this;
		}

		$val = preg_split("/[,;]/", $val);
		$val = array_map('trim', $val);

		foreach ($val as $acc)
		{
			// Is this a username or email address?
			if (!strstr($acc, '@'))
			{
				// Username or user ID - load the user
				$acc  = (is_string($acc)) ? strtolower($acc) : $acc;
				$user = User::getInstance($acc);

				// Did we find an account?
				if (is_object($user))
				{
					$this->_log['cc'][] = $user->get('username');
				}
				else
				{
					// Move on - nothing else we can do here
					continue;
				}
			}
			// Make sure it's a valid e-mail address
			else if (Validate::email($acc))
			{
				$this->_log['cc'][] = $acc;
			}
		}

		return $this;
	}

	/**
	 * Add an entry to the notifications list
	 *
	 * @param   string  $role     User role
	 * @param   string  $name     User name
	 * @param   string  $address  User email
	 * @return  object
	 */
	public function notified($role, $name, $address)
	{
		$obj = new stdClass();
		$obj->role    = (string) $role;
		$obj->name    = (string) $name;
		$obj->address = (string) $address;

		$this->_log['notifications'][] = $obj;

		return $this;
	}

	/**
	 * Get a count of or list of attachments on this model
	 *
	 * @param   string  $to      Category
	 * @param   string  $field   Field name
	 * @param   string  $before  Old value (if any)
	 * @param   string  $after   New value (if any)
	 * @return  object
	 */
	public function add($to, $field, $before='', $after='')
	{
		if (!isset($this->_log[$to]))
		{
			throw new InvalidArgumentException(Lang::txt('COM_SUPPORT_ERROR_CHANGELOG_UNKNOWN_CATEGORY', (string) $to));
		}

		switch ($to)
		{
			case 'changes':
				return $this->changed($field, $before, $after);
			break;

			case 'notifications':
				return $this->notified($field, $before, $after);
			break;

			case 'cc':
				return $this->cced($field);
			break;
		}

		return $this;
	}

	/**
	 * Remove an item form the log
	 *
	 * @param   string  $from   Area to remove from
	 * @param   string  $field  Field to remove
	 * @return  object
	 */
	public function remove($from, $field)
	{
		if (!isset($this->_log[$from]))
		{
			throw new InvalidArgumentException(Lang::txt('COM_SUPPORT_ERROR_CHANGELOG_UNKNOWN_CATEGORY', (string) $from));
		}

		foreach ($this->_log[$from] as $key => $item)
		{
			if ($item->field == $field)
			{
				unset($this->_log[$from][$key]);
			}
		}

		return $this;
	}

	/**
	 * Log changes from one version of the ticket to the next
	 *
	 * @param   object  $before
	 * @param   object  $after
	 * @return  object
	 */
	public function diff($before, $after)
	{
		if ($after->get('group') != $before->get('group'))
		{
			$this->changed(
				Lang::txt('COM_SUPPORT_CHANGELOG_FIELD_GROUP'),
				$before->get('group'),
				$after->get('group')
			);
		}
		if ($after->get('severity') != $before->get('severity'))
		{
			$this->changed(
				Lang::txt('COM_SUPPORT_CHANGELOG_FIELD_SEVERITY'),
				$before->get('severity'),
				$after->get('severity')
			);
		}
		if (intval($after->get('owner')) != intval($before->get('owner')))
		{
			$this->changed(
				Lang::txt('COM_SUPPORT_CHANGELOG_FIELD_OWNER'),
				$before->owner('username', Lang::txt('COM_SUPPORT_NONE')),
				$after->owner('username', Lang::txt('COM_SUPPORT_NONE'))
			);
		}
		/*if ($after->get('resolved') != $before->get('resolved'))
		{
			$this->changed(
				Lang::txt('COM_SUPPORT_CHANGELOG_FIELD_RESOLUTION'),
				$before->get('resolved', Lang::txt('COM_SUPPORT_UNRESOLVED')),
				$after->get('resolved', Lang::txt('COM_SUPPORT_UNRESOLVED'))
			);
		}*/
		if (intval($after->get('status')) != intval($before->get('status')))
		{
			$this->changed(
				Lang::txt('COM_SUPPORT_CHANGELOG_FIELD_STATUS'),
				$before->status('text'),
				$after->status('text')
			);
		}
		if ($after->get('category') != $before->get('category'))
		{
			$this->changed(
				Lang::txt('COM_SUPPORT_CHANGELOG_FIELD_CATEGORY'),
				$before->get('category', Lang::txt('COM_SUPPORT_BLANK')),
				$after->get('category', Lang::txt('COM_SUPPORT_BLANK'))
			);
		}
		if ($after->get('target_date') != $before->get('target_date'))
		{
			$b = Lang::txt('COM_SUPPORT_BLANK');
			$a = Lang::txt('COM_SUPPORT_BLANK');
			if ($before->get('target_date') != '0000-00-00 00:00:00')
			{
				$b = \Date::of($before->get('target_date'))->toLocal('Y-m-d H:i:s');
			}
			if ($after->get('target_date') != '0000-00-00 00:00:00')
			{
				$a = \Date::of($after->get('target_date'))->toLocal('Y-m-d H:i:s');
			}
			$this->changed(
				Lang::txt('COM_SUPPORT_CHANGELOG_FIELD_TARGET_DATE'),
				$b,
				$a
			);
		}

		if ($after->get('tags') != $before->get('tags'))
		{
			$this->changed(
				Lang::txt('COM_SUPPORT_CHANGELOG_FIELD_TAGS'),
				($before->get('tags') ? $before->get('tags') : Lang::txt('COM_SUPPORT_BLANK')),
				($after->get('tags')  ? $after->get('tags')  : Lang::txt('COM_SUPPORT_BLANK'))
			);
		}

		return $this;
	}

	/**
	 * Output log as a string
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return json_encode($this->_log);
	}
}


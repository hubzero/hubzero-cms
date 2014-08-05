<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Support mdoel for a ticket comment
 */
class SupportModelChangelog extends \Hubzero\Base\Object
{
	/**
	 * \Hubzero\ItemList
	 * 
	 * @var object
	 */
	private $_log = array();

	/**
	 * Log format
	 *
	 * @var string
	 */
	private $_format = 'json';

	/**
	 * Is the question open?
	 * 
	 * @return     boolean
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
							'role'    => 'commentor',
							'name'    => '[none]',
							'address' => trim($matches[1])
						);
					}
					if (preg_match('/E\-mailed ticket ([^ ]+) (.+)/i', $item, $matches))
					{
						$obj = array(
							'role'    => trim($matches[1]),
							'name'    => '[none]',
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
								$obj['after'] = trim($matches[2]);
							}
							else 
							{
								$obj['name'] = trim($matches[1]);
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
								$obj['name'] = '[none]';
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
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $default   The default value.
	 * @return  mixed    The value of the property.
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
	 * @return  mixed  Previous value of the property.
	 */
	public function set($property, $value = null)
	{
		$this->_log[$property] = $value;
		return $this;
	}

	/**
	 * Return a formatted timestamp
	 * 
	 * @param      string $as What format to return
	 * @return     boolean
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
					if ($type == 'changes')
					{
						$clog[] = '<li>' . JText::sprintf('%s changed from "%s" to "%s"', $items['field'], $items['before'], $items['after']) . '</li>';
					}
					else if ($type == 'notifications')
					{
						$clog[] = '<li>' . JText::_('Messaged') . ' (' . $items['role'] . ') ' . $items['name'] . ' - ' . $items['address'] . '</li>';
					}
				}
				$clog[] = '</ul>';
			}
		}
		if (!count($clog))
		{
			$clog[] = '<ul class="changes"><li>' . JText::_('No changes made.') . '</li></ul>';
		}
		return implode("\n", $clog);
	}

	/**
	 * Get a count of or list of attachments on this model
	 * 
	 * @param      string  $rtrn    Data to return state in [count, list]
	 * @param      array   $filters Filters to apply to the query
	 * @param      boolean $clear   Clear data cache?
	 * @return     mixed
	 */
	public function changed($field, $before='', $after='')
	{
		return $this->add('changes', $field, $before, $after);
	}

	/**
	 * Get a count of or list of attachments on this model
	 * 
	 * @param      string  $rtrn    Data to return state in [count, list]
	 * @param      array   $filters Filters to apply to the query
	 * @param      boolean $clear   Clear data cache?
	 * @return     mixed
	 */
	public function cced($field, $before='', $after='')
	{
		return $this->add('cc', $field, $before, $after);
	}

	/**
	 * Get a count of or list of attachments on this model
	 * 
	 * @param      string  $rtrn    Data to return state in [count, list]
	 * @param      array   $filters Filters to apply to the query
	 * @param      boolean $clear   Clear data cache?
	 * @return     mixed
	 */
	public function notified($field, $before='', $after='')
	{
		return $this->add('notifications', $field, $before, $after);
	}

	/**
	 * Get a count of or list of attachments on this model
	 * 
	 * @param      string  $rtrn    Data to return state in [count, list]
	 * @param      array   $filters Filters to apply to the query
	 * @param      boolean $clear   Clear data cache?
	 * @return     mixed
	 */
	public function add($to, $field, $before='', $after='')
	{
		if (!isset($this->_log[$to]))
		{
			throw new InvalidArgumentException(JText::sprintf('Unknown log category of %s', (string) $to));
		}

		$obj = new stdClass();
		$obj->field  = (string) $field;
		$obj->before = (string) $before;
		$obj->after  = (string) $after;

		$this->_log[$to][] = $obj;

		return $this;
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 * 
	 * @param      string  $as      Format to return state in [text, number]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     mixed String or Integer
	 */
	public function remove($from, $field)
	{
		if (!isset($this->_log[$from]))
		{
			throw new InvalidArgumentException(JText::sprintf('Unknown log category of %s', (string) $from));
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
	 * Get the state of the entry as either text or numerical value
	 * 
	 * @return     string
	 */
	public function __toString()
	{
		return json_encode($this->_log);
	}
}


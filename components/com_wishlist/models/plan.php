<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_wishlist' . DS . 'tables' . DS . 'wishlist.plan.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wishlist' . DS . 'models' . DS . 'abstract.php');

/**
 * Courses model class for a forum
 */
class WishlistModelPlan extends WishlistModelAbstract
{
	/**
	 * Table class name
	 *
	 * @var object
	 */
	protected $_tbl_name = 'WishlistPlan';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_wishlist.plan.pagetext';

	/**
	 * ForumModelAttachment
	 *
	 * @var object
	 */
	protected $_attachment = null;

	/**
	 * ForumModelAttachment
	 *
	 * @var object
	 */
	private $_creator = null;

	/**
	 * Constructor
	 *
	 * @param      mixed $oid Integer (ID), string (alias), object or array
	 * @return     void
	 */
	public function __construct($oid=null, $wish=null)
	{
		$this->_db = \JFactory::getDBO();

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (!($this->_tbl instanceof \JTable))
			{
				$this->_logError(
					__CLASS__ . '::' . __FUNCTION__ . '(); ' . \JText::_('Table class must be an instance of JTable.')
				);
				throw new \LogicException(\JText::_('Table class must be an instance of JTable.'));
			}

			if ($oid)
			{
				if (is_numeric($oid) || is_string($oid))
				{
					// Make sure $oid isn't empty
					// This saves a database call
					$this->_tbl->load($oid);
				}
				else if (is_object($oid) || is_array($oid))
				{
					$this->bind($oid);
				}
			}
			else if ($wish)
			{
				if ($plans = $this->_tbl->getPlan($wish))
				{
					$this->bind($plans[0]);
				}
			}
		}
	}

	/**
	 * Returns a reference to a forum post model
	 *
	 * @param      mixed $oid ID (int) or array or object
	 * @return     object ForumModelPost
	 */
	static function &getInstance($oid=null, $wish=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid;
		}
		else if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new self($oid, $wish);
		}

		return $instances[$oid];
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire object
	 *
	 * @param      string $property Property to retrieve
	 * @param      mixed  $default  Default value if property not set
	 * @return     mixed
	 */
	public function creator($property=null, $default=null)
	{
		if (!($this->_creator instanceof \Hubzero\User\Profile))
		{
			$this->_creator = \Hubzero\User\Profile::getInstance($this->get('created_by'));
			if (!$this->_creator)
			{
				$this->_creator = new \Hubzero\User\Profile();
			}
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			if ($property == 'picture')
			{
				return $this->_creator->getPicture($this->get('anonymous'));
			}
			return $this->_creator->get($property, $default);
		}
		return $this->_creator;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $rtrn What data to return
	 * @return     boolean
	 */
	public function created($rtrn='')
	{
		switch (strtolower($rtrn))
		{
			case 'date':
				return JHTML::_('date', $this->get('created'), JText::_('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return JHTML::_('date', $this->get('created'), JText::_('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('created');
			break;
		}
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 *
	 * @param      string  $as      Format to return state in [text, number]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     mixed String or Integer
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('pagetext.parsed', null);

				if ($content == null)
				{
					$config = array(
						'option'   => 'com_wishlist',
						'scope'    => 'wishlist/' . $this->get('wishid'),
						'pagename' => 'wishlist',
						'pageid'   => $this->get('wishid'),
						'filepath' => '',
						'domain'   => $this->get('wishid')
					);

					$this->set('pagetext', stripslashes($this->get('pagetext')));

					$content = $this->get('pagetext');
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('pagetext.parsed', $this->get('pagetext'));
					$this->set('pagetext', $content);

					return $this->content($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('pagetext'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = \Hubzero\Utility\String::truncate($content, $shorten, $options);
		}
		return $content;
	}
}


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

namespace Components\Wishlist\Models;

use Components\Members\Models\Member;
use Hubzero\Utility\String;
use Lang;
use Date;

require_once(__DIR__ . DS . 'base.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'wish' . DS . 'plan.php');

/**
 * Wishlist class for a wish plan model
 */
class Plan extends Base
{
	/**
	 * Table class name
	 *
	 * @var object
	 */
	protected $_tbl_name = '\\Components\\Wishlist\\Tables\\Wish\\Plan';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_wishlist.plan.pagetext';

	/**
	 * Attachment
	 *
	 * @var object
	 */
	protected $_attachment = null;

	/**
	 * User
	 *
	 * @var object
	 */
	private $_creator = null;

	/**
	 * Constructor
	 *
	 * @param   mixed  $oid  Integer (ID), string (alias), object or array
	 * @return  void
	 */
	public function __construct($oid=null, $wish=null)
	{
		$this->_db = \App::get('db');

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (!($this->_tbl instanceof \JTable))
			{
				$this->_logError(
					__CLASS__ . '::' . __FUNCTION__ . '(); ' . Lang::txt('Table class must be an instance of JTable.')
				);
				throw new \LogicException(Lang::txt('Table class must be an instance of JTable.'));
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
	 * Returns a reference to this model
	 *
	 * @param   mixed   $oid  ID (int) or array or object
	 * @return  object
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
	 * @param   string  $property  Property to retrieve
	 * @param   mixed   $default   Default value if property not set
	 * @return  mixed
	 */
	public function creator($property=null, $default=null)
	{
		if (!($this->_creator instanceof \Hubzero\User\User))
		{
			$this->_creator = \User::getInstance($this->get('created_by'));
		}
		if ($property)
		{
			if ($property == 'picture')
			{
				return $this->_creator->picture($this->get('anonymous'));
			}
			return $this->_creator->get($property, $default);
		}
		return $this->_creator;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string   $rtrn  What data to return
	 * @return  boolean
	 */
	public function created($rtrn='')
	{
		switch (strtolower($rtrn))
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
	 * Get the content of the entry in various formats
	 *
	 * @param   string   $as       Format to return state in [text, number]
	 * @param   integer  $shorten  Number of characters to shorten text to
	 * @return  mixed    String or Integer
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
			$content = String::truncate($content, $shorten, $options);
		}
		return $content;
	}
}


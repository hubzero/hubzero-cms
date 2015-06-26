<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Wiki\Models;

use Components\Wiki\Helpers\Parser;
use Components\Wiki\Tables;
use Hubzero\Base\Model;
use Hubzero\Utility\String;
use Request;
use Lang;
use User;
use Date;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'revision.php');

/**
 * Wiki model for a page revision
 */
class Revision extends Model
{
	/**
	 * User object
	 *
	 * @var object
	 */
	private $_creator = null;

	/**
	 * Constructor
	 *
	 * @param   integer $oid     Integer, object, or array
	 * @param   integer $page_id Page ID
	 * @return  void
	 */
	public function __construct($oid, $page_id=0)
	{
		$this->_db = \App::get('db');

		$this->_tbl = new Tables\Revision($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			if ($page_id)
			{
				$this->_tbl->loadByVersion($page_id, $oid);
			}
			else
			{
				$this->_tbl->load($oid);
			}
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}
	}

	/**
	 * Returns a reference to a revision model
	 *
	 * @param   integer $oid     Integer, object, or array
	 * @param   integer $page_id Page ID
	 * @return  object WikiModelRevision
	 */
	static function &getInstance($oid, $page_id=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}
		else
		{
			$key = $oid;
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $page_id);
		}

		return $instances[$key];
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string $as What data to return
	 * @return  boolean
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
	 * it returns the entire user object
	 *
	 * @param   string $property Property to find
	 * @param   mixed  $default  Value to return if property not found
	 * @return  mixed
	 */
	public function creator($property=null, $default=null)
	{
		if (!($this->_creator instanceof \JUser))
		{
			$this->_creator = User::getInstance($this->get('created_by'));
			if (!$this->_creator)
			{
				$this->_creator = new \JUser();
			}
		}
		if ($property)
		{
			return $this->_creator->get($property, $default);
		}
		return $this->_creator;
	}

	/**
	 * Get the content of the record.
	 * Optional argument to determine how content should be handled
	 *
	 * parsed - performs parsing on content (i.e., converting wiki markup to HTML)
	 * clean  - parses content and then strips tags
	 * raw    - as is, no parsing
	 *
	 * @param   string  $as      Format to return content in [parsed, clean, raw]
	 * @param   integer $shorten Number of characters to shorten text to
	 * @return  mixed   String or Integer
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);

		switch ($as)
		{
			case 'parsed':
				if ($this->get('pagetext_parsed'))
				{
					return $this->get('pagetext_parsed');
				}

				$p = Parser::getInstance();

				$wikiconfig = array(
					'option'   => Request::getCmd('option', 'com_wiki'),
					'scope'    => $this->get('scope', Request::getVar('scope')),
					'pagename' => $this->get('pagename', Request::getVar('pagename')),
					'pageid'   => $this->get('pageid'),
					'filepath' => '',
					'domain'   => $this->get('group_cn', Request::getVar('group'))
				);

				$this->set('pagetext_parsed', $p->parse(stripslashes($this->get('pagetext')), $wikiconfig));

				if ($shorten)
				{
					$content = String::truncate($this->get('pagetext_parsed'), $shorten, array('html' => true));

					return $content;
				}

				return $this->get('pagetext_parsed');
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
				if ($shorten)
				{
					$content = String::truncate($content, $shorten);
				}
				return $content;
			break;

			case 'raw':
			default:
				$content = $this->get('pagetext');
				if ($shorten)
				{
					$content = String::truncate($content, $shorten);
				}
				return $content;
			break;
		}
	}
}


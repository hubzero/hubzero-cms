<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Plugins\Antispam\BlackList;

use Plugins\Antispam\BlackList\Table\Word;
use Hubzero\Spam\Detector\DetectorInterface;
use Exception;

include_once(__DIR__ . DS . 'Table' . DS . 'Word.php');

/**
 * Spam detector for black listed words
 */
class Detector implements DetectorInterface
{
	/**
	 * Regex for word detection
	 *
	 * @var  string
	 */
	protected $regex;

	/**
	 * Rebuild the regex?
	 *
	 * @var  bool
	 */
	protected $rebuild = false;

	/**
	 * Holds blacklisted words
	 *
	 * @var  array
	 */
	protected $blackLists = array();

	/**
	 * Holds the file that stores blacklisted words
	 *
	 * @var  null
	 */
	protected $db = null;

	/**
	 * Message
	 *
	 * @var  string
	 */
	protected $message = '';

	/**
	 * Constructor
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		if (isset($options['blackLists']))
		{
			$this->blackLists = $options['blackLists'];
		}

		if (!isset($options['db']))
		{
			$options['db'] = \App::get('db');
		}

		$this->setDbo($options['db']);

		$this->message = '';
	}

	/**
	 * Adds a word/pattern to the black list.
	 * Set the second argument to true to treat
	 * the added word as a regular expression.
	 *
	 * @param   string  $vars   List of blacklisted words
	 * @param   bool    $regex  Flags word as regex pattern
	 * @return  BlackList
	 */
	public function add($vars, $regex = false)
	{
		if (!is_array($vars))
		{
			$vars = array($vars);
		}

		foreach ($vars as $var)
		{
			$this->blackLists[] = $regex ? '[' . $var . ']' : $var;
		}

		return $this;
	}

	/**
	 * Set database connection
	 *
	 * @param   string  $file
	 * @return  object
	 * @throws  Exception
	 */
	public function setDbo($db)
	{
		if (!($db instanceof \Hubzero\Database\Driver))
		{
			throw new Exception('Database object must extend the Hubzero database driver');
		}

		$this->db = $db;

		return $this;
	}

	/**
	 * Get the database
	 *
	 * @return  object
	 */
	public function getDbo()
	{
		return $this->db;
	}

	/**
	 * Set the flag for rebuilding the regex
	 *
	 * @param   boolean  $flag
	 * @return  object
	 */
	public function rebuildRegex($flag)
	{
		$this->rebuild = $flag;

		return $this;
	}

	/**
	 * Checks the text if it contains any word that is blacklisted.
	 *
	 * @param   array  $data
	 * @return  bool
	 */
	public function detect($data)
	{
		// We only need the text from the data
		$text = $data['text'];

		if (!$this->regex || $this->rebuild)
		{
			$dbList = array();

			$tbl = new Word($this->getDbo());
			if ($tbl->getFields())
			{
				$dbList = $tbl->find('array');
			}

			$blackLists = array_merge($this->blackLists, $dbList);

			$this->regex = sprintf('~%s~', implode('|', array_map(function ($value)
			{
				if (isset($value[0]) && $value[0] == '[')
				{
					$value = substr($value, 1, -1);
				}
				else
				{
					$value = preg_quote($value);
				}

				return '(?:' . $value . ')';
			}, $blackLists)));
		}

		return (bool) preg_match($this->regex, $text);
	}

	/**
	 * {@inheritDocs}
	 */
	public function message()
	{
		return $this->message;
	}
}

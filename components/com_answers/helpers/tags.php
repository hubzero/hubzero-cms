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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'helpers' . DS . 'handler.php');

/**
 * Answers Tagging class
 */
class AnswersTags extends TagsHandler
{
	/**
	 * Constructor
	 * 
	 * @param      object $db     JDatabase
	 * @param      array  $config Optional configurations
	 * @return     void
	 */
	public function __construct($db, $config=array())
	{
		$this->_db  = $db;
		$this->_tbl = 'answers';
	}

	/**
	 * Add a tag to an object
	 * This will:
	 * 1) First, check if the tag already exists
	 *    a) if not, creates a database entry for the tag
	 * 2) Adds a reference linking tag with object
	 * 
	 * @param      integer $tagger_id Tagger ID
	 * @param      integer $object_id Object ID
	 * @param      string  $tag       Tag
	 * @param      integer $strength  Tag strength
	 * @return     boolean True on success, false if errors
	 */
	public function safe_tag($tagger_id, $object_id, $tag, $strength=1, $label='', $admintag=0)
	{
		if (!isset($tagger_id) || !isset($object_id) || !isset($tag)) 
		{
			$this->setError('safe_tag argument missing');
			return false;
		}

		if ($this->normalize_tag($tag) === '0') 
		{
			return true;
		}

		$to = new TagsTableObject($this->_db);
		$to->objectid = $object_id;
		$to->tbl = $this->_tbl;
		$to->label = $label;

		// First see if the tag exists.
		$t = new TagsTableTag($this->_db);
		$t->loadTag($t->normalize($tag));
		if (!$t->id) 
		{
			// Add new tag! 
			$t->tag     = $t->normalize($tag);
			$t->raw_tag = addslashes($tag);
			$t->admin   = $admintag;

			if (!$t->store()) 
			{
				$this->setError($t->getError());
				return false;
			}
			if (!$t->id) 
			{
				return false;
			}
			$to->tagid = $t->id;
		} 
		else 
		{
			$to->tagid = $t->id;

			// Check if the object has already been tagged
			if ($to->getCountForObject() > 0) 
			{
				return true;
			}
		}

		// Add an entry linking the tag to the object it was used on
		$to->strength = $strength;
		$to->taggerid = $tagger_id;
		$to->taggedon = date('Y-m-d H:i:s', time());

		if (!$to->store()) 
		{
			$this->setError($to->getError());
			return false;
		}

		return true;
	}
}


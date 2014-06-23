<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2012 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2012 Purdue University. All rights reserved.
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// get all Dublin Core elements based on custom tables and fields for OAIPMH
class TablesOaipmhResult
{
	var $title;
	var $creator;
	var $subject;
	var $date;
	var $identifer;
	var $description;
	var $type;
	var $publisher;
	var $rights;
	var $contributor;
	var $relation;
	var $format;
	var $coverage;
	var $language;
	var $source;

	/**
	 * Constructor
	 * 
	 * @param      object &$db     JDatabase
	 * @param      string $customs
	 * @param      string $id
	 * @return     void
	 */
	public function __construct(&$db, $customs, $id)
	{
		// get element names
		$db->setQuery("SELECT `name` FROM `#__oaipmh_dcspecs` ORDER BY id LIMIT 15");
		if ($elements = $db->loadResultArray())
		{
			// loop through
			for ($x=0; $x<15; $x++)
			{
				$var = $elements[$x];
				// check for hard coded fields
				if (stristr($customs->$var,"SELECT") === false)
				{
					$hard = $customs->$var;
					eval("\$hard = \"$hard\";");
					$this->$var = $hard;
				}
				else
				{
					$SQL = $customs->$var;
					// check for empty SQL 
					if (!empty($SQL))
					{
						// check for DOI as ID
						// TODO: make generic !!
						if (preg_match("{^10\.}", $id))
						{
							$SQL2 = "SELECT publication_id FROM `#__publication_versions` WHERE doi = '$id' AND state = 1";
							$db->setQuery($SQL2);
							$id = $db->loadResult();
						}

						eval("\$SQL = \"$SQL\";");

						$db->setQuery($SQL);
						$db->query();
						$count = $db->getNumRows();
						// check for repeatable entries
						if ($count > 1)
						{
							$this->$var = $db->loadResultArray();
						}
						else
						{
							$this->$var = $db->loadResult();
						}
					}
				}
			}
		}
	}
}
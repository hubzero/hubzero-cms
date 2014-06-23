<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// get all custom SQL statements for OAIPMH
class TablesOaipmhCustom
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
	var $records;
	var $sets;

	/**
	 * Constructor
	 * 
	 * @param      object &$db  JDatabase
	 * @param      string $qset
	 * @return     void
	 */
	public function __construct(&$db, $qset)
	{
		// set custom query for each element
		$db->setQuery("SELECT `query` FROM `#__oaipmh_dcspecs` WHERE display = $qset ORDER BY id");

		if ($dcs = $db->loadResultArray())
		{
			$this->records     = $dcs[0];
			$this->sets        = $dcs[1];
			$this->title       = $dcs[2];
			$this->creator     = $dcs[3];
			$this->subject     = $dcs[4];
			$this->date        = $dcs[5];
			$this->identifier  = $dcs[6];
			$this->description = $dcs[7];
			$this->type        = $dcs[8];
			$this->publisher   = $dcs[9];
			$this->rights      = $dcs[10];
			$this->contributor = $dcs[11];
			$this->relation    = $dcs[12];
			$this->format      = $dcs[13];
			$this->coverage    = $dcs[14];
			$this->language    = $dcs[15];
			$this->source      = $dcs[16];
		}
	}
}
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * HTML helper for the billboards component
 */
class BillboardsHtml
{
	/**
	 * Build a select list of the collections available
	 *
	 * @param  $collection_id of currently selected collection
	 * @return $clist
	 */
	public static function buildCollectionsList($collection_id)
	{
		$clist    = array();
		$filters  = array('limit'=>'100', 'start'=>'0');
		$selected = '';

		$database = JFactory::getDBO();

		$collection  = new BillboardsCollection($database);
		$collections = $collection->getRecords($filters);

		// Go through all the collections and add a select option for each
		foreach($collections as $collection)
		{
			$options[] = JHTML::_('select.option', $collection->id, $collection->name, 'value', 'text');
			if ($collection->id == $collection_id)
			{
				$selected = $collection->id;
			}
		}
		$clist = JHTML::_('select.genericlist', $options, 'billboard[collection_id]', '', 'value', 'text', $selected, 'billboardcollection', false, false);

		return $clist;
	}

	/**
	 * Build the learn more locations select list
	 *
	 * @param  $currentlocation of learn more link
	 * @return $learnmorelocation
	 */
	public static function buildLearnMoreList($currentlocation)
	{
		$locations = array();
		$locations[] = JHTML::_('select.option', 'topleft', JText::_('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_TOP_LEFT'), 'value', 'text');
		$locations[] = JHTML::_('select.option', 'topright', JText::_('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_TOP_RIGHT'), 'value', 'text');
		$locations[] = JHTML::_('select.option', 'bottomleft', JText::_('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_BOTTOM_LEFT'), 'value', 'text');
		$locations[] = JHTML::_('select.option', 'bottomright', JText::_('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_BOTTOM_RIGHT'), 'value', 'text');
		$locations[] = JHTML::_('select.option', 'relative', JText::_('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_RELATIVE'), 'value', 'text');

		$lselected = $currentlocation;

		$learnmorelocation = JHTML::_('select.genericlist', $locations, 'billboard[learn_more_location]', '', 'value', 'text', $lselected, 'billboardlearnmorelocation', false, false);

		return $learnmorelocation;
	}
}
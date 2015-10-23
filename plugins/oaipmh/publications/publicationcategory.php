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

defined('JPATH_PLATFORM') or die;

\JFormHelper::loadFieldClass('list');

/**
 * Renders a list of support ticket statuses
 */
class JFormFieldPublicationcategory extends \JFormFieldList
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	public $type = 'Publicationcategory';

	/**
	 * Method to get the field options for category
	 * Use the extension attribute in a form to specify the.specific extension for
	 * which categories should be displayed.
	 * Use the show_root attribute to specify whether to show the global category root in the list.
	 *
	 * @return  array    The field option objects.
	 */
	protected function getOptions()
	{
		$options = array();

		$options[] =  Html::select('option', '0', Lang::txt('All'));

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'category.php');

		$db = JFactory::getDBO();
		$sr = new PublicationCategory($db);

		$types = $sr->getCategories();

		foreach ($types as $anode)
		{
			$options[] = JHtml::_('select.option', $anode->id, stripslashes($anode->name));
		}

		return $options;
	}
}

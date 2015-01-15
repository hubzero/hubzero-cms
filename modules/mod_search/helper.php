<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 * All rights reserved.
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

namespace Modules\Search;

use Hubzero\Module\Module;
use JFactory;
use JURI;
use JText;
use JRequest;
use JRoute;

/**
 * Module class for displaying a search form
 */
class Helper extends Module
{
	/**
	 * Display the search form
	 *
	 * @return  void
	 */
	public function display()
	{
		if ($this->params->get('opensearch', 0))
		{
			$ostitle = $this->params->get('opensearch_title', JText::_('MOD_SEARCH_SEARCHBUTTON_TEXT') . ' ' . JFactory::getApplication()->getCfg('sitename'));

			JFactory::getDocument()->addHeadLink(
				JURI::getInstance()->toString(array('scheme', 'host', 'port')) . JRoute::_('&option=com_search&format=opensearch'), 
				'search',
				'rel',
				array('title' => htmlspecialchars($ostitle), 'type' => 'application/opensearchdescription+xml')
			);
		}

		//$upper_limit = JFactory::getLanguage()->getUpperLimitSearchWord();
		//$maxlength   = $upper_limit;

		$params          = $this->params;
		$button          = $this->params->get('button', '');
		$button_pos      = $this->params->get('button_pos', 'right');
		$button_text     = htmlspecialchars($this->params->get('button_text', JText::_('MOD_SEARCH_SEARCHBUTTON_TEXT')));
		$width           = intval($this->params->get('width', 20));
		$text            = htmlspecialchars($this->params->get('text', JText::_('MOD_SEARCH_SEARCHBOX_TEXT')));
		$label           = htmlspecialchars($this->params->get('label', JText::_('MOD_SEARCH_LABEL_TEXT')));
		$moduleclass_sfx = htmlspecialchars($this->params->get('moduleclass_sfx'));

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}

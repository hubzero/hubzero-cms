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
 * @author    Sam Wilson <samwilson@purdue.edu
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Hubs model for time component
 */
class TimeModelHub extends \Hubzero\Base\Model
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'TimeHubs';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_time.hub.notes';

	/**
	 * Get the content of the entry
	 *
	 * @param      string  $as      Format to return state in [text, number]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     string
	 */
	public function notes($as='parsed', $shorten=0)
	{
		$as = strtolower($as);

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('notes.parsed');
				if (isset($content))
				{
					if ($shorten)
					{
						$content = \Hubzero\Utility\String::truncate($content, $shorten, array('html' => true));
					}
					return $content;
				}

				$scope  = JHTML::_('date', $this->get('publish_up'), 'Y') . '/';
				$scope .= JHTML::_('date', $this->get('publish_up'), 'm');

				$config = array(
					'option'   => 'com_time',
					'scope'    => 'time',
					'pagename' => 'hubs',
					'pageid'   => $this->get('id'),
					'filepath' => '',
					'domain'   => $this->get('id')
				);

				$content = stripslashes($this->get('notes'));
				$this->importPlugin('content')->trigger('onContentPrepare', array(
					$this->_context,
					&$this,
					&$config
				));

				$this->set('notes.parsed', $this->get('notes'));
				$this->set('notes', $content);

				return $this->notes($as, $shorten);
			break;

			case 'clean':
				$content = strip_tags($this->notes('parsed'));
				if ($shorten)
				{
					$content = \Hubzero\Utility\String::truncate($content, $shorten);
				}
				return $content;
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('notes'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
				if ($shorten)
				{
					$content = \Hubzero\Utility\String::truncate($content, $shorten);
				}
				return $content;
			break;
		}
	}
}
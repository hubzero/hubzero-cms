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
 * @since     Class available since release 1.3.2
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Hubs database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Hub extends \Hubzero\Database\Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'time';

	/**
	 * Default order by for model
	 *
	 * @var string
	 **/
	public $orderBy = 'name';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'name'    => 'notempty',
		'liaison' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var array
	 **/
	public $always = array(
		'name_normalized',
		'asset_id'
	);

	/**
	 * Generates automatic owned by field value
	 *
	 * @param  array $data the data being saved
	 * @return int
	 * @since  1.3.2
	 **/
	public function automaticNameNormalized($data)
	{
		return strtolower(str_replace(" ", "", $data['name']));
	}

	/**
	 * Defines a one to many relationship with tasks
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function tasks()
	{
		return $this->oneToMany('Task');
	}

	/**
	 * Defines a one to many through relationship with records by way of tasks
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function records()
	{
		return $this->oneToManyThrough('Record', 'Task');
	}

	/**
	 * Defines a one to many relationship with hub contacts
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function contacts()
	{
		return $this->oneToMany('Contact');
	}

	/**
	 * Returns sum of hours for the hub
	 *
	 * @return float
	 * @since  1.3.2
	 **/
	public function helperTotalHours()
	{
		$time = $this->records()->select('SUM(time)', 'time')->rows()->first()->time;
		return $time ? $time : 0;
	}

	/**
	 * Gets the content of the notes entry
	 *
	 * @param  string  $as      Format to return state in [text, number]
	 * @param  integer $shorten Number of characters to shorten text to
	 * @return string
	 * @since  1.3.2
	 */
	public function transformNotes($as='parsed', $shorten=0)
	{
		$as = strtolower($as);

		switch ($as)
		{
			case 'parsed':
				$content = isset($this->_notesParsed) ? $this->_notesParsed : null;
				if (isset($content))
				{
					if ($shorten)
					{
						$content = \Hubzero\Utility\String::truncate($content, $shorten, array('html' => true));
					}
					return $content;
				}

				$config = array(
					'option'   => 'com_time',
					'scope'    => 'time',
					'pagename' => 'hubs',
					'pageid'   => $this->id,
					'filepath' => '',
					'domain'   => $this->id
				);

				$object  = new \Hubzero\Base\Object;
				$object->set('notes', stripslashes($this->get('notes')));

				\JPluginHelper::importPlugin('content');
				\JDispatcher::getInstance()->trigger('onContentPrepare', array(
					'com_time.hub.notes',
					&$object,
					&$config
				));

				$this->_notesParsed = $object->get('notes');

				return $this->notes($as, $shorten);
			break;

			case 'raw':
			default:
				$content = '';

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
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
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* Object Asset handler class
*/
class ObjectAssetHandler extends ContentAssetHandler
{
	/**
	 * Class info
	 *
	 * Action message - what the user will see if presented with multiple handlers for this extension
	 * Responds to    - what extensions this handler responds to
	 *
	 * @var array
	 **/
	protected static $info = array(
			'action_message' => 'As a HTML embed object',
			'responds_to'    => array('object')
		);

	/**
	 * Create method for this handler
	 *
	 * @return array of assets created
	 **/
	public function create()
	{
		$object = JRequest::getVar('content', '', 'post', 'string', JREQUEST_ALLOWRAW);

		// Check if valid youtube or kaltura video
		// @FIXME: we need a safer way!
		if (preg_match('/<iframe(.*?)src="([^"]+)"([^>]*)>(.*?)<\/iframe>/si', $object, $matches))
		{
			if (stristr($matches[2], 'youtube'))
			{
				$this->asset['title'] = 'New YouTube video';
			}
			else if (stristr($matches[2], 'vimeo'))
			{
				$this->asset['title'] = 'New Vimeo video';
			}
			else if (stristr($matches[2], 'blip'))
			{
				$this->asset['title'] = 'New Blip.tv video';
			}
			else if (stristr($matches[2], 'kaltura'))
			{
				$this->asset['title'] = 'New Kaltura video';
			}
		}
		elseif (preg_match('/\<script[\s]+(type="text\/javascript")?[\s]*src="http[s]*\:\/\/cdnapi(sec)?\.kaltura\.com/is', $object))
		{
			$this->asset['title'] = 'New Kaltura video';
		}
		else
		{
			return array('error'=>'Content did not match the pre-defined filter for an object');
		}

		$this->asset['type']    = (!empty($this->asset['type'])) ? $this->asset['type'] : 'video';
		$this->asset['subtype'] = (!empty($this->asset['subtype'])) ? $this->asset['subtype'] : 'embedded';
		$this->asset['content'] = $object;

		// Return info
		return parent::create();
	}
}
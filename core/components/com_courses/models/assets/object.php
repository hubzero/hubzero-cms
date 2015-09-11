<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Assets;

use Request;

/**
* Object Asset handler class
*/
class Object extends Content
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
		$object = Request::getVar('content', '', 'post', 'string', JREQUEST_ALLOWRAW);

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
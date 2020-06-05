<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Assets;

use Request;

/**
 * Object Asset handler class
 *
 * Note: Renamed because 'Object' is a reserved word in PHP 7
 */
class Objct extends Content
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
		$object = Request::getString('content', '', 'post');

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
			return array('error' => 'Content did not match the pre-defined filter for an object');
		}

		$this->asset['type']    = (!empty($this->asset['type'])) ? $this->asset['type'] : 'video';
		$this->asset['subtype'] = (!empty($this->asset['subtype'])) ? $this->asset['subtype'] : 'embedded';
		$this->asset['content'] = $object;

		// Return info
		return parent::create();
	}
}

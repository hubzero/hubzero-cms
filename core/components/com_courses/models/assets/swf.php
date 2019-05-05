<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Assets;

/**
 * Video Asset handler class
 */
class Swf extends File
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
		'action_message' => 'As an Adobe Shockwave Flash file',
		'responds_to'    => array('swf'),
	);

	/**
	 * Create method for this handler
	 *
	 * @return array of assets created
	 **/
	public function create($localPath = null)
	{
		// Set the asset type to video
		$this->asset['type']    = 'file';
		$this->asset['subtype'] = 'swf';

		// Call the primary create method on the file asset handler
		return parent::create($localPath);
	}
}

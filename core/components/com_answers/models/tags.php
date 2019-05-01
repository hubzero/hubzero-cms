<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Answers\Models;

use Components\Tags\Models\Cloud;

require_once \Component::path('com_tags') . DS . 'models' . DS . 'cloud.php';

/**
 * Answers Tagging class
 */
class Tags extends Cloud
{
	/**
	 * Object type, used for linking objects (such as resources) to tags
	 *
	 * @var  string
	 */
	protected $_scope = 'answers';

	/**
	 * Turn a comma-separated string of tags into an array of normalized tags
	 *
	 * @param   string   $tag_string  Comma-separated string of tags
	 * @param   integer  $keep        Use normalized tag as array key
	 * @return  array
	 */
	public function parse($tag_string, $keep=0)
	{
		return $this->_parse($tag_string, $keep);
	}
}

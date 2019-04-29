<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

/**
 * Wiki macro class for dipslaying a tag
 */
class Tag extends Macro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $allowPartial = true;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'This macro will generate a link to a Tag.';
		$txt['html'] = '<p>This macro will generate a link to a Tag.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		$tag = $this->args;

		if ($tag)
		{
			// Perform query
			$this->_db->setQuery("SELECT raw_tag FROM `#__tags` WHERE tag=" . $this->_db->quote($tag) . " LIMIT 1");
			$a = $this->_db->loadResult();

			// Did we get a result from the database?
			if ($a)
			{
				// Build and return the link
				return '<a href="' . \Route::url('index.php?option=com_tags&tag=' . $tag) . '">' . stripslashes($a) . '</a>';
			}
			else
			{
				// Return error message
				return '(' . $tag . ' not found)';
			}
		}
		else
		{
			return '';
		}
	}
}

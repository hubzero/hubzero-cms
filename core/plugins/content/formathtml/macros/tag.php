<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
				// raw_tag is whatever the user who created the tag typed, and the
				// macro argument is raw wiki source -- the parser hands args
				// through unescaped, so both are sinks here.
				return '<a href="' . \Route::url('index.php?option=com_tags&tag=' . $tag) . '">' . htmlspecialchars(stripslashes((string) $a), ENT_QUOTES, 'UTF-8') . '</a>';
			}
			else
			{
				// Return error message
				return '(' . htmlspecialchars((string) $tag, ENT_QUOTES, 'UTF-8') . ' not found)';
			}
		}
		else
		{
			return '';
		}
	}
}

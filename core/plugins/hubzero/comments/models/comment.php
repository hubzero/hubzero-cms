<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Hubzero\Comments\Models;

use Hubzero\Item\Comment as ItemComment;

require_once __DIR__ . DS . 'file.php';

/**
 * Model for a comment
 */
class Comment extends ItemComment
{
	/**
	 * Flagged state
	 *
	 * @var  integer
	 */
	const STATE_FLAGGED = 3;

	/**
	 * URL for this entry
	 *
	 * @var string
	 */
	private $_base = null;

	/**
	 * Get a list of files
	 *
	 * @return  object
	 */
	public function files()
	{
		return $this->oneToMany('Plugins\Hubzero\Comments\Models\File', 'comment_id');
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type The type of link to return
	 * @return     string
	 */
	public function link($type='')
	{
		if (!isset($this->_base))
		{
			$this->_base = $this->get('url', 'index.php?option=com_' . $this->get('item_type') . '&id=' . $this->get('item_id') . '&active=comments');
		}
		$link = $this->_base;

		// check for page slug  (remove for now)
		$slug = '';
		if (strpos($link, '#') !== false)
		{
			list($link, $slug) = explode('#', $link);
			$slug = "#{$slug}";
		}

		$s = '&';
		if (strstr($link, '?') === false)
		{
			$s = '?';
		}

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
				$link .= $slug;
			break;

			case 'edit':
				$link .= $s . 'commentedit=' . $this->get('id') . $slug;
			break;

			case 'delete':
				$link .= $s . 'action=commentdelete&comment=' . $this->get('id') . $slug;
			break;

			case 'reply':
				$link .= $s . 'commentreply=' . $this->get('id') . '#c' . $this->get('id');
			break;

			case 'abuse':
			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=itemcomment&id=' . $this->get('id') . '&parent=' . $this->get('parent');
			break;

			case 'permalink':
			default:
				$link .= '#c' . $this->get('id');
			break;
		}

		return $link;
	}
}

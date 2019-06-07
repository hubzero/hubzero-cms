<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Hubzero\Item\Comment as ItemComment;
use Components\Courses\Models\Course;

/**
 * Courses model for a comment
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
	 * @var  string
	 */
	private $_base = null;

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
	 */
	public function link($type='')
	{
		if (!isset($this->_base))
		{
			if (!$this->get('course'))
			{
				$course = Course::getInstance($this->get('item_id'));
				$this->set('course', $course->get('alias'));
			}
			$this->_base = 'index.php?option=com_courses&gid=' . $this->get('course') . '&active=reviews';
		}
		$link = $this->_base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&action=edit&comment=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&action=delete&comment=' . $this->get('id');
			break;

			case 'reply':
				$link .= '&action=reply&comment=' . $this->get('id');
			break;

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=itemcomment&id=' . $this->get('id') . '&parent=' . $this->get('item_id');
			break;

			case 'permalink':
			default:
				$link .= '#c' . $this->get('id');
			break;
		}

		return $link;
	}
}

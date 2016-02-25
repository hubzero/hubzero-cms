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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Hubzero\Comments\Models;

use Hubzero\Item\Comment as ItemComment;
use Hubzero\User\Profile;
use Hubzero\Utility\String;
use Hubzero\Item\Comment\File;
use Hubzero\Item\Vote;

include __DIR__ . DS . 'file.php';

/**
 * Model for a comment
 */
class Comment extends ItemComment
{
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

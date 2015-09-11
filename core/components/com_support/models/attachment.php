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

namespace Components\Support\Models;

use Hubzero\Base\Model;
use Component;
use Request;
use Route;
use Lang;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'attachment.php');

/**
 * Support mdoel for a ticket resolution
 */
class Attachment extends Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Support\\Tables\\Attachment';

	/**
	 * URL for this entry
	 *
	 * @var string
	 */
	private $_base = 'index.php?option=com_support';

	/**
	 * File size
	 *
	 * @var string
	 */
	private $_size = null;

	/**
	 * Diemnsions for file (must be an image)
	 *
	 * @var array
	 */
	private $_dimensions = null;

	/**
	 * Scan text for attachment macros {attachment#}
	 *
	 * @param   string $text Text to search
	 * @return  string HTML
	 */
	public function parse($text)
	{
		return preg_replace_callback('/\{attachment#[0-9]*\}/sU', array(&$this,'getAttachment'), $text);
	}

	/**
	 * Process an attachment macro and output a link to the file
	 *
	 * @param   array $matches Macro info
	 * @return  string HTML
	 */
	public function getAttachment($matches)
	{
		$match = $matches[0];
		$tokens = explode('#', $match);
		$id = intval(end($tokens));

		$this->_tbl->load($id);

		if ($this->output != 'web' && $this->output != 'email')
		{
			return $this->link();
		}

		if (is_file($this->link('filepath')))
		{
			$url = $this->link();

			if ($this->output != 'email' && $this->isImage())
			{
				$size = getimagesize($this->link('filepath'));
				if ($size[0] > 400)
				{
					$img = '<a href="' . $url . '"><img src="' . $url . '" alt="' . $this->get('description') . '" width="400" /></a>';
				}
				else
				{
					$img = '<img src="' . $url . '" alt="' . $this->get('description') . '" />';
				}
				return $img;
			}
			else
			{
				return '<a href="' . $url . '" title="' . $this->get('description') . '">' . $this->get('description', $this->get('filename')) . '</a>';
			}
		}

		return '[attachment #' . $id . ' not found]';
	}

	/**
	 * Is the file an image?
	 *
	 * @return  boolean
	 */
	public function isImage()
	{
		return preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $this->get('filename'));
	}

	/**
	 * Does the file exist on the server?
	 *
	 * @return  boolean
	 */
	public function hasFile()
	{
		return file_exists($this->link('filepath'));
	}

	/**
	 * Get the file size
	 *
	 * @return  integer
	 */
	public function size()
	{
		if ($this->_size === null)
		{
			$this->_size = 0;
			if ($this->hasFile())
			{
				$this->_size = filesize($this->link('filepath'));
			}
		}

		return $this->_size;
	}

	/**
	 * Get image width
	 *
	 * @return  integer
	 */
	public function width()
	{
		if (!$this->_dimensions)
		{
			$this->_dimensions = $this->isImage() && $this->hasFile() ? getimagesize($this->link('filepath')) : array(0, 0);
		}

		return $this->_dimensions[0];
	}

	/**
	 * Get image height
	 *
	 * @return  integer
	 */
	public function height()
	{
		if (!$this->_dimensions)
		{
			$this->_dimensions = $this->isImage() && $this->hasFile() ? getimagesize($this->link('filepath')) : array(0, 0);
		}

		return $this->_dimensions[1];
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon type desired
	 *
	 * @param   string   $type      The type of link to return
	 * @param   boolean  $absolute  Get the URL absolute to the domain?
	 * @return  string
	 */
	public function link($type='', $absolute=false)
	{
		static $path;

		$link = $this->_base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
			case 'component':
				return $this->_base;
			break;

			case 'filepath':
				if (!$path)
				{
					$config = Component::params('com_support');
					$path = PATH_APP . DS . trim($config->get('webpath', '/site/tickets'), DS);
				}
				return $path . DS . $this->get('ticket') . DS . $this->get('filename');
			break;

			case 'permalink':
			default:
				$link .= '&task=download&id=' . $this->get('id') . '&file=' . $this->get('filename');
			break;
		}

		if ($absolute)
		{
			$link = rtrim(Request::base(), '/') . '/' . trim(Route::url($link), '/');
		}

		return $link;
	}

	/**
	 * Delete record and associated file
	 *
	 * @return  boolean
	 */
	public function delete()
	{
		if (!parent::delete())
		{
			return false;
		}

		if ($this->hasFile())
		{
			$file = $this->get('filename');
			$path = $this->link('filepath');

			if (!\Filesystem::delete($path))
			{
				$this->setError(Lang::txt('COM_SUPPORT_ERROR_UNABLE_TO_DELETE_FILE', $file));
				return false;
			}
		}

		return true;
	}
}


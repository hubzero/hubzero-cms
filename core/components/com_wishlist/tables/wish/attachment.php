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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Tables\Wish;

use Route;
use Lang;

/**
 * Table class for wish attachments
 */
class Attachment extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wish_attachments', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (!$this->wish)
		{
			$this->setError(Lang::txt('Error: wish not found.'));
		}

		$this->filename = trim($this->filename);
		if ($this->filename == '')
		{
			$this->setError(Lang::txt('Error: attachment not found.'));
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Get the ID for the record matching all specified columns
	 *
	 * @return  void
	 */
	public function getID()
	{
		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE filename=" . $this->_db->quote($this->filename) . " AND description=" . $this->_db->quote($this->description) . " AND wish=" . $this->_db->quote($this->wish));
		$id = $this->_db->loadResult();
		$this->id = $id;
	}

	/**
	 * Look for attachment string and replace with file/link
	 *
	 * @param   string  $text  Text to parse
	 * @return  string
	 */
	public function parse($text)
	{
		return preg_replace_callback('/{attachment#[0-9]*}/sU', array(&$this,'getAttachment'), $text);
	}

	/**
	 * Find a record and generate a linkt o the file
	 *
	 * @param   array   $matches  preg_replace_callback matches
	 * @return  string  HTML
	 */
	public function getAttachment($matches)
	{
		$match = $matches[0];

		if (!$match)
		{
			return '';
		}

		$tokens = explode('#', $match);
		$id = intval(end($tokens));

		$this->_db->setQuery(
			"SELECT a.filename, a.description, a.wish, l.category, l.referenceid
			FROM `$this->_tbl` AS a
			JOIN `#__wishlist_item` AS i ON i.id=a.wish
			JOIN `#__wishlist` AS l ON l.id=i.wishlist
			WHERE a.id=" . $id
		);
		$a = $this->_db->loadRow();

		if ($this->output == 'web')
		{
			if (is_file($this->uppath . DS . $a[0]))
			{
				$path = rtrim(Route::url('index.php?option=com_wishlist&task=wish&category=' . $a[3] . '&rid=' . $a[4] . '&wishid=' . $a[2]), DS);

				if (preg_match("/bmp|gif|jpg|jpe|jpeg|tif|tiff|png/i", $a[0]))
				{
					$size = getimagesize($this->uppath . DS . $a[0]);
					if ($size[0] > 300)
					{
						$img = '<a href="' . $path . '/' . $a[0] . '" rel="lightbox" title="' . $a[1] . '"><img src="' . $path . '/' . $a[0] . '" alt="' . $a[1] . '" width="300" /></a>';
					}
					else
					{
						$img = '<img src="' . $path . '/' . $a[0] . '" alt="' . $a[1] . '" />';
					}
					$this->description = $img;
				}
				else
				{
					$html  = '<a href="' . $path . '/' . $a[0] . '" title="' . $a[1] . '">';
					$html .= ($a[1]) ? $a[1] : $a[0];
					$html .= '</a>';
					$this->description = $html;
				}
			}
			else
			{
				$this->description = ''; //'[attachment #' . $id . ' not found]';
			}
		}
		else
		{
			$this->description = $this->webpath . '/' . $a[0];
		}

		return '';
	}

	/**
	 * Remove a record
	 *
	 * @param   string   $filename  File to remove
	 * @param   integer  $wish      Wish ID
	 * @return  mixed    String if error, True otherwise
	 */
	public function deleteAttachment($filename, $wish)
	{
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE filename=" . $this->_db->quote($filename) . " AND wish=" . $this->_db->quote($wish));
		if (!$this->_db->query())
		{
			return $this->_db->getErrorMsg();
		}
		return true;
	}

	/**
	 * Get a record based off of filename and wish ID
	 *
	 * @param   string   $filename  File name
	 * @param   integer  $wish      Wish ID
	 * @return  mixed    False if error, object otherwise
	 */
	public function loadAttachment($filename=NULL, $wish=NULL)
	{
		if ($filename === NULL || $wish === NULL)
		{
			return false;
		}

		$fields = array(
			'filename' => $filename,
			'wish'     => $wish
		);

		return parent::load($fields);
	}
}


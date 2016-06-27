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
 * @author 		Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Utility\Sanitize;
use Components\Support\Models\Orm\Ticket;

require_once Component::path('com_support') . DS . 'models' . DS . 'orm' . DS . 'ticket.php';

class plgSearchTickets extends \Hubzero\Plugin\Plugin
{
	/****************************
	Query-time / General Methods
	****************************/

	/**
	 * onGetTypes - Announces the available hubtype
	 *
	 * @param mixed $type
	 * @access public
	 * @return void
	 */
	public function onGetTypes($type = null)
	{
		// The name of the hubtype
		$hubtype = 'ticket';

		if (isset($type) && $type == $hubtype)
		{
			return $hubtype;
		}
		elseif (!isset($type))
		{
			return $hubtype;
		}
	}

	/**
	 * onGetModel 
	 * 
	 * @param string $type 
	 * @access public
	 * @return void
	 */
	public function onGetModel($type = '')
	{
		if ($type == 'ticket')
		{
			return new Ticket;
		}
	}

	/*********************
		Index-time methods
	*********************/
	/**
	 * onProcessFields - Set SearchDocument fields which have conditional processing
	 *
	 * @param mixed $type 
	 * @param mixed $row
	 * @access public
	 * @return void
	 */
	public function onProcessFields($type, $row, &$db)
	{
		if ($type == 'ticket')
		{
			// Instantiate new $fields object
			$fields = new stdClass;

			// Get data needed from comments
			$comments = array();
			$owners = array();

			$cRows = $row->comments()->rows();
			foreach ($cRows as $comment)
			{
				// Strips out some nasty stuff
				$cleaned = strip_tags($comment->comment);
				$cleaned = str_replace("\n", '', $cleaned);
				$cleaned = str_replace("\r", '', $cleaned);
				$cleaned = preg_replace("/[^a-zA-Z0-9\s]/", "", $cleaned);

				array_push($comments, $cleaned);
				array_push($owners, $comment->created_by);
			}

			$fields->comments = $comments;

			// Format the date for SOLR
			$date = Date::of($row->created)->format('Y-m-d');
			$date .= 'T';
			$date .= Date::of($row->created)->format('h:m:s') . 'Z';

			// Look up ID by username
			$authorIDquery = "SELECT id, name FROM #__users WHERE username = '" . $row->login . "';";
			$db->setQuery($authorIDquery);
			$author = $db->query()->loadAssoc();

			// Valid?
			if (isset($author['id']) && $author['id'] != 0)
			{
				array_push($owners, $author['id']);
			}

			// Clean up the title
			$title = $row->summary;
			$title = trim(preg_replace('/\s+/', ' ', $title));
			$fields->title = $title;

			$fields->date = $date;
			$fields->fulltext = $row->report;
			$fields->author = isset($author['name']) ? $author['name'] : '';
			$fields->access_level = "private";
			$fields->url = '/support/ticket/' . $row->id;
			$fields->owner_type = 'user';
			$fields->owner = $owners;

			return $fields;
		}
	}
}



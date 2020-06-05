<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Utility\Sanitize;

/**
 * Search ticket object?
 */
class plgSearchTickets extends \Hubzero\Plugin\Plugin
{
	/**
	 * onGetTypes - Announces the available hubtype
	 * 
	 * @param   mixed   $type 
	 * @access  public
	 * @return  void
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
	 * onIndex 
	 * 
	 * @param   string   $type
	 * @param   integer  $id 
	 * @param   boolean  $run 
	 * @access  public
	 * @return  void
	 */
	public function onIndex($type, $id, $run = false)
	{
		if ($type == 'ticket')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT * FROM `#__support_tickets` WHERE id={$id};";
				$row = $db->setQuery($sql)->query()->loadObject();

				// Get the name of the author
				$sql1 = "SELECT name FROM `#__users` WHERE username={$row->login};";
				$author = $db->setQuery($sql1)->query()->loadResult();
				if (!$author)
				{
					$sql1 = "SELECT name FROM `#__users` WHERE email={$row->email};";
					$author = $db->setQuery($sql1)->query()->loadResult();
				}
				if (!$author)
				{
					$author = $row->name;
				}

				// Get any tags
				$sql2 = "SELECT tag 
					FROM #__tags
					LEFT JOIN #__tags_object
					ON #__tags.id=#__tags_object.tagid
					WHERE #__tags_object.objectid = {$id} AND #__tags_object.tbl = 'blog';";
				$tags = $db->setQuery($sql2)->query()->loadColumn();

				// Determine the path
				$path = '/support/ticket/' . $row->id;

				// Default private
				$access_level = 'private';

				$owner_type = 'user';
				$owner = $row->login;

				// Get the title
				$title = $row->summary;

				// Build the description, clean up text
				$content = preg_replace('/<[^>]*>/', ' ', $row->report);
				$content = preg_replace('/ {2,}/', ' ', $content);
				$description = \Hubzero\Utility\Sanitize::stripAll($content);

				// Create a record object
				$record = new \stdClass;
				$record->id = $type . '-' . $id;
				$record->hubtype = $type;
				$record->title = $title;
				$record->description = $description;
				$record->author = array($author);
				$record->tags = $tags;
				$record->path = $path;
				$record->access_level = $access_level;
				$record->owner = $owner;
				$record->owner_type = $owner_type;

				// Return the formatted record
				return $record;
			}
			else
			{
				$db = App::get('db');
				$sql = "SELECT id FROM `#__support_tickets`;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return $ids;
			}
		}
	}
}

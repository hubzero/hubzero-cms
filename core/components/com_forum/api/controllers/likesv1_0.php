<?php
/**
 * @package	hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license	http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Api\Controllers;

use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Component;
use Exception;
use stdClass;
use Request;
use Config;
use Event;
use Route;
use Lang;
use User;

/**
 * API controller class for forum posts
 */
class Likesv1_0 extends ApiController 
{
	/**
	 * GET: List ALL the likes from table
	 */
	// https://woo.aws.hubzero.org/api/forum/likes/list
	public function listTask() 
	{
		$database = \App::get('db');
		$query = "SELECT * FROM `#__forum_posts_like`";
		$database->setQuery($query);
		$rows = $database->loadObjectList();

		// return results
		$object = new stdClass();
		$object->assertions = $rows;

		$this->send($object);
	}

	/**
	 * POST: Create a like for a forum post
	 */
	public function addLikeToPostTask() 
	{
		$threadId = Request::getString('threadId');
		$postId  = Request::getString('postId');
		$userId = Request::getString('userId');
		$created = Date::of('now')->toSql();

		if (!userId || (userId === 0))
		{
			throw new Exception("Please sign into post a Like", 404);
		}

		$db = \App::get('db');
		$insertQuery = "INSERT INTO `#__forum_posts_like` (`threadId`, `postId`, `userId`, `created`)
		  VALUES (?,?,?,?)";

		$insertVars = array($threadId, $postId, $userId, $created);
		$db->prepare($insertQuery);
		$db->bind($insertVars);
		$insertResult = $db->execute();

		$this->send($insertResult);
	}

	// DELETE: Delete a like from a post
	public function deleteLikeFromPostTask() 
	{
		$threadId = Request::getString('threadId');
		$postId  = Request::getString('postId');
		$userId = Request::getString('userId');

		// Open up the database tables
		$db = \App::get('db');

		$deleteQuery = "DELETE FROM `#__forum_posts_like` WHERE threadId = ? AND postId = ? AND userId = ?";
		$deleteVars = array($threadId, $postId, $userId);
		$db->prepare($deleteQuery);
		$db->bind($deleteVars);
		$deleteResult = $db->execute();

		$this->send($deleteResult);
	}
}


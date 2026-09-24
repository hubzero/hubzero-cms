<?php
/**
 * @package	hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license	http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Api\Controllers;

use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Components\Forum\Models\Post;
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
		// The whole table names who liked what, private group forums
		// included; nothing on the site reads it (the thread views count
		// likes themselves), so keep it to forum managers.
		$this->requiresAuthentication();

		if (!User::authorise('core.manage', 'com_forum'))
		{
			throw new Exception(Lang::txt('JERROR_ALERTNOAUTHOR'), 403);
		}

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
		$this->requiresAuthentication();

		$threadId = Request::getString('threadId');
		$postId  = Request::getString('postId');
		$userId = \App::get('authn')['user_id'];
		$created = Date::of('now')->toSql();

		if (!$userId)
		{
			throw new Exception("Please sign into post a Like", 404);
		}

		// Only a published post the caller can see, and only once per user.
		$post = Post::oneOrNew((int) $postId);

		$access = User::getAuthorisedViewLevels();
		if ($post->get('scope') == 'group')
		{
			$group = \Hubzero\User\Group::getInstance($post->get('scope_id'));

			if ($group && in_array($userId, $group->get('members')))
			{
				$access[] = 5; // Private
			}
		}

		if ($post->isNew()
		 || $post->get('state') != Post::STATE_PUBLISHED
		 || !in_array($post->get('access'), $access)
		 || (int) ($post->get('thread') ?: $post->get('id')) !== (int) $threadId)
		{
			throw new Exception(Lang::txt('COM_FORUM_POST_NOT_FOUND'), 404);
		}

		$db = \App::get('db');

		$db->prepare("SELECT COUNT(*) FROM `#__forum_posts_like` WHERE postId = ? AND userId = ?");
		$db->bind(array($postId, $userId));
		if ($db->loadResult())
		{
			$this->send(true);
			return;
		}

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
		$this->requiresAuthentication();

		$threadId = Request::getString('threadId');
		$postId  = Request::getString('postId');
		$userId = \App::get('authn')['user_id'];

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


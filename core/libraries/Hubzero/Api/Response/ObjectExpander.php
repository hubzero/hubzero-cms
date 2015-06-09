<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Api\Response;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;
use Hubzero\User\Group;
use Hubzero\User\Profile;

/**
 * Expander Response Modifier
 */
class ObjectExpander extends Middleware
{
	/**
	 * Array that normalizes all the different keys we use for
	 * the different objects
	 * 
	 * @var  array
	 */
	private $acceptedKeys = array(
		'profile' => array(
			'action_by',
			'actor_id',
			'addedBy',
			'approved_by',
			'assigned',
			'assigned_to',
			'author',
			'authorid',
			'checked_out',
			'closed_by',
			'commenter',
			'commenter_id',
			'comment_by',
			'created_by',
			'created_by_user',
			'created_user_id',
			'creator_id',
			'editedBy',
			'follower_id',
			'following_id',
			'foreign_key',
			'granted_by',
			'modified_by',
			'modified_user_id',
			'object_id',
			'owned_by_user',
			'posted_by',
			'proposed_by',
			'ran_by',
			'redeemed_by',
			'reviewed_by',
			'sent_by',
			'taggerid',
			'uid',
			'uidNumber',
			'uploaded_by',
			'userid',
			'user_id',
			'user_id_to',
			'user_id_from',
			'voter'
		),
		'group' => array(
			'groupid',
			'group_id',
			'gidNumber'
		)
	);

	/**
	 * Handle request in HTTP stack
	 * 
	 * @param   objct  $request  HTTP Request
	 * @return  mixes
	 */
	public function handle(Request $request)
	{
		// execute response
		$response = $this->next($request);

		// only do this if user wants it expanded
		if (!$expand = $request->getVar('expand', null))
		{
			return $response;
		}

		// normalize keys
		$expandKeys = $this->normalizeExpandKeys($expand);

		// only do on json data
		if (!$response->isJson())
		{
			return $response;
		}

		// get the response content and json decode
		$content = json_decode($response->getContent());

		// make sure to handle array different then a single object
		if (is_array($content))
		{
			// loop through each item in array and covert dates
			foreach ($content as $key => $value)
			{
				$content[$key] = $this->convertExpandKeysInObjects($expandKeys, $value);
			}
		}
		else
		{
			// convert single object dates
			$content = $this->convertExpandKeysInObjects($expandKeys, $content);
		}

		// set the response content to modified content
		$response->setContent(json_encode($content));

		// return response
		return $response;
	}

	/**
	 * Normalize expand keys
	 * 
	 * @param  array $expandKeys Raw expand keys
	 * @return array             Normalized expand keys
	 */
	private function normalizeExpandKeys($expandKeys)
	{
		// clean up expand keys
		$expandKeys = array_map('trim', explode(',', $expandKeys));
		$normalized = [];

		foreach ($this->acceptedKeys as $type => $acceptedKeys)
		{
			foreach ($expandKeys as $expandKey)
			{
				if (in_array($expandKey, $acceptedKeys))
				{
					$normalized[$expandKey] = $type;
				}
			}
		}

		return $normalized;
	}

	/**
	 * Convert keys in object
	 * 
	 * @param   array  $expandKeys
	 * @param   mixed  $object
	 * @return  object
	 */
	private function convertExpandKeysInObjects($expandKeys, $object)
	{
		// only hanlde objects
		if (!is_object($object))
		{
			return $object;
		}

		// spin over each key replacing the date with new format
		foreach (array_keys(get_object_vars($object)) as $key)
		{
			if (array_key_exists($key, $expandKeys))
			{
				$func = $expandKeys[$key] . 'Expander';
				if (method_exists($this, $func))
				{
					$object->$key = $this->$func($object->$key);
				}
			}
		}

		// return object
		return $object;
	}

	/**
	 * Function to return profile object 
	 * 
	 * @param   integer  $uidNumber  User identifier
	 * @return  object   Profile object
	 */
	private function profileExpander($uidNumber)
	{
		return Profile::getInstance($uidNumber);
	}

	/**
	 * Function to return group object 
	 * 
	 * @param   integer  $gidNumber  Group identifier
	 * @return  object   Group object
	 */
	private function groupExpander($gidNumber)
	{
		return Group::getInstance($gidNumber);
	}
}
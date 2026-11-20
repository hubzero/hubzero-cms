<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Api\Response;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;
use Hubzero\User\Group;
use Hubzero\User\User;

/**
 * Expander Response Modifier
 */
class ObjectExpander extends Middleware
{
    /**
     * Array that normalizes all the different keys we use for
     * the different objects
     *
     * @var  array<mixed>
     */
    private $acceptedKeys = [
        'profile' => [
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
            'voter',
        ],
        'group' => [
            'groupid',
            'group_id',
            'gidNumber',
        ],
    ];

    /**
     * Handle request in HTTP stack
     *
     * @param   \Hubzero\Http\Request $request  HTTP Request
     * @return  mixed
     */
    public function handle(Request $request)
    {
        // execute response
        $response = $this->next($request);

        // only do this if user wants it expanded
        if (!$expand = $request->getVar('expand', null)) {
            return $response;
        }

        // normalize keys
        $expandKeys = $this->normalizeExpandKeys($expand);

        // only do on json data
        if (!$response->isJson()) {
            return $response;
        }

        // get the response content and json decode
        $content = json_decode($response->getContent());

        // make sure to handle array different then a single object
        if (is_array($content)) {
            // loop through each item in array and covert dates
            foreach ($content as $key => $value) {
                $content[$key] = $this->convertExpandKeysInObjects($expandKeys, $value);
            }
        } else {
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
     * @param   string|array<mixed>  $expandKeys  Raw expand keys: a comma-separated list, or several (expand[]=...)
     * @return  array<mixed>  Normalized expand keys
     */
    private function normalizeExpandKeys($expandKeys)
    {
        if (is_array($expandKeys)) {
            $expandKeys = implode(',', $expandKeys);
        }

        // clean up expand keys
        $expandKeys = array_map('trim', explode(',', $expandKeys));
        $normalized = [];

        foreach ($this->acceptedKeys as $type => $acceptedKeys) {
            foreach ($expandKeys as $expandKey) {
                if (in_array($expandKey, $acceptedKeys)) {
                    $normalized[$expandKey] = $type;
                }
            }
        }

        return $normalized;
    }

    /**
     * Convert keys in object
     *
     * @param   array<mixed>  $expandKeys
     * @param   mixed  $object
     * @return  mixed
     */
    private function convertExpandKeysInObjects($expandKeys, $object)
    {
        // only hanlde objects
        if (!is_object($object)) {
            return $object;
        }

        // spin over each key replacing the date with new format
        foreach (array_keys(get_object_vars($object)) as $key) {
            if (array_key_exists($key, $expandKeys)) {
                $func = $expandKeys[$key] . 'Expander';
                if (method_exists($this, $func)) {
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
     * @param   integer  $user_id  User identifier
     * @return  \Hubzero\User\User   User object
     */
    private function profileExpander($user_id)
    {
        return User::oneOrNew($user_id);
    }

    /**
     * Function to return group object
     *
     * @param   integer  $gidNumber  Group identifier
     * @return  \Hubzero\User\Group|false   The group, or false when there is none
     */
    private function groupExpander($gidNumber)
    {
        return Group::getInstance($gidNumber);
    }
}

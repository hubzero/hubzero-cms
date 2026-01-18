<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Files.SideEffects
namespace Components\Collections\Models;

use Components\Collections\Tables;
use Hubzero\Base\Obj;
use Hubzero\Base\ItemList;
use Hubzero\Plugin\Params;
use User;
use Lang;

require_once __DIR__ . DS . 'post.php';
require_once __DIR__ . DS . 'following.php';
require_once __DIR__ . DS . 'collection.php';

/**
 * Collections archive model
 */
class Archive extends Obj
{
    /**
     * Object type [member, group, etc.]
     *
     * @var string
     */
    private $objectType = null;

    /**
     * Object ID [member ID, group ID, etc.]
     *
     * @var integer
     */
    private $objectId = null;

    /**
     * Database
     *
     * @var object
     */
    private $db = null;

    /**
     * \Hubzero\Base\ItemList
     *
     * @var object
     */
    private $collectionsCache = null;

    /**
     * Collection
     *
     * @var object
     */
    private $collectionItem = null;

    /**
     * \Hubzero\Base\ItemList
     *
     * @var object
     */
    private $followers = null;

    /**
     * \Hubzero\Base\ItemList
     *
     * @var object
     */
    private $following = null;

    /**
     * Is following?
     *
     * @var boolean
     */
    private $isFollowingFlag = null;

    /**
     * Is being followed?
     *
     * @var boolean
     */
    private $isFollowedFlag = null;

    /**
     * Constructor
     *
     * @param   string   $object_type  The object type
     * @param   itneger  $object_id    The object ID
     * @return  void
     */
    public function __construct($object_type = '', $object_id = 0)
    {
        $this->db = \App::get('db');

        $this->objectType = (string) $object_type;
        $this->objectId   = (int) $object_id;
    }

    /**
     * Returns a reference to this model
     *
     * @param   string   $object_type  The object type
     * @param   itneger  $object_id    The object ID
     * @return  object
     */
    public static function &getInstance($object_type = '', $object_id = 0)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = array();
        }

        $oid = $object_type . '_' . $object_id;

        if (!isset($instances[$oid])) {
            $instances[$oid] = new self($object_type, $object_id);
        }

        return $instances[$oid];
    }

    /**
     * Map of external property names to internal property names
     *
     * @var  array
     */
    private static $propertyMap = array(
        'object_type' => 'objectType',
        'object_id'   => 'objectId',
        'db'          => 'db',
    );

    /**
     * Returns a property of the object or the default value if the property is not set.
     *
     * @param   string  $property  The name of the property
     * @param   mixed   $default   The default value
     * @return  mixed   The value of the property
     */
    public function get($property, $default = null)
    {
        $property = isset(self::$propertyMap[$property])
            ? self::$propertyMap[$property]
            : $property;
        if (isset($this->$property)) {
            return $this->$property;
        }
        return $default;
    }

    /**
     * Modifies a property of the object, creating it if it does not already exist.
     *
     * @param   string  $property  The name of the property
     * @param   mixed   $value     The value of the property to set
     * @return  mixed   Previous value of the property
     */
    public function set($property, $value = null)
    {
        $property = isset(self::$propertyMap[$property])
            ? self::$propertyMap[$property]
            : $property;
        $previous = isset($this->$property) ? $this->$property : null;
        $this->$property = $value;
        return $previous;
    }

    /**
     * Set and get a specific collection
     *
     * @param   mixed   $id
     * @return  object
     */
    public function collection($id = null)
    {
        // If the current offering isn't set
        //    OR the ID passed doesn't equal the current offering's ID or alias
        if (
            !isset($this->collectionItem)
            || ($id !== null
                && (int) $this->collectionItem->get('id') != $id
                && (string) $this->collectionItem->get('alias') != $id)
        ) {
            // Reset current offering
            $this->collectionItem = null;

            // If the list of all offerings is available ...
            if ($this->collectionsCache instanceof ItemList) {
                // Find an offering in the list that matches the ID passed
                foreach ($this->collections() as $key => $collection) {
                    if ((int) $collection->get('id') == $id || (string) $collection->get('alias') == $id) {
                        // Set current offering
                        $this->collectionItem = $collection;
                        break;
                    }
                }
            }

            if (!$this->collectionItem) {
                $this->collectionItem = Collection::getInstance($id, $this->objectId, $this->objectType);
            }
        }
        // Return current offering
        return $this->collectionItem;
    }

    /**
     * Get a count or list of collections
     *
     * @param   array  $filters  Filters to apply to the query that retrieves records
     * @return  mixed  Integer or object
     */
    public function collections($filters = array())
    {
        if ($this->objectId) {
            $filters['object_id']   = $this->objectId;
        }
        if ($this->objectType) {
            $filters['object_type'] = $this->objectType;
        }
        if (!isset($filters['state'])) {
            $filters['state'] = 1;
        }

        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Collection($this->db);

            return $tbl->getCount($filters);
        }

        if (!($this->collectionsCache instanceof ItemList)) {
            $tbl = new Tables\Collection($this->db);

            if (($results = $tbl->getRecords($filters))) {
                // Loop through all the items and push assets and tags
                foreach ($results as $key => $result) {
                    $results[$key] = new Collection($result);
                }
            }

            $this->collectionsCache = new ItemList($results);
        }

        return $this->collectionsCache;
    }

    /**
     * Get a count or list of followers
     *
     * @param   array  $filters  Filters to apply to the query that retrieves records
     * @return  mixed  Integer or object
     */
    public function followers($filters = array())
    {
        $filters['following_id']   = $this->objectId;
        $filters['following_type'] = $this->objectType;

        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Following($this->db);

            return $tbl->count($filters);
        }
        if (!($this->followers instanceof ItemList)) {
            $tbl = new Tables\Following($this->db);

            if ($results = $tbl->find($filters)) {
                // Loop through all the items and push assets and tags
                foreach ($results as $key => $result) {
                    $results[$key] = new Following($result);
                }
            }

            $this->followers = new ItemList($results);
        }

        return $this->followers;
    }

    /**
     * Get a count or list of following
     *
     * @param   array   $filters  Filters to apply to the query that retrieves records
     * @param   string  $what     Following what? A collection or a member, etc.
     * @return  mixed   Integer or object
     */
    public function following($filters = array(), $what = 'all')
    {
        $filters['follower_id']   = $this->objectId;
        $filters['follower_type'] = $this->objectType;

        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Following($this->db);

            return $tbl->count($filters);
        }

        if ($what == 'first') {
            $filters['limit'] = 1;
        }

        if (!($this->following instanceof ItemList)) {
            $tbl = new Tables\Following($this->db);

            if ($results = $tbl->find($filters)) {
                // Loop through all the items and push assets and tags
                foreach ($results as $key => $result) {
                    $results[$key] = new Following($result);
                }
            }

            $this->following = new ItemList($results);
        }

        if ($what == 'collections') {
            $ids     = array();
            $members = array();
            $groups  = array();
            foreach ($this->following as $following) {
                if ($following->get('following_type') == 'collection') {
                    $ids[] = $following->get('following_id');
                } else {
                    if ($following->get('following_type') == 'member') {
                        $members[] = $following->get('following_id');
                    } elseif ($following->get('following_type') == 'group') {
                        $groups[] = $following->get('following_id');
                    }
                }
            }
            if (count($members) > 0 || count($groups) > 0) {
                if (count($members) > 0) {
                    $query1 = "SELECT id FROM `#__collections` "
                        . "WHERE object_id IN (" . implode(',', $members) . ") "
                        . "AND object_type='member'";
                }
                if (count($groups) > 0) {
                    $query2 = "SELECT id FROM `#__collections` "
                        . "WHERE object_id IN (" . implode(',', $groups) . ") "
                        . "AND object_type='group'";
                }
                if (count($members) > 0 && count($groups) > 0) {
                    $query = "( $query1 ) UNION ( $query2 );";
                } elseif (count($members) > 0) {
                    $query = $query1;
                } elseif (count($groups) > 0) {
                    $query = $query2;
                }

                $this->db->setQuery($query);
                $ids = array_merge($ids, $this->db->loadColumn());
            }

            return $ids;
        }

        return $this->following;
    }

    /**
     * Get a count or list of posts
     *
     * @param   array  $filters  Filters to apply to the query that retrieves records
     * @return  mixed  Integer or array
     */
    public function posts($filters = array())
    {
        $filters['object_id']   = $this->objectId;
        $filters['object_type'] = $this->objectType;
        if (!isset($filters['state'])) {
            $filters['state'] = 1;
        }

        if (isset($filters['count']) && $filters['count']) {
            $tbl = new Tables\Post($this->db);
            return $tbl->getCount($filters);
        }

        $tbl = new Tables\Post($this->db);
        return $tbl->getRecords($filters);
    }

    /**
     * Get a list of collections for a user
     *
     * - If no type or type='member', returns an array of collections
     *   that user created.
     * - If type='group', returns an array of collections in the groups
     *   the user is a member of.
     *
     * @param   string $type What type ot get collections for [group, member]
     * @return  array
     */
    public function mine($type = '')
    {
        $user = User::getInstance();

        $tbl = new Tables\Collection($this->db);

        switch (strtolower(trim($type))) {
            case 'group':
            case 'groups':
                $collections = array();

                $usergroups = $user->groups('members');
                $usergroups_manager = $user->groups('managers');

                if ($usergroups) {
                    if ($usergroups_manager) {
                        foreach ($usergroups_manager as $manager_group) {
                            foreach ($usergroups as $user_group) {
                                if ($user_group->gidNumber == $manager_group->gidNumber) {
                                    $user_group->manager = $manager_group->manager;
                                }
                            }
                        }
                    }
                    foreach ($usergroups as $usergroup) {
                        $groups = $tbl->getRecords(array(
                            'object_type' => 'group',
                            'object_id'   => $usergroup->gidNumber,
                            'state'       => 1
                        ));
                        if ($groups) {
                            if (!isset($usergroup->params) || !is_object($usergroup->params)) {
                                $usergroup->params = Params::getCustomParams(
                                    $usergroup->gidNumber,
                                    'groups',
                                    'collections'
                                );
                            }
                            foreach ($groups as $s) {
                                if (!isset($collections[$s->group_alias])) {
                                    $collections[$s->group_alias] = array();
                                }
                                if ($usergroup->params->get('create_post', 0) == 1 && !$usergroup->manager) {
                                    continue;
                                }
                                /*if ($s->access == 4 && !$usergroup->manager)
                                {
                                    continue;
                                }*/
                                $collections[$s->group_alias][] = $s;
                                asort($collections[$s->group_alias]);
                            }
                        }
                    }
                }

                asort($collections);
                break;

            case 'member':
            default:
                $collections = $tbl->getRecords(array(
                    'object_type' => 'member',
                    'object_id'   => $user->get('id'),
                    'state'       => 1
                ));
                break;
        }

        return $collections;
    }

    /**
     * Check if someone or a group is following this collection
     *
     * @param   integer $follower_id   ID of the follower
     * @param   string  $follower_type Type of the follower [member, group]
     * @return  boolean
     */
    public function isFollowing($follower_id = null, $follower_type = 'member')
    {
        if (!isset($this->isFollowingFlag)) {
            $this->isFollowingFlag = false;

            if (!$follower_id && $follower_type == 'member') {
                $follower_id = User::get('id');
            }

            $follow = new Following($this->objectId, $this->objectType, $follower_id, $follower_type);
            if ($follow->exists()) {
                $this->isFollowingFlag = true;
            }
        }
        return $this->isFollowingFlag;
    }

    /**
     * Check if someone or a group is following this collection
     *
     * @param   integer  $follower_id    ID of the follower
     * @param   string   $follower_type  Type of the follower [member, group]
     * @return  boolean
     */
    public function isFollowed($follower_id = null, $follower_type = 'member')
    {
        if (!isset($this->isFollowedFlag)) {
            $this->isFollowedFlag = false;

            if (!$follower_id && $follower_type == 'member') {
                $follower_id = User::get('id');
            }

            $follow = new Following($follower_id, $follower_type, $this->objectId, $this->objectType);
            if ($follow->exists()) {
                $this->isFollowedFlag = true;
            }
        }
        return $this->isFollowedFlag;
    }

    /**
     * Unfollow this collection
     *
     * @param   integer  $follower_id    ID of the follower
     * @param   string   $follower_type  Type of the follower [member, group]
     * @return  boolean
     */
    public function unfollow($id, $what = 'collection', $follower_id = 0, $follower_type = 'member')
    {
        $follow = new Following($id, $what, $follower_id, $follower_type);

        if (!$follow->exists()) {
            $this->setError(Lang::txt('Item is not being followed'));
            return true;
        }

        if (!$follow->delete()) {
            $this->setError($follow->getError());
            return false;
        }

        return true;
    }

    /**
     * Follow something [collection, member, goup]
     *
     * @param   integer  $id             ID of the thing being followed
     * @param   string   $what           What's being followed
     * @param   integer  $follower_id    ID of the follower
     * @param   string   $follower_type  Type of the follower [member, group]
     * @return  boolean
     */
    public function follow($id, $what = 'collection', $follower_id = 0, $follower_type = 'member')
    {
        $follow = new Following($id, $what, $follower_id, $follower_type);

        if (!$follow->exists()) {
            $follow->bind(array(
                'following_id'   => $id,
                'following_type' => $what,
                'follower_id'    => $follower_id,
                'follower_type'  => $follower_type
            ));
            if (!$follow->store(true)) {
                $this->setError($follow->getError());
                return false;
            }
        }

        return true;
    }

    /**
     * Get a collectible item based on component
     *
     * @param   string  $option
     * @return  object
     */
    public function collectible($option)
    {
        $cls = __NAMESPACE__ . '\\Item';

        if ($option != 'com_collections') {
            $option = strtolower(substr($option, 4));

            $path = __DIR__ . DS . 'item' . DS . $option . '.php';

            if (file_exists($path)) {
                include_once $path;

                $cls = __NAMESPACE__ . '\\Item\\' . ucfirst($option);
            }
        }

        return new $cls();
    }
}

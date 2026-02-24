<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Support plugin class for com_resources entries
 */
namespace Plugins\Support\Resources;

use Hubzero\Plugin\Plugin;

class Resources extends Plugin
{
    /**
     * Is the category one this plugin handles?
     *
     * @param   string  $category  Element type (determines table to look in)
     * @return  boolean
     */
    private function canHandle($category)
    {
        if (in_array($category, array('review', 'reviewcomment'))) {
            return true;
        }
        return false;
    }

    /**
     * Get items reported as abusive
     *
     * @param   integer  $refid     Comment ID
     * @param   string   $category  Item type (kb)
     * @param   integer  $parent    Parent ID
     * @return  array
     */
    public function getReportedItem($refid, $category, $parent)
    {
        if (!$this->canHandle($category)) {
            return null;
        }

        $database = App::get('db');

        if ($category == 'review') {
            $query = "SELECT rr.id, rr.comment as `text`, rr.created, rr.user_id as `author`,
						NULL as subject, 'review' as parent_category, rr.anonymous as anon
						FROM `#__resource_ratings` AS rr
						WHERE rr.id=" . $database->quote($refid);
        } elseif ($category == 'reviewcomment') {
            $query = "SELECT rr.id, rr.content as `text`, rr.created, rr.created_by as `author`,
						NULL as subject, 'reviewcomment' as parent_category, rr.anonymous as anon
						FROM `#__item_comments` AS rr
						WHERE rr.id=" . $database->quote($refid);
        }

        $database->setQuery($query);
        $rows = $database->loadObjectList();
        if ($rows) {
            foreach ($rows as $key => $row) {
                if (preg_match('/^<!-- \{FORMAT:(.*)\} -->/i', $row->text, $matches)) {
                    $rows[$key]->text = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $row->text);
                }
                $rows[$key]->href = ($parent)
                    ? Route::url('index.php?option=com_resources&id=' . $parent . '&active=reviews')
                    : '';
            }
        }
        return $rows;
    }

    /**
     * Looks up ancestors to find root element
     *
     * @param   integer  $parentid  ID to check for parents of
     * @param   string   $category  Element type (determines table to look in)
     * @return  integer
     */
    public function getParentId($parentid, $category)
    {
        $database = App::get('db');
        $refid = $parentid;

        if ($category == 'reviewcomment') {
            $pdata = $this->parent($parentid);

            $refid = $pdata->get('item_id');
            $category = 'review';
        }

        if ($category == 'review') {
            $database->setQuery("SELECT resource_id FROM `#__resource_ratings` WHERE id=" . $refid);
            return $database->loadResult();
        }
    }

    /**
     * Retrieve parent element
     *
     * @param   integer  $parentid  ID of element to retrieve
     * @return  object
     */
    public function parent($parentid)
    {
        return \Hubzero\Item\Comment::oneOrFail($parentid);
    }

    /**
     * Returns the appropriate text for category
     *
     * @param   string   $category  Element type (determines text)
     * @param   integer  $parentid  ID of element to retrieve
     * @return  string
     */
    public function getTitle($category, $parentid)
    {
        if (!$this->canHandle($category)) {
            return null;
        }

        $this->loadLanguage();

        switch ($category) {
            case 'review':
                return Lang::txt('PLG_SUPPORT_RESOURCES_REVIEW_OF', $parentid);
                break;

            case 'reviewcomment':
                return Lang::txt('PLG_SUPPORT_RESOURCES_COMMENT_OF', $parentid);
                break;
        }
    }

    /**
     * Mark an item as flagged
     *
     * @param   string  $refid     ID of the database table row
     * @param   string  $category  Element type (determines table to look in)
     * @return  string
     */
    public function onReportItem($refid, $category)
    {
        if (!$this->canHandle($category)) {
            return null;
        }

        $comment = \Plugins\Resources\Reviews\Models\Review::oneOrFail($refid);
        $comment->set('state', 3);
        $comment->save();

        return '';
    }

    /**
     * Release a reported item
     *
     * @param   string  $refid     ID of the database table row
     * @param   string  $parent    If the element has a parent element
     * @param   string  $category  Element type (determines table to look in)
     * @return  array
     */
    public function releaseReportedItem($refid, $parent, $category)
    {
        if (!$this->canHandle($category)) {
            return null;
        }

        $comment = \Plugins\Resources\Reviews\Models\Review::oneOrFail($refid);
        $comment->set('state', 1);
        $comment->save();

        return '';
    }

    /**
     * Removes an item reported as abusive
     *
     * @param      integer $referenceid ID of the database table row
     * @param      integer $parentid    If the element has a parent element
     * @param      string  $category    Element type (determines table to look in)
     * @param      string  $message     Message to user to append to
     * @return     string
     */
    public function deleteReportedItem($referenceid, $parentid, $category, $message)
    {
        if (!$this->canHandle($category)) {
            return null;
        }

        $this->loadLanguage();

        $database = App::get('db');

        switch ($category) {
            case 'review':
                // Delete the review
                $review = \Plugins\Resources\Reviews\Models\Review::oneOrFail($referenceid);
                $review->set('state', 2);
                $review->save();

                $rating = \Plugins\Resources\Reviews\Models\Review::averageByResource($parentid);

                // Recalculate the average rating for the parent resource
                $resource = \Components\Resources\Models\Entry::oneOrFail($parentid);
                $resource->set('rating', $rating['rating']);
                $resource->set('times_rated', $rating['times_rated']);
                if (!$resource->save()) {
                    $this->setError($resource->getError());
                    return false;
                }

                $message .= Lang::txt('PLG_SUPPORT_RESOURCES_NOTIFICATION_OF_REMOVAL', $parentid);
                break;

            case 'reviewcomment':
                $comment = \Hubzero\Item\Comment::oneOrFail($referenceid);
                $comment->set('state', $comment::STATE_DELETED);

                if (!$comment->save()) {
                    $this->setError($comment->getError());
                    return false;
                }

                $message .= Lang::txt('PLG_SUPPORT_RESOURCES_NOTIFICATION_OF_REMOVAL', $parentid);
                break;
        }

        return $message;
    }
}

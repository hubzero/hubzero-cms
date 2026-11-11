<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Publications\Reviews\Models;

use Hubzero\Item\Comment as ItemComment;

/**
 * Publications model for a comment
 *
 * Not a column, read and set through __get() and __set():
 *
 * @property  string  $base  The URL of the reviews of the publication the reply is on, cached by link()
 */
class Comment extends ItemComment
{
    /**
     * Generate and return various links to the entry
     * Link will vary depending upon action desired, such as edit, delete, etc.
     *
     * @param      string $type The type of link to return
     * @return     string
     */
    public function link($type = '')
    {
        if (!isset($this->base)) {
            // A reply's item is the review; the page is the publication reviewed
            $review = new Review((int) $this->get('item_id'));

            $this->base = 'index.php?option=com_publications&id=' . $review->get('publication_id')
                . '&active=reviews';
        }
        $link = $this->base;

        // If it doesn't exist or isn't published
        switch (strtolower($type)) {
            case 'edit':
                $link .= '&action=edit&comment=' . $this->get('id');
                break;

            case 'delete':
                $link .= '&action=delete&comment=' . $this->get('id');
                break;

            case 'reply':
                $link .= '&action=reply&category=review&refid=' . $this->get('id');
                break;

            case 'report':
                $link = 'index.php?option=com_support&task=reportabuse&category=itemcomment&id='
                    . $this->get('id')
                    . '&parent='
                    . $this->get('item_id');
                break;

            case 'permalink':
            default:
                $link .= '#c' . $this->get('id');
                break;
        }

        return $link;
    }
}

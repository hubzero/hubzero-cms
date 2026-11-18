<?php

namespace Plugins\Content\Categories;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Plugin for ensuring categories are empty before deleting
 *
 */
class Categories extends Plugin
{
    /**
     * Don't allow categories to be deleted if they contain items or subcategories with items
     *
     * @param   string   $context  The context for the content passed to the plugin.
     * @param   object   $data     The data relating to the content that was deleted.
     * @return  boolean
     */
    public function onContentBeforeDelete($context, $data)
    {
        // Skip plugin if we are deleting something other than categories
        if ($context != 'com_categories.category') {
            return true;
        }

        // The checks below walk the nested set of the categories model
        if (!($data instanceof \Components\Categories\Models\Category)) {
            return true;
        }

        // Check if this function is enabled.
        if (!$this->params->def('check_categories', 1)) {
            return true;
        }

        $extension = Request::getString('extension');

        // Default to true if not a core extension
        $result = true;

        $tableInfo =  [
            'com_content'   => ['table_name' => '#__content'],
            'com_newsfeeds' => ['table_name' => '#__newsfeeds'],
        ];

        // Now check to see if this is a known core extension
        if (isset($tableInfo[$extension])) {
            // Get table name for known core extensions
            $table = $tableInfo[$extension]['table_name'];

            // See if this category has any content items
            $count = $this->countItemsInCategory($table, $data->get('id'));

            // Return false if db error
            if ($count === false) {
                $result = false;
            } else {
                // Show error if items are found in the category
                if ($count > 0) {
                    $msg = Lang::txt('COM_CATEGORIES_DELETE_NOT_ALLOWED', $data->get('title'));
                    $msg .= Lang::txts('COM_CATEGORIES_N_ITEMS_ASSIGNED', $count);

                    App::abort(403, $msg);
                }
                // Check for items in any child categories (a leaf of the nested set has none)
                if ($data->get('rgt') - $data->get('lft') > 1) {
                    $count = $this->countItemsInChildren($table, $data->get('id'), $data);

                    if ($count === false) {
                        $result = false;
                    } elseif ($count > 0) {
                        $msg = Lang::txt('COM_CATEGORIES_DELETE_NOT_ALLOWED', $data->get('title'));
                        $msg .= Lang::txts('COM_CATEGORIES_HAS_SUBCATEGORY_ITEMS', $count);

                        App::abort(403, $msg);
                    }
                }
            }

            return $result;
        }
        return $result;
    }

    /**
     * Get count of items in a category
     *
     * @param   string  $table  table name of component table (column is catid)
     * @param   int     $catid  id of the category to check
     * @return  mixed   Count of items found or false if db error
     */
    private function countItemsInCategory($table, $catid)
    {
        $db = App::get('db');

        // Count the items in this category
        $query = $db
            ->getQuery()
            ->select('COUNT(id)')
            ->from($table)
            ->whereEquals('catid', $catid);
        $db->setQuery($query->toString());
        $count = $db->loadResult();

        // Check for DB error.

        return $count;
    }

    /**
     * Get count of items in a category's child categories
     *
     * @param   string  $table  table name of component table (column is catid)
     * @param   int     $catid  id of the category to check
     * @param   \Components\Categories\Models\Category  $data  The category
     * @return  mixed   Count of items found or false if db error
     */
    private function countItemsInChildren($table, $catid, $data)
    {
        $db = App::get('db');

        // The child categories, at every depth
        $childCategoryIds = [];
        foreach ($data->getDescendants() as $node) {
            $childCategoryIds[] = $node->get('id');
        }

        // Make sure we only do the query if we have some categories to look in
        if (count($childCategoryIds)) {
            // Count the items in this category
            $query = $db
                ->getQuery()
                ->select('COUNT(id)')
                ->from($table)
                ->whereIn('catid', $childCategoryIds);
            $db->setQuery($query->toString());
            $count = $db->loadResult();

            // Check for DB error.

            return $count;
        }

        // If we didn't have any categories to check, return 0
        return 0;
    }
}

<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\ArticlesCategories;

use Hubzero\Module\Module;
use Components\Categories\Models\Category;
use Hubzero\Facades\Component;

/**
 * Module class for displaying a list of categories
 */
class ArticlesCategories extends Module
{
    /**
     * Display module contents
     *
     * @return  void
     */
    public function display()
    {
        // [!] Legacy compatibility
        $params = $this->params;
        $module = $this->module;

        $list = self::getList($params);

        if ($list instanceof \Hubzero\Database\Rows && $list->count()) {
            $moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx', ''));
            $startLevel = Category::oneOrNew($list->first()->parent_id)->level;
            require $this->getLayoutPath($params->get('layout', 'default'));
        }
    }

    /**
     * Display module contents
     *
     * @param   object  $params  Registry
     * @return  \Hubzero\Database\Rows|array  The categories; an empty array when the parent is not found
     */
    public static function getList(&$params)
    {

        $parent = $params->get('parent', 'root');
        if ($parent == 'root') {
            $category = Category::all()
                ->whereEquals('alias', 'root')
                ->row();
        } else {
            $category = Category::one($parent);
        }

        if ($category != null) {
            // The children come as rows, which cannot be sliced as an array; ask for no more than wanted
            $children = $category->children();
            $count = (int) $params->get('count', 0);
            if ($count > 0) {
                $children->limit($count);
            }
            return $children->rows();
        }

        return [];
    }
}

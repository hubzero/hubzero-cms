<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\RelatedItems;

use Hubzero\Module\Module;
use stdClass;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;
use Hubzero\Facades\Lang;

/**
 * Module class for displaying related articles
 */
class RelatedItems extends Module
{
    /**
     * Display module content
     *
     * @return  void
     */
    public function display()
    {

        // [!] Legacy compatibility
        $params = $this->params;

        // Module::cache() is a stub that returns true, so the list is built
        // directly; count(true) is a TypeError on PHP 8
        $list = self::getList($params);

        if (!count($list)) {
            return;
        }

        $moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx', ''));
        $showDate = $params->get('showDate', 0);

        require $this->getLayoutPath($params->get('layout', 'default'));
    }

    /**
     * Get a list of articles
     *
     * @param   \Hubzero\Config\Registry|null  $params  Registry
     * @return  array<int, mixed>
     */
    public static function getList($params)
    {
        $db     = \Hubzero\Facades\App::get('db');
        $userId = (int) User::get('id');
        $count  = intval($params->get('count', 5));
        $date   = \Hubzero\Facades\Date::toSql();

        $option = Request::getCmd('option');
        $view   = Request::getCmd('view');

        $temp   = Request::getString('id');
        $temp   = explode(':', $temp);
        $id     = $temp[0];

        $showDate = $params->get('showDate', 0);
        $nullDate = $db->getNullDate();
        $now      = $date;
        $related  = [];
        $query    = $db->getQuery();

        if ($option == 'com_content' && $view == 'article' && $id) {
            // Select the meta keywords from the item
            $query->select('metakey');
            $query->from('#__content');
            $query->where('id', '=', (int) $id);
            if ($metakey = trim((string) $query->value('metakey'))) {
                // Explode the meta keys on a comma
                $keys  = explode(',', $metakey);
                $likes = [];

                // Assemble any non-blank word(s)
                foreach ($keys as $key) {
                    $key = trim($key);
                    if ($key) {
                        // Bound below, so not escaped here
                        $likes[] = $key;
                    }
                }

                if (count($likes)) {
                    // Select other items based on the metakey field 'like' the keys found
                    $query->clear();
                    $query->select('a.id');
                    $query->select('a.title');
                    $query->select($db->sqlDateFormat('a.created', '%Y-%m-%d') . ' as created');
                    $query->select('a.catid');
                    $query->select('a.language');
                    $query->select('cc.access AS cat_access');
                    $query->select('cc.published AS cat_state');

                    // The slugs: id:alias when there is an alias, else the id. This
                    // used Joomla query methods (charLength(), castAsChar(),
                    // concatenate()) the query builder does not have.
                    $query->select(
                        'CASE WHEN ' . $db->sqlLength('a.alias') . ' THEN '
                        . $db->sqlConcatWs(':', ['a.id', 'a.alias']) . ' ELSE a.id END as slug',
                    );
                    $query->select(
                        'CASE WHEN ' . $db->sqlLength('cc.alias') . ' THEN '
                        . $db->sqlConcatWs(':', ['cc.id', 'cc.alias']) . ' ELSE cc.id END as catslug',
                    );
                    $query->from('#__content AS a');
                    $query->joinRaw('#__content_frontpage AS f', 'f.content_id = a.id', 'left');
                    $query->joinRaw('#__categories AS cc', 'cc.id = a.catid', 'left');
                    $query->where('a.id', '!=', (int) $id);
                    $query->where('a.state', '=', 1);
                    $query->whereIn('a.access', User::getAuthorisedViewLevels());

                    // The item's keywords, without the space after each comma and
                    // with a comma at each end, holding any of this article's
                    $keywords = $db->sqlConcat(["','", "REPLACE(a.metakey, ', ', ',')", "','"]);
                    $matches  = [];
                    $bindings = [];
                    foreach ($likes as $like) {
                        $matches[]  = $keywords . ' LIKE ?';
                        $bindings[] = '%' . $like . '%';
                    }
                    $query->whereRaw('(' . implode(' OR ', $matches) . ')', $bindings);
                    $query->whereRaw(
                        '(a.publish_up IS NULL OR a.publish_up = ? OR a.publish_up <= ?)',
                        [$nullDate, $now],
                    );
                    $query->whereRaw(
                        '(a.publish_down IS NULL OR a.publish_down = ? OR a.publish_down >= ?)',
                        [$nullDate, $now],
                    );

                    // Filter by language
                    if (\Hubzero\Facades\App::get('language.filter')) {
                        $query->whereIn('a.language', [Lang::getTag(), '*']);
                    }

                    $temp = $query->fetch();

                    if (count($temp)) {
                        foreach ($temp as $row) {
                            if ($row->cat_state == 1) {
                                $articleRoute = \Components\Content\Site\Helpers\Route::getArticleRoute(
                                    $row->slug,
                                    $row->catslug,
                                    $row->language,
                                );
                                $row->route = Route::url($articleRoute);
                                $related[] = $row;
                            }
                        }
                    }
                    unset($temp);
                }
            }
        }

        return $related;
    }
}

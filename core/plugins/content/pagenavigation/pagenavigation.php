<?php

namespace Plugins\Content\Pagenavigation;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\App;
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Pagenavigation plugin class.
 *
 */
class Pagenavigation extends Plugin
{
    /**
     * Prepare the content for display
     *
     * @param   string   $context  The context of the content being passed to the plugin.
     * @param   \stdClass   $row      The article object.  Note $article->text is also available
     * @param   \Hubzero\Config\Registry   $params   The article params
     * @param   integer  $page     The 'page' number
     * @return  bool|null
     */
    public function onContentBeforeDisplay($context, &$row, &$params, $page = 0)
    {
        $view  = Request::getCmd('view');
        $print = Request::getBool('print');

        if ($print) {
            return false;
        }

        if ($params->get('show_item_navigation') && ($context == 'com_content.article') && ($view == 'article')) {
            $html = '';
            $db   = App::get('db');

            $nullDate = $db->getNullDate();

            $date = Date::of('now');
            $now  = $date->toSql();

            $uid = $row->id;
            $option = 'com_content';
            $canPublish = User::authorise('core.edit.state', $option . '.article.' . $row->id);

            // The following is needed as different menu items types utilise a different param to control ordering.
            // For Blogs the `orderby_sec` param is the order controlling param.
            // For Table and List views it is the `orderby` param.
            $params_list = $params->toArray();
            if (array_key_exists('orderby_sec', $params_list)) {
                $order_method = $params->get('orderby_sec', '');
            } else {
                $order_method = $params->get('orderby', '');
            }
            // Additional check for invalid sort ordering.
            if ($order_method == 'front') {
                $order_method = '';
            }

            // Determine sort order: columns and their directions, since the
            // query builder's order() takes them apart
            switch ($order_method) {
                case 'date':
                    $orderby = [['a.created', 'asc']];
                    break;
                case 'rdate':
                    $orderby = [['a.created', 'desc']];
                    break;
                case 'alpha':
                    $orderby = [['a.title', 'asc']];
                    break;
                case 'ralpha':
                    $orderby = [['a.title', 'desc']];
                    break;
                case 'hits':
                    $orderby = [['a.hits', 'asc']];
                    break;
                case 'rhits':
                    $orderby = [['a.hits', 'desc']];
                    break;
                case 'author':
                    $orderby = [['a.created_by_alias', 'asc'], ['u.name', 'asc']];
                    break;
                case 'rauthor':
                    $orderby = [['a.created_by_alias', 'desc'], ['u.name', 'desc']];
                    break;
                case 'order':
                default:
                    $orderby = [['a.ordering', 'asc']];
                    break;
            }

            $xwhere = ' AND (a.state = 1 OR a.state = -1)'
                . ' AND (publish_up IS NULL OR publish_up = ' . $db->quote($nullDate)
                . ' OR publish_up <= ' . $db->quote($now) . ')'
                . ' AND (publish_down IS NULL OR publish_down = ' . $db->quote($nullDate)
                . ' OR publish_down >= ' . $db->quote($now) . ')';

            // Array of articles in same category correctly ordered.
            $query = $db->getQuery();
            // The slugs: id:alias when there is an alias, else the id. This
            // used Joomla query methods (charLength(), castAsChar(),
            // concatenate()) the query builder does not have.
            $query->select('a.id');
            $query->select('a.language');
            $query->select(
                'CASE WHEN ' . $db->sqlLength('a.alias') . ' THEN '
                . $db->sqlConcatWs(':', ['a.id', 'a.alias']) . ' ELSE a.id END as slug',
            );
            $query->select(
                'CASE WHEN ' . $db->sqlLength('cc.alias') . ' THEN '
                . $db->sqlConcatWs(':', ['cc.id', 'cc.alias']) . ' ELSE cc.id END as catslug',
            );
            $query->from('#__content AS a');
            $query->joinRaw('#__categories AS cc', 'cc.id = a.catid', 'left');
            // The author orderings name the user
            $query->joinRaw('#__users AS u', 'u.id = a.created_by', 'left');
            $whereClause = 'a.catid = ' . (int) $row->catid . ' AND a.state = ' . (int) $row->state;
            $whereClause .= $canPublish ? '' : ' AND a.access = ' . (int) $row->access;
            $whereClause .= $xwhere;
            $query->whereRaw($whereClause);
            foreach ($orderby as $order) {
                $query->order($order[0], $order[1]);
            }
            if (App::isSite() && App::get('language.filter')) {
                $query->whereIn('a.language', [Lang::getTag(), '*']);
            }

            $db->setQuery($query);
            $list = $db->loadObjectList('id');

            reset($list);

            // Location of current content item in array list.
            $location = array_search($uid, array_keys($list));

            $rows = array_values($list);

            $row->prev = null;
            $row->next = null;

            if ($location - 1 >= 0) {
                // The previous content item cannot be in the array position -1.
                $row->prev = $rows[$location - 1];
            }

            if (($location + 1) < count($rows)) {
                // The next content item cannot be in an array position greater than the number of array postions.
                $row->next = $rows[$location + 1];
            }

            $pnSpace = '';
            if (Lang::txt('JGLOBAL_LT') || Lang::txt('JGLOBAL_GT')) {
                $pnSpace = ' ';
            }

            if ($row->prev) {
                $prevRoute = \Components\Content\Site\Helpers\Route::getArticleRoute(
                    $row->prev->slug,
                    $row->prev->catslug,
                    $row->prev->language,
                );
                $row->prev = Route::url($prevRoute);
            } else {
                $row->prev = '';
            }

            if ($row->next) {
                $nextRoute = \Components\Content\Site\Helpers\Route::getArticleRoute(
                    $row->next->slug,
                    $row->next->catslug,
                    $row->next->language,
                );
                $row->next = Route::url($nextRoute);
            } else {
                $row->next = '';
            }

            // Output.
            if ($row->prev || $row->next) {
                $html = '<ul class="pagenav">';
                if ($row->prev) {
                    $prevText = Lang::txt('JGLOBAL_LT') . $pnSpace . Lang::txt('JPREV');
                    $html .= '
						<li class="pagenav-prev">
							<a href="' . $row->prev . '" rel="prev">' . $prevText . '</a>
						</li>';
                }

                if ($row->next) {
                    $nextText = Lang::txt('JNEXT') . $pnSpace . Lang::txt('JGLOBAL_GT');
                    $html .= '
						<li class="pagenav-next">
							<a href="' . $row->next . '" rel="next">' . $nextText . '</a>
						</li>';
                }
                $html .= '</ul>';

                $row->pagination = $html;
                $row->paginationposition = $this->params->get('position', 1);
                // This will default to the 1.5 and 1.6-1.7 behavior.
                $row->paginationrelative = $this->params->get('relative', 0);
            }
        }

        return null;
    }
}

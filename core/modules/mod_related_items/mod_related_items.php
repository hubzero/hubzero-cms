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

        $cacheparams = new stdClass();
        $cacheparams->cachemode    = 'safeuri';
        $cacheparams->class        = '\Modules\RelatedItems\Helper';
        $cacheparams->method       = 'getList';
        $cacheparams->methodparams = $params;
        $cacheparams->modeparams   = array(
            'id'     => 'int',
            'Itemid' => 'int'
        );

        $list = \Hubzero\Facades\Module::cache($this->module, $params, $cacheparams);

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
     * @param   object  $params  Registry
     * @return  array
     */
    public static function getList($params)
    {
        $db     = \Hubzero\Facades\App::get('db');
        $userId = (int) User::get('id');
        $count  = intval($params->get('count', 5));
        $groups = implode(',', User::getAuthorisedViewLevels());
        $date   = \Hubzero\Facades\Date::toSql();

        $option = Request::getCmd('option');
        $view   = Request::getCmd('view');

        $temp   = Request::getString('id');
        $temp   = explode(':', $temp);
        $id     = $temp[0];

        $showDate = $params->get('showDate', 0);
        $nullDate = $db->getNullDate();
        $now      = $date->toSql();
        $related  = array();
        $query    = $db->getQuery();

        if ($option == 'com_content' && $view == 'article' && $id) {
            // Select the meta keywords from the item
            $query->select('metakey');
            $query->from('#__content');
            $query->where('id', '=', (int) $id);
            if ($metakey = trim((string) $query->value('metakey'))) {
                // Explode the meta keys on a comma
                $keys  = explode(',', $metakey);
                $likes = array();

                // Assemble any non-blank word(s)
                foreach ($keys as $key) {
                    $key = trim($key);
                    if ($key) {
                        $likes[] = $db->escape($key);
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

                    // sqlsrv changes
                    $case_when = ' CASE WHEN ';
                    $case_when .= $query->charLength('a.alias');
                    $case_when .= ' THEN ';
                    $a_id = $query->castAsChar('a.id');
                    $case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
                    $case_when .= ' ELSE ';
                    $case_when .= $a_id . ' END as slug';
                    $query->select($case_when);

                    $case_when = ' CASE WHEN ';
                    $case_when .= $query->charLength('cc.alias');
                    $case_when .= ' THEN ';
                    $c_id = $query->castAsChar('cc.id');
                    $case_when .= $query->concatenate(array($c_id, 'cc.alias'), ':');
                    $case_when .= ' ELSE ';
                    $case_when .= $c_id . ' END as catslug';
                    $query->select($case_when);
                    $query->from('#__content AS a');
                    $query->leftJoin('#__content_frontpage AS f ON f.content_id = a.id');
                    $query->leftJoin('#__categories AS cc ON cc.id = a.catid');
                    $query->where('a.id', '!=', (int) $id);
                    $query->where('a.state', '=', 1);
                    $query->where('a.access', 'IN', '(' . $groups . ')');
                    $concat_string = $query->concatenate(
                        array('","', ' REPLACE(a.metakey, ", ", ",")', ' ","')
                    );
                    // TODO: Fix the where clauses below. Not sure how to break
                    // the params out to work with the new where clause
                    // remove single space after commas in keywords
                    $likeConditions = implode(
                        '%" OR ' . $concat_string . ' LIKE "%',
                        $likes
                    );
                    $query->where('(' . $concat_string . ' LIKE "%' . $likeConditions . '%")');
                    $query->where(
                        '(a.publish_up IS NULL OR a.publish_up = ' . $db->quote($nullDate) .
                        ' OR a.publish_up <= ' . $db->quote($now) . ')'
                    );
                    $query->where(
                        '(a.publish_down IS NULL OR a.publish_down = ' . $db->quote($nullDate) .
                        ' OR a.publish_down >= ' . $db->quote($now) . ')'
                    );

                    // Filter by language
                    if (\Hubzero\Facades\App::get('language.filter')) {
                        $query->where(
                            'a.language in (' . $db->quote(Lang::getTag()) . ',' .
                            $db->quote('*') . ')'
                        );
                    }

                    $temp = $query->fetch();

                    if (count($temp)) {
                        foreach ($temp as $row) {
                            if ($row->cat_state == 1) {
                                $articleRoute = \Components\Content\Site\Helpers\Route::getArticleRoute(
                                    $row->slug,
                                    $row->catslug,
                                    $row->language
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

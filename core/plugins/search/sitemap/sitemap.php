<?php

namespace Plugins\Search\Sitemap;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\Document;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Search plugin for site map
 */
/**
 */
class Sitemap extends Plugin
{
    /**
     * Get the plugin name
     *
     * @return  string
     */
    public static function getName()
    {
        return 'Site Map';
    }

    /**
     * On search
     *
     * @param   object  $request
     * @param   object  &$results
     * @return  void
     */
    public static function onSearch($request, &$results)
    {
        $terms = $request->get_term_ar();
        $weight = 'match(s.title, s.description) against (\'' . join(' ', $terms['stemmed']) . '\')';

        $addtl_where = array();
        foreach ($terms['mandatory'] as $mand) {
            $addtl_where[] = "(s.title LIKE '%$mand%' OR s.description LIKE '%$mand%')";
        }
        foreach ($terms['forbidden'] as $forb) {
            $addtl_where[] = "(s.title NOT LIKE '%$forb%' AND s.description NOT LIKE '%$forb%')";
        }

        $results->add(new \Components\Search\Models\Basic\Result\Sql(
            "SELECT
				title, description, link, $weight as weight
			FROM
				`#__ysearch_site_map` s
			WHERE $weight > 0" . ($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '')
        ));
    }

    /**
     * Show administrative options
     *
     * @param   array  $context
     * @return  array
     */
    public function onSearchAdministrate($context)
    {
        $dbh = App::get('db');
        $dbh->setQuery('SELECT id, title, link, description FROM `#__ysearch_site_map` ORDER BY title');
        $map = $dbh->loadAssocList();

        $edit = null;
        $hasSitemap = array_key_exists('sitemap', $context);
        $hasEditId = $hasSitemap && array_key_exists('edit_id', $context['sitemap']);
        $hasSaveId = $hasSitemap && array_key_exists('save_id', $context['sitemap']);
        $saveMatchesEdit = $hasSaveId
            && $context['sitemap']['save_id'] == $context['sitemap']['edit_id'];
        if ($hasSitemap && $hasEditId && !$saveMatchesEdit) {
            $edit = $context['sitemap']['edit_id'];
        }

        $view = $this->view('default', 'sitemap');
        $view->set('map', $map);
        $view->set('edit', $edit);

        return array('Site Map', $view->loadTemplate());
    }

    /**
     * Save an entry from POST data
     *
     * @param   boolean  $update
     * @return  array
     */
    private static function saveEntryFromPost($update = false)
    {
        $dbh = App::get('db');
        $fields = array('sm-title', 'sm-link', 'sm-description');
        if ($update) {
            $fields[] = 'sm-id';
        }

        foreach ($fields as $key) {
            if (!$update) {
                $key = 'new-' . $key;
            }
            if (!array_key_exists($key, $_POST) || empty($_POST[$key])) {
                $errorMsg = self::alertHtml(
                    Lang::txt('COM_SEARCH_ERROR_REQUIRED_FIELDS'),
                    'error'
                );
                return array('sitemap', $errorMsg, array());
            }
        }

        $id = null;
        if ($update) {
            $updateSql = 'UPDATE `#__ysearch_site_map` SET title = '
                . $dbh->quote($_POST['sm-title'])
                . ', description = ' . $dbh->quote($_POST['sm-description'])
                . ', link = ' . $dbh->quote($_POST['sm-link'])
                . ' WHERE id = ' . (int)$_POST['sm-id'];
            $dbh->execute($updateSql);
            $id = (int)$_POST['sm-id'];
        } else {
            $insertSql = 'INSERT INTO `#__ysearch_site_map` (title, description, link) VALUES ('
                . $dbh->quote($_POST['new-sm-title']) . ', '
                . $dbh->quote($_POST['new-sm-description']) . ', '
                . $dbh->quote($_POST['new-sm-link']) . ')';
            $dbh->execute($insertSql);
            unset($_POST['new-sm-title']);
            unset($_POST['new-sm-description']);
            unset($_POST['new-sm-link']);
            $id = $dbh->insertid();
        }
        $successMsg = self::alertHtml(
            Lang::txt('COM_SEARCH_ENTRY_SAVED'),
            'success'
        );
        return array('sitemap', $successMsg, array('save_id' => $id));
    }

    /**
     * Edit site map
     *
     * @return  array
     */
    public static function onSearchTaskSiteMapEdit()
    {
        if (array_key_exists('add', $_POST)) {
            return self::saveEntryFromPost();
        }
        foreach ($_POST as $k => $v) {
            if (preg_match('/(delete|edit)-(\d+)/', $k, $id)) {
                if ($id[1] == 'edit') {
                    return array('sitemap', '', array('edit_id' => (int)$id[2]));
                } else {
                    $dbh = App::get('db');
                    $dbh->execute('DELETE FROM `#__ysearch_site_map` WHERE id = ' . (int)$id[2]);
                    $deleteMsg = self::alertHtml(
                        Lang::txt('COM_SEARCH_ENTRY_DELETED'),
                        'success'
                    );
                    return array('sitemap', $deleteMsg, array());
                }
            }
        }
        return array('sitemap', '', array());
    }

    /**
     * Save edit
     *
     * @return  mixed
     */
    public static function onSearchTaskSiteMapSaveEdit()
    {
        if (array_key_exists('cancel', $_POST)) {
            return array('sitemap', '', array());
        }

        return self::saveEntryFromPost(true);
    }

    /**
     * Build framework-appropriate alert markup.
     *
     * @param   string  $message  Alert text
     * @param   string  $type     Alert type (error or success)
     * @return  string
     */
    private static function alertHtml(string $message, string $type): string
    {
        if (Document::getCssFramework() === 'daisyui') {
            return '<div role="alert" class="alert alert-' . $type . ' mb-4"><span>'
                . $message . '</span></div>';
        }

        return '<p class="' . $type . '">' . $message . '</p>';
    }
}

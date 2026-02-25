<?php

namespace Plugins\Search\Sitemap;

use Hubzero\Plugin\Plugin;
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
    public static function onSearchAdministrate($context)
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
        if (
            $hasSitemap
            && $hasEditId
            && !$saveMatchesEdit
        ) {
            $edit = $context['sitemap']['edit_id'];
        }

        $html = array();
        $html[] = '<form action="index.php?option=com_search" method="post">';
        $html[] = '<input type="hidden" name="search-task" value="SiteMap' . ($edit ? 'SaveEdit' : 'Edit') . '" />';
        $html[] = '<table class="adminlist">';
        $html[] = '<thead>';
        $html[] = '<tr><th>' . Lang::txt('COM_SEARCH_COL_TITLE') . '</th>'
            . '<th>' . Lang::txt('COM_SEARCH_COL_LINK') . '</th>'
            . '<th>' . Lang::txt('COM_SEARCH_COL_DESCRIPTION') . '</th>'
            . '<th></th></tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        foreach ($map as $item) {
            $html[] = '<tr>';
            if ($edit == $item['id']) {
                $titleVal = array_key_exists('sm-title', $_POST)
                    ? $_POST['sm-title']
                    : $item['title'];
                $html[] = '<td><input type="text" name="sm-title" value="'
                    . htmlentities($titleVal) . '" /></td>';
                $linkVal = array_key_exists('sm-link', $_POST)
                    ? $_POST['sm-link']
                    : $item['link'];
                $html[] = '<td><input type="text" name="sm-link" value="'
                    . htmlentities($linkVal) . '" /></td>';
                $descVal = array_key_exists('sm-description', $_POST)
                    ? $_POST['sm-description']
                    : $item['description'];
                $html[] = '<td><textarea cols="60" rows="3" name="sm-description">'
                    . htmlentities($descVal) . '</textarea></td>';
                $html[] = '<td><input type="hidden" name="sm-id" value="' . $item['id'] . '" />'
                    . '<input type="submit" name="save" value="'
                    . Lang::txt('COM_SEARCH_SAVE') . '" />'
                    . '<input type="submit" name="cancel" value="'
                    . Lang::txt('COM_SEARCH_CANCEL') . '" /></td>';
            } else {
                $html[] = '<td>' . htmlentities($item['title']) . '</td>';
                $html[] = '<td>' . htmlentities($item['link']) . '</td>';
                $html[] = '<td>' . htmlentities($item['description']) . '</td>';
                if ($edit) {
                    $html[] = '<td></td>';
                } else {
                    $html[] = '<td>'
                        . '<input type="hidden" name="ysearch-task" value="SiteMapEdit" />'
                        . '<input type="submit" name="edit-' . $item['id'] . '" value="'
                        . Lang::txt('COM_SEARCH_EDIT') . '" />'
                        . '<input type="submit" name="delete-' . $item['id'] . '" value="'
                        . Lang::txt('COM_SEARCH_DELETE') . '" /></td>';
                }
            }
            $html[] = '</tr>';
        }
        if (!$edit) {
            $html[] = '<tr>';
            $newTitleVal = array_key_exists('new-sm-title', $_POST)
                ? $_POST['new-sm-title']
                : '';
            $html[] = '<td><input type="text" name="new-sm-title" value="'
                . htmlentities($newTitleVal) . '" /></td>';
            $newLinkVal = array_key_exists('new-sm-link', $_POST)
                ? $_POST['new-sm-link']
                : '';
            $html[] = '<td><input type="text" name="new-sm-link" value="'
                . htmlentities($newLinkVal) . '" /></td>';
            $newDescVal = array_key_exists('new-sm-description', $_POST)
                ? $_POST['new-sm-description']
                : '';
            $html[] = '<td><textarea cols="60" rows="3" name="new-sm-description">'
                . htmlentities($newDescVal) . '</textarea></td>';
            $html[] = '<td><input type="submit" name="add" value="'
                . Lang::txt('COM_SEARCH_ADD') . '" /></td>';
            $html[] = '</tr>';
        }
        $html[] = '</tbody>';
        $html[] = '</table>';
        $html[] = '</form>';
        return array('Site Map', join("\n", $html));
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
                $errorMsg = '<p class="error">'
                    . Lang::txt('COM_SEARCH_ERROR_REQUIRED_FIELDS') . '</p>';
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
        $successMsg = '<p class="success">' . Lang::txt('COM_SEARCH_ENTRY_SAVED') . '</p>';
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
                    $deleteMsg = '<p class="success">'
                        . Lang::txt('COM_SEARCH_ENTRY_DELETED') . '</p>';
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
}

<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Helpers\Html;

/**
 * Extended Utility class for the Users component.
 */
class HtmlUsers
{
    /**
     * Display an image.
     *
     * @param   string  $src  The source of the image
     * @return  string  A <img> element if the specified file exists, otherwise, a null string
     */
    public static function image($src)
    {
        $src = preg_replace('#[^A-Z0-9\-_\./]#i', '', $src);
        $file = PATH_ROOT . '/' . $src;

        Hubzero\Filesystem\Util::checkPath($file);

        if (!file_exists($file)) {
            return '';
        }

        return '<img src="' . Request::root() . $src . '" alt="" />';
    }

    /**
     * Displays an icon to add a note for this user.
     *
     * @param   integer  $userId  The user ID
     * @return  string   A link to add a note
     */
    public static function addNote($userId)
    {
        $title = Lang::txt('COM_MEMBERS_ADD_NOTE');
        $url = Route::url('index.php?option=com_members&controller=notes&task=add&u_id=' . (int) $userId);
        $notes = Lang::txt('COM_MEMBERS_NOTES');

        return '<a class="state notes" href="' . $url . '" title="' . $title . '"><span>' . $notes . '</span></a>';
    }

    /**
     * Displays an icon to filter the notes list on this user.
     *
     * @param   integer  $count   The number of notes for the user
     * @param   integer  $userId  The user ID
     * @return  string   A link to apply a filter
     */
    public static function filterNotes($count, $userId)
    {
        if (empty($count)) {
            return '';
        }

        $title = Lang::txt('COM_MEMBERS_FILTER_NOTES');
        $url = Route::url('index.php?option=com_members&controller=notes&filter_search=uid:' . (int) $userId);
        $notes = Lang::txt('COM_MEMBERS_NOTES');

        return '<a class="state filter" href="' . $url . '" title="' . $title . '"><span>' . $notes . '</span></a>';
    }

    /**
     * Displays a note icon.
     *
     * @param   integer  $count   The number of notes for the user
     * @param   integer  $userId  The user ID
     * @return  string   A link to a modal window with the user notes
     */
    public static function notes($count, $userId)
    {
        if (empty($count)) {
            return '';
        }

        $title = Lang::txts('COM_MEMBERS_N_USER_NOTES', $count);
        $url = Route::url(
            'index.php?option=com_members&controller=notes&tmpl=component&layout=modal&u_id=' . (int) $userId
        );
        $notes = Lang::txt('COM_MEMBERS_NOTES');

        return '<a class="modal state notes"'
            . ' href="' . $url . '"'
            . ' rel="{handler: \'iframe\', size: {x: 800, y: 450}}" title="' . $title . '">'
            . '<span>' . $notes . '</span></a>';
    }
}

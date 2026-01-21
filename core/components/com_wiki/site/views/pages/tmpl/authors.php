<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if ($this->page->param('mode', 'wiki') == 'knol' && !$this->page->param('hide_authors', 0)) {
    $author = $this->escape(stripslashes($this->page->creator->get('name', Lang::txt('COM_WIKI_UNKNOWN'))));

    $auths = array();
    $creatorLevels = User::getAuthorisedViewLevels();
    $creatorAccess = $this->page->creator->get('access');
    $creatorLink = '<a href="' . Route::url($this->page->creator->link()) . '">' . $author . '</a>';
    $auths[] = (in_array($creatorAccess, $creatorLevels) ? $creatorLink : $author);

    foreach ($this->page->authors()->rows() as $auth) {
        if ($auth->get('user_id') == $this->page->get('created_by')) {
            continue;
        }

        $name = $this->escape(stripslashes($auth->user->get('name', '')));
        $authLink = '<a href="' . Route::url($auth->user->link()) . '">' . $name . '</a>';
        $name = (in_array($auth->user->get('access'), $creatorLevels) ? $authLink : $name);

        $auths[] = $name;
    }
    ?>
    <p class="topic-authors"><?php echo Lang::txt('COM_WIKI_BY_AUTHORS', implode(', ', $auths)); ?></p>
    <?php
}

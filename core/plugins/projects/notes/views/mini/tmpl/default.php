<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div class="sidebox<?php if (count($this->notes) == 0) {
    echo ' suggestions';
                   } ?>">
    <?php
    $notesUrl = Route::url($this->model->link('notes'));
    $notesTitle = Lang::txt('COM_PROJECTS_VIEW') . ' '
        . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) . ' '
        . strtolower(Lang::txt('PLG_PROJECTS_NOTES'));
    $newNoteUrl = Route::url(
        $this->model->link('notes') . '&action=new'
    );
    ?>
    <h4>
        <a href="<?php echo $notesUrl; ?>"
            class="hlink"
            title="<?php echo $notesTitle; ?>"
            ><?php echo ucfirst(Lang::txt('PLG_PROJECTS_NOTES')); ?></a>
        <?php if (count($this->notes) > 0) { ?>
            <span><a href="<?php echo $notesUrl; ?>"><?php
                echo ucfirst(Lang::txt('COM_PROJECTS_SEE_ALL'));
            ?> </a></span>
        <?php } ?>
    </h4>
    <?php if (count($this->notes) == 0) { ?>
        <p class="s-notes"><a href="<?php echo $newNoteUrl; ?>"><?php
            echo Lang::txt('PLG_PROJECTS_NOTES_ADD_NOTE');
        ?></a></p>
    <?php } else { ?>
        <ul>
            <?php foreach ($this->notes as $note) {
                $pagePath = ($note->path ? $note->path . '/' : '')
                    . $note->pagename;
                $noteUrl = Route::url(
                    $this->model->link('notes')
                    . '&pagename=' . $pagePath
                );
                $noteTitle = $this->escape(
                    \Hubzero\Utility\Str::truncate($note->title, 35)
                );
                ?>
                <li>
                    <a href="<?php echo $noteUrl; ?>"
                        class="notes"><?php echo $noteTitle; ?></a>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
</div>

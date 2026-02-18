<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Sort notes to display hierarchy by scope
$notes = array();
$order = array();
$thirdlevel = array();

$pNotes = $this->note->getNotes();
if ($pNotes) {
    foreach ($pNotes as $note) {
        $show = 1;

        if (!$show) {
            // Skip
            continue;
        }

        $level = 1;
        $parent = '';

        if (trim($note->path)) {
            $parts = explode('/', trim($note->path));
            $level = count($parts) + 1;
            $parent = $level > 1 ? array_pop($parts) : '';
        }

        if ($level == 1) {
            $notes[$note->pagename] = array($level => array($note));
        } elseif ($level == 2) {
            $notes[$parent][$level][] = $note;
        } elseif ($level >= 3) {
            $thirdlevel[$parent][] = $note;
        }
    }
}
?>
<div class="notes-list">
    <h4><?php echo ucfirst(Lang::txt('COM_PROJECTS_NOTES_MULTI')); ?></h4>
    <ul>
        <?php if ($notes) { ?>
            <?php
            foreach ($notes as $note) {
                foreach ($note as $level => $parent) {
                    foreach ($parent as $entry) {
                        ?>
                        <li <?php if ($entry->pagename == $this->page->get('pagename')) {
                            echo 'class="active"';
                            } ?>>
                            <?php
                                $entryUrl = Route::url(
                                    'index.php?option=' . $this->option
                                    . '&alias=' . $this->project->get('alias')
                                    . '&active=notes&pagename='
                                    . ($entry->path ? $entry->path . '/' : '')
                                    . $entry->pagename
                                );
                                $entryTitle = \Hubzero\Utility\Str::truncate($entry->title, 35);
                            ?>
                            <a href="<?php echo $entryUrl; ?>"
                                class="note wikilevel_<?php echo $level; ?>"
                            ><?php echo $entryTitle; ?></a>
                        </li>
                        <?php
                        // Third level of notes
                        if (isset($thirdlevel[$entry->pagename]) && count($thirdlevel[$entry->pagename]) > 0) {
                            foreach ($thirdlevel[$entry->pagename] as $subpage) {
                                ?>
                                <li <?php if ($subpage->pagename == $this->page->get('pagename')) {
                                    echo 'class="active"';
                                    } ?>>
                                    <?php
                                        $subUrl = Route::url(
                                            'index.php?option=' . $this->option
                                            . '&alias=' . $this->project->get('alias')
                                            . '&active=notes&pagename='
                                            . ($subpage->path ? $subpage->path . '/' : '')
                                            . $subpage->pagename
                                        );
                                        $subTitle = \Hubzero\Utility\Str::truncate($subpage->title, 35);
                                    ?>
                                    <a href="<?php echo $subUrl; ?>"
                                        class="note wikilevel_3"
                                    ><?php echo $subTitle; ?></a>
                                </li>
                                <?php
                            }
                        }
                    }
                }
            }
            ?>
        <?php } else { ?>
            <li class="faded"><?php echo Lang::txt('COM_PROJECTS_NOTES_NO_NOTES'); ?></li>
        <?php } ?>
    </ul>
</div>

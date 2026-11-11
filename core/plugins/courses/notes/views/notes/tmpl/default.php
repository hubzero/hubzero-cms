<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$this
     ->css()
     ->js();

$results = [];

$entries = \Plugins\Courses\Notes\Models\Note::all()
    ->whereEquals('section_id', $this->offering->section()->get('id'))
    ->whereEquals('created_by', User::get('id'))
    ->whereEquals('state', 1);

if ($this->filters['search']) {
    $entries->whereLike('content', $this->filters['search']);
}
$notes = $entries->rows();

if ($notes->count()) {
    foreach ($notes as $note) {
        $ky = $note->get('scope_id');
        if (!isset($results[$ky])) {
            $results[$ky] = [];
        }
        $results[$ky][] = $note;
    }
}

$base = $this->offering->link();
?>

<?php if ($this->course->offering()->section()->access('view')) : ?>
    <form action="<?php echo Route::url($base . '&active=notes'); ?>" method="get">
        <fieldset class="filters">
            <div class="filters-inner">
                <ul>
                    <li>
                        <a class="download btn"
                            href="<?= Route::url($base . '&active=notes&action=download&frmt=txt') ?>">
                            <span><?php echo Lang::txt('PLG_COURSES_NOTES_DOWNLOAD'); ?></span>
                        </a>
                    </li>
                </ul>
                <div class="clear"></div>
                <p>
                    <label for="filter-search">
                        <span><?php echo Lang::txt('PLG_COURSES_NOTES_SEARCH'); ?></span>
                        <input type="text" name="search" id="filter-search"
                            value="<?= $this->escape($this->filters['search']) ?>"
                            placeholder="<?= Lang::txt('PLG_COURSES_NOTES_SEARCH_NOTES') ?>" />
                    </label>
                    <input type="submit" class="filter-submit" value="<?= Lang::txt('PLG_COURSES_NOTES_GO') ?>" />
                </p>
            </div><!-- / .filters-inner -->
        </fieldset>
    </form>

    <div class="notes-wrap">
    <?php if (count($results)) : ?>
        <?php
        // Pre-load lectures and their unit display order, then sort by unit
        // ordering (not unit id) and lecture ordering within the unit.
        $lectures  = [];
        $unitOrder = [];
        foreach ($results as $id => $notes) {
            $lectures[$id]  = new \Components\Courses\Models\Assetgroup($id);
            $unitOrder[$id] = (int) \Components\Courses\Models\Unit::getInstance($lectures[$id]->get('unit_id'))->get(
                'ordering',
                0,
            );
        }
        uksort($results, function ($a, $b) use ($lectures, $unitOrder) {
            if ($unitOrder[$a] != $unitOrder[$b]) {
                return $unitOrder[$a] - $unitOrder[$b];
            }
            return $lectures[$a]->get('ordering', 0) - $lectures[$b]->get('ordering', 0);
        });

        foreach ($results as $id => $notes) :
            $lecture = $lectures[$id];
            $unit = \Components\Courses\Models\Unit::getInstance($lecture->get('unit_id'));
            ?>
            <div class="section">
                <h3><?php echo $this->escape(stripslashes($lecture->get('title'))); ?></h3>
                <?php foreach ($notes as $note) : ?>
                    <div class="jSticky-medium static<?= $note->get('access') ? ' annotation' : '' ?>"
                        id="note-<?= $note->get('id') ?>" data-id="<?= $note->get('id') ?>">
                        <div class="jSticky-header">
                            <?php if ($note->get('timestamp') && $note->get('timestamp') != '00:00:00') : ?>
                                <?php
                                $linkUrl = str_replace(
                                    '%3A',
                                    ':',
                                    Route::url(
                                        $base . '&active=outline&unit=' . $unit->get('alias') . '&b='
                                            . $lecture->get('alias') . '&time='
                                            . $this->escape($note->get('timestamp')),
                                    ),
                                );
                                ?>
                                <a href="<?= $linkUrl ?>" class="time"><?= $this->escape($note->get('timestamp')) ?></a>
                            <?php endif; ?>
                        </div>
                        <div class="jStickyNote">
                            <textarea name="note_<?= $note->get('id') ?>"><?= $this->escape(stripslashes($note->get('content'))) ?></textarea>
                        </div>
                        <?php
                        $deleteUrl = Route::url($base . '&active=notes&action=delete&note=' . $note->get('id'));
                        ?>
                        <button type="submit" form="hz-post-action" formaction="<?= $deleteUrl ?>"
                            class="jSticky-delete" title="<?= Lang::txt('PLG_COURSES_NOTES_DELETE_NOTE') ?>">x</button>
                    </div>
                <?php endforeach; ?>
                <div class="clear"></div>
            </div>
            <?php
        endforeach; ?>
        <script type="text/javascript">
        jQuery(document).ready(function(jQuery) {
            var $ = jQuery;

            var url = "<?= Request::base(true) . '/' . $this->offering->link() . '&active=notes&no_html=1&note=' ?>";

            $('#page_content textarea').each(function(i, el) {
                var hgt = $(this).parent().parent().height();
                    $(this).css('height', hgt - 32);

                $(el).on('keyup', function (e) {
                    var id  = $(this).parent().parent().attr('data-id'),
                        txt = $(this).val();

                    typewatch(function () {
                        $.post(url + id + '&action=save', $('#hz-post-action').serialize() + '&' + $.param({txt: txt}), function(data) {
                            // Nothing going on here...
                        }, 'json');
                    }, 500);
                });
            });
        });
        </script>
    <?php else : ?>
        <div id="notes-introduction">
            <div class="instructions">
                <ol>
                    <li><?php echo Lang::txt('PLG_COURSES_NOTES_STEP1'); ?></li>
                    <li><?php echo Lang::txt('PLG_COURSES_NOTES_STEP2'); ?></li>
                    <li><?php echo Lang::txt('PLG_COURSES_NOTES_STEP3'); ?></li>
                    <li><?php echo Lang::txt('PLG_COURSES_NOTES_STEP4'); ?></li>
                </ol>
            </div><!-- / .instructions -->
            <div class="questions">
                <p><strong><?php echo Lang::txt('PLG_COURSES_NOTES_WHERE_IS_SAVE_BUTTON'); ?></strong></p>
                <p><?php echo Lang::txt('PLG_COURSES_NOTES_WHERE_IS_SAVE_BUTTON_EXPLANATION'); ?></p>
                <p><strong><?php echo Lang::txt('PLG_COURSES_NOTES_WHO_CAN_SEE_MY_NOTES'); ?></strong></p>
                <p><?php echo Lang::txt('PLG_COURSES_NOTES_WHO_CAN_SEE_MY_NOTES_EXPLANATION'); ?></p>
            </div><!-- / .post-type -->
        </div><!-- / #notes-introduction -->
    <?php endif; ?>
    </div>
<?php else : ?>
    <?php
    $view = new \Hubzero\Plugin\View([
        'folder'  => 'courses',
        'element' => 'outline',
        'name'    => 'shared',
        'layout'  => '_not_enrolled',
    ]);

    $view
         ->set('course', $this->course)
         ->set('option', $this->option)
         ->set('message', Lang::txt('PLG_COURSES_NOTES_ENROLLMENT_REQUIRED'))
         ->display();
    ?>
<?php endif;

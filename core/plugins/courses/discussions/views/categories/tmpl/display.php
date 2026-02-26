<?php

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// @phpcs:disable PSR1.Files.SideEffects
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = $this->offering->link() . '&active=forum';
?>

<section class="main section">
    <div class="section-inner">
        <form action="<?php echo Route::url($base); ?>" method="post">
            <div class="container data-entry">
                <input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('Search'); ?>" />
                <fieldset class="entry-search">
                    <legend><?php echo Lang::txt('Search for posts'); ?></legend>

                    <label for="entry-search-field"><?php echo Lang::txt('Enter keyword or phrase'); ?></label>
                    <input type="text" name="q" id="entry-search-field"
                        value="<?php echo $this->escape($this->filters['search']); ?>" />

                    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                    <input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
                    <input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
                    <input type="hidden" name="active" value="forum" />
                    <input type="hidden" name="action" value="search" />
                </fieldset>
            </div><!-- / .container -->

            <?php if ($this->category->closed) { ?>
                <p class="warning">
                    <?php echo Lang::txt('This category is closed and no new discussions may be created.'); ?>
                </p>
            <?php } ?>

            <div class="container">
                <table class="entries">
                    <caption>
                        <?php
                        if ($this->filters['search']) {
                            if ($this->category->get('id')) {
                                echo Lang::txt(
                                    'Search for "%s" in "%s"',
                                    $this->escape($this->filters['search']),
                                    $this->escape(stripslashes($this->category->get('title')))
                                );
                            } else {
                                echo Lang::txt('Search for "%s"', $this->escape($this->filters['search']));
                            }
                        } else {
                            if ($this->category->get('id')) {
                                echo Lang::txt(
                                    'Discussions in "%s"',
                                    $this->escape(stripslashes($this->category->get('title')))
                                );
                            } else {
                                echo Lang::txt('Discussions');
                            }
                        }
                        ?>
                    </caption>
                    <?php if (!$this->category->get('closed')) { ?>
                        <?php
                        $canDelete = $this->config->get('access-delete-thread');
                        $canEdit = $this->config->get('access-edit-thread');
                        $footColspan = ($canDelete || $canEdit) ? '5' : '4';
                        $addUrl = Route::url(
                            $base . '&unit='
                            . $this->filters['category'] . '&b=new'
                        );
                        ?>
                        <tfoot>
                            <tr>
                                <td colspan="<?php echo $footColspan; ?>">
                                    <a class="add btn"
                                        href="<?php echo $addUrl; ?>">
                                        <?php echo Lang::txt('Add Discussion'); ?>
                                    </a>
                                </td>
                            </tr>
                        </tfoot>
                    <?php } ?>
                    <tbody>
                        <?php
                        if ($this->threads->count() > 0) {
                            foreach ($this->threads as $row) {
                                $name = Lang::txt('JANONYMOUS');
                                if (!$row->get('anonymous')) {
                                    $name = $this->escape(stripslashes($row->creator->get('name', $name)));
                                    if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels())) {
                                        $name = '<a href="' . Route::url($row->creator->link()) . '">' . $name . '</a>';
                                    }
                                }
                                $cls = array();
                                if ($row->isClosed()) {
                                    $cls[] = 'closed';
                                }
                                if ($row->isSticky()) {
                                    $cls[] = 'sticky';
                                }

                                $row->set('category', $this->filters['category']);
                                $row->set('section', $this->filters['section']);
                                ?>
                                <tr<?php if (count($cls) > 0) {
                                    echo ' class="' . implode(' ', $cls) . '"';
                                   } ?>>
                                    <th>
                                        <span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
                                    </th>
                                    <td>
                                        <?php
                                        $entryUrl = Route::url(
                                            $base . '&unit='
                                            . $this->filters['category']
                                            . '&b=' . $row->get('id')
                                        );
                                        ?>
                                        <a class="entry-title"
                                            href="<?php echo $entryUrl; ?>">
                                            <span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
                                        </a>
                                        <?php
                                        $createdDatetime = $row->created();
                                        $createdDate = $row->created('date');
                                        $authorSpan = '<span class="entry-author">'
                                            . $name . '</span>';
                                        $byUser = Lang::txt(
                                            'PLG_COURSES_DISCUSSIONS_BY_USER',
                                            $authorSpan
                                        );
                                        ?>
                                        <span class="entry-details">
                                            <span class="entry-date">
                                                <time datetime="<?php echo $createdDatetime; ?>">
                                                    <?php echo $createdDate; ?>
                                                </time>
                                            </span>
                                            <?php echo $byUser; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span><?php
                                        echo $row->thread()
                                            ->whereEquals('state', $row->get('state'))
                                            ->whereIn('access', $this->filters['access'])
                                            ->total();
                                        ?></span>
                                        <span class="entry-details">
                                            <?php echo Lang::txt('Comments'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span><?php echo Lang::txt('Last Post:'); ?></span>
                                        <span class="entry-details">
                                            <?php
                                            $lastpost = $row->lastActivity();
                                            if ($lastpost->get('id')) {
                                                $lname = Lang::txt('JANONYMOUS');
                                                if (!$lastpost->get('anonymous')) {
                                                    $lname =
                                                    $this->escape(stripslashes($lastpost->creator->get('name')));
                                                    if (
                                                        in_array(
                                                            $lastpost->creator->get('access'),
                                                            User::getAuthorisedViewLevels()
                                                        )
                                                    ) {
                                                        $lname = '<a href="'
                                                            . Route::url($lastpost->creator->link())
                                                            . '">'
                                                            . $lname
                                                            . '</a>';
                                                    }
                                                }
                                                $lastDatetime = $lastpost->created();
                                                $lastDate = $lastpost->created('date');
                                                $lastAuthorSpan = '<span class="entry-author">'
                                                    . $lname . '</span>';
                                                $lastByUser = Lang::txt(
                                                    'PLG_COURSES_DISCUSSIONS_BY_USER',
                                                    $lastAuthorSpan
                                                );
                                                ?>
                                                <span class="entry-date">
                                                    <time datetime="<?php echo $lastDatetime; ?>">
                                                        <?php echo $lastDate; ?>
                                                    </time>
                                                </span>
                                                <?php echo $lastByUser; ?>
                                            <?php } else { ?>
                                                <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NONE'); ?>
                                            <?php } ?>
                                        </span>
                                    </td>
                                    <?php
                                    $canDeleteThread = $this->config->get('access-delete-thread');
                                    $canEditThread = $this->config->get('access-edit-thread');
                                    if ($canDeleteThread || $canEditThread) {
                                        $isCreator = $row->get('created_by') == User::get('id');
                                        $editUrl = Route::url(
                                            $base . '&scope='
                                            . $this->filters['category']
                                            . '&b=' . $row->get('id')
                                            . '&c=edit'
                                        );
                                        $deleteUrl = Route::url(
                                            $base . '&scope='
                                            . $this->filters['category']
                                            . '&b=' . $row->get('id')
                                            . '&c=delete'
                                        );
                                        ?>
                                        <td class="entry-options">
                                            <?php if ($isCreator || $canEditThread) { ?>
                                                <a class="edit"
                                                    href="<?php echo $editUrl; ?>">
                                                    <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_EDIT'); ?>
                                                </a>
                                            <?php } ?>
                                            <?php if ($canDeleteThread) { ?>
                                                <a class="delete"
                                                    href="<?php echo $deleteUrl; ?>">
                                                    <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_DELETE'); ?>
                                                </a>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                    </tr>
                                    <?php
                            }
                        } else {
                            $hasThreadAccess = $this->config->get('access-delete-thread')
                                || $this->config->get('access-edit-thread');
                            $emptyColspan = $hasThreadAccess ? '5' : '4';
                            ?>
                                <tr>
                                    <td colspan="<?php echo $emptyColspan; ?>">
                                        <?php echo Lang::txt('There are currently no discussions.'); ?>
                                    </td>
                                </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                if ($this->threads->count() > $this->filters['limit']) {
                    $pageNav = $this->threads->pagination;
                    $pageNav->setAdditionalUrlParam('gid', $this->course->get('alias'));
                    $pageNav->setAdditionalUrlParam('offering', $this->offering->get('alias'));
                    $pageNav->setAdditionalUrlParam('active', 'forum');
                    $pageNav->setAdditionalUrlParam('unit', $this->filters['category']);
                    echo $pageNav;
                }
                ?>
            </div><!-- / .container -->
        </form>
    </div>
</section><!-- /.main -->
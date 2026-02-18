<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$this->css();
?>
    <?php /*if ($v = $this->publication->version->get('forked_from')) { ?>
            <?php
            $db = App::get('db');
            $db->setQuery("SELECT publication_id FROM `#__publication_versions` WHERE `id`=" . $db->quote($v));

            $p = $db->loadResult();

            $publication = new Components\Publications\Models\Publication($p, 'default', $v);
            ?>
            <h3 class="section-header">
                <?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_FROM'); ?>
            </h3>
            <div class="publication-fork icon-fork fork-source">
                <div class="publication-datetime">
                    <?php
                    $created = $publication->version->get('created');
                    $dateFormatted = Date::of($created)->toLocal(
                        Lang::txt('DATE_FORMAT_HZ1')
                    );
                    $timeFormatted = Date::of($created)->toLocal(
                        Lang::txt('TIME_FORMAT_HZ1')
                    );
                    ?>
                    <span class="publication-date">
                        <time datetime="<?php echo $created; ?>">
                            <?php echo $dateFormatted; ?>
                        </time>
                    </span>
                    <span class="publication-time">
                        <time datetime="<?php echo $created; ?>">
                            <?php echo $timeFormatted; ?>
                        </time>
                    </span>
                </div>
                <div class="publication-details">
                    <div class="publication-title">
                        <?php if ($publication->version->get('state') == 1
                                && (
                                    !$publication->version->get('published_up')
                                    || $publication->version->get('published_up') == '0000-00-00 00:00:00'
                                    || $publication->version->get('published_up') <= Date::toSql()
                                )
                                && (
                                    !$publication->version->get('published_down')
                                    || $publication->version->get('published_down') == '0000-00-00 00:00:00'
                                    || $publication->version->get('published_down') > Date::toSql()
                                )
                            ) { ?>
                            <?php
                            $pubUrl = Route::url(
                                'index.php?option=com_publications'
                                . '&id=' . $publication->get('id')
                                . '&v=' . $publication->version->get('version_number')
                            );
                            ?>
                            <a href="<?php echo $pubUrl; ?>">
                                <?php echo $this->escape($publication->version->get('title')); ?>
                            </a>
                        <?php } else { ?>
                            <?php echo $this->escape($publication->version->get('title')); ?>
                            <span class="publication-status">
                                <?php echo Lang::txt('(unpublished)'); ?>
                            </span>
                        <?php } ?>
                        <?php
                        $versionLabel = $this->escape(
                            $publication->version->get('version_label')
                        );
                        $versionTitle = Lang::txt('Version');
                        ?>
                        <span class="publication-version">
                            <abbr title="<?php echo $versionTitle; ?>">v</abbr>
                            <?php echo $versionLabel; ?>
                        </span>
                    </div>
                    <div class="publication-meta">
                        <?php
                        $creator = User::getInstance($publication->version->get('created_by'));
                        $name = $this->escape($creator->get('name', Lang::txt('unknown')));
                        if (in_array($creator->get('access'), User::getAuthorisedViewLevels()))
                        {
                            $name = '<a href="'
                                . Route::url('index.php?option=com_members&id=' . $creator->get('id'))
                                . '">'
                                . $name
                                . '</a>';
                        }
                        ?>
                        <?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_FORKED_BY', $name); ?>
                    </div>
                </div>
            </div>
    <?php }*/ ?>

    <h3 class="section-header">
        <?php echo Lang::txt('PLG_PUBLICATIONS_FORKS'); ?>
    </h3>
    <div class="publication-forks">
        <?php if (count($this->forks)) { ?>
            <?php foreach ($this->forks as $publication) { ?>
                <?php
                $version = $publication->version;
                $pubUp = $version->get('published_up');
                $pubDown = $version->get('published_down');
                $noDate = '0000-00-00 00:00:00';
                $now = Date::toSql();

                $isPublished = $version->get('state') == 1
                    && (!$pubUp || $pubUp == $noDate
                        || ($pubUp != $noDate && $pubUp <= $now))
                    && (!$pubDown || $pubDown == $noDate
                        || ($pubDown != $noDate && $pubDown > $now));

                $created = $version->get('created');
                $dateFormatted = Date::of($created)->toLocal(
                    Lang::txt('DATE_FORMAT_HZ1')
                );
                $timeFormatted = Date::of($created)->toLocal(
                    Lang::txt('TIME_FORMAT_HZ1')
                );
                $forkClass = $isPublished
                    ? 'published'
                    : 'unpublished';
                ?>
                <div class="publication-fork icon-fork <?php echo $forkClass; ?>">
                    <div class="publication-datetime">
                        <span class="publication-date">
                            <time datetime="<?php echo $created; ?>">
                                <?php echo $dateFormatted; ?>
                            </time>
                        </span>
                        <span class="publication-time">
                            <time datetime="<?php echo $created; ?>">
                                <?php echo $timeFormatted; ?>
                            </time>
                        </span>
                    </div>
                    <div class="publication-details">
                        <div class="publication-title">
                            <?php if ($isPublished) { ?>
                                <?php
                                $pubUrl = Route::url(
                                    'index.php?option=com_publications'
                                    . '&id=' . $publication->get('id')
                                    . '&v=' . $version->get('version_number')
                                );
                                ?>
                                <a href="<?php echo $pubUrl; ?>">
                                    <?php echo $this->escape($version->get('title')); ?>
                                </a>
                            <?php } else { ?>
                                <?php echo $this->escape($version->get('title')); ?>
                                <span class="publication-status">
                                    <?php echo Lang::txt('COM_PUBLICATIONS_STATUS_UNPUBLISHED'); ?>
                                </span>
                            <?php } ?>
                            <?php
                            $versionLabel = $this->escape(
                                $version->get('version_label')
                            );
                            $versionTitle = Lang::txt(
                                'COM_PUBLICATIONS_VERSION'
                            );
                            ?>
                            <span class="publication-version">
                                <abbr title="<?php echo $versionTitle; ?>">v</abbr>
                                <?php echo $versionLabel; ?>
                            </span>
                        </div>
                        <div class="publication-meta">
                            <?php
                            $creator = User::getInstance(
                                $version->get('created_by')
                            );
                            $name = $this->escape(
                                $creator->get('name', Lang::txt('unknown'))
                            );
                            $viewLevels = User::getAuthorisedViewLevels();
                            if (in_array($creator->get('access'), $viewLevels)) {
                                $memberUrl = Route::url(
                                    'index.php?option=com_members&id='
                                    . $creator->get('id')
                                );
                                $name = '<a href="' . $memberUrl . '">'
                                    . $name . '</a>';
                            }

                            $forkedBy = Lang::txt(
                                'PLG_PUBLICATIONS_FORKS_FORKED_BY',
                                $name
                            );

                            $sourceUrl = Route::url(
                                'index.php?option=com_publications'
                                . '&id=' . $this->publication->get('id')
                                . '&v=' . $publication->get('forked_version')
                            );
                            $sourceLink = '<a href="' . $sourceUrl . '">'
                                . $publication->get('forked_from') . '</a>';
                            $forkedFrom = Lang::txt(
                                'PLG_PUBLICATIONS_FORKS_FROM',
                                $sourceLink
                            );
                            ?>
                            <span class="publication-creator">
                                <?php echo $forkedBy; ?>
                            </span><br />
                            <span class="publication-source">
                                <?php echo $forkedFrom; ?>
                            </span>
                        </div>
                    </div>
                    <?php if ($isPublished) { ?>
                        <?php
                        $diffUrl = Route::url(
                            'index.php?option=com_publications'
                            . '&task=compare'
                            . '&left=' . $this->publication->version->get('id')
                            . '&right=' . $version->get('id')
                        );
                        $diffLabel = Lang::txt('PLG_PUBLICATIONS_FORKS_DIFF');
                        ?>
                        <a class="btn" href="<?php echo $diffUrl; ?>">
                            <?php echo $diffLabel; ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="results-none">
                <p><?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_NONE'); ?></p>
                <p><?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_EXPLANATION'); ?></p>
            </div>
        <?php } ?>
    </div>

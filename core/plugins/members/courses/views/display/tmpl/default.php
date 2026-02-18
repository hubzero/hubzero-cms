<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = $this->member->link() . '&active=courses';

$this->css();
?>
<h3 class="section-header">
    <?php echo Lang::txt('PLG_MEMBERS_COURSES'); ?>
</h3>

<section class="section">
<?php if ($this->hasRoles) { ?>
    <div class="container" id="courses-container">
        <form method="get" action="<?php echo Route::url($base); ?>">
            <nav class="entries-filters" aria-label="<?php echo Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>">

                <?php if ($this->roles && $this->hasRoles > 1) { ?>
                    <ul class="entries-menu user-options">
                        <?php foreach ($this->roles as $s) { ?>
                            <?php

                            if ($s->total <= 0) {
                                continue;
                            }
                            $sel = '';
                            if ($this->filters['task'] == $s->alias) {
                                //$active = $s;
                                $sel = 'active';
                            }
                            $roleTitle = $this->escape(stripslashes($s->title));
                            $roleHref = Route::url(
                                $base . '&task=' . $s->alias
                                . '&sort=' . $this->filters['sort']
                            );
                            ?>
                            <li>
                                <a class="<?php echo $s->alias . ' ' . $sel; ?>"
                                    title="<?php echo $roleTitle; ?>"
                                    href="<?php echo $roleHref; ?>">
                                    <?php echo $roleTitle; ?>
                                    (<?php echo $this->escape($s->total); ?>)
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>

                <ul class="entries-menu order-options">
                    <?php
                    $taskParam = urlencode($this->filters['task']);
                    $titleSortUrl = Route::url(
                        $base . '&task=' . $taskParam . '&sort=title'
                    );
                    $enrolledSortUrl = Route::url(
                        $base . '&task=' . $taskParam . '&sort=enrolled'
                    );
                    $titleSortClass = ($this->filters['sort'] == 'title')
                        ? ' class="active"' : '';
                    $enrolledSortClass = ($this->filters['sort'] == 'enrolled')
                        ? ' class="active"' : '';
                    $titleSortTitle = Lang::txt(
                        'PLG_MEMBERS_COURSES_SORT_BY_TITLE'
                    );
                    $enrolledSortTitle = Lang::txt(
                        'PLG_MEMBERS_COURSES_SORT_BY_ENROLLED'
                    );
                    ?>
                    <li>
                        <a<?php echo $titleSortClass; ?>
                            href="<?php echo $titleSortUrl; ?>"
                            title="<?php echo $titleSortTitle; ?>">
                            <?php echo Lang::txt('PLG_MEMBERS_COURSES_SORT_TITLE'); ?>
                        </a>
                    </li>
                    <li>
                        <a<?php echo $enrolledSortClass; ?>
                            href="<?php echo $enrolledSortUrl; ?>"
                            title="<?php echo $enrolledSortTitle; ?>">
                            <?php echo Lang::txt('PLG_MEMBERS_COURSES_SORT_ENROLLED'); ?>
                        </a>
                    </li>
                </ul>
            </nav>

            <table class="courses entries">
                <caption>
                    <?php

                    $s = ($this->total > 0)
                        ? $this->filters['start'] + 1
                        : 0;
                    $e = ($this->total > ($this->filters['start'] + $this->filters['limit']))
                        ? ($this->filters['start'] + $this->filters['limit'])
                        : $this->total;

                    echo $this->escape(
                        stripslashes($this->active->title)
                    );
                    ?>
                    <span>(<?php echo Lang::txt('PLG_MEMBERS_COURSES_RESULTS_TOTAL', $s, $e, $this->total); ?>)</span>
                </caption>
                <tbody>
        <?php if (count($this->results) > 0) { ?>
            <?php

                $rtrn = base64_encode(Request::getString('REQUEST_URI', $base, 'server'));
            foreach ($this->results as $row) {
                $cls = '';
                $sfx = '';

                if (isset($row->offering_alias)) {
                    $sfx .= '&offering=' . $row->offering_alias;
                }
                if (isset($row->section_alias) && !$row->is_default) {
                    $sfx .= ':' . $row->section_alias;
                }

                switch ($this->filters['task']) {
                    case 'student':
                        $cls = 'student';
                        $dateText = Lang::txt('PLG_MEMBERS_COURSES_ENROLLED');
                        break;

                    case 'manager':
                    case 'instructor':
                    case 'ta':
                    default:
                        $cls = 'manager';
                        $dateText = Lang::txt('PLG_MEMBERS_COURSES_EMPOWERED');
                        break;
                }

                $courseUrl = Route::url(
                    'index.php?option=com_courses&gid='
                    . $row->alias . $sfx
                );
                $enrolledTime = Date::of($row->enrolled)->toLocal(
                    Lang::txt('TIME_FORMAT_HZ1')
                );
                $enrolledDate = Date::of($row->enrolled)->toLocal(
                    Lang::txt('DATE_FORMAT_HZ1')
                );
                $sectionLabel = Lang::txt(
                    'PLG_MEMBERS_COURSES_SECTION'
                );
                $escapedSection = $this->escape(
                    stripslashes($row->section_title)
                );
                ?>
                    <tr class="course<?php echo ($cls) ? ' ' . $cls : ''; ?>">
                        <th>
                            <span class="entry-id"><?php echo $row->id; ?></span>
                        </th>
                        <td>
                            <a class="entry-title"
                                href="<?php echo $courseUrl; ?>">
                            <?php echo $this->escape(stripslashes($row->title)); ?>
                            </a><br />
                            <span class="entry-details">
                            <?php echo $dateText; ?>
                                <!--
                                <span class="entry-date-at">
                                    <?php echo Lang::txt('PLG_MEMBERS_COURSES_AT'); ?>
                                </span>
                                <span class="entry-time">
                                    <time datetime="<?php echo $row->enrolled; ?>">
                                        <?php echo $enrolledTime; ?>
                                    </time>
                                </span>
                                -->
                                <span class="entry-date-on">
                                    <?php echo Lang::txt('PLG_MEMBERS_COURSES_ON'); ?>
                                </span>
                                <span class="entry-date">
                                    <time datetime="<?php echo $row->enrolled; ?>">
                                        <?php echo $enrolledDate; ?>
                                    </time>
                                </span>
                            <?php if ($row->section_title) { ?>
                                    <span class="entry-section">
                                         &mdash;
                                         <strong><?php echo $sectionLabel; ?></strong>
                                         <?php echo $escapedSection; ?>
                                    </span>
                            <?php } ?>
                            </span>
                        </td>
                        <td>
                        <?php if ($row->state == 3) { ?>
                                <span class="entry-state draft">
                                    <?php echo Lang::txt('PLG_MEMBERS_COURSES_STATE_DRAFT'); ?>
                                </span>
                        <?php } ?>
                        </td>
                        <td>
                        <?php if ($row->starts) { ?>
                                <?php
                                $startsTime = Date::of($row->starts)->toLocal(
                                    Lang::txt('TIME_FORMAT_HZ1')
                                );
                                $startsDate = Date::of($row->starts)->toLocal(
                                    Lang::txt('DATE_FORMAT_HZ1')
                                );
                                $atLabel = Lang::txt('PLG_MEMBERS_COURSES_AT');
                                $onLabel = Lang::txt('PLG_MEMBERS_COURSES_ON');
                                ?>
                                <?php echo Lang::txt('PLG_MEMBERS_COURSES_STARTS'); ?><br />
                                <span class="entry-details">
                                    <?php if ($row->starts && $row->starts != '0000-00-00 00:00:00') { ?>
                                        <span class="entry-date-at">
                                            <?php echo $atLabel; ?>
                                        </span>
                                        <span class="entry-time">
                                            <time datetime="<?php echo $row->starts; ?>">
                                                <?php echo $startsTime; ?>
                                            </time>
                                        </span>
                                        <span class="entry-date-on">
                                            <?php echo $onLabel; ?>
                                        </span>
                                        <span class="entry-date">
                                            <time datetime="<?php echo $row->starts; ?>">
                                                <?php echo $startsDate; ?>
                                            </time>
                                        </span>
                                    <?php } else { ?>
                                        <?php echo Lang::txt('PLG_MEMBERS_COURSES_NA'); ?>
                                    <?php } ?>
                                </span>
                        <?php } ?>
                        </td>
                        <td>
                        <?php if ($row->ends) { ?>
                                <?php
                                $endsTime = Date::of($row->ends)->toLocal(
                                    Lang::txt('TIME_FORMAT_HZ1')
                                );
                                $endsDate = Date::of($row->ends)->toLocal(
                                    Lang::txt('DATE_FORMAT_HZ1')
                                );
                                $atLabel = Lang::txt('PLG_MEMBERS_COURSES_AT');
                                $onLabel = Lang::txt('PLG_MEMBERS_COURSES_ON');
                                ?>
                                <?php echo Lang::txt('PLG_MEMBERS_COURSES_ENDS'); ?><br />
                                <span class="entry-details">
                                    <?php if ($row->ends && $row->ends != '0000-00-00 00:00:00') { ?>
                                        <span class="entry-date-at">
                                            <?php echo $atLabel; ?>
                                        </span>
                                        <span class="entry-time">
                                            <time datetime="<?php echo $row->ends; ?>">
                                                <?php echo $endsTime; ?>
                                            </time>
                                        </span>
                                        <span class="entry-date-on">
                                            <?php echo $onLabel; ?>
                                        </span>
                                        <span class="entry-date">
                                            <time datetime="<?php echo $row->ends; ?>">
                                                <?php echo $endsDate; ?>
                                            </time>
                                        </span>
                                    <?php } else { ?>
                                        <?php echo Lang::txt('PLG_MEMBERS_COURSES_NA'); ?>
                                    <?php } ?>
                                </span>
                        <?php } ?>
                        </td>
                        <td>
                        <?php if ($this->filters['task'] == 'manager' || $this->filters['task'] == 'instructor') { ?>
                                <?php
                                $copyUrl = Route::url(
                                    'index.php?option=com_courses&gid='
                                    . $row->alias . '&task=copy&return=' . $rtrn
                                );
                                $copyLabel = Lang::txt(
                                    'PLG_MEMBERS_COURSES_ACTION_COPY'
                                );
                                ?>
                                <a class="icon-copy btn btn-copy"
                                    href="<?php echo $copyUrl; ?>">
                                    <?php echo $copyLabel; ?>
                                </a>
                        <?php } ?>
                        </td>
                    </tr>
                    <?php
            }
            ?>
        <?php } else { ?>
                    <tr>
                        <td>
                            <?php echo Lang::txt('PLG_MEMBERS_COURSES_NO_RESULTS'); ?>
                        </td>
                    </tr>
        <?php } // end if (count($this->results) > 0) { ?>
                </tbody>
            </table>

            <?php

            $pageNav = $this->pagination(
                $this->total,
                $this->filters['start'],
                $this->filters['limit']
            );
            $pageNav->setAdditionalUrlParam('id', $this->member->get('id'));
            $pageNav->setAdditionalUrlParam('active', 'courses');
            $pageNav->setAdditionalUrlParam('task', $this->filters['task']);
            $pageNav->setAdditionalUrlParam('action', '');
            $pageNav->setAdditionalUrlParam('sort', $this->filters['sort']);

            echo $pageNav->render();
            ?>
            <div class="clearfix"></div>
        </form>
    </div>
<?php } else { ?>
    <div id="courses-introduction">
        <div class="instructions">
            <ol>
                <?php
                $coursesUrl = Route::url('index.php?option=com_courses');
                $findCourse = Lang::txt(
                    'PLG_MEMBERS_COURSES_FIND_COURSE',
                    $coursesUrl
                );
                ?>
                <li><?php echo $findCourse; ?></li>
                <li><?php echo Lang::txt('PLG_MEMBERS_COURSES_ENROLL'); ?></li>
                <li><?php echo Lang::txt('PLG_MEMBERS_COURSES_GET_LEARNING'); ?></li>
            </ol>
        </div><!-- / .instructions -->
        <div class="questions">
            <p><strong><?php echo Lang::txt('PLG_MEMBERS_COURSES_WHAT_ARE_COURSES'); ?></strong></p>
            <p><?php echo Lang::txt('PLG_MEMBERS_COURSES_EXPLANATION'); ?><p>
        </div><!-- / .post-type -->
    </div><!-- / #collection-introduction -->
<?php } ?>
</section>

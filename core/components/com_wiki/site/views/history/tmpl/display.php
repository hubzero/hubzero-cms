<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

if (!$this->sub) {
    $this->css();
}

$revisions = $this->page->versions()
    ->where('approved', '!=', \Components\Wiki\Models\Version::STATE_DELETED)
    ->order('id', 'desc')
    ->rows();
?>
<header id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
    <?php if (count($this->parents)) { ?>
        <p class="wiki-crumbs">
            <?php foreach ($this->parents as $parent) { ?>
                <a class="wiki-crumb" href="<?php echo Route::url($parent->link()); ?>">
                    <?php echo $parent->title; ?>
                </a> /
            <?php } ?>
        </p>
    <?php } ?>

    <h2><?php echo $this->escape($this->page->title); ?></h2>
    <?php
    if (!$this->page->isStatic()) {
        $this->view('authors', 'pages')
            //->setBasePath($this->base_path)
            ->set('page', $this->page)
            ->display();
    }
    ?>
</header><!-- /#content-header -->

<?php if (!$this->sub) { ?>
<section class="main section">
    <div class="aside">
        <?php
        $this->view('wikimenu', 'pages')
            ->set('option', $this->option)
            ->set('controller', $this->controller)
            ->set('page', $this->page)
            ->set('task', $this->task)
            ->set('sub', $this->sub)
            ->display();
        ?>
    </div>
    <div class="subject">
<?php } ?>

        <?php if ($this->getError()) { ?>
            <p class="error"><?php echo $this->getError(); ?></p>
        <?php } ?>

        <?php
        $this->view('submenu', 'pages')
            //->setBasePath($this->base_path)
            ->set('option', $this->option)
            ->set('controller', $this->controller)
            ->set('page', $this->page)
            ->set('task', $this->task)
            ->set('sub', $this->sub)
            ->display();
        ?>

<?php if ($this->sub) { ?>
<section class="main section">
    <div class="section-inner">
<?php } ?>

        <div class="grid">
            <div class="col">
                <?php
                $histHelpUrl = Route::url(
                    $this->page->link('base') . '&pagename=Help:PageHistory'
                );
                ?>
                <p><?php echo Lang::txt('COM_WIKI_HISTORY_EXPLANATION', $histHelpUrl); ?></p>
            </div><!-- / .aside -->
        </div>

        <form action="<?php echo Route::url($this->page->link('compare')); ?>" method="post">
            <?php
            $createdTime = '<time datetime="' . $this->page->get('created') . '">'
                . Date::of($this->page->get('created'))->toSql(true) . '</time>';
            $modifiedTime = '<time datetime="' . $this->page->get('modified') . '">'
                . Date::of($this->page->get('modified'))->toSql(true) . '</time>';
            ?>
            <p class="info">
                <?php echo Lang::txt(
                    'COM_WIKI_HISTORY_SUMMARY',
                    count($revisions),
                    $createdTime,
                    $modifiedTime
                ); ?>
            </p>

            <div class="container">
                <p><input type="submit" class="btn" value="<?php echo Lang::txt('COM_WIKI_HISTORY_COMPARE'); ?>" /></p>

                <table class="entries" id="revisionhistory">
                    <caption><?php echo Lang::txt('COM_WIKI_HISTORY_TBL_SUMMARY'); ?></caption>
                    <thead>
                        <tr>
                            <th scope="col" colspan="2"><?php echo Lang::txt('COM_WIKI_HISTORY_COL_COMPARE'); ?></th>
                            <th scope="col"><?php echo Lang::txt('COM_WIKI_HISTORY_COL_WHEN'); ?></th>
                            <th scope="col"><?php echo Lang::txt('COM_WIKI_HISTORY_COL_MADE_BY'); ?></th>
                            <th scope="col"><?php echo Lang::txt('COM_WIKI_HISTORY_COL_LENGTH'); ?></th>
                            <th scope="col"><?php echo Lang::txt('COM_WIKI_HISTORY_COL_STATUS'); ?></th>
                            <th scope="col"></th>
                            <?php
                            $canManage = ($this->page->isLocked() && $this->page->access('manage'))
                                || (!$this->page->isLocked() && $this->page->access('delete'));
                            if ($canManage) { ?>
                                <th scope="col"></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        $cur = 0;
                        $comparefirst = true;
                        $cls = 'even';
                        $total = $revisions->count();

                        $lengths = array();
                        foreach ($revisions as $revision) {
                            $lengths[] = $revision->get('length');
                        }
                        foreach ($revisions as $revision) {
                            $i++;

                            $cls = ($cls == 'odd') ? 'even' : 'odd';
                            $level = ($revision->get('minor_edit')) ? 'minor' : 'major';

                            $xname = $revision->creator->get('name', Lang::txt('COM_WIKI_AUTHOR_UNKNOWN'));

                            $summary = ($revision->get('summary') && trim($revision->get('summary')))
                                ? $revision->get('summary')
                                : Lang::txt('COM_WIKI_REVISION_NO_SUMMARY');

                            switch ($revision->get('approved')) {
                                case 1:
                                    $status = '<span class="approved icon-success">approved</span>';
                                    break;
                                case 0:
                                default:
                                    $status = '<span class="suggested icon-warning">suggested</span>';
                                    break;
                            }

                            $prvLength = 0;
                            if (isset($lengths[$i])) {
                                $prvLength = $lengths[$i];
                            }

                            $diff = $revision->get('length') - $prvLength;

                            $revUrl = Route::url(
                                $this->page->link('', 'version=' . $revision->get('version'))
                            );
                            $revTitle = Lang::txt('COM_WIKI_REVISION_SUMMARY')
                                . ' :: ' . $summary;
                            $revRawUrl = Route::url(
                                $this->page->link(
                                    '',
                                    'version=' . $revision->get('version') . '&format=raw'
                                )
                            );
                            $approveUrl = Route::url(
                                $this->page->link('approve', 'oldid=' . $revision->get('id'))
                            );
                            $setCurrentUrl = Route::url(
                                $this->page->link(
                                    'setcurrentrevision',
                                    'version_id=' . $revision->get('id')
                                )
                            );
                            $deleteRevUrl = Route::url(
                                $this->page->link('deleterevision', 'oldid=' . $revision->get('id'))
                            );
                            $diffCls = ($diff > 0) ? 'increase' : ($diff == 0 ? 'created' : 'decrease');
                            $diffVal = ($diff > 0)
                                ? '+' . number_format($diff)
                                : number_format($diff);
                            ?>
                            <tr class="<?php echo $cls; ?>">
                                <?php if ($this->page->get('version_id') == $revision->get('id')) { ?>
                                    <td>

                                    </td>
                                    <td>
                                        <input type="radio" name="diff"
                                            value="<?php echo $revision->get('version'); ?>"
                                            checked="checked" />
                                    </td>
                                <?php } else { ?>
                                    <td>
                                        <input type="radio" name="oldid"
                                            value="<?php echo $revision->get('version'); ?>"
                                            <?php if ($comparefirst == true) {
                                                echo ' checked="checked"';
                                                $comparefirst = false;
                                            } ?> />
                                    </td>
                                    <td>

                                    </td>
                                <?php } ?>
                                <td>
                                    <a href="<?php echo $revUrl; ?>"
                                        class="tooltips"
                                        title="<?php echo $this->escape($revTitle); ?>">
                                        <time datetime="<?php echo $revision->get('created'); ?>">
                                            <?php echo $this->escape(
                                                Date::of($revision->get('created'))->toLocal('Y-m-d h:i:s')
                                            ); ?>
                                        </time>
                                    </a>
                                    <a class="tooltips markup icon-file-alt-text"
                                        href="<?php echo $revRawUrl; ?>"
                                        title="<?php echo Lang::txt('COM_WIKI_HISTORY_MARKUP_TITLE'); ?>">
                                        <?php echo Lang::txt('COM_WIKI_HISTORY_MARKUP'); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo $this->escape($xname); ?>
                                </td>
                                <td>
                                    <?php echo Lang::txt(
                                        'COM_WIKI_HISTORY_BYTES',
                                        number_format($revision->get('length'))
                                    ); ?>
                                    (<span class="page-length <?php echo $diffCls; ?>">
                                        <?php echo $diffVal; ?>
                                    </span>)
                                </td>
                                <td>
                                    <?php echo $status; ?>
                                    <?php if (!$revision->get('approved') && $this->page->access('manage')) { ?>
                                        <br />
                                        <a href="<?php echo $approveUrl; ?>">
                                            <?php echo Lang::txt('COM_WIKI_ACTION_APPROVED'); ?>
                                        </a>
                                    <?php } ?>
                                </td>
                                <?php if ($canManage) { ?>
                                    <?php if ($this->page->get('version_id') == $revision->get('id')) { ?>
                                    <td>
                                        (Current Version)
                                    </td>
                                    <?php } else { ?>
                                    <td>
                                        <a class="icon-circle-arrow-up"
                                            href="<?php echo $setCurrentUrl; ?>"
                                            title="<?php echo Lang::txt('COM_WIKI_REVISION_DELETE'); ?>">
                                            <?php echo Lang::txt('COM_WIKI_HISTORY_SET_CURRENT'); ?>
                                        </a>
                                    </td>
                                    <?php } ?>
                                <td>
                                    <a class="icon-trash delete"
                                        href="<?php echo $deleteRevUrl; ?>"
                                        title="<?php echo Lang::txt('COM_WIKI_REVISION_DELETE'); ?>">
                                        <?php echo Lang::txt('JACTION_DELETE'); ?>
                                    </a>
                                </td>
                                <?php } ?>
                            </tr>
                            <?php
                            $prvLength = $revision->get('length');
                        }
                        ?>
                    </tbody>
                </table>
                <p><input type="submit" class="btn" value="<?php echo Lang::txt('COM_WIKI_HISTORY_COMPARE'); ?>" /></p>
            </div><!-- / .container -->

            <div class="clear"></div>

            <input type="hidden" name="pagename" value="<?php echo $this->escape($this->page->pagename); ?>" />
            <input type="hidden" name="pageid" value="<?php echo $this->escape($this->page->get('id')); ?>" />

            <?php foreach ($this->page->adapter()->routing('compare') as $name => $val) { ?>
                <input type="hidden" name="<?php echo $this->escape($name); ?>"
                    value="<?php echo $this->escape($val); ?>" />
            <?php } ?>
        </form>
    </div>
</section><!-- / .main section -->

<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Event;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

$this->css('create.css');
?>
<div id="small-page">
    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
    <?php } ?>
    <form action="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller) ?>"
        id="authors-form" method="post" enctype="multipart/form-data">
        <fieldset>
            <div class="grid">
                <div class="col span8">
                    <label>
                        <?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_ENTER_LOGINS'); ?>
                        <?php
                        $mc = Event::trigger('hubzero.onGetMultiEntry', [['members', 'new_authors', 'acmembers']]);
                        if (count($mc) > 0) {
                            echo $mc[0];
                        } else {
                            ?> <span class="hint"><?php echo Lang::txt('COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
                        <input type="text" name="new_authors" id="acmembers" value="" />
                        <?php } ?>
                    </label>
                </div>
                <div class="col span2">
                    <label for="new-authors-role">
                        <span id="new-authors-role-label"><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_ROLE'); ?></span>
                        <select name="role" id="new-authors-role">
                            <option value=""><?php echo Lang::txt('COM_CONTRIBUTE_AUTHOR'); ?></option>
                            <?php
                            if ($this->roles) {
                                foreach ($this->roles as $role) {
                                    ?>
                                    <option value="<?= $this->escape($role->alias) ?>"><?= $this->escape(stripslashes($role->title)) ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </label>
                </div>
                <div class="col span2 omega">
                    <p class="submit">
                        <input type="submit" value="<?php echo Lang::txt('COM_CONTRIBUTE_ADD'); ?>" />
                    </p>
                </div>
            </div>

            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
            <input type="hidden" name="tmpl" value="component" />
            <input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
            <input type="hidden" name="task" value="save" />
            <?php echo Html::input('token'); ?>
        </fieldset>
    </form>
<?php
// Do we have any contributors associated with this resource?
if ($this->contributors) {
    $i = 0;
    $n = $this->contributors->count();
    ?>
    <form action="<?= Request::base(true) ?>/index.php?option=<?= $this->option ?>&amp;controller=<?= $this->controller ?>&amp;task=update&amp;tmpl=component" id="authors-list" method="post" enctype="multipart/form-data">
        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
        <input type="hidden" name="task" value="update" />
        <?php echo Html::input('token'); ?>

        <table class="list">
            <tfoot>
                <td>
                    <span class="caption">
                        <?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_CHANGES_INSTRUCTIONS'); ?>
                    </span>
                </td>
                <td>
                    <input type="submit" value="<?php echo Lang::txt('COM_CONTRIBUTE_SAVE'); ?>"/>
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tfoot>
            <tbody>
            <?php
            foreach ($this->contributors as $contributor) {
                if (!$contributor->name) {
                    $contributor->populateFromProfile();
                }

                $name = stripslashes($contributor->name);
                ?>
                <tr class="author-<?php echo $contributor->authorid; ?>">
                    <td>
                        <span class="author-name"><?php echo $this->escape($name); ?></span><br />
                        <input type="text" name="authors[<?= $contributor->authorid ?>][organization]"
                            id="organization-<?= $contributor->authorid ?>" size="35"
                            value="<?= $this->escape(stripslashes($contributor->organization)) ?>"
                            placeholder="<?= Lang::txt('COM_CONTRIBUTE_AUTHORS_ORGANIZATION') ?>" />
                        <?php //echo ($contributor->org) ? ' <span class="caption">(' . $this->escape($contributor->org) . ')</span>' : '';?>
                    </td>
                    <td>
                        <select name="authors[<?= $contributor->authorid ?>][role]"
                            id="role-<?= $contributor->authorid ?>">
                            <option value=""<?= $contributor->role == '' ? ' selected="selected"' : '' ?>><?= Lang::txt('COM_CONTRIBUTE_AUTHOR') ?></option>
                            <?php
                            if ($this->roles) {
                                foreach ($this->roles as $role) {
                                    ?>
                                    <option value="<?= $this->escape($role->alias) ?>"<?= $contributor->role == $role->alias ? ' selected="selected"' : '' ?>><?= $this->escape(stripslashes($role->title)) ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td class="u"><?php
                    if ($i > 0 || ($i + 0 > 0)) {
                        echo '<a href="' . Request::base(true) . '/index.php?option=' . $this->option
                            . '&amp;controller=' . $this->controller . '&amp;tmpl=component&amp;pid=' . $this->id
                            . '&amp;id=' . $contributor->authorid . '&amp;task=reorder&amp;move=up&amp;'
                            . Session::getFormToken() . '=1" class="order up" title="'
                            . Lang::txt('COM_CONTRIBUTE_MOVE_UP') . '"><span>' . Lang::txt('COM_CONTRIBUTE_MOVE_UP')
                            . '</span></a>';
                    } else {
                        echo '&nbsp;';
                    }
                    ?></td>
                    <td class="d"><?php
                    if ($i < $n - 1 || $i + 0 < $n - 1) {
                        echo '<a href="' . Request::base(true) . '/index.php?option=' . $this->option
                            . '&amp;controller=' . $this->controller . '&amp;tmpl=component&amp;pid=' . $this->id
                            . '&amp;id=' . $contributor->authorid . '&amp;task=reorder&amp;move=down&amp;'
                            . Session::getFormToken() . '=1" class="order down" title="'
                            . Lang::txt('COM_CONTRIBUTE_MOVE_DOWN') . '"><span>' . Lang::txt('COM_CONTRIBUTE_MOVE_DOWN')
                            . '</span></a>';
                    } else {
                        echo '&nbsp;';
                    }
                    ?></td>
                    <td class="t">
                        <a class="icon-delete delete" href="<?= Request::base(true) ?>/index.php?option=<?= $this->option ?>&amp;controller=<?= $this->controller ?>&amp;task=remove&amp;tmpl=component&amp;id=<?= $contributor->authorid ?>&amp;pid=<?= $this->id ?>&amp;<?= Session::getFormToken() ?>=1" title="<?= Lang::txt('JACTION_DELETE') ?>">
                            <span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
                        </a>
                    </td>
                </tr>
                <?php
                $i++;
            }
            ?>
            </tbody>
        </table>
    </form>
<?php } else { ?>
    <p><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_NONE_FOUND'); ?></p>
<?php } ?>
 </div><!-- / #small-page -->
<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$mylistIds = array();

$this->css()
     ->js();
?>

<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <div id="content-header-extra">
        <ul>
            <li>
                <a class="btn icon-browse" href="<?php echo Route::url('index.php?option=com_newsletter'); ?>">
                    <?php echo Lang::txt('COM_NEWSLETTER_BROWSE'); ?>
                </a>
            </li>
        </ul>
    </div>
</header>

<section class="main section">
    <?php
    if ($this->getError()) {
        echo '<p class="error">' . $this->getError() . '</p>';
    }
    ?>
    <div class="subscribe">
        <form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
            <?php if (count($this->mylists) > 0) : ?>
                <fieldset>
                    <legend><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_MYLISTS'); ?></legend>
                    <?php foreach ($this->mylists as $mylist) : ?>
                        <?php $mylistIds[] = $mylist->id; ?>
                        <?php if ($mylist->status != 'removed') : ?>
                            <?php
                            $listId = $mylist->id;
                            $isActive = ($mylist->status == 'active'
                                || $mylist->status == 'inactive');
                            $checked = $isActive ? 'checked="checked"' : '';
                            ?>
                            <label for="newsletterlist<?php echo $listId; ?>">
                                <input
                                    type="checkbox"
                                    name="lists[]"
                                    id="newsletterlist<?php echo $listId; ?>"
                                    value="<?php echo $listId; ?>"
                                    <?php echo $checked; ?>
                                />
                                <strong><?php echo $this->escape($mylist->name); ?></strong>
                                <?php
                                if ($isActive) {
                                    if (!$mylist->confirmed) {
                                        $tooltip = Lang::txt(
                                            'COM_NEWSLETTER_MAILINGLISTS_NOTCONFIRMED_TOOLTIP'
                                        );
                                        $notConfirmed = Lang::txt(
                                            'COM_NEWSLETTER_MAILINGLISTS_NOTCONFIRMED'
                                        );
                                        $resendUrl = Route::url(
                                            'index.php?option=com_newsletter'
                                            . '&task=resendconfirmation'
                                            . '&mid=' . $listId
                                            . '&e=' . urlencode($this->email)
                                        );
                                        $linkText = Lang::txt(
                                            'COM_NEWSLETTER_MAILINGLISTS_CONFIRMLINK_TEXT'
                                        );
                                        echo ' - <span title="' . $tooltip
                                            . '" class="unconfirmed tooltips">'
                                            . $notConfirmed . '</span>'
                                            . ' <span class="unconfirmed-link">'
                                            . '(<a href="' . $resendUrl
                                            . '" class="">' . $linkText
                                            . '</a>)</span>';
                                    }
                                } elseif ($mylist->status == 'unsubscribed') {
                                    $unsubTxt = Lang::txt(
                                        'COM_NEWSLETTER_MAILINGLISTS_UNSUBSCRIBED'
                                    );
                                    echo ' - <span class="unsubscribed">'
                                        . $unsubTxt . '</span>';
                                }
                                ?>
                                <?php
                                $noDesc = Lang::txt(
                                    'COM_NEWSLETTER_MAILINGLISTS_LIST_NODESCRIPTION'
                                );
                                $descTxt = $mylist->description
                                    ? nl2br($mylist->description)
                                    : $noDesc;
                                ?>
                                <span class="desc">
                                    <?php echo $descTxt; ?>
                                </span>
                            </label>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </fieldset>
            <?php endif; ?>

            <?php if (count($this->alllists) > 0) : ?>
                <fieldset>
                    <legend><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_PUBLICLISTS'); ?></legend>
                    <?php foreach ($this->alllists as $list) : ?>
                        <?php
                        if (in_array($list->id, $mylistIds)) {
                            continue;
                        }
                        ?>
                        <label for="newsletterlist<?php echo $list->id; ?>">
                            <input
                                type="checkbox"
                                name="lists[]"
                                id="newsletterlist<?php echo $list->id; ?>"
                                value="<?php echo $list->id; ?>"
                            />
                            <strong><?php echo $this->escape($list->name); ?></strong>
                            <?php
                            $noDescPub = Lang::txt(
                                'COM_NEWSLETTER_MAILINGLISTS_LIST_NODESCRIPTION'
                            );
                            $descPub = ($list->description)
                                ? nl2br($list->description)
                                : $noDescPub;
                            ?>
                            <span class="desc"><?php echo $descPub; ?></span>
                        </label>
                    <?php endforeach; ?>
                </fieldset>
            <?php endif; ?>
            <?php if (count($this->mylists) > 0 || count($this->alllists) > 0) : ?>
                <p class="submit">
                    <?php $saveTxt = Lang::txt('COM_NEWSLETTER_MAILINGLISTS_SAVE'); ?>
                    <input type="submit" class="btn btn-success" value="<?php echo $saveTxt; ?>">
                </p>
            <?php else : ?>
                <p class="info">
                    <?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_NONE'); ?>
                </p>
            <?php endif; ?>
            <input type="hidden" name="e" value="<?php echo urlencode($this->email); ?>" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
            <input type="hidden" name="task" value="domultisubscribe" />
            <?php echo Html::input('token'); ?>
        </form>
    </div>
</section>

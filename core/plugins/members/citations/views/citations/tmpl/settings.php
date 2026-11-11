<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{
 *     citations_show_badges: mixed,
 *     citations_show_tags: mixed,
 *     coins_only: mixed,
 *     currentFormat: mixed,
 *     customFormat: mixed,
 *     formats: mixed,
 *     include_coins: mixed,
 *     member: mixed,
 *     templateKeys: mixed,
 * } $this
 */

$this
    ->css()
    ->js();

$base = Route::url($this->member->link() . '&active=' . $this->getName());
?>

<div id="browsebox" class="frm">
    <h3><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS'); ?></h3>

    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo $this->getError(); ?></p>
    <?php } ?>

    <form action="<?= Route::url($base . '?action=settings') ?>" method="post" id="hubForm" class="add-citation">
        <!-- Badge and Tag options -->
        <fieldset>
            <legend><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_BADGE_OPTIONS'); ?></legend>

            <div class="grid">
                <div class="col span6">
                    <div class="form-group">
                        <label for="show_tags">
                            <?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_DISPLAY_TAGS'); ?>
                            <select name="citations_show_tags" id="show_tags" class="form-control">
                                <option
                                    value="yes" <?= ($this->citations_show_tags == "yes") ? "selected=selected" : "" ?>>
                                    <?= Lang::txt('Yes') ?>
                                </option>
                                <option
                                    value="no" <?= ($this->citations_show_tags == "no") ? "selected=selected" : "" ?>>
                                    <?= Lang::txt('No') ?>
                                </option>
                            </select>
                        </label>
                    </div>
                </div>
                <div class="col span6 omega">
                    <div class="form-group">
                        <label for="show_badges">
                            <?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_DISPLAY_BADGES'); ?>
                            <select name="citations_show_badges" id="show_badges" class="form-control">
                                <?php
                                $attributes = ($this->citations_show_badges == "yes") ? "selected=selected" : "";
                                ?>
                                <option value="yes" <?= $attributes ?>><?= Lang::txt('Yes') ?></option>
                                <option
                                    value="no" <?= ($this->citations_show_badges == "no") ? "selected=selected" : "" ?>>
                                    <?= Lang::txt('No') ?>
                                </option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
        </fieldset>
        <div class="clear"></div>

        <!-- Coins and other other options -->
        <div class="explaination">
            <p>
                <?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_WHAT_ARE_COINS'); ?><br />
                <?php
                $altText = Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_READ_MORE_COINS');
                ?>
                <a href="http://ocoins.info/" rel="nofollow external"
                    alt="<?= $altText ?>"><?= Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_READ_MORE_COINS') ?></a>
            </p>
        </div>
        <fieldset>
            <legend><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_COINS_OPTIONS'); ?></legend>

            <div class="grid">
                <div class="col span6">
                    <div class="form-group">
                        <label for="include-coins">
                            <?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_INCLUDE_COINS'); ?>
                            <select name="include_coins" id="include-coins" class="form-control">
                                <option value="no" <?= ($this->include_coins == "no") ? "selected=selected" : "" ?>>
                                    <?= Lang::txt('No') ?>
                                </option>
                                <option value="yes" <?= ($this->include_coins == "yes") ? "selected=selected" : "" ?>>
                                    <?= Lang::txt('Yes') ?>
                                </option>
                            </select>
                        </label>
                    </div>
                </div>
                <div class="col span6 omega">
                    <div class="form-group">
                        <label for="coins-only">
                            <?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_COINS_ONLY'); ?>
                            <select name="coins_only" id="coins-only" class="form-control">
                                <option value="no" <?= ($this->coins_only == "no") ? "selected=selected" : "" ?>>
                                    <?= Lang::txt('No') ?>
                                </option>
                                <option value="yes" <?= ($this->coins_only == "yes") ? "selected=selected" : "" ?>>
                                    <?= Lang::txt('Yes') ?>
                                </option>
                            </select>
                        </label>
                    </div>
                </div>
        </fieldset>
        <div class="clear"></div>

        <div class="explaination">
            <p>
                <?php echo Lang::txt('PLG_MEMBERS_CITATIONS_FORMAT_EXPLAIN'); ?>
            </p>
        </div>
        <fieldset>
            <legend><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_CITATION_FORMAT'); ?></legend>

            <div class="form-group">
                <label for="format-selector">
                    <?php echo Lang::txt('PLG_MEMBERS_CITATIONS_CITATION_FORMAT'); ?>:
                    <select name="citation-format" id="format-selector" class="form-control"
                        data-uid="<?= $this->member->get('id') ?>">
                        <?php foreach ($this->formats as $format) : ?>
                                <?php if ($format->style != 'custom-member-' . $this->member->get('id')) : ?>
                            <option <?= $this->currentFormat->id == $format->id ? 'selected' : '' ?>
                                value="<?= $format->id ?>" data-format="<?= $format->format ?>">
                                    <?php echo $format->style; ?>
                            </option>
                                <?php elseif ($format->style == 'custom-member-' . $this->member->get('id')) : ?>
                                <option <?= $this->currentFormat->id == $format->id ? 'selected' : '' ?>
                                    value="<?= $format->id ?>" data-format="<?= $format->format ?>">
                                    <?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_CUSTOM_FORMAT'); ?>
                                </option>
                                <?php endif; ?> 
                        <?php endforeach; ?>
                        <?php if ($this->customFormat === false) : ?>
                        <option value="custom" data-format="">
                                    <?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_CUSTOM_FORMAT'); ?>
                        </option>
                        <?php endif; ?>
                    </select>

                    <span class="hint"><?= Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_FORMAT_EXPLAINATION') ?></span>
                </label>
            </div>

            <!-- some space -->
            <div class="clear"></div>

            <div class="form-group">
                <label for="format-string"
                    class="sr-only"><?= Lang::txt('PLG_MEMBERS_CITATIONS_FORMAT_STRING') ?></label>
                    <?php
                    $ariaLabel = Lang::txt('PLG_MEMBERS_CITATIONS_FORMAT_STRING');
                    ?>
                    <textarea name="template" rows="10" id="format-string" class="form-control"
                        aria-label="<?= $ariaLabel ?>"><?= addslashes($this->currentFormat->format) ?></textarea>
            </div>

            <table class="templateTable">
                <caption id="templateExplaination"><?= Lang::txt('PLG_MEMBERS_CITATIONS_CLICK_TABLE') ?></caption>
                <thead>
                    <tr class="clickable">
                        <th scope="col"><?php echo Lang::txt('Key'); ?></th>
                        <th scope="col"><?php echo Lang::txt('Value'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // get the keys
                    foreach ($this->templateKeys as $k => $v) {
                        ?>
                        <tr id="<?php echo $v; ?>">
                            <td><?php echo $v; ?></td>
                            <td><?php echo $k; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </fieldset><div class="clear"></div>

        <!-- submit -->
        <p class="submit">
            <input class="btn btn-success" type="submit" name="create"
                value="<?= Lang::txt('PLG_MEMBERS_CITATIONS_SAVE') ?>" />
        </p>

        <div class="clear"></div>
        <?php echo Html::input('token'); ?>
    </form>
</div>

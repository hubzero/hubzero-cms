<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$this
     ->css()
     ->js();
$this->js('import');
//declare vars
$citations_require_attention = $this->citations_require_attention;
$citations_require_no_attention = $this->citations_require_no_attention;

//dont show array
$no_show = ["errors","duplicate"];
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>
</header>

<section id="import" class="section">
    <div class="section-inner">
        <?php
        foreach ($this->messages as $message) {
            echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
        }
        ?>

        <ul id="steps">
            <?php
            $spanText = Lang::txt('COM_CITATIONS_IMPORT_STEP1_NAME');
            ?>
            <li><a href="<?= Request::base(true) ?>/citations/import"
                class="passed"><?= Lang::txt('COM_CITATIONS_IMPORT_STEP1') ?><span><?= $spanText ?></span></a></li>
            <?php
            $spanText = Lang::txt('COM_CITATIONS_IMPORT_STEP2_NAME');
            ?>
            <li><a class="active"><?= Lang::txt('COM_CITATIONS_IMPORT_STEP2') ?><span><?= $spanText ?></span></a></li>
            <?php
            $spanText = Lang::txt('COM_CITATIONS_IMPORT_STEP3_NAME');
            ?>
            <li><a><?= Lang::txt('COM_CITATIONS_IMPORT_STEP3') ?><span><?= $spanText ?></span></a></li>
        </ul><!-- / #steps -->

        <form method="post" action="<?= Route::url('index.php?option=' . $this->option . '&task=import_save') ?>">
            <?php if ($citations_require_attention) : ?>
                <table class="upload-list require-action">
                    <thead>
                        <tr>
                            <!--<th></th>-->
                            <th>
                                <?= Lang::txt(
                                    'COM_CITATIONS_IMPORT_REQUIRE_ATTENTION',
                                    count($citations_require_attention),
                                ) ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 0; ?>
                        <?php foreach ($citations_require_attention as $c) : ?>
                            <?php
                                $type_title = $c['duplicate']->relatedType->get('type_title');
                                $tags = implode(
                                    ', ',
                                    \Components\Citations\Helpers\Format::citationTags($c['duplicate'], false),
                                );
                                $badges = implode(
                                    ', ',
                                    \Components\Citations\Helpers\Format::citationBadges($c['duplicate'], false),
                                );
                            ?>
                            <tr>
                                <!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
                                <td>
                                    <span class="citation-title"><u><?= Lang::txt('COM_CITATIONS_IMPORT_DUPLICATE') ?></u>: <?= $this->escape(html_entity_decode((string) $c['title'])) ?></span>
                                    <?php
                                    $clickMoreText = Lang::txt('COM_CITATIONS_IMPORT_SHOW_CITATION_DETAILS');
                                    ?>
                                    <span class="click-more"><?= $clickMoreText ?></span>
                            <?php if (1) { ?>
                                    <table class="citation-details hide">
                                        <thead>
                                            <tr>
                                                <th><?= Lang::txt('COM_CITATIONS_IMPORT_CITATION_DETAILS') ?></th>
                                                <th class="options">
                                                    <label>
                                                        <input
                                                            type="radio"
                                                            class="citation_require_attention_option"
                                                            name="citation_action_attention[<?php echo $counter; ?>]"
                                                            value="overwrite"
                                                            checked="checked" /> <?= Lang::txt('COM_CITATIONS_IMPORT_CITATION_REPLACE') ?>
                                                    </label>
                                                    <label>
                                                        <input
                                                            type="radio"
                                                            class="citation_require_attention_option"
                                                            name="citation_action_attention[<?php echo $counter; ?>]"
                                                            value="both" /> <?= Lang::txt('COM_CITATIONS_IMPORT_CITATION_KEEP') ?>
                                                    </label>
                                                    <label>
                                                        <input
                                                            type="radio"
                                                            class="citation_require_attention_option"
                                                            name="citation_action_attention[<?php echo $counter; ?>]"
                                                            value="discard" /> <?= Lang::txt('COM_CITATIONS_IMPORT_CITATION_NOTHING') ?>
                                                    </label>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $recordAttributes = $c['duplicate']->getAttributes();
                                                $changedKeys = [];
                                            foreach ($c as $attribute => $value) {
                                                if (!empty($recordAttributes[$attribute]) || !empty($value)) {
                                                    $changedKeys[] = $attribute;
                                                }
                                            }
                                            ?>
                                            <?php foreach ($changedKeys as $k) : ?>
                                                <?php if (!in_array($k, $no_show)) : ?>
                                                    <tr>
                                                        <td class="key">
                                                            <?= $this->escape(str_replace("_", " ", (string) $k)) ?>
                                                        </td>
                                                        <td>
                                                            <table class="citation-differences">
                                                                <tr>
                                                                    <?php
                                                                    $cellText = Lang::txt(
                                                                        'COM_CITATIONS_IMPORT_JUST_UPLOADED',
                                                                    );
                                                                    ?>
                                                                    <td><?= $cellText ?>:</td>
                                                                    <td>
                                                                        <?php
                                                                        $newText = nl2br(
                                                                            $this->escape(
                                                                                html_entity_decode((string) $c[$k]),
                                                                            ),
                                                                        );
                                                                        ?>
                                                                        <span class="new insert"><?= $newText ?></span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <?php
                                                                    $cellText = Lang::txt(
                                                                        'COM_CITATIONS_IMPORT_ON_FILE',
                                                                    );
                                                                    ?>
                                                                    <td><?= $cellText ?>:</td>
                                                                    <td>
                                                                        <span class="old delete">
                                                                            <?php
                                                                            switch ($k) {
                                                                                case 'type':
                                                                                        echo $this->escape($type_title);
                                                                                    break;
                                                                                case 'tags':
                                                                                        echo $this->escape($tags);
                                                                                    break;
                                                                                case 'badges':
                                                                                        echo $this->escape($badges);
                                                                                    break;
                                                                                default:
                                                                                        echo nl2br($this->escape(html_entity_decode((string) $c['duplicate']->get($k))));
                                                                                    break;
                                                                            }
                                                                            ?>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php
                            }
                            ?>
                                </td>
                            </tr>
                            <?php $counter++; ?>
                        <?php endforeach; ?>
                    <tbody>
                </table>
            <?php endif; ?>

            <!-- /////////////////////////////////////// -->

            <?php if ($citations_require_no_attention) : ?>
                <table class="upload-list no-action">
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="checkall" name="select-all-no-attention"
                                checked="checked" /></th>
                            <th>
                                <?= Lang::txt(
                                    'COM_CITATIONS_IMPORT_REQUIRE_NO_ATTENTION',
                                    count($citations_require_no_attention),
                                ) ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 0; ?>
                            <?php foreach ($citations_require_no_attention as $c) : ?>
                            <tr>
                                <td><input type="checkbox" class="check-single"
                                    name="citation_action_no_attention[<?= $counter++ ?>]" checked="checked"
                                    value="1" /></td>
                                <td>
                                    <span class="citation-title">
                                        <?php
                                        if (array_key_exists("title", $c)) {
                                            echo $this->escape(html_entity_decode((string) $c['title']));
                                        } else {
                                            echo "NO TITLE FOUND";
                                        }
                                        ?>
                                    </span>
                                    <?php
                                    $clickMoreText = Lang::txt('COM_CITATIONS_IMPORT_SHOW_CITATION_DETAILS');
                                    ?>
                                    <span class="click-more"><?= $clickMoreText ?></span>
                                    <table class="citation-details hide">
                                        <thead>
                                            <tr>
                                                <th colspan="2">
                                                    <?= Lang::txt('COM_CITATIONS_IMPORT_CITATION_DETAILS') ?>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_keys($c) as $k) : ?>
                                                <?php if (!in_array($k, $no_show)) : ?>
                                                    <tr>
                                                        <td class="key">
                                                            <?= $this->escape(str_replace("_", " ", (string) $k)) ?>
                                                        </td>
                                                        <td>
                                                            <?= nl2br(
                                                                $this->escape(html_entity_decode((string) $c[$k])),
                                                            ) ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <p class="submit">
                <input type="submit" name="submit" value="<?= Lang::txt('COM_CITATIONS_IMPORT_SUBMIT_IMPORTED') ?>" />
            </p>

            <?php echo Html::input('token'); ?>
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <?php if (isset($this->group) && $this->group != '') : ?>
                <input type="hidden" name="group" value="<?php echo $this->group; ?>" />
            <?php endif; ?>
            <input type="hidden" name="task" value="import_save" />
        </form>
    </div>
</section><!-- / .section -->

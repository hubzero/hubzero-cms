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

$this->css()
     ->js();
$this->js('import');
//declare vars
$citations_require_attention = $this->citations_require_attention;
$citations_require_no_attention = $this->citations_require_no_attention;

//dont show array
$no_show = array("errors","duplicate");

$baseUrl = Request::base(true);
$step1 = Lang::txt('COM_CITATIONS_IMPORT_STEP1');
$step1Name = Lang::txt('COM_CITATIONS_IMPORT_STEP1_NAME');
$step2 = Lang::txt('COM_CITATIONS_IMPORT_STEP2');
$step2Name = Lang::txt('COM_CITATIONS_IMPORT_STEP2_NAME');
$step3 = Lang::txt('COM_CITATIONS_IMPORT_STEP3');
$step3Name = Lang::txt('COM_CITATIONS_IMPORT_STEP3_NAME');
$saveUrl = Route::url(
    'index.php?option=' . $this->option . '&task=import_save'
);
$showDetailsLabel = Lang::txt(
    'COM_CITATIONS_IMPORT_SHOW_CITATION_DETAILS'
);
$detailsLabel = Lang::txt('COM_CITATIONS_IMPORT_CITATION_DETAILS');
$replaceLabel = Lang::txt('COM_CITATIONS_IMPORT_CITATION_REPLACE');
$keepLabel = Lang::txt('COM_CITATIONS_IMPORT_CITATION_KEEP');
$nothingLabel = Lang::txt('COM_CITATIONS_IMPORT_CITATION_NOTHING');
$uploadedLabel = Lang::txt('COM_CITATIONS_IMPORT_JUST_UPLOADED');
$onFileLabel = Lang::txt('COM_CITATIONS_IMPORT_ON_FILE');
$dupLabel = Lang::txt('COM_CITATIONS_IMPORT_DUPLICATE');
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>
</header>

<section id="import" class="section">
    <div class="section-inner">
        <?php
        foreach ($this->messages as $message) {
            echo "<p class=\"{$message['type']}\">"
                . $message['message'] . "</p>";
        }
        ?>

        <ul id="steps">
            <li>
                <a href="<?php echo $baseUrl; ?>/citations/import" class="passed">
                    <?php echo $step1; ?><span><?php echo $step1Name; ?></span>
                </a>
            </li>
            <li>
                <a class="active">
                    <?php echo $step2; ?><span><?php echo $step2Name; ?></span>
                </a>
            </li>
            <li>
                <a>
                    <?php echo $step3; ?><span><?php echo $step3Name; ?></span>
                </a>
            </li>
        </ul><!-- / #steps -->

        <form method="post" action="<?php echo $saveUrl; ?>">
            <?php if ($citations_require_attention) : ?>
                <?php
                $attentionCount = count($citations_require_attention);
                $attentionLabel = Lang::txt(
                    'COM_CITATIONS_IMPORT_REQUIRE_ATTENTION',
                    $attentionCount
                );
                ?>
                <table class="upload-list require-action">
                    <thead>
                        <tr>
                            <!--<th></th>-->
                            <th><?php echo $attentionLabel; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 0; ?>
                        <?php foreach ($citations_require_attention as $c) : ?>
                            <?php
                            $type_title = $c['duplicate']
                                ->relatedType->get('type_title');
                            $citeTags = \Components\Citations\Helpers\Format
                                ::citationTags($c['duplicate'], false);
                            $tags = implode(', ', $citeTags);
                            $citeBadges = \Components\Citations\Helpers\Format
                                ::citationBadges($c['duplicate'], false);
                            $badges = implode(', ', $citeBadges);
                            $decodedTitle = html_entity_decode($c['title']);
                            $inputName = 'citation_action_attention['
                                . $counter . ']';
                            ?>
                            <tr>
                                <!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
                                <td>
                                    <span class="citation-title">
                                        <u><?php echo $dupLabel; ?></u>: <?php echo $decodedTitle; ?>
                                    </span>
                                    <span class="click-more"><?php echo $showDetailsLabel; ?></span>
                            <?php if (1) { ?>
                                    <table class="citation-details hide">
                                        <thead>
                                            <tr>
                                                <th><?php echo $detailsLabel; ?></th>
                                                <th class="options">
                                                    <label>
                                                        <input
                                                            type="radio"
                                                            class="citation_require_attention_option"
                                                            name="<?php echo $inputName; ?>"
                                                            value="overwrite"
                                                            checked="checked"
                                                        /> <?php echo $replaceLabel; ?>
                                                    </label>
                                                    <label>
                                                        <input
                                                            type="radio"
                                                            class="citation_require_attention_option"
                                                            name="<?php echo $inputName; ?>"
                                                            value="both"
                                                        /> <?php echo $keepLabel; ?>
                                                    </label>
                                                    <label>
                                                        <input
                                                            type="radio"
                                                            class="citation_require_attention_option"
                                                            name="<?php echo $inputName; ?>"
                                                            value="discard"
                                                        /> <?php echo $nothingLabel; ?>
                                                    </label>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $recordAttributes = $c['duplicate']
                                                ->getAttributes();
                                            $changedKeys = array();
                                            foreach ($c as $attribute => $value) {
                                                if (
                                                    !empty($recordAttributes[$attribute])
                                                    || !empty($value)
                                                ) {
                                                    $changedKeys[] = $attribute;
                                                }
                                            }
                                            ?>
                                            <?php foreach ($changedKeys as $k) : ?>
                                                <?php if (!in_array($k, $no_show)) : ?>
                                                <tr>
                                                    <td class="key">
                                                        <?php echo str_replace("_", " ", $k); ?>
                                                    </td>
                                                    <td>
                                                        <table class="citation-differences">
                                                        <tr>
                                                            <td><?php echo $uploadedLabel; ?>:</td>
                                                            <td>
                                                                <?php $decoded = html_entity_decode(nl2br($c[$k])); ?>
                                                                <span class="new insert"><?php echo $decoded; ?></span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $onFileLabel; ?>:</td>
                                                            <td>
                                                                <span class="old delete">
                                                                <?php
                                                                switch ($k) {
                                                                    case 'type':
                                                                        echo $type_title;
                                                                        break;
                                                                    case 'tags':
                                                                        echo $tags;
                                                                        break;
                                                                    case 'badges':
                                                                        echo $badges;
                                                                        break;
                                                                    default:
                                                                        echo html_entity_decode(
                                                                            nl2br($c['duplicate']->get($k))
                                                                        );
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
                <?php
                $noAttentionCount = count($citations_require_no_attention);
                $noAttentionLabel = Lang::txt(
                    'COM_CITATIONS_IMPORT_REQUIRE_NO_ATTENTION',
                    $noAttentionCount
                );
                ?>
                <table class="upload-list no-action">
                    <thead>
                        <tr>
                            <th>
                                <input
                                    type="checkbox"
                                    class="checkall"
                                    name="select-all-no-attention"
                                    checked="checked"
                                />
                            </th>
                            <th><?php echo $noAttentionLabel; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 0; ?>
                            <?php foreach ($citations_require_no_attention as $c) : ?>
                            <tr>
                                <td>
                                    <?php $cbName = 'citation_action_no_attention[' . $counter++ . ']'; ?>
                                    <input
                                        type="checkbox"
                                        class="check-single"
                                        name="<?php echo $cbName; ?>"
                                        checked="checked"
                                        value="1"
                                    />
                                </td>
                                <td>
                                    <span class="citation-title">
                                        <?php
                                        if (array_key_exists("title", $c)) {
                                            echo html_entity_decode($c['title']);
                                        } else {
                                            echo "NO TITLE FOUND";
                                        }
                                        ?>
                                    </span>
                                    <span class="click-more"><?php echo $showDetailsLabel; ?></span>
                                    <table class="citation-details hide">
                                        <thead>
                                            <tr>
                                                <th colspan="2"><?php echo $detailsLabel; ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_keys($c) as $k) : ?>
                                                <?php if (!in_array($k, $no_show)) : ?>
                                                    <tr>
                                                        <td class="key"><?php echo str_replace("_", " ", $k); ?></td>
                                                        <td><?php echo html_entity_decode(nl2br($c[$k])); ?></td>
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
                <?php $submitLabel = Lang::txt('COM_CITATIONS_IMPORT_SUBMIT_IMPORTED'); ?>
                <input type="submit" name="submit" value="<?php echo $submitLabel; ?>" />
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

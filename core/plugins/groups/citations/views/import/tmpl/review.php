<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('import.css')
     ->js('import.js');

//database object
$database = App::get('db');

//declare vars
$citations_require_attention    = $this->citations_require_attention;
$citations_require_no_attention = $this->citations_require_no_attention;

//dont show array
$no_show = array('errors', 'duplicate', 'bdsk-file-1');

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=citations';
?>
<section id="import" class="section">
    <div class="section-inner">
        <?php if ($this->messages) : ?>
            <?php
            foreach ($this->messages as $message) {
                echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
            }
            ?>
        <?php endif; ?>

        <?php
        $importUrl  = Route::url($base . '&action=import');
        $reviewUrl  = Route::url($base . '&action=review');
        $step1      = Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP1');
        $step1Name  = Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP1_NAME');
        $step2      = Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP2');
        $step2Name  = Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP2_NAME');
        $step3      = Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3');
        $step3Name  = Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3_NAME');
        ?>
        <ul id="steps">
            <li>
                <a href="<?php echo $importUrl; ?>" class="passed">
                    <?php echo $step1; ?>
                    <span><?php echo $step1Name; ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo $reviewUrl; ?>" class="active">
                    <?php echo $step2; ?>
                    <span><?php echo $step2Name; ?></span>
                </a>
            </li>
            <li>
                <a>
                    <?php echo $step3; ?>
                    <span><?php echo $step3Name; ?></span>
                </a>
            </li>
        </ul><!-- / #steps -->

        <form method="post" id="hubForm" class="full" action="<?php echo Route::url($base . '&action=process'); ?>">
            <?php if ($citations_require_attention) : ?>
                <table class="upload-list require-action">
                    <thead>
                        <tr>
                            <!--<th></th>-->
                            <?php
                            $attentionCount = count($citations_require_attention);
                            $attentionText = Lang::txt(
                                'PLG_GROUPS_CITATIONS_IMPORT_REQUIRE_ATTENTION',
                                $attentionCount
                            );
                            ?>
                            <th><?php echo $attentionText; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 0; ?>
                        <?php foreach ($citations_require_attention as $c) : ?>
                            <?php
                                //load the duplicate citation
                                $cc = $c['duplicate'];

                                $type_title = $cc->relatedType->get('type_title');
                                $tags = implode(
                                    ', ',
                                    \Components\Citations\Helpers\Format::citationTags($c['duplicate'], false)
                                );
                                $badges = implode(
                                    ', ',
                                    \Components\Citations\Helpers\Format::citationBadges($c['duplicate'], false)
                                );
                            ?>
                            <tr>
                                <!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
                                <td>
                                    <?php
                                    $dupLabel = Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_DUPLICATE');
                                    $cTitle = html_entity_decode($c['title']);
                                    $showDetails = Lang::txt(
                                        'PLG_GROUPS_CITATIONS_IMPORT_SHOW_CITATION_DETAILS'
                                    );
                                    ?>
                                    <span class="citation-title">
                                        <u><?php echo $dupLabel; ?></u>:
                                        <?php echo $cTitle; ?>
                                    </span>
                                    <span class="click-more">
                                        <?php echo $showDetails; ?>
                                    </span>
                            <?php if (1) { ?>
                                    <table class="citation-details hide">
                                        <thead>
                                            <tr>
                                                <?php
                                                $detailsText = Lang::txt(
                                                    'PLG_GROUPS_CITATIONS_IMPORT_CITATION_DETAILS'
                                                );
                                                $replaceText = Lang::txt(
                                                    'PLG_GROUPS_CITATIONS_IMPORT_CITATION_REPLACE'
                                                );
                                                $keepText = Lang::txt(
                                                    'PLG_GROUPS_CITATIONS_IMPORT_CITATION_KEEP'
                                                );
                                                $nothingText = Lang::txt(
                                                    'PLG_GROUPS_CITATIONS_IMPORT_CITATION_NOTHING'
                                                );
                                                $replaceId  = "citation_action_attention-{$counter}-replace";
                                                $keepId     = "citation_action_attention-{$counter}-keep";
                                                $nothingId  = "citation_action_attention-{$counter}-nothing";
                                                $attName    = "citation_action_attention[{$counter}]";
                                                ?>
                                                <th><?php echo $detailsText; ?></th>
                                                <th class="options">
                                                    <label for="<?php echo $replaceId; ?>">
                                                        <input
                                                            type="radio"
                                                            class="citation_require_attention_option"
                                                            name="<?php echo $attName; ?>"
                                                            id="<?php echo $replaceId; ?>"
                                                            value="overwrite"
                                                            checked="checked" />
                                                        <?php echo $replaceText; ?>
                                                    </label>
                                                    <label for="<?php echo $keepId; ?>">
                                                        <input
                                                            type="radio"
                                                            class="citation_require_attention_option"
                                                            name="<?php echo $attName; ?>"
                                                            id="<?php echo $keepId; ?>"
                                                            value="both" />
                                                        <?php echo $keepText; ?>
                                                    </label>
                                                    <label for="<?php echo $nothingId; ?>">
                                                        <input
                                                            type="radio"
                                                            class="citation_require_attention_option"
                                                            name="<?php echo $attName; ?>"
                                                            id="<?php echo $nothingId; ?>"
                                                            value="discard" />
                                                        <?php echo $nothingText; ?>
                                                    </label>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $recordAttributes = $cc->getAttributes();
                                            $changedKeys = array();
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
                                                            <?php echo str_replace("_", " ", $k); ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $uploadedLabel = Lang::txt(
                                                                'PLG_GROUPS_CITATIONS_IMPORT_JUST_UPLOADED'
                                                            );
                                                            $onFileLabel = Lang::txt(
                                                                'PLG_GROUPS_CITATIONS_IMPORT_ON_FILE'
                                                            );
                                                            $newVal = html_entity_decode(nl2br($c[$k]));
                                                            $oldVal = '';
                                                            switch ($k) {
                                                                case 'type':
                                                                    $oldVal = $type_title;
                                                                    break;
                                                                case 'tags':
                                                                    $oldVal = $tags;
                                                                    break;
                                                                case 'badges':
                                                                    $oldVal = $badges;
                                                                    break;
                                                                default:
                                                                    $attrKeys = array_keys($cc->getAttributes());
                                                                    if (in_array($k, $attrKeys)) {
                                                                        $oldVal = html_entity_decode(nl2br($cc->$k));
                                                                    }
                                                                    break;
                                                            }
                                                            ?>
                                                            <table class="citation-differences">
                                                                <tr>
                                                                    <td><?php echo $uploadedLabel; ?>:</td>
                                                                    <td>
                                                                        <span class="new insert">
                                                                            <?php echo $newVal; ?>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><?php echo $onFileLabel; ?>:</td>
                                                                    <td>
                                                                        <span class="old delete">
                                                                            <?php echo $oldVal; ?>
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
                            <th><input type="checkbox" class="checkall" name="select-all-no-attention" checked="checked"
                            /></th>
                            <?php
                            $noAttentionCount = count($citations_require_no_attention);
                            $noAttentionText = Lang::txt(
                                'PLG_GROUPS_CITATIONS_IMPORT_REQUIRE_NO_ATTENTION',
                                $noAttentionCount
                            );
                            ?>
                            <th><?php echo $noAttentionText; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 0; ?>
                            <?php foreach ($citations_require_no_attention as $c) : ?>
                            <tr>
                                <?php $noAttName = "citation_action_no_attention[{$counter}]";
                                $counter++; ?>
                                <td>
                                    <input
                                        type="checkbox"
                                        class="check-single"
                                        name="<?php echo $noAttName; ?>"
                                        checked="checked"
                                        value="1" />
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
                                    <?php
                                    $showDetailsText = Lang::txt(
                                        'PLG_GROUPS_CITATIONS_IMPORT_SHOW_CITATION_DETAILS'
                                    );
                                    $citDetailsText = Lang::txt(
                                        'PLG_GROUPS_CITATIONS_IMPORT_CITATION_DETAILS'
                                    );
                                    ?>
                                    <span class="click-more">
                                        <?php echo $showDetailsText; ?>
                                    </span>
                                    <table class="citation-details hide">
                                        <thead>
                                            <tr>
                                                <th colspan="2">
                                                    <?php echo $citDetailsText; ?>
                                                </th>
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
                <input type="submit"
                    class="btn btn-success"
                    id="review-input"
                    name="submit"
                    value="<?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_SUBMIT_IMPORTED'); ?>"/>

                <a class="btn btn-secondary" href="<?php echo Route::url($base); ?>">
                    <?php echo Lang::txt('JCANCEL'); ?>
                </a>
            </p>

            <?php echo Html::input('token'); ?>
            <input type="hidden" name="option" value="com_groups" />
            <input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
            <input type="hidden" name="active" value="citations" />
            <input type="hidden" name="action" value="process" />
        </form>
    </div>
</section><!-- / .section -->

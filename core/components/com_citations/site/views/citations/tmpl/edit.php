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
use Hubzero\Facades\Event;

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$allow_tags = $this->config->get("citation_allow_tags", "no");
$allow_badges = $this->config->get("citation_allow_badges", "no");

$fieldset_label = ($allow_tags == "yes") ? "Tags" : "";
$fieldset_label = ($allow_badges == "yes") ? "Badges" : $fieldset_label;
$fieldset_label = ($allow_tags == "yes" && $allow_badges == "yes")
    ? "Tags and Badges" : $fieldset_label;

//get the referrer
$backLink = Route::url('index.php?option=' . $this->option);
if (
    isset($_SERVER['HTTP_REFERER'])
    && filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL)
) {
    $backLink = $_SERVER['HTTP_REFERER'];
}

$pid = Request::getInt('publication', 0);

$row = $this->row;
$actionUrl = Route::url('index.php?option=' . $this->option);
$authorsBase = 'index.php?option=com_citations&controller=authors'
    . '&citation=' . $row->id;
$addUrl = Route::url($authorsBase . '&task=add&' . $this->token . '=1');
$updateUrl = Route::url($authorsBase . '&task=update&' . $this->token . '=1');
$listUrl = Route::url($authorsBase . '&task=display&' . $this->token . '=1');
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <div id="content-header-extra">
        <p>
            <a class="icon-browse browse btn" href="<?php echo $backLink; ?>">
                <?php echo Lang::txt('COM_CITATIONS_BACK'); ?>
            </a>
        </p>
    </div>
</header><!-- / #content-header -->

<section class="main section">
    <?php if ($pid) { ?>
        <?php
        $citationFor = Lang::txt('COM_CITATIONS_CITATION_FOR');
        $pubLabel = Lang::txt('COM_CITATIONS_PUBLICATION') . ' #' . $pid;
        ?>
        <h3><?php echo $citationFor; ?> <?php echo $pubLabel; ?></h3>
    <?php } ?>
    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo $this->getError(); ?></p>
    <?php } ?>
    <form action="<?php echo $actionUrl; ?>" method="post" id="hubForm" class="add-citation">
        <div class="explaination">
            <p><?php echo Lang::txt('COM_CITATIONS_DETAILS_DESC'); ?></p>
        </div>
        <fieldset>
            <legend><?php echo Lang::txt('COM_CITATIONS_DETAILS'); ?></legend>

            <div class="grid">
                <div class="col span6">
                    <label for="type">
                        <?php echo Lang::txt('COM_CITATIONS_TYPE'); ?>:
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                        <select name="fields[type]" id="type">
                            <option value=""> <?php echo Lang::txt('COM_CITATIONS_TYPE_SELECT'); ?></option>
                            <?php
                            foreach ($this->types as $t) {
                                $sel = ($row->type == $t['id'])
                                    ? "selected=\"selected\"" : "";
                                echo "<option {$sel} value=\"{$t['id']}\">"
                                    . "{$t['type_title']}</option>";
                            }
                            ?>
                        </select>
                    </label>
                </div>
                <div class="col span6 omega">
                    <label for="cite">
                        <?php echo Lang::txt('COM_CITATIONS_CITE_KEY'); ?>:
                        <?php $citeVal = $this->escape($row->cite); ?>
                        <input
                            type="text"
                            name="fields[cite]"
                            id="cite"
                            size="30"
                            maxlength="250"
                            value="<?php echo $citeVal; ?>"
                        />
                        <span class="hint"><?php echo Lang::txt('COM_CITATIONS_CITE_KEY_EXPLANATION'); ?></span>
                    </label>
                </div>
            </div>

            <label for="ref_type">
                <?php echo Lang::txt('COM_CITATIONS_REF_TYPE'); ?>:
                <?php $refTypeVal = $this->escape($row->ref_type); ?>
                <input
                    type="text"
                    name="fields[ref_type]"
                    id="ref_type"
                    size="11"
                    maxlength="50"
                    value="<?php echo $refTypeVal; ?>"
                />
            </label>

            <div class="grid">
                <div class="col span4">
                    <label for="date_submit">
                        <?php echo Lang::txt('COM_CITATIONS_DATE_SUBMITTED'); ?>:
                        <?php $dsVal = $this->escape($row->date_submit); ?>
                        <input
                            type="text"
                            name="fields[date_submit]"
                            id="date_submit"
                            size="30"
                            maxlength="250"
                            value="<?php echo $dsVal; ?>"
                        />
                        <span class="hint"><?php echo Lang::txt('COM_CITATIONS_DATE_HINT'); ?></span>
                    </label>
                </div>
                <div class="col span4">
                    <label for="date_accept">
                        <?php echo Lang::txt('COM_CITATIONS_DATE_ACCEPTED'); ?>:
                        <?php $daVal = $this->escape($row->date_accept); ?>
                        <input
                            type="text"
                            name="fields[date_accept]"
                            id="date_accept"
                            size="30"
                            maxlength="250"
                            value="<?php echo $daVal; ?>"
                        />
                        <span class="hint"><?php echo Lang::txt('COM_CITATIONS_DATE_HINT'); ?></span>
                    </label>
                </div>
                <div class="col span4 omega">
                    <label for="date_publish">
                        <?php echo Lang::txt('COM_CITATIONS_DATE_PUBLISHED'); ?>:
                        <?php $dpVal = $this->escape($row->date_publish); ?>
                        <input
                            type="text"
                            name="fields[date_publish]"
                            id="date_publish"
                            size="30"
                            maxlength="250"
                            value="<?php echo $dpVal; ?>"
                        />
                        <span class="hint"><?php echo Lang::txt('COM_CITATIONS_DATE_HINT'); ?></span>
                    </label>
                </div>
            </div>

            <div class="grid">
                <div class="col span6">
                    <label for="year">
                        <?php echo Lang::txt('COM_CITATIONS_YEAR'); ?>:
                        <?php $yearVal = $this->escape($row->year); ?>
                        <input
                            type="text"
                            name="fields[year]"
                            id="year"
                            size="4"
                            maxlength="4"
                            value="<?php echo $yearVal; ?>"
                        />
                    </label>
                </div>
                <div class="col span6 omega">
                    <label for="month">
                        <?php echo Lang::txt('COM_CITATIONS_MONTH'); ?>:
                        <?php $monthVal = $this->escape($row->month); ?>
                        <input
                            type="text"
                            name="fields[month]"
                            id="month"
                            size="11"
                            maxlength="50"
                            value="<?php echo $monthVal; ?>"
                        />
                    </label>
                </div>
            </div>
            <fieldset
                class="author-manager"
                data-add="<?php echo $addUrl; ?>"
                data-update="<?php echo $updateUrl; ?>"
                data-list="<?php echo $listUrl; ?>"
            >
                    <div class="grid">
                        <div class="col span10">
                            <label for="field-author">
                                <?php echo Lang::txt('COM_CITATIONS_AUTHORS') . ' '; ?>
                                <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
                                <?php
                                $authorVal = isset($this->authorString)
                                    ? $this->authorString : '';
                                $mc = Event::trigger(
                                    'hubzero.onGetMultiEntry',
                                    array(array(
                                        'members', 'author',
                                        'field-author', '', $authorVal
                                    ))
                                );
                                if (count($mc) > 0) {
                                    echo $mc[0];
                                } else { ?>
                                    <input type="text" name="author" id="field-author" value="" />
                                <?php } ?>
                            </label>
                        </div>
                        <div class="col span2 omega">
                            <button class="btn btn-success add-author">
                                <?php echo Lang::txt('COM_CITATIONS_ADD'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="field-wrap author-list">
                        <?php echo $this->setName('authors')->setLayout('display')->loadTemplate('authors'); ?>
                    </fieldset>

            <label for="authoraddress">
                <?php echo Lang::txt('COM_CITATIONS_AUTHOR_ADDRESS'); ?>:
                <?php $addrVal = $this->escape($row->author_address); ?>
                <input
                    type="text"
                    name="fields[author_address]"
                    id="authoraddress"
                    size="30"
                    value="<?php echo $addrVal; ?>"
                />
            </label>

            <label for="editor">
                <?php echo Lang::txt('COM_CITATIONS_EDITORS'); ?>:
                <?php $editorVal = $this->escape($row->editor); ?>
                <input
                    type="text"
                    name="fields[editor]"
                    id="editor"
                    size="30"
                    maxlength="250"
                    value="<?php echo $editorVal; ?>"
                />
                <span class="hint"><?php echo Lang::txt('COM_CITATIONS_AUTHORS_HINT'); ?></span>
            </label>

            <label for="title">
                <?php echo Lang::txt('COM_CITATIONS_TITLE_CHAPTER'); ?>:
                <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                <?php $titleVal = $this->escape($row->title); ?>
                <input
                    type="text"
                    name="fields[title]"
                    id="title"
                    size="30"
                    maxlength="250"
                    value="<?php echo $titleVal; ?>"
                />
            </label>

            <?php
            // Pre-extract values for remaining text fields
            $booktitleVal = $this->escape($row->booktitle);
            $shortTitleVal = $this->escape($row->short_title);
            $journalVal = $this->escape($row->journal);
            $volumeVal = $this->escape($row->volume);
            $numberVal = $this->escape($row->number);
            $pagesVal = $this->escape($row->pages);
            $isbnVal = $this->escape($row->isbn);
            $doiVal = $this->escape($row->doi);
            $callNumVal = $this->escape($row->call_number);
            $accNumVal = $this->escape($row->accession_number);
            $seriesVal = $this->escape($row->series);
            $editionVal = $this->escape($row->edition);
            $schoolVal = $this->escape($row->school);
            $publisherVal = $this->escape($row->publisher);
            $institutionVal = $this->escape($row->institution);
            $addressVal = $this->escape($row->address);
            $locationVal = $this->escape($row->location);
            $howpubVal = $this->escape($row->howpublished);
            $urlVal = $this->escape($row->url);
            $eprintVal = $this->escape($row->eprint);
            $abstractVal = $this->escape(stripslashes($row->abstract ?? ''));
            $noteVal = $this->escape(stripslashes($row->note ?? ''));
            $keywordsVal = $this->escape(stripslashes($row->keywords ?? ''));
            $researchVal = $this->escape(stripslashes($row->research_notes ?? ''));
            $languageVal = $this->escape($row->language);
            $labelVal = $this->escape($row->label);
            $doiFull = Lang::txt('COM_CITATIONS_DOI_FULL');
            $doiLabel = Lang::txt('COM_CITATIONS_DOI');
            ?>

            <label for="booktitle">
                <?php echo Lang::txt('COM_CITATIONS_BOOK_TITLE'); ?>:
                <input
                    type="text"
                    name="fields[booktitle]"
                    id="booktitle"
                    size="30"
                    maxlength="250"
                    value="<?php echo $booktitleVal; ?>"
                />
            </label>

            <label for="shorttitle">
                <?php echo Lang::txt('COM_CITATIONS_SHORT_TITLE'); ?>:
                <input
                    type="text"
                    name="fields[short_title]"
                    id="shorttitle"
                    size="30"
                    maxlength="250"
                    value="<?php echo $shortTitleVal; ?>"
                />
            </label>

            <label for="journal">
                <?php echo Lang::txt('COM_CITATIONS_JOURNAL'); ?>:
                <input
                    type="text"
                    name="fields[journal]"
                    id="journal"
                    size="30"
                    maxlength="250"
                    value="<?php echo $journalVal; ?>"
                />
            </label>

            <div class="grid">
                <div class="col span4">
                    <label for="volume">
                        <?php echo Lang::txt('COM_CITATIONS_VOLUME'); ?>:
                        <input
                            type="text"
                            name="fields[volume]"
                            id="volume"
                            size="11"
                            maxlength="11"
                            value="<?php echo $volumeVal; ?>"
                        />
                    </label>
                </div>
                <div class="col span4">
                    <label for="number">
                        <?php echo Lang::txt('COM_CITATIONS_ISSUE'); ?>:
                        <input
                            type="text"
                            name="fields[number]"
                            id="number"
                            size="11"
                            maxlength="50"
                            value="<?php echo $numberVal; ?>"
                        />
                    </label>
                </div>
                <div class="col span4 omega">
                    <label for="pages">
                        <?php echo Lang::txt('COM_CITATIONS_PAGES'); ?>:
                        <input
                            type="text"
                            name="fields[pages]"
                            id="pages"
                            size="11"
                            maxlength="250"
                            value="<?php echo $pagesVal; ?>"
                        />
                    </label>
                </div>
            </div>

            <div class="grid">
                <div class="col span6">
                    <label for="isbn">
                        <?php echo Lang::txt('COM_CITATIONS_ISBN'); ?>:
                        <input
                            type="text"
                            name="fields[isbn]"
                            id="isbn"
                            size="11"
                            maxlength="50"
                            value="<?php echo $isbnVal; ?>"
                        />
                    </label>
                </div>
                <div class="col span6 omega">
                    <label for="doi">
                        <abbr title="<?php echo $doiFull; ?>"><?php echo $doiLabel; ?></abbr>:
                        <input
                            type="text"
                            name="fields[doi]"
                            id="doi"
                            size="30"
                            maxlength="250"
                            value="<?php echo $doiVal; ?>"
                        />
                    </label>
                </div>
            </div>

            <div class="grid">
                <div class="col span6">
                    <label for="callnumber">
                        <?php echo Lang::txt('COM_CITATIONS_CALL_NUMBER'); ?>:
                        <input
                            type="text"
                            name="fields[call_number]"
                            id="callnumber"
                            value="<?php echo $callNumVal; ?>"
                        />
                    </label>
                </div>
                <div class="col span6 omega">
                    <label for="accessionnumber">
                        <?php echo Lang::txt('COM_CITATIONS_ACCESSION_NUMBER'); ?>:
                        <input
                            type="text"
                            name="fields[accession_number]"
                            id="accessionnumber"
                            value="<?php echo $accNumVal; ?>"
                        />
                    </label>
                </div>
            </div>

            <label for="series">
                <?php echo Lang::txt('COM_CITATIONS_SERIES'); ?>:
                <input
                    type="text"
                    name="fields[series]"
                    id="series"
                    size="30"
                    maxlength="250"
                    value="<?php echo $seriesVal; ?>"
                />
            </label>

            <label for="edition">
                <?php echo Lang::txt('COM_CITATIONS_EDITION'); ?>:
                <input
                    type="text"
                    name="fields[edition]"
                    id="edition"
                    size="30"
                    maxlength="250"
                    value="<?php echo $editionVal; ?>"
                />
                <span class="hint"><?php echo Lang::txt('COM_CITATIONS_EDITION_EXPLANATION'); ?></span>
            </label>

            <label for="school">
                <?php echo Lang::txt('COM_CITATIONS_SCHOOL'); ?>:
                <input
                    type="text"
                    name="fields[school]"
                    id="school"
                    size="30"
                    maxlength="250"
                    value="<?php echo $schoolVal; ?>"
                />
            </label>

            <label for="publisher">
                <?php echo Lang::txt('COM_CITATIONS_PUBLISHER'); ?>:
                <input
                    type="text"
                    name="fields[publisher]"
                    id="publisher"
                    size="30"
                    maxlength="250"
                    value="<?php echo $publisherVal; ?>"
                />
            </label>

            <label for="institution">
                <?php echo Lang::txt('COM_CITATIONS_INSTITUTION'); ?>:
                <input
                    type="text"
                    name="fields[institution]"
                    id="institution"
                    size="30"
                    maxlength="250"
                    value="<?php echo $institutionVal; ?>"
                />
                <span class="hint"><?php echo Lang::txt('COM_CITATIONS_INSTITUTION_EXPLANATION'); ?></span>
            </label>

            <label for="address">
                <?php echo Lang::txt('COM_CITATIONS_ADDRESS'); ?>:
                <input
                    type="text"
                    name="fields[address]"
                    id="address"
                    size="30"
                    maxlength="250"
                    value="<?php echo $addressVal; ?>"
                />
            </label>

            <label for="location">
                <?php echo Lang::txt('COM_CITATIONS_LOCATION'); ?>:
                <input
                    type="text"
                    name="fields[location]"
                    id="location"
                    size="30"
                    maxlength="250"
                    value="<?php echo $locationVal; ?>"
                />
                <span class="hint"><?php echo Lang::txt('COM_CITATIONS_LOCATION_EXPLANATION'); ?></span>
            </label>

            <label for="howpublished">
                <?php echo Lang::txt('COM_CITATIONS_PUBLISH_METHOD'); ?>:
                <input
                    type="text"
                    name="fields[howpublished]"
                    id="howpublished"
                    size="30"
                    maxlength="250"
                    value="<?php echo $howpubVal; ?>"
                />
                <span class="hint"><?php echo Lang::txt('COM_CITATIONS_PUBLISH_METHOD_EXPLANATION'); ?></span>
            </label>

            <label for="url">
                <?php echo Lang::txt('COM_CITATIONS_URL'); ?>:
                <input
                    type="text"
                    name="fields[url]"
                    id="url"
                    size="30"
                    maxlength="250"
                    value="<?php echo $urlVal; ?>"
                />
            </label>

            <label for="eprint">
                <?php echo Lang::txt('COM_CITATIONS_EPRINT'); ?>:
                <input
                    type="text"
                    name="fields[eprint]"
                    id="eprint"
                    size="30"
                    maxlength="250"
                    value="<?php echo $eprintVal; ?>"
                />
                <span class="hint"><?php echo Lang::txt('COM_CITATIONS_EPRINT_EXPLANATION'); ?></span>
            </label>

            <label for="abstract">
                <?php echo Lang::txt('COM_CITATIONS_ABSTRACT'); ?>:
                <textarea
                    name="fields[abstract]"
                    id="abstract"
                    rows="8"
                    cols="10"
                ><?php echo $abstractVal; ?></textarea>
            </label>

            <label for="note">
                <?php echo Lang::txt('COM_CITATIONS_NOTES'); ?>:
                <textarea name="fields[note]" id="note" rows="8" cols="10"><?php echo $noteVal; ?></textarea>
            </label>

            <label for="keywords">
                <?php echo Lang::txt('COM_CITATIONS_KEYWORDS'); ?>:
                <textarea
                    name="fields[keywords]"
                    id="keywords"
                    rows="8"
                    cols="10"
                ><?php echo $keywordsVal; ?></textarea>
            </label>

            <label for="research_notes">
                <?php echo Lang::txt('COM_CITATIONS_RESEARCH_NOTES'); ?>:
                <textarea
                    name="fields[research_notes]"
                    id="research_notes"
                    rows="8"
                    cols="10"
                ><?php echo $researchVal; ?></textarea>
            </label>

            <div class="group twoup">
                <label for="language">
                    <?php echo Lang::txt('COM_CITATIONS_LANGUAGE'); ?>:
                    <input
                        type="text"
                        name="fields[language]"
                        id="language"
                        size="11"
                        maxlength="50"
                        value="<?php echo $languageVal; ?>"
                    />
                </label>

                <label for="label">
                    <?php echo Lang::txt('COM_CITATIONS_LABEL'); ?>:
                    <input
                        type="text"
                        name="fields[label]"
                        id="label"
                        size="30"
                        maxlength="250"
                        value="<?php echo $labelVal; ?>"
                    />
                </label>
            </div>
        </fieldset><div class="clear"></div>

        <fieldset>
            <legend><?php echo Lang::txt('COM_CITATIONS_MANUALLY_FORMAT'); ?>:</legend>
            <p class="warning"><?php echo Lang::txt('COM_CITATIONS_MANUALLY_FORMAT_HINT'); ?></p>
            <label for="format">
                <?php echo Lang::txt('COM_CITATIONS_MANUALLY_FORMAT_FORMAT'); ?>:
                <?php $apaSel = ($row->format == 'apa') ? 'selected="selected"' : ''; ?>
                <?php $ieeeSel = ($row->format == 'ieee') ? 'selected="selected"' : ''; ?>
                <?php $apaLabel = Lang::txt('COM_CITATIONS_MANUALLY_FORMAT_APA'); ?>
                <?php $ieeeLabel = Lang::txt('COM_CITATIONS_MANUALLY_FORMAT_IEEE'); ?>
                <select id="format" name="fields[format]">
                    <option value="apa" <?php echo $apaSel; ?>><?php echo $apaLabel; ?></option>
                    <option value="ieee" <?php echo $ieeeSel; ?>><?php echo $ieeeLabel; ?></option>
                </select>
            </label>
            <label for="formatted">
                <?php echo Lang::txt('COM_CITATIONS_MANUALLY_FORMAT_CITATION'); ?>:
                <?php
                $formattedVal = $this->escape(
                    stripslashes($row->get('formatted', ''))
                );
                ?>
                <textarea
                    name="fields[formatted]"
                    id="formatted"
                    rows="8"
                    cols="10"
                ><?php echo $formattedVal; ?></textarea>
            </label>
        </fieldset><div class="clear"></div>

        <?php if ($allow_tags == "yes" || $allow_badges == "yes") : ?>
            <fieldset>
                <legend><?php echo $fieldset_label; ?></legend>
                <?php if ($allow_tags == "yes") : ?>
                    <label>
                        <?php echo Lang::txt('COM_CITATIONS_TAGS'); ?>:
                        <?php
                            $tags_list = Event::trigger(
                                'hubzero.onGetMultiEntry',
                                array(array(
                                    'tags', 'tags', 'actags', '',
                                    implode(",", $this->tags)
                                ))
                            );

                        if (count($tags_list) > 0) {
                            echo $tags_list[0];
                        } else {
                            echo '<input type="text" name="tags" value="'
                                . $tags . '" />';
                        }
                        ?>
                        <span class="hint"><?php echo Lang::txt('COM_CITATIONS_TAGS_HINT'); ?></span>
                    </label>
                <?php endif; ?>

                <?php if ($allow_badges == "yes") : ?>
                    <label class="badges">
                        <?php echo Lang::txt('COM_CITATIONS_BADGES'); ?>:
                        <?php
                            $badges_list = Event::trigger(
                                'hubzero.onGetMultiEntry',
                                array(array(
                                    'tags', 'badges', 'actags1', '',
                                    implode(",", $this->badges)
                                ))
                            );
                        if (count($badges_list) > 0) {
                            echo $badges_list[0];
                        } else {
                            echo '<input type="text" name="badges" value="'
                                . $badges . '" />';
                        }
                        ?>
                        <span class="hint"><?php echo Lang::txt('COM_CITATIONS_BADGES_HINT'); ?></span>
                    </label>
                <?php endif; ?>
            </fieldset><div class="clear"></div>
        <?php endif; ?>

        <?php if ($pid) { ?>
            <input type="hidden" name="assocs[0][oid]" value="<?php echo $pid; ?>" />
            <input type="hidden" name="assocs[0][tbl]" value="publication" />
            <input type="hidden" name="assocs[0][id]" value="0" />
        <?php } else { ?>
            <div class="explaination">
                <p><?php echo Lang::txt('COM_CITATIONS_ASSOCIATION_DESC'); ?></p>
            </div>
            <fieldset>
                <legend><?php echo Lang::txt('COM_CITATIONS_CITATION_FOR'); ?></legend>

                <div class="field-wrap">
                    <table id="assocs">
                        <thead>
                            <tr>
                                <th><?php echo Lang::txt('COM_CITATIONS_TYPE'); ?></th>
                                <th><?php echo Lang::txt('COM_CITATIONS_ID'); ?></th>
                                <th><?php echo Lang::txt('COM_CITATIONS_CONTEXT'); ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="3">
                                    <button class="btn icon-add" id="add_row">
                                        <?php echo Lang::txt('COM_CITATIONS_ADD_A_ROW'); ?>
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                        <tbody>
                        <?php
                                $r = count($this->assocs);
                        if ($r > 5) {
                            $n = $r;
                        } else {
                            $n = 5;
                        }

                        $selectTxt = Lang::txt('COM_CITATIONS_SELECT');
                        $resourceTxt = Lang::txt('COM_CITATIONS_RESOURCE');
                        $pubTxt = Lang::txt('COM_CITATIONS_PUBLICATION');
                        $refsTxt = Lang::txt('COM_CITATIONS_CONTEXT_REFERENCES');
                        $refdByTxt = Lang::txt('COM_CITATIONS_CONTEXT_REFERENCEDBY');

                        for ($i = 0; $i < $n; $i++) {
                            if ($r == 0 || !isset($this->assocs[$i])) {
                                $this->assocs[$i] = new stdClass();
                                $this->assocs[$i]->id   = null;
                                $this->assocs[$i]->cid  = $row->id;
                                $this->assocs[$i]->oid  = null;
                                $this->assocs[$i]->type = null;
                                $this->assocs[$i]->tbl  = null;
                            }

                            $a = $this->assocs[$i];
                            $tblSel = ($a->tbl == '') ? ' selected="selected"' : '';
                            $tblResSel = ($a->tbl == 'resource') ? ' selected="selected"' : '';
                            $tblPubSel = ($a->tbl == 'publication') ? ' selected="selected"' : '';
                            $typeSel = ($a->type == '') ? ' selected="selected"' : '';
                            $typeRefSel = ($a->type == 'references') ? ' selected="selected"' : '';
                            $typeRefBySel = ($a->type == 'referencedby') ? ' selected="selected"' : '';

                            echo "\t\t\t" . '  <tr>' . "\n";
                            echo "\t\t\t" . '   <td>'
                                . '<select name="assocs[' . $i . '][tbl]">' . "\n";
                            echo ' <option value=""' . $tblSel . '>'
                                . $selectTxt . '</option>' . "\n";
                            echo ' <option value="resource"' . $tblResSel . '>'
                                . $resourceTxt . '</option>' . "\n";
                            echo ' <option value="publication"' . $tblPubSel . '>'
                                . $pubTxt . '</option>' . "\n";
                            echo '</select></td>' . "\n";
                            echo "\t\t\t" . '<td>'
                                . '<input type="text" name="assocs[' . $i . '][oid]"'
                                . ' value="' . $a->oid . '" />' . "\n";
                            echo "\t\t\t\t"
                                . '<input type="hidden" name="assocs[' . $i . '][id]"'
                                . ' value="' . $a->id . '" />' . "\n";
                            echo "\t\t\t\t"
                                . '<input type="hidden" name="assocs[' . $i . '][cid]"'
                                . ' value="' . $a->cid . '" /></td>' . "\n";
                            echo "\t\t\t" . '<td>'
                                . '<select name="assocs[' . $i . '][type]">' . "\n";
                            echo ' <option value=""' . $typeSel . '>'
                                . $selectTxt . '</option>' . "\n";
                            echo ' <option value="references"' . $typeRefSel . '>'
                                . $refsTxt . '</option>' . "\n";
                            echo ' <option value="referencedby"' . $typeRefBySel . '>'
                                . $refdByTxt . '</option>' . "\n";
                            echo '</select></td>' . "\n";
                            echo "\t\t\t" . '</tr>' . "\n";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </fieldset><div class="clear"></div>
        <?php } ?>

        <fieldset>
            <legend><?php echo Lang::txt('COM_CITATIONS_AFFILIATION'); ?></legend>

            <label for="affiliated">
                <?php $affChecked = $row->affiliated ? ' checked="checked"' : ''; ?>
                <input
                    type="checkbox"
                    class="option"
                    name="fields[affiliated]"
                    id="affiliated"
                    value="1"<?php echo $affChecked; ?>
                />
                <?php echo Lang::txt('COM_CITATIONS_AFFILIATED_WITH_YOUR_ORG'); ?>
            </label>

            <label for="fundedby">
                <?php $fundChecked = $row->fundedby ? ' checked="checked"' : ''; ?>
                <input
                    type="checkbox"
                    class="option"
                    name="fields[fundedby]"
                    id="fundedby"
                    value="1"<?php echo $fundChecked; ?>
                />
                <?php echo Lang::txt('COM_CITATIONS_FUNDED_BY_YOUR_ORG'); ?>
            </label>

            <input type="hidden" name="fields[uid]" value="<?php echo $row->uid; ?>" />
            <input type="hidden" name="fields[created]" value="<?php echo $this->escape($row->created); ?>" />
            <input type="hidden" name="fields[scope]" value="<?php echo $this->escape($row->scope); ?>" />
            <input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($row->scope_id); ?>" />
            <?php $pubVal = ($row->id) ? $this->escape($row->published) : 1; ?>
            <input type="hidden" name="fields[published]" value="<?php echo $pubVal; ?>" />
            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="task" value="save" />

            <?php echo Html::input('token'); ?>
        </fieldset>
        <div class="clear"></div>

        <p class="submit">
            <input
                type="submit"
                class="btn btn-success"
                name="create"
                value="<?php echo Lang::txt('COM_CITATIONS_SAVE'); ?>"
            />

            <a class="btn btn-secondary" href="<?php echo $actionUrl; ?>">
                <?php echo Lang::txt('JCANCEL'); ?>
            </a>
        </p>
    </form>
</section>

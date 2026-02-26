<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Citations\Helpers\Permissions::getActions('citation');

$text = ($this->task == 'edit' ? Lang::txt('EDIT') : Lang::txt('NEW'));

Toolbar::title(Lang::txt('CITATION') . ': ' . $text, 'citation');
if ($canDo->get('core.edit')) {
    Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('citation');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

//set the escape callback
$this->setEscape("htmlentities");

//need to fix these fields
$author = html_entity_decode($this->row->getAuthorString() ?? '');
$ceditor = html_entity_decode($this->row->editor ?? '');
$title = html_entity_decode($this->row->title ?? '');
$booktitle = html_entity_decode($this->row->booktitle ?? '');
$short_title = html_entity_decode($this->row->short_title ?? '');
$journal = html_entity_decode($this->row->journal ?? '');

if (function_exists('mbstring')) {
    $author = (!preg_match('!\S!u', $author)) ? mbstring($author) : $author;
    $ceditor = (!preg_match('!\S!u', $ceditor)) ? mbstring($ceditor) : $ceditor;
    $title = (!preg_match('!\S!u', $title)) ? mbstring($title) : $title;
    $booktitle = (!preg_match('!\S!u', $booktitle)) ? mbstring($booktitle) : $booktitle;
    $short_title = (!preg_match('!\S!u', $short_title)) ? mbstring($short_title) : $short_title;
    $journal = (!preg_match('!\S!u', $journal)) ? mbstring($journal) : $journal;
}

$formAction = Route::url('index.php?option=' . $this->option);
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
$editorOpts = array('class' => 'minimal no-footer', 'buttons' => false);
?>

<?php
if ($this->getError()) {
    echo '<p class="error">' . $this->getError() . '</p>';
}
?>
<form
    action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
>
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('DETAILS'); ?></span></legend>

                <div class="input-wrap">
                    <label for="type"><?php echo Lang::txt('TYPE'); ?>:</label><br />
                    <select name="citation[type]" id="type">
                        <?php foreach ($this->types as $t) : ?>
                            <?php
                            $sel = ($t['id'] == $this->row->type)
                                ? 'selected="selected"' : '';
                            $typeTitle = $this->escape(
                                stripslashes($t['type_title'] ?? '')
                            );
                            $typeVal = $this->escape($t['type']);
                            ?>
                            <option <?php echo $sel; ?> value="<?php echo $t['id']; ?>">
                                <?php echo $typeTitle; ?> (<?php echo $typeVal; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid">
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php
                            $citeVal = $this->escape(
                                stripslashes($this->row->cite ?? '')
                            );
                            ?>
                            <label for="cite">
                                <?php echo Lang::txt('CITE_KEY'); ?>:
                            </label><br />
                            <input
                                type="text"
                                name="citation[cite]"
                                id="cite"
                                maxlength="250"
                                value="<?php echo $citeVal; ?>"
                            />
                            <span class="hint">
                                <?php echo Lang::txt('CITE_KEY_EXPLANATION'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php
                            $refTypeVal = $this->escape(
                                stripslashes($this->row->ref_type ?? '')
                            );
                            ?>
                            <label for="ref_type">
                                <?php echo Lang::txt('REF_TYPE'); ?>:
                            </label><br />
                            <input
                                type="text"
                                name="citation[ref_type]"
                                id="ref_type"
                                size="11"
                                maxlength="50"
                                value="<?php echo $refTypeVal; ?>"
                            />
                        </div>
                    </div>
                </div>

                <div class="grid">
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php
                            $dateSubmitVal = $this->escape(stripslashes(
                                $this->row->date_submit == null
                                    ? '' : $this->row->date_submit
                            ));
                            ?>
                            <label for="date_submit">
                                <?php echo Lang::txt('DATE_SUBMITTED'); ?>:
                            </label><br />
                            <input
                                type="text"
                                name="citation[date_submit]"
                                id="date_submit"
                                maxlength="250"
                                value="<?php echo $dateSubmitVal; ?>"
                            />
                        </div>
                    </div>
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php
                            $dateAcceptVal = $this->escape(stripslashes(
                                $this->row->date_accept == null
                                    ? '' : $this->row->date_accept
                            ));
                            ?>
                            <label for="date_accept">
                                <?php echo Lang::txt('DATE_ACCEPTED'); ?>:
                            </label><br />
                            <input
                                type="text"
                                name="citation[date_accept]"
                                id="date_accept"
                                maxlength="250"
                                value="<?php echo $dateAcceptVal; ?>"
                            />
                        </div>
                    </div>
                </div>

                <div class="grid">
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php
                            $datePubVal = $this->escape(stripslashes(
                                $this->row->date_publish == null
                                    ? '' : $this->row->date_publish
                            ));
                            ?>
                            <label for="date_publish">
                                <?php echo Lang::txt('DATE_PUBLISHED'); ?>:
                            </label><br />
                            <input
                                type="text"
                                name="citation[date_publish]"
                                id="date_publish"
                                maxlength="250"
                                value="<?php echo $datePubVal; ?>"
                            />
                        </div>
                    </div>
                    <div class="col span6">
                        <div class="grid">
                            <div class="col span6">
                                <div class="input-wrap">
                                    <?php
                                    $yearVal = $this->escape(
                                        stripslashes($this->row->year ?? '')
                                    );
                                    ?>
                                    <label for="year">
                                        <?php echo Lang::txt('YEAR'); ?>:
                                    </label><br />
                                    <input
                                        type="text"
                                        name="citation[year]"
                                        id="year"
                                        size="4"
                                        maxlength="4"
                                        value="<?php echo $yearVal; ?>"
                                    />
                                </div>
                            </div>
                            <div class="col span6">
                                <div class="input-wrap">
                                    <?php
                                    $monthVal = $this->escape(
                                        stripslashes($this->row->month ?? '')
                                    );
                                    ?>
                                    <label for="month">
                                        <?php echo Lang::txt('MONTH'); ?>:
                                    </label><br />
                                    <input
                                        type="text"
                                        name="citation[month]"
                                        id="month"
                                        size="11"
                                        maxlength="50"
                                        value="<?php echo $monthVal; ?>"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="input-wrap">
                    <label for="author">
                        <?php echo Lang::txt('AUTHORS'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[author]"
                        id="author"
                        value="<?php echo $this->escape($author); ?>"
                    />
                </div>
                <div class="input-wrap">
                    <?php
                    $authorAddrLabel = Lang::txt(
                        'COM_CITATIONS_FIELD_AUTHOR_ADDRESS'
                    );
                    $authorAddrVal = $this->escape(
                        stripslashes($this->row->author_address ?? '')
                    );
                    ?>
                    <label for="author_address">
                        <?php echo $authorAddrLabel; ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[author_address]"
                        id="author_address"
                        value="<?php echo $authorAddrVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <label for="editor">
                        <?php echo Lang::txt('EDITORS'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[editor]"
                        id="editor"
                        maxlength="250"
                        value="<?php echo $this->escape($ceditor); ?>"
                    />
                </div>
                <div class="input-wrap">
                    <label for="title">
                        <?php echo Lang::txt('TITLE_CHAPTER'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[title]"
                        id="title"
                        maxlength="250"
                        value="<?php echo $this->escape($title); ?>"
                    />
                </div>
                <div class="input-wrap">
                    <label for="booktitle">
                        <?php echo Lang::txt('BOOK_TITLE'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[booktitle]"
                        id="booktitle"
                        maxlength="250"
                        value="<?php echo $this->escape($booktitle); ?>"
                    />
                </div>
                <div class="input-wrap">
                    <?php
                    $shortTitleLabel = Lang::txt(
                        'COM_CITATIONS_FIELD_SHORT_TITLE'
                    );
                    ?>
                    <label for="shorttitle">
                        <?php echo $shortTitleLabel; ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[short_title]"
                        id="shorttitle"
                        maxlength="250"
                        value="<?php echo $this->escape($short_title); ?>"
                    />
                </div>
                <div class="input-wrap">
                    <label for="journal">
                        <?php echo Lang::txt('JOURNAL'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[journal]"
                        id="journal"
                        maxlength="250"
                        value="<?php echo $this->escape($journal); ?>"
                    />
                </div>

                <div class="grid">
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php
                            $volumeVal = $this->escape(
                                stripslashes($this->row->volume ?? '')
                            );
                            ?>
                            <label for="volume">
                                <?php echo Lang::txt('VOLUME'); ?>:
                            </label><br />
                            <input
                                type="text"
                                name="citation[volume]"
                                id="volume"
                                maxlength="11"
                                value="<?php echo $volumeVal; ?>"
                            />
                        </div>
                    </div>
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php
                            $numberVal = $this->escape(
                                stripslashes($this->row->number ?? '')
                            );
                            ?>
                            <label for="number">
                                <?php echo Lang::txt('ISSUE'); ?>:
                            </label><br />
                            <input
                                type="text"
                                name="citation[number]"
                                id="number"
                                maxlength="50"
                                value="<?php echo $numberVal; ?>"
                            />
                        </div>
                    </div>
                </div>

                <div class="grid">
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php
                            $pagesVal = $this->escape(stripslashes(
                                $this->row->pages == null
                                    ? '' : $this->row->pages
                            ));
                            ?>
                            <label for="pages">
                                <?php echo Lang::txt('PAGES'); ?>:
                            </label><br />
                            <input
                                type="text"
                                name="citation[pages]"
                                id="pages"
                                maxlength="250"
                                value="<?php echo $pagesVal; ?>"
                            />
                        </div>
                    </div>
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php
                            $isbnVal = $this->escape(
                                stripslashes($this->row->isbn ?? '')
                            );
                            ?>
                            <label for="isbn">
                                <?php echo Lang::txt('ISBN'); ?>:
                            </label><br />
                            <input
                                type="text"
                                name="citation[isbn]"
                                id="isbn"
                                maxlength="50"
                                value="<?php echo $isbnVal; ?>"
                            />
                        </div>
                    </div>
                </div>

                <div class="input-wrap">
                    <?php
                    $doiVal = $this->escape(
                        stripslashes($this->row->doi ?? '')
                    );
                    ?>
                    <label for="doi">
                        <?php echo Lang::txt('DOI'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[doi]"
                        id="doi"
                        maxlength="250"
                        value="<?php echo $doiVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <?php
                    $callNumLabel = Lang::txt(
                        'COM_CITATIONS_FIELD_CALL_NUMBER'
                    );
                    $callNumVal = $this->escape(
                        stripslashes($this->row->call_number ?? '')
                    );
                    ?>
                    <label for="callnumber">
                        <?php echo $callNumLabel; ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[call_number]"
                        id="callnumber"
                        maxlength="250"
                        value="<?php echo $callNumVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <?php
                    $accNumLabel = Lang::txt(
                        'COM_CITATIONS_FIELD_ACCESSION_NUMBER'
                    );
                    $accNumVal = $this->escape(
                        stripslashes($this->row->accession_number ?? '')
                    );
                    ?>
                    <label for="accessionnumber">
                        <?php echo $accNumLabel; ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[accession_number]"
                        id="accessionnumber"
                        maxlength="250"
                        value="<?php echo $accNumVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <?php
                    $seriesVal = $this->escape(
                        stripslashes($this->row->series ?? '')
                    );
                    ?>
                    <label for="series">
                        <?php echo Lang::txt('SERIES'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[series]"
                        id="series"
                        maxlength="250"
                        value="<?php echo $seriesVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <?php
                    $editionVal = $this->escape(
                        stripslashes($this->row->edition ?? '')
                    );
                    ?>
                    <label for="edition">
                        <?php echo Lang::txt('EDITION'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[edition]"
                        id="edition"
                        maxlength="250"
                        value="<?php echo $editionVal; ?>"
                    />
                    <br />
                    <span class="hint">
                        <?php echo Lang::txt('EDITION_EXPLANATION'); ?>
                    </span>
                </div>
                <div class="input-wrap">
                    <?php
                    $schoolVal = $this->escape(
                        stripslashes($this->row->school ?? '')
                    );
                    ?>
                    <label for="school">
                        <?php echo Lang::txt('SCHOOL'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[school]"
                        id="school"
                        maxlength="250"
                        value="<?php echo $schoolVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <?php
                    $publisherVal = $this->escape(
                        stripslashes($this->row->publisher ?? '')
                    );
                    ?>
                    <label for="publisher">
                        <?php echo Lang::txt('PUBLISHER'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[publisher]"
                        id="publisher"
                        maxlength="250"
                        value="<?php echo $publisherVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <?php
                    $institutionVal = $this->escape(
                        stripslashes($this->row->institution ?? '')
                    );
                    $instLabel = Lang::txt('INSTITUTION_EXPLANATION');
                    ?>
                    <label for="institution">
                        <?php echo Lang::txt('INSTITUTION'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[institution]"
                        id="institution"
                        maxlength="250"
                        value="<?php echo $institutionVal; ?>"
                    />
                    <br />
                    <span class="hint"><?php echo $instLabel; ?></span>
                </div>
                <div class="input-wrap">
                    <?php
                    $addressVal = $this->escape(
                        stripslashes($this->row->address ?? '')
                    );
                    ?>
                    <label for="address">
                        <?php echo Lang::txt('ADDRESS'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[address]"
                        id="address"
                        maxlength="250"
                        value="<?php echo $addressVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <?php
                    $locationVal = $this->escape(
                        stripslashes($this->row->location ?? '')
                    );
                    ?>
                    <label for="location">
                        <?php echo Lang::txt('LOCATION'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[location]"
                        id="location"
                        maxlength="250"
                        value="<?php echo $locationVal; ?>"
                    />
                    <span class="hint">
                        <?php echo Lang::txt('LOCATION_EXPLANATION'); ?>
                    </span>
                </div>
                <div class="input-wrap">
                    <?php
                    $howpubVal = $this->escape(
                        stripslashes($this->row->howpublished ?? '')
                    );
                    ?>
                    <label for="howpublished">
                        <?php echo Lang::txt('PUBLISH_METHOD'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[howpublished]"
                        id="howpublished"
                        maxlength="250"
                        value="<?php echo $howpubVal; ?>"
                    />
                    <br />
                    <span class="hint">
                        <?php echo Lang::txt('PUBLISH_METHOD_EXPLANATION'); ?>
                    </span>
                </div>
                <div class="input-wrap">
                    <?php
                    $urlVal = $this->escape(
                        stripslashes($this->row->url ?? '')
                    );
                    ?>
                    <label for="url">
                        <?php echo Lang::txt('URL'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[url]"
                        id="url"
                        maxlength="250"
                        value="<?php echo $urlVal; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <?php
                    $eprintVal = $this->escape(
                        stripslashes($this->row->eprint ?? '')
                    );
                    ?>
                    <label for="eprint">
                        <?php echo Lang::txt('EPRINT'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="citation[eprint]"
                        id="eprint"
                        maxlength="250"
                        value="<?php echo $eprintVal; ?>"
                    />
                    <br />
                    <span class="hint">
                        <?php echo Lang::txt('EPRINT_EXPLANATION'); ?>
                    </span>
                </div>
                <div class="input-wrap">
                    <?php
                    $abstractLabel = Lang::txt(
                        'COM_CITATIONS_FIELD_ABSTRACT'
                    );
                    $abstractContent = stripslashes(
                        $this->row->abstract ?? ''
                    );
                    ?>
                    <label for="abstract">
                        <?php echo $abstractLabel; ?>:
                    </label><br />
                    <?php echo $this->editor(
                        'citation[abstract]',
                        $abstractContent,
                        50,
                        10,
                        'abstract',
                        $editorOpts
                    ); ?>
                </div>
                <div class="input-wrap">
                    <label for="note">
                        <?php echo Lang::txt('NOTES'); ?>:
                    </label><br />
                    <?php
                    $noteContent = stripslashes(
                        $this->row->note ?? ''
                    );
                    echo $this->editor(
                        'citation[note]',
                        $noteContent,
                        50,
                        10,
                        'note',
                        $editorOpts
                    ); ?>
                </div>
                <div class="input-wrap">
                    <?php
                    $keywordsLabel = Lang::txt(
                        'COM_CITATIONS_FIELD_KEYWORDS'
                    );
                    $keywordsContent = stripslashes(
                        $this->row->keywords ?? ''
                    );
                    ?>
                    <label for="keywords">
                        <?php echo $keywordsLabel; ?>:
                    </label><br />
                    <?php echo $this->editor(
                        'citation[keywords]',
                        $keywordsContent,
                        50,
                        10,
                        'keywords',
                        $editorOpts
                    ); ?>
                </div>
                <div class="input-wrap">
                    <?php
                    $resNotesLabel = Lang::txt(
                        'COM_CITATIONS_FIELD_RESEARCH_NOTES'
                    );
                    $resNotesContent = stripslashes(
                        $this->row->research_notes ?? ''
                    );
                    ?>
                    <label for="research_notes">
                        <?php echo $resNotesLabel; ?>:
                    </label><br />
                    <?php echo $this->editor(
                        'citation[research_notes]',
                        $resNotesContent,
                        50,
                        10,
                        'research_notes',
                        $editorOpts
                    ); ?>
                </div>
            </fieldset>

            <fieldset class="adminform">
                <legend>
                    <span><?php echo Lang::txt('MANUAL_FORMAT'); ?></span>
                </legend>
                <div class="input-wrap">
                    <?php
                    $formatLabel = Lang::txt('MANUAL_FORMAT_FORMAT');
                    ?>
                    <label for="format_type">
                        <?php echo $formatLabel; ?>:
                    </label>
                    <select id="format_type" name="citation[format]">
                        <?php
                        $apaSel = ($this->row->format == 'apa')
                            ? 'selected="selected"' : '';
                        $ieeeSel = ($this->row->format == 'ieee')
                            ? 'selected="selected"' : '';
                        $apaLabel = Lang::txt('MANUAL_FORMAT_FORMAT_APA');
                        $ieeeLabel = Lang::txt('MANUAL_FORMAT_FORMAT_IEEE');
                        ?>
                        <option value="apa" <?php echo $apaSel; ?>>
                            <?php echo $apaLabel; ?>
                        </option>
                        <option value="ieee" <?php echo $ieeeSel; ?>>
                            <?php echo $ieeeLabel; ?>
                        </option>
                    </select>
                </div>
                <div class="input-wrap">
                    <?php
                    $manualLabel = Lang::txt('MANUAL_FORMAT_CITATION');
                    $formattedContent = stripslashes(
                        $this->row->get('formatted', '')
                    );
                    ?>
                    <label for="citation-formatted">
                        <?php echo $manualLabel; ?>:
                    </label>
                    <?php echo $this->editor(
                        'citation[formatted]',
                        $formattedContent,
                        50,
                        10,
                        'formatted',
                        $editorOpts
                    ); ?>
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <fieldset class="adminform">
                <legend>
                    <span><?php echo Lang::txt('CITATION_FOR'); ?></span>
                </legend>

                <table class="admintable" id="assocs">
                    <thead>
                        <tr>
                            <th scope="col">
                                <?php echo Lang::txt('TYPE'); ?>
                            </th>
                            <th scope="col">
                                <?php echo Lang::txt('ID'); ?>
                            </th>
                            <th scope="col">
                                <?php echo Lang::txt('COM_CITATIONS_CONTEXT'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="3">
                                <button id="add_row">
                                    <?php echo Lang::txt('ADD_A_ROW'); ?>
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $assocs = $this->assocs;
                        $r = count($assocs);
                        if ($r > 5) {
                            $n = $r;
                        } else {
                            $n = 5;
                        }
                        for ($i = 0; $i < $n; $i++) {
                            if ($r == 0 || !isset($assocs[$i])) {
                                $assocs[$i] = new stdClass();
                                $assocs[$i]->id   = null;
                                $assocs[$i]->cid  = null;
                                $assocs[$i]->oid  = null;
                                $assocs[$i]->type = null;
                                $assocs[$i]->tbl  = null;
                            }

                            $tblName = "assocs[{$i}][tbl]";
                            $oidName = "assocs[{$i}][oid]";
                            $idName  = "assocs[{$i}][id]";
                            $cidName = "assocs[{$i}][cid]";
                            $typeName = "assocs[{$i}][type]";

                            $tblEmpty = ($assocs[$i]->tbl == '')
                                ? ' selected="selected"' : '';
                            $tblRes = ($assocs[$i]->tbl == 'resource')
                                ? ' selected="selected"' : '';
                            $tblPub = ($assocs[$i]->tbl == 'publication')
                                ? ' selected="selected"' : '';

                            $typeEmpty = ($assocs[$i]->type == '')
                                ? ' selected="selected"' : '';
                            $typeRef = ($assocs[$i]->type == 'references')
                                ? ' selected="selected"' : '';
                            $typeRefBy = ($assocs[$i]->type == 'referencedby')
                                ? ' selected="selected"' : '';

                            $oidVal = $this->escape($assocs[$i]->oid);
                            $idVal  = $this->escape($assocs[$i]->id);
                            $cidVal = $this->escape($assocs[$i]->cid);

                            $selectLabel = Lang::txt('SELECT');
                            $resourceLabel = Lang::txt('RESOURCE');
                            $refsLabel = Lang::txt(
                                'COM_CITATIONS_CONTEXT_REFERENCES'
                            );
                            $refByLabel = Lang::txt(
                                'COM_CITATIONS_CONTEXT_REFERENCEDBY'
                            );
                            ?>
                            <tr>
                                <td>
                                    <select
                                        name="<?php echo $tblName; ?>"
                                        class="noUniform"
                                    >
                                        <option value=""<?php echo $tblEmpty; ?>>
                                            <?php echo $selectLabel; ?>
                                        </option>
                                        <option value="resource"<?php echo $tblRes; ?>>
                                            <?php echo $resourceLabel; ?>
                                        </option>
                                        <option value="publication"<?php echo $tblPub; ?>>
                                            <?php echo Lang::txt('Publication'); ?>
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        name="<?php echo $oidName; ?>"
                                        value="<?php echo $oidVal; ?>"
                                        size="5"
                                    />
                                    <input
                                        type="hidden"
                                        name="<?php echo $idName; ?>"
                                        value="<?php echo $idVal; ?>"
                                    />
                                    <input
                                        type="hidden"
                                        name="<?php echo $cidName; ?>"
                                        value="<?php echo $cidVal; ?>"
                                    />
                                </td>
                                <td>
                                    <select
                                        name="<?php echo $typeName; ?>"
                                        class="noUniform"
                                    >
                                        <option value=""<?php echo $typeEmpty; ?>>
                                            <?php echo $selectLabel; ?>
                                        </option>
                                        <option value="references"<?php echo $typeRef; ?>>
                                            <?php echo $refsLabel; ?>
                                        </option>
                                        <option value="referencedby"<?php echo $typeRefBy; ?>>
                                            <?php echo $refByLabel; ?>
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </fieldset>

            <fieldset class="adminform">
                <legend>
                    <span><?php echo Lang::txt('AFFILIATION'); ?></span>
                </legend>

                <div class="input-wrap">
                    <?php
                    $affChecked = $this->row->affiliated
                        ? ' checked="checked"' : '';
                    ?>
                    <input
                        type="checkbox"
                        name="citation[affiliated]"
                        id="affiliated"
                        value="1"<?php echo $affChecked; ?>
                    />
                    <label for="affiliated">
                        <?php echo Lang::txt('AFFILIATED_WITH_YOUR_ORG'); ?>
                    </label>
                </div>
                <div class="input-wrap">
                    <?php
                    $fundChecked = $this->row->fundedby
                        ? ' checked="checked"' : '';
                    ?>
                    <input
                        type="checkbox"
                        name="citation[fundedby]"
                        id="fundedby"
                        value="1"<?php echo $fundChecked; ?>
                    />
                    <label for="fundedby">
                        <?php echo Lang::txt('FUNDED_BY_YOUR_ORG'); ?>
                    </label>
                </div>
            </fieldset>

            <fieldset class="adminform">
                <legend>
                    <span><?php echo Lang::txt('SCOPE'); ?></span>
                </legend>
                <div class="input-wrap">
                    <label for="scope">
                        <?php echo Lang::txt('SCOPE'); ?>
                    </label>
                    <?php
                    $hubSel = ($this->row->scope == "hub")
                        ? 'selected="selected"' : '';
                    $groupSel = ($this->row->scope == "group")
                        ? 'selected="selected"' : '';
                    $memberSel = ($this->row->scope == "member")
                        ? 'selected="selected"' : '';
                    ?>
                    <select name="citation[scope]" id="scope">
                        <option <?php echo $hubSel; ?> value="hub">
                            Hub
                        </option>
                        <option <?php echo $groupSel; ?> value="group">
                            Group
                        </option>
                        <option <?php echo $memberSel; ?> value="member">
                            Member
                        </option>
                    </select>
                </div>
                <div class="input-wrap">
                    <?php
                    $scopeIdVal = $this->escape(
                        stripslashes($this->row->scope_id ?? '')
                    );
                    ?>
                    <label for="scope_id">
                        <?php echo Lang::txt('SCOPE_ID'); ?>
                    </label>
                    <input
                        type="text"
                        name="citation[scope_id]"
                        id="scope_id"
                        maxlength="10"
                        value="<?php echo $scopeIdVal; ?>"
                    />
                </div>
            </fieldset>

            <fieldset class="adminform">
                <legend>
                    <span>
                        <?php echo Lang::txt('COM_CITATIONS_OPTIONS'); ?>
                    </span>
                </legend>

                <?php
                $sponsorsHint = Lang::txt(
                    'COM_CITATIONS_FIELD_SPONSORS_HINT'
                );
                $sponsorsLabel = Lang::txt(
                    'COM_CITATIONS_FIELD_SPONSORS'
                );
                ?>
                <div
                    class="input-wrap"
                    data-hint="<?php echo $sponsorsHint; ?>"
                >
                    <label for="field-sponsors">
                        <?php echo $sponsorsLabel; ?>
                    </label>
                    <select
                        name="sponsors[]"
                        id="field-sponsors"
                        class="noUniform"
                        multiple="multiple"
                    >
                        <option value="">- Select Citation Sponsor -</option>
                        <?php foreach ($this->sponsors as $s) : ?>
                            <?php
                            $sel = (in_array($s->get('id'), $this->row_sponsors))
                                ? 'selected="selected"' : '';
                            $sponsorVal = $this->escape(
                                stripslashes($s['sponsor'] ?? '')
                            );
                            ?>
                            <option <?php echo $sel; ?> value="<?php echo $s['id']; ?>">
                                <?php echo $sponsorVal; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="hint"><?php echo $sponsorsHint; ?></span>
                </div>

                <?php if ($this->config->get('citation_allow_tags', 'no') == 'yes') : ?>
                    <div class="input-wrap">
                        <?php
                            $t = array();
                        foreach ($this->tags as $tag) {
                            $t[] = stripslashes($tag ?? '');
                        }
                        $tagsLabel = Lang::txt('COM_CITATIONS_FIELD_TAGS');
                        ?>
                        <label for="field-tags">
                            <?php echo $tagsLabel; ?>
                        </label>
                        <textarea name="tags" id="field-tags" rows="10">
                            <?php echo implode(',', $t); ?>
                        </textarea>
                    </div>
                <?php endif; ?>

                <?php if ($this->config->get('citation_allow_badges', 'no') == 'yes') : ?>
                    <div class="input-wrap">
                        <?php
                            $b = array();
                        foreach ($this->badges as $badge) {
                            $b[] = stripslashes($badge ?? '');
                        }
                        $badgesLabel = Lang::txt(
                            'COM_CITATIONS_FIELD_BADGES'
                        );
                        ?>
                        <label for="field-badges">
                            <?php echo $badgesLabel; ?>
                        </label>
                        <textarea name="badges" id="field-badges" rows="10">
                            <?php echo implode(',', $b); ?>
                        </textarea>
                    </div>
                <?php endif; ?>


                <div class="input-wrap">
                    <?php
                    $excludeLabel = Lang::txt(
                        'COM_CITATIONS_FIELD_EXCLUDE_FROM_EXPORT'
                    );
                    ?>
                    <label for="field-exclude">
                        <?php echo $excludeLabel; ?>
                    </label>
                    <textarea
                        name="exclude"
                        id="field-exclude"
                        rows="10"
                    ><?php echo $this->params->get('exclude'); ?></textarea>
                </div>

                <?php
                    $rollovers = $this->config->get("citation_rollover", "no");
                    $rollover  = $this->params->get("rollover");

                    //check the the global setting
                if ($rollovers == 'yes') {
                    $ckd = 'checked="checked"';
                } else {
                    $ckd = '';
                }

                    //check this citations setting
                if ($rollover == 1) {
                    $ckd = 'checked="checked"';
                } elseif ($rollover == 0 && is_numeric($rollover)) {
                    $ckd = '';
                } else {
                    $ckd = $ckd;
                }

                $rolloverLabel = Lang::txt(
                    'COM_CITATIONS_FIELD_ABSTRACT_ROLLOVER'
                );
                ?>
                <div class="input-wrap">
                    <input
                        type="checkbox"
                        name="rollover"
                        id="rollover"
                        value="1"
                        <?php echo $ckd; ?>
                    />
                    <label for="rollover">
                        <?php echo $rolloverLabel; ?>
                    </label>
                </div>
            </fieldset>
        </div>
    </div>

    <input
        type="hidden"
        name="citation[uid]"
        value="<?php echo $this->row->uid; ?>"
    />
    <input
        type="hidden"
        name="citation[created]"
        value="<?php echo $this->row->created; ?>"
    />
    <input
        type="hidden"
        name="citation[id]"
        value="<?php echo $this->row->id; ?>"
    />
    <input
        type="hidden"
        name="citation[published]"
        value="<?php echo $this->row->published; ?>"
    />
    <input
        type="hidden"
        name="option"
        value="<?php echo $this->option; ?>"
    />
    <input
        type="hidden"
        name="controller"
        value="<?php echo $this->controller; ?>"
    />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>

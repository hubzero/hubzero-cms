<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->question->isOpen() && !$this->question->isReported()) {
    $status = 'open';
} elseif ($this->question->isReported()) {
    $status = 'underreview';
} else {
    $status = 'closed';
}

$link = rtrim(Request::base(), '/') . '/'
    . ltrim(Route::url($this->question->link()), '/');

$questionCreatorName = $this->question->get('anonymous')
    ? Lang::txt('JANONYMOUS')
    : $this->escape(
        stripslashes($this->question->creator->get('name'))
    );

$responseCreatorName = $this->row->get('anonymous')
    ? Lang::txt('JANONYMOUS')
    : $this->escape(
        stripslashes($this->row->creator->get('name'))
    );

$thStyle = 'text-align: right; padding: 0 0.5em;'
    . ' font-weight: bold; white-space: nowrap;';
$tdStyle = 'text-align: left; padding: 0 0.5em;';
$msgStyle = 'border-collapse: collapse; color: #666;'
    . ' line-height: 1; padding: 5px; text-align: center;';
$questionCellStyle = 'font-size: 2.5em; font-weight: bold;'
    . ' text-align: center; padding: 0 30px 8px 0;'
    . ' vertical-align: top;';
$tagsThStyle = $thStyle . ' vertical-align: top;';
$divStyle = 'line-height: 1.6em; margin: 1em 0;'
    . ' padding: 0; text-align: left;';

// phpcs:disable Generic.Files.LineLength
$tableStyle = 'border-collapse: collapse; border: 1px solid #c8e3c2; background: #eafbe6; font-size: 0.9em; line-height: 1.6em; background-image: -webkit-gradient(linear, 0 0, 100% 100%,
                                        color-stop(.25, rgba(255, 255, 255, .075)), color-stop(.25, transparent),
                                        color-stop(.5, transparent), color-stop(.5, rgba(255, 255, 255, .075)),
                                        color-stop(.75, rgba(255, 255, 255, .075)), color-stop(.75, transparent),
                                        to(transparent));
    background-image: -webkit-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
                                    transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
                                    transparent 75%, transparent);
    background-image: -moz-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
                                    transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
                                    transparent 75%, transparent);
    background-image: -ms-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
                                    transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
                                    transparent 75%, transparent);
    background-image: -o-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
                                    transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
                                    transparent 75%, transparent);
    background-image: linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
                                    transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
                                    transparent 75%, transparent);
                                    -webkit-background-size: 30px 30px;
                                    -moz-background-size: 30px 30px;
                                    background-size: 30px 30px;';
// phpcs:enable Generic.Files.LineLength

$questionMsg = 'A response has been posted to Question #'
    . $this->question->get('id') . ' by '
    . $responseCreatorName . '.';

$commentCellStyle = 'font-size: 2.5em; font-weight: bold;'
    . ' text-align: center; vertical-align: top;'
    . ' padding: 0 30px 8px 0;';
$commentPStyle = 'display: block; border: 1px solid #c2e1e3;'
    . ' margin:0; padding: 1em; background-color: #e6fafb;';
$questionInfoCellStyle = 'font-size: 2.5em; font-weight: bold;'
    . ' text-align: center; padding: 8px 30px 8px 0;'
    . ' vertical-align: top;';
?>
    <!-- Start Header -->
    <table
        class="tbl-header"
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
    >
        <tbody>
            <tr>
                <td
                    width="10%"
                    align="left"
                    valign="bottom"
                    nowrap="nowrap"
                    class="sitename"
                >
                    <?php echo Config::get('sitename'); ?>
                </td>
                <td
                    width="80%"
                    align="left"
                    valign="bottom"
                    class="tagline mobilehide"
                >
                    <span class="home">
                        <a href="<?php echo Request::base(); ?>">
                            <?php echo Request::base(); ?>
                        </a>
                    </span>
                    <br />
                    <span class="description">
                        <?php echo Config::get('MetaDesc'); ?>
                    </span>
                </td>
                <td
                    width="10%"
                    align="right"
                    valign="bottom"
                    nowrap="nowrap"
                    class="component"
                >
                    <?php echo Lang::txt('Questions &amp; Answers'); ?>
                </td>
            </tr>
        </tbody>
    </table>
    <!-- End Header -->

    <!-- Start Spacer -->
    <table
        class="tbl-spacer"
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
    >
        <tbody>
            <tr>
                <td height="30"></td>
            </tr>
        </tbody>
    </table>
    <!-- End Spacer -->

    <!-- Start Message -->
    <table
        class="tbl-message"
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
    >
        <tbody>
            <tr>
                <td
                    align="left"
                    valign="bottom"
                    style="<?php echo $msgStyle; ?>"
                >
                    <?php echo $questionMsg; ?>
                </td>
            </tr>
        </tbody>
    </table>
    <!-- End Message -->

    <!-- Start Spacer -->
    <table
        class="tbl-spacer"
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
    >
        <tbody>
            <tr>
                <td height="30"></td>
            </tr>
        </tbody>
    </table>
    <!-- End Spacer -->

    <table
        id="question-info"
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        style="<?php echo $tableStyle; ?>"
    >
        <thead>
            <tr>
                <th
                    colspan="2"
                    style="font-weight: normal; border-bottom: 1px solid #c8e3c2; padding: 8px; text-align: left"
                    align="left"
                >
                    <?php echo $this->question->subject; ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td
                    class="mobilehide"
                    style="<?php echo $questionInfoCellStyle; ?>"
                    align="center"
                    valing="top"
                >
                    <p style="display: block; border: 1px solid transparent; margin:0; padding: 1em;">?</p>
                </td>
                <td width="100%" style="padding: 8px;">
                    <table
                        width="100%"
                        style="border-collapse: collapse; font-size: 0.9em;"
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                    >
                        <tbody>
                            <tr>
                                <th style="<?php echo $thStyle; ?>" align="right">
                                    Question:
                                </th>
                                <td
                                    style="<?php echo $tdStyle; ?>"
                                    width="100%"
                                    align="left"
                                >
                                    # <?php echo $this->question->get('id'); ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="<?php echo $thStyle; ?>" align="right">
                                    Created:
                                </th>
                                <td
                                    style="<?php echo $tdStyle; ?>"
                                    width="100%"
                                    align="left"
                                >
                                    @ <?php echo $this->question->created('time'); ?>
                                    on <?php echo $this->question->created('date'); ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="<?php echo $thStyle; ?>" align="right">
                                    Creator:
                                </th>
                                <td
                                    style="<?php echo $tdStyle; ?>"
                                    width="100%"
                                    align="left"
                                >
                                    <?php echo $questionCreatorName; ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="<?php echo $thStyle; ?>" align="right">
                                    Status:
                                </th>
                                <td
                                    style="<?php echo $tdStyle; ?>"
                                    width="100%"
                                    align="left"
                                >
                                    <?php echo $status ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="<?php echo $tagsThStyle; ?>" align="right">
                                    Tags:
                                </th>
                                <td
                                    style="<?php echo $tdStyle; ?>"
                                    width="100%"
                                    align="left"
                                >
                                    <?php echo $this->escape($this->question->tags('string')); ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="<?php echo $thStyle; ?>" align="right">
                                    Link:
                                </th>
                                <td
                                    style="<?php echo $tdStyle; ?>"
                                    width="100%"
                                    align="left"
                                >
                                    <a href="<?php echo $link; ?>">
                                        <?php echo $link; ?>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <table
        width="100%"
        id="question-comments"
        style="border-collapse: collapse; margin: 3em 0 0 0; padding: 0"
        cellpadding="0"
        cellspacing="0"
        border="0"
    >
        <tbody>
            <tr>
                <td
                    class="mobilehide"
                    rowspan="2"
                    style="<?php echo $commentCellStyle; ?>"
                    align="center"
                    valign="top"
                >
                    <p style="<?php echo $commentPStyle; ?>">!</p>
                </td>
                <th style="text-align: left;" align="left">
                    <?php echo $responseCreatorName; ?>
                </th>
                <th
                    class="timestamp"
                    style="color: #999; text-align: right;"
                    align="right"
                >
                    <span class="mobilehide">
                        @ <?php echo $this->row->created('time'); ?>
                        on <?php echo $this->row->created('date'); ?>
                    </span>
                </th>
            </tr>
            <tr>
                <td
                    colspan="2"
                    style="padding: 0"
                    cellpadding="0"
                    cellspacing="0"
                    border="0"
                >
                    <div style="<?php echo $divStyle; ?>">
                        <?php echo $this->row->content; ?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Start Spacer -->
    <table
        class="tbl-spacer"
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
    >
        <tbody>
            <tr>
                <td height="30"></td>
            </tr>
        </tbody>
    </table>
    <!-- End Spacer -->

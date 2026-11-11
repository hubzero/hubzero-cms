<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{message: mixed, project: mixed, subject: mixed} $this
 */

// Set some styles
$ulStyle = 'list-style: none; font-size: 0.9em; margin: 0.5em 0; line-height: 1.6em; text-align: left;';

$base = trim(preg_replace('/\/administrator/', '', Request::base()), '/');
$link = $base . '/projects/' . $this->project->get('alias');

// Get the actual message
$comment = $this->message;

// Parse admin comment
if (!strstr($comment, '</p>') && !strstr($comment, '<pre class="wiki">')) {
    $comment = str_replace("<br />", '', $comment);
    $comment = $this->escape($comment);
    $comment = nl2br($comment);
    $comment = str_replace("\t", ' &nbsp; &nbsp;', $comment);
    $comment = preg_replace('/  /', ' &nbsp;', $comment);
}

if ($comment) {
    $comment = '<p style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;">' . $comment . '</p>';
}

// Project owner
if ($this->project->isProvisioned()) {
    $owner = null;
} else {
    $owner = $this->project->groupOwner()
            ? $this->project->groupOwner('cn') . ' ' . Lang::txt('COM_PROJECTS_GROUP')
            : $this->project->owner('name');
}
?>

<!-- Start Header -->
<table class="tbl-header" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td width="10%" align="left" valign="bottom" nowrap="nowrap" class="sitename">
                <?php echo Config::get('sitename'); ?>
            </td>
            <td width="80%" align="left" valign="bottom" class="tagline mobilehide">
                <span class="home">
                    <a href="<?php echo $base; ?>"><?php echo $base; ?></a>
                </span>
                <br />
                <span class="description"><?php echo Config::get('MetaDesc'); ?></span>
            </td>
            <td width="10%" align="right" valign="bottom" nowrap="nowrap" class="component">
                <?php echo Lang::txt('COM_PROJECTS_PROJECTS'); ?>
            </td>
        </tr>
    </tbody>
</table>
<!-- End Header -->

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td height="30"></td>
        </tr>
    </tbody>
</table>
<!-- End Spacer -->

<!-- Start Message -->
<table class="tbl-message" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td align="left" valign="bottom"
                style="border-collapse: collapse; color: #666; line-height: 1; padding: 5px; text-align: center;">
            <?php echo $this->escape($this->subject); ?>
            </td>
        </tr>
    </tbody>
</table>
<!-- End Message -->

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td height="30"></td>
        </tr>
    </tbody>
</table>
<!-- End Spacer -->

<table id="project-info" width="100%"  cellpadding="0" cellspacing="0" border="0"
    style="border-collapse: collapse; line-height: 1.6em;">
    <tbody>
        <tr>
            <?php
            $tdStyle = 'font-size: 2.5em; font-weight: bold; text-align: center; padding: 0 30px 8px 0; '
                . 'vertical-align: top;';
            ?>
            <td class="mobilehide" style="<?= $tdStyle ?>" align="center" valing="top">
            </td>
            <td width="100%" style="padding: 18px 8px 8px 8px; border-top: 2px solid #e9e9e9;">
                <table width="100%" style="border-collapse: collapse; font-size: 0.9em;" cellpadding="0" cellspacing="0"
                    border="0">
                    <tbody>
                        <tr>
                            <th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;"
                                align="right">Project:</th>
                            <td style="text-align: left; padding: 0 0.5em;" width="100%" align="left">
                                <?= $this->escape(
                                    $this->project->get('title') . ' (' . $this->project->get('alias') . ')',
                                ) ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;"
                                align="right">Created:</th>
                            <td style="text-align: left; padding: 0 0.5em;" width="100%" align="left">
                                @ <?= $this->project->created('time') ?> on <?= $this->project->created('date') ?>
                            </td>
                        </tr>
                        <?php if ($owner) { ?>
                        <tr>
                            <th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;"
                                align="right">Owner:</th>
                            <td style="text-align: left; padding: 0 0.5em;" width="100%"
                                align="left"><?= $this->escape($owner) ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;"
                                align="right">Link:</th>
                            <td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><a
                                href="<?= $link ?>"><?= $link ?></a></td>
                        </tr>
                    </tbody>
                </table>

                <?php
                $tableStyle = 'margin: 18px 0 0 0; border-top: 2px solid #e9e9e9; border-collapse: collapse; '
                    . 'font-size: 1em;';
                ?>
                <table width="100%" style="<?= $tableStyle ?>">
                    <tbody>
                        <tr>
                            <td style="text-align: left; padding: 0 0.5em;" cellpadding="0" cellspacing="0" border="0">
                                <div style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;">
                                    <?= $comment ?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td height="30"></td>
        </tr>
    </tbody>
</table>
<!-- End Spacer -->

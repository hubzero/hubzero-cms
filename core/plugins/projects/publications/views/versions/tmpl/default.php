<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

// Get publication properties
$typetitle = \Components\Publications\Helpers\Html::writePubCategory(
    $this->pub->category()->alias,
    $this->pub->category()->name,
);

?>
<form action="<?php echo Route::url($this->pub->link('editbase')); ?>" method="post" id="plg-form" >
    <div id="plg-header">
    <?php if ($this->project->isProvisioned()) { ?>
        <?php
        $linkUrl = Route::url($this->pub->link('editversion'));
        $linkText = ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS'));
        $headingText = ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSIONS'));
        ?>
        <h3 class="prov-header"><a
            href="<?= Route::url($this->pub->link('editbase')) ?>"><?= $linkText ?></a> &raquo; <a
            href="<?= $linkUrl ?>">"<?= $this->escape($this->pub->title) ?>"</a> &raquo; <?= $headingText ?></h3>
    <?php } else { ?>
        <?php
        $linkUrl = Route::url($this->pub->link('editversion'));
        ?>
        <h3 class="publications c-header"><a
            href="<?= Route::url($this->pub->link('editbase')) ?>"><?= $this->title ?></a> &raquo; <span
            class="restype indlist"><?= $typetitle ?></span> <span class="indlist"><a
            href="<?= $linkUrl ?>">"<?= $this->escape($this->pub->title) ?>"</a></span> &raquo; <span
            class="indlist"> &raquo; <?= ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSIONS')) ?></span>
        </h3>
    <?php } ?>
    </div>
    <div class="list-editing">
     <p><?= ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_TOTAL_VERSIONS')) ?>: <span
         class="prominent"><?= count($this->versions) ?></span></p>
    </div>
    <?php if ($this->versions) { ?>
        <table class="listing">
         <thead>
            <tr>
                <th class="tdmini" aria-label="#">#</th>
                <th class="tdmini"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION'); ?></th>
                <th><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_TITLE'); ?></th>
                <th><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATUS'); ?></th>
                <th>
                    <?= Lang::txt('PLG_PROJECTS_PUBLICATIONS_DOI') . '/' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_ARK') ?>
                </th>
                <th><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_OPTIONS')); ?></th>
            </tr>
         </thead>
         <tbody>
        <?php foreach ($this->versions as $v) {
            // Get DOI
            $doi = $v->doi ? 'doi:' . $v->doi : '';
            $doi_notice = $doi ? $doi : Lang::txt('PLG_PROJECTS_PUBLICATIONS_NA');

            // Version status
            $status = $this->pub->getStatusName($v->state);
            $class  = $this->pub->getStatusCss($v->state);
            $date   = $this->pub->getStatusDate($v);

            $options = '<a href="' . Route::url($this->pub->link('edit') . '&version=' . $v->version_number) . '">'
            . Lang::txt('PLG_PROJECTS_PUBLICATIONS_MANAGE_VERSION') . '</a>';

            $options .= '<span class="block"><a href="' . Route::url('index.php?option=com_publications'
            . '&id=' . $this->pid . '&v=' . $v->version_number) . '">'
            . Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_PAGE') . '</a></span>';

            ?>
            <tr class="mini <?php echo $v->main == 1 ? ' vprime' : ''; ?>">
                <td class="centeralign"><?php echo $v->version_number ? $v->version_number : ''; ?></td>
                <td><?php echo $this->escape($v->version_label); ?></td>
                <td><?php echo $this->escape($v->title); ?></td>
                <td>
                    <span class="<?php echo $class; ?>"><?php echo $status; ?></span>
                    <?php echo $date ? '<span class="block ipadded faded">' . $date . '</span>' : ''; ?>
                </td>
                <td><?php echo $doi_notice; ?></td>
                <td><?php echo $options; ?></td>
            </tr>
        <?php } ?>
         </tbody>
        </table>
    <?php } ?>
</form>
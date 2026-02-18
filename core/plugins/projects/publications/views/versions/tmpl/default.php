<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get publication properties
$typetitle = \Components\Publications\Helpers\Html::writePubCategory(
    $this->pub->category()->alias,
    $this->pub->category()->name
);

?>
<form action="<?php echo Route::url($this->pub->link('editbase')); ?>" method="post" id="plg-form" >
    <div id="plg-header">
    <?php
    $editBaseUrl = Route::url($this->pub->link('editbase'));
    $editVersionUrl = Route::url($this->pub->link('editversion'));
    $submissionsLabel = ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS'));
    $versionsLabel = ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSIONS'));
    ?>
    <?php if ($this->project->isProvisioned()) { ?>
        <h3 class="prov-header">
            <a href="<?php echo $editBaseUrl; ?>"><?php
                echo $submissionsLabel;
            ?></a>
            &raquo;
            <a href="<?php echo $editVersionUrl; ?>">"<?php
                echo $this->pub->title;
            ?>"</a>
            &raquo;
            <?php echo $versionsLabel; ?>
        </h3>
    <?php } else { ?>
        <h3 class="publications c-header">
            <a href="<?php echo $editBaseUrl; ?>"><?php
                echo $this->title;
            ?></a>
            &raquo;
            <span class="restype indlist"><?php echo $typetitle; ?></span>
            <span class="indlist">
                <a href="<?php echo $editVersionUrl; ?>">"<?php
                    echo $this->pub->title;
                ?>"</a>
            </span>
            &raquo;
            <span class="indlist">
                &raquo;
                <?php echo $versionsLabel; ?>
            </span>
        </h3>
    <?php } ?>
    </div>
    <div class="list-editing">
     <?php $totalLabel = ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_TOTAL_VERSIONS')); ?>
     <p><?php echo $totalLabel; ?>:
        <span class="prominent"><?php echo count($this->versions); ?></span>
     </p>
    </div>
    <?php if ($this->versions) { ?>
        <table class="listing">
         <thead>
            <tr>
                <th class="tdmini"></th>
                <th class="tdmini"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION'); ?></th>
                <th><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_TITLE'); ?></th>
                <th><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATUS'); ?></th>
                <?php
                $doiArkLabel = Lang::txt('PLG_PROJECTS_PUBLICATIONS_DOI')
                    . '/' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_ARK');
                ?>
                <th><?php echo $doiArkLabel; ?></th>
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
            <tr class="mini <?php if ($v->main == 1) {
                echo ' vprime';
                            } ?>">
                <td class="centeralign"><?php echo $v->version_number ? $v->version_number : ''; ?></td>
                <td><?php echo $v->version_label; ?></td>
                <td><?php echo $v->title; ?></td>
                <td>
                    <span class="<?php echo $class; ?>"><?php echo $status; ?></span>
                    <?php if ($date) {
                        echo '<span class="block ipadded faded">' . $date . '</span>';
                    } ?>
                </td>
                <td><?php echo $doi_notice; ?></td>
                <td><?php echo $options; ?></td>
            </tr>
        <?php } ?>
         </tbody>
        </table>
    <?php } ?>
</form>
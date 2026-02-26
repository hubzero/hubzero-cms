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

$row = $this->row;

// What's the publication status?
$status = $row->getStatusName();
$class  = $row->getStatusCss();
$date   = $row->getStatusDate();

$trClass = $this->i % 2 == 0 ? ' even' : ' odd';

$pubUrl = '';
if ($this->get('new_pubs')) {
    $pubUrl = 'pubs/#/pubs/' . $row->get('publication_id') . '/v/' . $row->get('version_id') . '/edit';
} else {
    $pubUrl = Route::url($this->project->link('publications'))
        . '&pid='
        . $row->get('id')
        . '&action=continue&version=dev';
}

?>
<tr class="mini faded mline<?php echo $trClass; ?>" id="tr_<?php echo $row->get('id'); ?>">
<td class="pub-image"><img src="<?php echo Route::url($row->link('thumb')); ?>" alt="" /></td>
<td><a href="<?php echo $pubUrl; ?>" <?php if ($row->get('abstract')) {
    echo 'title="' . $this->escape($row->get('abstract')) . '"';
             } ?>><?php echo $row->get('title'); ?></a> v.<?php echo $row->get('version_label'); ?>
</td>
<td><?php echo $row->get('id'); ?></td>
<td class="restype"><?php echo $row->get('base'); ?></td>
<td class="showstatus">
    <span class="<?php echo $class; ?> major_status"><?php echo $status; ?></span>
    <span class="mini faded block"><?php echo $date; ?></span>
</td>
<td>
<?php
$devLabel = $row->versionProperty('version_label', 'dev');
if ($devLabel && $devLabel != $row->get('version_label')) {
    $devUrl = Route::url(
        $this->project->link('publications')
        . '&pid=' . $row->get('id') . '&version=dev'
    );
    echo '<a href="' . $devUrl
    . '">&raquo; ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEW_VERSION_DRAFT')
    . ' <strong>' . $devLabel  . '</strong></a> '
    . Lang::txt('PLG_PROJECTS_PUBLICATIONS_IN_PROGRESS');
    if ($this->project->access('content')) {
        if ($this->get('new_pubs')) {
            echo ' <span><a href="pubs/#/pubs/'
                . $row->get('publication_id')
                . '/v/'
                . $row->get('version_id')
                . '/edit" class="btn mini icon-next">'
                . Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTINUE')
                . '</a></span>';
        } else {
            echo ' <span class="block"><a href="' . Route::url($this->project->link('publications') . '&pid=' .
            $row->get('id') . '&action=continue&version=dev') . '" class="btn mini icon-next">' .
            Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTINUE') . '</a></span>';
        }
    }
} elseif ($row->isDev() && $this->project->access('content')) {
    if ($this->get('new_pubs')) {
        echo ' <span><a href="pubs/#/pubs/'
            . $row->get('publication_id')
            . '/v/'
            . $row->get('version_id')
            . '/edit" class="btn mini icon-next">'
            . Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTINUE')
            . '</a></span>';
    } else {
        echo ' <span><a href="' . Route::url($this->project->link('publications') . '&pid=' . $row->get('id') .
        '&action=continue&version=dev') . '" class="btn mini icon-next">' .
        Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTINUE') . '</a></span>';
    }
} elseif ($row->isWorked()) {
    $workedUrl = Route::url(
        $this->project->link('publications')
        . '&pid=' . $row->get('id')
        . '&action=continue&version=' . $row->get('version_number')
    );
    echo ' <span><a href="' . $workedUrl
    . '" class="btn mini icon-next btn-action">'
    . Lang::txt('PLG_PROJECTS_PUBLICATIONS_MAKE_CHANGES')
    . '</a></span>';
} ?></td>

<?php
    $versionsUrl = Route::url(
        $this->project->link('publications')
        . '&pid=' . $row->get('id') . '&action=versions'
    );
    $versionsTitle = Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_VERSIONS');
    ?>
<td class="centeralign mini faded"><?php if ($row->versions > 0) {
    ?><a href="<?php echo $versionsUrl; ?>"
        title="<?php echo $versionsTitle; ?>"><?php
                                   } ?><?php echo $row->get('versions'); ?><?php if ($row->get('versions') > 0) {
    ?></a><?php
                                   } ?></td>
<td class="autowidth">
    <a href="<?php echo Route::url($this->project->link('publications') . '&pid=' . $row->get('id')); ?>"
        class="manageit"
        title="<?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MANAGE_VERSION')); ?>">&nbsp;</a>

    <?php
        $pubPageUrl = Route::url(
            'index.php?option=com_publications&id=' . $row->get('id')
            . '&v=' . $row->get('version_number')
        );
        $pubPageTitle = Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_PUB_PAGE');
        ?>
    <a href="<?php echo $pubPageUrl; ?>"
        class="public-page"
        title="<?php echo $pubPageTitle; ?>">&nbsp;</a></td>
</tr>

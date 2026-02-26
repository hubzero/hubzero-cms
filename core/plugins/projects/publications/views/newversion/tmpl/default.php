<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

// Get publication properties
$typetitle = \Components\Publications\Helpers\Html::writePubCategory(
    $this->pub->category()->alias,
    $this->pub->category()->name
);

// Suggest new label
$suggested = is_numeric($this->pub->version_label) ? number_format(($this->pub->version_label + 1.0), 1, '.', '') : '';

?>

<?php if ($this->ajax) { ?>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEW_VERSION_PROVIDE_LABEL'); ?></h3>
<?php } ?>
<?php
// Display error  message
if ($this->getError()) {
    echo '<p class="error">' . $this->getError() . '</p>';
} ?>

<?php if (!$this->ajax) { ?>
<form action="<?php echo Route::url($this->project->link('publications')); ?>" method="post" id="plg-form" >
    <div id="plg-header">
    <?php
        $editBaseUrl = Route::url($this->pub->link('editbase'));
        $editVersionUrl = Route::url($this->pub->link('editversion'));
        $mySubmissions = ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS'));
        $newVersion = ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEW_VERSION'));
        $pubsUrl = Route::url($this->project->link('publications'));
    ?>
    <?php if ($this->project->isProvisioned()) { ?>
        <h3 class="prov-header"><a href="<?php echo $editBaseUrl; ?>"><?php
            echo $mySubmissions; ?></a>
            &raquo; <a href="<?php echo $editVersionUrl; ?>">"<?php
            echo $this->pub->title; ?>"</a>
            &raquo; <?php echo $newVersion; ?></h3>
    <?php } else { ?>
        <h3 class="publications"><a href="<?php echo $pubsUrl; ?>"><?php
            echo $this->title; ?></a>
            &raquo; <span class="restype indlist"><?php echo $typetitle; ?></span>
            <span class="indlist"><a href="<?php echo $editVersionUrl; ?>">"<?php
            echo $this->pub->title; ?>"</a></span>
            <span class="indlist"> &raquo; <?php echo $newVersion; ?></span>
        </h3>
    <?php } ?>
    </div>
    <h4><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEW_VERSION_PROVIDE_LABEL'); ?></h4>
<?php } else { ?>
<form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->pub->link('editversion')); ?>">
<?php } ?>
    <fieldset>
        <input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" id="projectid" />
        <input type="hidden" name="active" value="publications" />
        <input type="hidden" name="action" value="savenew" />
        <input type="hidden"
            name="option"
            value="<?php echo $this->project->isProvisioned() ? 'com_publications' : $this->option; ?>"/>
        <input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
        <input type="hidden" name="selected_version" value="<?php echo $this->selected_version; ?>" />
        <input type="hidden"
            name="provisioned"
            id="provisioned"
            value="<?php echo $this->project->isProvisioned() ? 1 : 0; ?>"/>
        <?php if ($this->project->isProvisioned()) { ?>
        <input type="hidden" name="task" value="submit" />
        <?php } ?>
    </fieldset>
    <div <?php if (!$this->ajax) {
        echo 'class="vform"';
         } ?>>
        <p>
            <?php $prevLabel = ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PREVIOUS_LABEL')); ?>
            <span class="faded"><?php echo $prevLabel; ?></span>
            <?php echo $this->pub->version_label; ?>
        </p>
        <label>
            <span class="faded block"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEW_VERSION_LABEL'); ?></span>
            <input type="text" name="version_label"  value="<?php echo $suggested; ?>" />
        </label>
    </div>
        <p class="submitarea">
            <input type="submit"
                class="btn active btn-success"
                value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_NEW_VERSION'); ?>"/>
                <?php if ($this->ajax) { ?>
                <input type="reset"
                    id="cancel-action"
                    class="btn btn-cancel"
                    value="<?php echo Lang::txt('JCANCEL'); ?>"/>
                <?php } else {
                    $rtn = Request::getString('HTTP_REFERER', Route::url($this->pub->link('editversion')), 'server');
                    ?>
                <span class="btn btncancel"><a href="<?php echo $rtn; ?>"><?php echo Lang::txt('JCANCEL'); ?></a></span>
                <?php } ?>
        </p>
</form>
<?php if ($this->ajax) { ?>
</div>
<?php }

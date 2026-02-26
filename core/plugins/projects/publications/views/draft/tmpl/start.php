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

?>
<div id="plg-header">
<?php
$routeUrl = Route::url($this->route);
$mySubmissions = ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS'));
$startPub = ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION'));
?>
<?php if ($this->project->isProvisioned()) { ?>
<h3 class="prov-header">
    <a href="<?php echo $routeUrl; ?>"><?php echo $mySubmissions; ?></a>
    &raquo; <?php echo $startPub; ?>
</h3>
<?php } else { ?>
<h3 class="publications c-header">
    <a href="<?php echo $routeUrl; ?>"><?php echo $this->title; ?></a>
    &raquo;
    <span class="indlist"><?php echo $startPub; ?></span>
</h3>
<?php } ?>
</div>
<?php if ($this->project->isProvisioned()) { ?>
<div class="grid">
    <div class="col span9">
<?php } ?>
<div class="welcome">
    <h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEWPUB_WHAT'); ?></h3>
    <div id="suggestions" class="suggestions">
        <?php for ($i = 0; $i < count($this->choices); $i++) {
            $current = $this->choices[$i];
            $action = 'publication';

            ?>      
            <?php
            $actionUrl = Route::url(
                $this->route . '&action=' . $action
                . '&base=' . $current->alias
            );
            $contactEmail = $this->pubConfig->get('contact_email');
            $doiPublisher = $this->pubConfig->get('doi_publisher');
            $pubTypeText = '';
            if (!empty($contactEmail) && !empty($doiPublisher)) {
                $filesType = Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEWPUB_FILES');
                $dbType = Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEWPUB_DATABASES');
                $seriesType = Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEWPUB_SERIES');
                if ($current->type == $filesType) {
                    $pubTypeText = Lang::txt(
                        'PLG_PROJECTS_PUBLICATIONS_NEWPUB_FILES_DESCRIPTION'
                    );
                } elseif ($current->type == $dbType) {
                    $pubTypeText = Lang::txt(
                        'PLG_PROJECTS_PUBLICATIONS_NEWPUB_DATABASES_DESCRIPTION',
                        $contactEmail,
                        $doiPublisher
                    );
                } elseif ($current->type == $seriesType) {
                    $pubTypeText = Lang::txt(
                        'PLG_PROJECTS_PUBLICATIONS_NEWPUB_SERIES_DESCRIPTION',
                        $contactEmail,
                        $doiPublisher
                    );
                }
            }
            ?>
        <div class="s-<?php echo $current->alias; ?>">
            <p>
                <a href="<?php echo $actionUrl; ?>">
                    <?php echo $current->type; ?>
                    <span class="block">
                        <?php echo $current->description; ?>
                    </span>
                </a>
                <?php if ($pubTypeText) { ?>
                <span class="pubType">
                    <?php echo $pubTypeText; ?>
                </span>
                <?php } ?>
            </p>
        </div>
        <?php } ?>
        <div class="clear"></div>
    </div>
</div>
<?php if ($this->project->isProvisioned()) { ?>
    </div><!-- / .subject -->
    <div class="col span3 omega">
        <div id="start-projectnote">
            <h4><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEED_PROJECT'); ?></h4>
            <p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTRIB_START'); ?></p>
            <?php
            $viewProjectsTxt = Lang::txt(
                'PLG_PROJECTS_PUBLICATIONS_VIEW_YOUR_PROJECTS'
            );
            $startProjectTxt = Lang::txt(
                'PLG_PROJECTS_PUBLICATIONS_START_PROJECT'
            );
            ?>
            <p class="getstarted-links">
                <a href="/members/myaccount/projects">
                    <?php echo $viewProjectsTxt; ?>
                </a>
                |
                <a href="/projects/start" class="addnew">
                    <?php echo $startProjectTxt; ?>
                </a>
            </p>
        </div>
    </div><!-- / .aside -->
</div>
<?php }

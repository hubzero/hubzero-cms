<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$privacy = !$this->model->isPublic()
    ? Lang::txt('COM_PROJECTS_PRIVATE')
    : Lang::txt('COM_PROJECTS_PUBLIC');
$config  = $this->model->config();

$alias = $this->model->get('alias');
$editInfoUrl = Route::url(
    'index.php?option=' . $this->option
    . '&task=edit&alias=' . $alias
    . '&active=info'
);
$canEdit = $this->model->access('manager')
    || ($this->model->access('content')
    && $config->get('edit_description'));
?>
<div id="plg-header">
    <h3 class="inform"><?php echo Lang::txt('COM_PROJECTS_PROJECT_INFO'); ?></h3>
</div>

<?php if ($canEdit) { ?>
    <p class="editing">
        <a href="<?php echo $editInfoUrl; ?>"><?php
            echo Lang::txt('COM_PROJECTS_EDIT_PROJECT');
        ?></a>
    </p>
<?php } ?>

<div id="basic_info">
    <table id="infotbl">
        <tbody>
            <tr>
                <th class="htd"><?php echo Lang::txt('COM_PROJECTS_TITLE'); ?></th>
                <td><?php echo $this->escape($this->model->get('title')); ?></td>
                <?php if ($config->get('grantinfo', 0) && $this->model->params->get('grant_title')) { ?>
                    <?php
                    $params = $this->model->params;
                    $grantTitleLabel = Lang::txt(
                        'COM_PROJECTS_SETUP_TERMS_GRANT_TITLE'
                    );
                    $grantPiLabel = Lang::txt(
                        'COM_PROJECTS_SETUP_TERMS_GRANT_PI'
                    );
                    $awardNumLabel = Lang::txt(
                        'COM_PROJECTS_SETUP_TERMS_AWARD_NUMBER'
                    );
                    $grantAgencyLabel = Lang::txt(
                        'COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY'
                    );
                    $grantBudgetLabel = Lang::txt(
                        'COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'
                    );
                    $editSettingsUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&task=edit&alias=' . $alias
                        . '&active=settings'
                    );
                    ?>
                    <td rowspan="5" class="grantinfo">
                        <h4><?php echo Lang::txt('COM_PROJECTS_INFO_GRANTINFO'); ?></h4>
                        <p>
                            <span class="block">
                                <span class="faded"><?php echo $grantTitleLabel; ?>:</span>
                                <?php echo $params->get('grant_title'); ?>
                            </span>
                            <span class="block">
                                <span class="faded"><?php echo $grantPiLabel; ?>:</span>
                                <?php echo $params->get('grant_PI', 'N/A'); ?>
                            </span>
                            <span class="block">
                                <span class="faded"><?php echo $awardNumLabel; ?>:</span>
                                <?php echo $params->get('award_number', 'N/A'); ?>
                            </span>
                            <span class="block">
                                <span class="faded"><?php echo $grantAgencyLabel; ?>:</span>
                                <?php echo $params->get('grant_agency', 'N/A'); ?>
                            </span>
                            <span class="block">
                                <span class="faded"><?php echo $grantBudgetLabel; ?>:</span>
                                <?php echo $params->get('grant_budget', 'N/A'); ?>
                            </span>
                            <?php if ($this->model->access('manager')) { ?>
                                <a href="<?php echo $editSettingsUrl; ?>"><?php
                                    echo Lang::txt('COM_PROJECTS_EDIT_THIS');
                                ?></a>
                            <?php } ?>
                        </p>
                    </td>
                <?php } ?>
            </tr>
            <tr>
                <th class="htd"><?php echo Lang::txt('COM_PROJECTS_ALIAS'); ?></th>
                <td><?php echo $this->model->get('alias'); ?></td>
            </tr>
            <tr>
                <th class="htd"><?php echo Lang::txt('COM_PROJECTS_ACCESS'); ?></th>
                <?php
                $previewUrl = Route::url(
                    'index.php?option=' . $this->option
                    . '&alias=' . $alias . '&preview=1'
                );
                $previewLabel = Lang::txt(
                    'COM_PROJECTS_PREVIEW_PUBLIC_PROFILE'
                );
                ?>
                <td>
                    <?php echo $privacy; ?>
                    <?php if ($this->model->isPublic()) { ?>
                        <span class="mini faded">[<a
                            href="<?php echo $previewUrl; ?>"><?php
                                echo $previewLabel;
                            ?></a>]</span>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th class="htd"><?php echo Lang::txt('COM_PROJECTS_CREATED'); ?></th>
                <td><?php echo $this->model->created('date'); ?></td>
            </tr>
            <tr>
                <th class="htd"><?php echo Lang::txt('COM_PROJECTS_OWNER'); ?></th>
                <?php
                $ownerName = $this->model->groupOwner()
                    ? $this->model->groupOwner('description')
                    : $this->model->owner('name');
                ?>
                <td><?php echo $ownerName; ?></td>
            </tr>

            <?php
                // This is for the admin-defined project information
            if ($this->info) {
                foreach ($this->info as $field) { ?>
                        <tr>
                            <th class="htd"><?php echo $field->label; ?></th>
                            <td><?php echo $field->value; ?></td>
                        </tr>
                <?php } // end foreach
            } // end if
            ?>

            <?php if ($this->model->about('parsed')) {
                $val = $this->model->about('parsed');
                $componentPath = Component::path('com_redirect');
                if ($componentPath) {
                    $val = \Components\Redirect\Helpers\Converter::convert($val);
                } else {
                    $val = preg_replace(
                        '#<a\s[^>]*href="([^"]*)"[^>]*?>(.*?)</a>#is',
                        "<a href='$1' rel='nofollow'>$2</a>",
                        $val
                    );
                }
                ?>
            <tr>
                <th class="htd"><?php echo Lang::txt('COM_PROJECTS_ABOUT'); ?></th>
                <td><?php echo $val; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div><!-- / .basic info -->

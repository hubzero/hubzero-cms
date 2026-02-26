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

$this->css();

$label = Lang::txt('COM_PUBLICATIONS_PUBLICATION_MANAGER');
$label2 = Lang::txt('COM_PUBLICATIONS_PUBLICATION');
$label3 = Lang::txt('COM_PUBLICATIONS_VERSIONS');
Toolbar::title(
    $label . ' - ' . $label2 . ': #' . $this->pub->id . ' - ' . $label3,
    'publications'
);
Toolbar::spacer();
Toolbar::cancel();

?>
<?php
$mgrUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
$editUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=edit&id[]=' . $this->pub->id
);
$mgrLabel = Lang::txt('COM_PUBLICATIONS_PUBLICATION_MANAGER');
$pubLabel = Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ' #' . $this->pub->id;
$versLabel = Lang::txt('COM_PUBLICATIONS_VERSIONS');
?>
<p class="crumbs">
    <a href="<?php echo $mgrUrl; ?>"><?php echo $mgrLabel; ?></a> &raquo;
    <a href="<?php echo $editUrl; ?>"><?php echo $pubLabel; ?></a>
    &raquo; <?php echo $versLabel; ?>
</p>

<form
    action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"
    method="post"
    name="adminForm"
>
    <table class="adminlist">
        <thead>
            <tr>
                <th class="tdmini"></th>
                <th><?php echo Lang::txt('COM_PUBLICATIONS_ID'); ?></th>
                <th class="tdmini"><?php echo Lang::txt('COM_PUBLICATIONS_VERSION'); ?></th>
                <th><?php echo Lang::txt('COM_PUBLICATIONS_TITLE'); ?></th>
                <th><?php echo Lang::txt('COM_PUBLICATIONS_STATUS'); ?></th>
                <th><?php echo Lang::txt('COM_PUBLICATIONS_DOI'); ?></th>
                <th><?php echo ucfirst(Lang::txt('COM_PUBLICATIONS_OPTIONS')); ?></th>
            </tr>
         </thead>
        <tbody>
        <?php
        $k = 0;
        foreach ($this->versions as $v) :
            // Get DOI
            $doi = $v->doi ? 'doi:' . $v->doi : '';
            $doi_notice = $doi ? $doi : Lang::txt('COM_PUBLICATIONS_NA');

            // Version status
            $status = $this->pub->getStatusName($v->state);
            $class  = $this->pub->getStatusCss($v->state);
            $date   = $this->pub->getStatusDate($v);
            ?>
            <tr class="mini <?php if ($v->main == 1) {
                echo ' vprime';
                            } ?>">
                <td class="centeralign"><?php echo $v->version_number ? $v->version_number : ''; ?></td>
                <td><?php echo $v->id; ?></td>
                <td><?php echo $v->version_label; ?></td>
                <td><?php echo $v->title; ?></td>
                <td class="v-status">
                    <span class="<?php echo $class; ?>"><?php echo $status; ?></span>
                    <?php
                    if ($date) :
                        echo '<span class="block faded">' . $date . '</span>';
                    endif;
                    ?>
                </td>
                <td><?php echo $doi_notice; ?></td>
                <?php
                $vEditUrl = Route::url(
                    'index.php?option=' . $this->option
                    . '&controller=' . $this->controller
                    . '&task=edit&id[]=' . $this->pub->id
                    . '&version=' . $v->version_number
                );
                $manageTxt = Lang::txt('COM_PUBLICATIONS_MANAGE_VERSION');
                ?>
                <td><a href="<?php echo $vEditUrl; ?>"><?php echo $manageTxt; ?></a></td>
            </tr>
            <?php
        endforeach;
        ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />

    <?php echo Html::input('token'); ?>
</form>

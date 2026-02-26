<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

// add styles & scripts
$this->css()
     ->js()
     ->css('jquery.colpick.css', 'system')
     ->js('jquery.colpick', 'system');

$title = Lang::txt('COM_GROUPS_PAGES_ADD_CATEGORY');
if ($this->category->get('id')) :
    $title = Lang::txt('COM_GROUPS_PAGES_EDIT_CATEGORY');
endif;
?>
<?php if (!Request::getInt('no_html', 0)) : ?>
<header id="content-header">
    <h2><?php echo Lang::txt($title); ?></h2>

    <div id="content-header-extra">
        <ul id="useroptions">
            <?php $url = Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')); ?>
            <li><a class="icon-prev prev btn" href="<?php echo $url . '&controller=pages#categories'; ?>">
                <?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_MANAGE_PAGES'); ?>
            </a></li>
        </ul>
    </div>
</header>
<?php endif; ?>

<section class="main section">
    <?php foreach ($this->notifications as $notification) : ?>
        <p class="<?php echo $notification['type']; ?>"><?php echo $notification['message']; ?></p>
    <?php endforeach; ?>

    <?php $val = Route::url(
        'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&controller=categories&task=savecategory'
    ); ?>
    <form action="<?php echo $val; ?>" method="POST" id="hubForm" class="full editcategory">
        <fieldset>
            <legend><?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_DETAILS'); ?></legend>

            <div class="form-group">
                <label for="field-category-title">
                    <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_CATEGORY_TITLE'); ?>
                    <?php $txt = Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?>
                    <?php echo $txt1?>: <span class="required"><?php echo $txt; ?></span>
                    <input
                        type="text"
                        name="category[title]"
                        id="field-category-title"
                        class="form-control"
                        value="<?php echo $this->escape($this->category->get('title')); ?>" />
                </label>
            </div>

            <div class="form-group">
                <label for="field-category-color">
                    <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR'); ?>
                    <?php $txt = Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?>
                    <?php echo $txt1?>: <span class="optional"><?php echo $txt; ?></span>
                    <input
                        type="text"
                        maxlength="6"
                        name="category[color]"
                        id="field-category-color"
                        class="form-control"
                        value="<?php echo $this->escape($this->category->get('color')); ?>" />
                </label>
            </div>
        </fieldset>

        <p class="submit">
            <?php $txt = Lang::txt('COM_GROUPS_PAGES_SAVE_CATEGORY'); ?>
            <button type="submit" class="btn btn-info save icon-save"><?php echo $txt; ?></button>
            <?php $val1 = Route::url(
                'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&controller=pages#categories'
            ); ?>
            <?php $val = Lang::txt('COM_GROUPS_PAGES_CANCEL'); ?>
            <a href="<?php echo $val1; ?>" class="btn cancel"><?php echo $val; ?></a>
        </p>
        <input type="hidden" name="option" value="com_groups" />
        <input type="hidden" name="controller" value="categories" />
        <input type="hidden" name="task" value="save" />
        <input type="hidden" name="category[id]" value="<?php echo intval($this->category->get('id', 0)); ?>" />
    </form>
</section>
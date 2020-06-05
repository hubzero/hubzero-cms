<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// add styles & scripts
$this->css()
     ->js()
     ->css('jquery.colpick.css', 'system')
     ->js('jquery.colpick', 'system');

$title = Lang::txt('COM_GROUPS_PAGES_ADD_CATEGORY');
if ($this->category->get('id')):
	$title = Lang::txt('COM_GROUPS_PAGES_EDIT_CATEGORY');
endif;
?>
<?php if (!Request::getInt('no_html', 0)) : ?>
<header id="content-header">
	<h2><?php echo Lang::txt($title); ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-prev prev btn" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages#categories'); ?>">
				<?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_MANAGE_PAGES'); ?>
			</a></li>
		</ul>
	</div>
</header>
<?php endif; ?>

<section class="main section">
	<?php foreach ($this->notifications as $notification): ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $notification['message']; ?></p>
	<?php endforeach; ?>

	<form action="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=categories&task=savecategory'); ?>" method="POST" id="hubForm" class="full editcategory">
		<fieldset>
			<legend><?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_DETAILS'); ?></legend>

			<div class="form-group">
				<label for="field-category-title">
					<?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_TITLE')?>: <span class="required"><?php echo Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?></span>
					<input type="text" name="category[title]" id="field-category-title" class="form-control" value="<?php echo $this->escape($this->category->get('title')); ?>" />
				</label>
			</div>

			<div class="form-group">
				<label for="field-category-color">
					<?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR')?>: <span class="optional"><?php echo Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?></span>
					<input type="text" maxlength="6" name="category[color]" id="field-category-color" class="form-control" value="<?php echo $this->escape($this->category->get('color')); ?>" />
				</label>
			</div>
		</fieldset>

		<p class="submit">
			<button type="submit" class="btn btn-info save icon-save"><?php echo Lang::txt('COM_GROUPS_PAGES_SAVE_CATEGORY'); ?></button>
			<a href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages#categories'); ?>" class="btn cancel"><?php echo Lang::txt('COM_GROUPS_PAGES_CANCEL'); ?></a>
		</p>
		<input type="hidden" name="option" value="com_groups" />
		<input type="hidden" name="controller" value="categories" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="category[id]" value="<?php echo intval($this->category->get('id', 0)); ?>" />
	</form>
</section>